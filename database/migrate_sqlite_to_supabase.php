<?php

/**
 * Script de Migração: SQLite Local -> Supabase PostgreSQL
 * Execute via terminal: php database/migrate_sqlite_to_supabase.php
 */

// Configurações do SQLite Local
$sqlitePath = __DIR__ . '/database.sqlite';

if (!file_exists($sqlitePath)) {
    die("Erro: Banco de dados SQLite local não encontrado em: $sqlitePath\n");
}

// Mapeia o status atual do processo para o checkpoint/instância do fluxo
function perfilDoStatus($status)
{
    $map = [
        'Aguardando Análise' => 'SPU/UF',
        'Indicação do Imóvel' => 'SPU/UF',
        'Indicação do imóvel' => 'SPU/UF',
        'Diagnóstico do Imóvel' => 'SPU/UF',
        'Análise de Viabilidade' => 'SPU/UF',
        'Validação - Chefia' => 'Chefia',
        'Validação - Coordenação' => 'Coordenação',
        'Deliberação - Superintendência' => 'Superintendência',
        'Deliberado - SPU/UF' => 'Superintendência',
        'Indeferido - SPU/UF' => 'Superintendência',
        'Conformidade Prévia' => 'Equipe C.G.',
        'Validação - Equipe C.G.' => 'Equipe C.G.',
        'Validação - Coordenação-Geral' => 'Coordenação-Geral',
        'Validação - Direção' => 'Direção',
        'Deliberação - CDE' => 'CDE',
        'Deliberado - CDE' => 'CDE',
        'Indeferido - CDE' => 'CDE',
        'Concluído - CDE' => 'CDE',
        'Cancelado' => 'SPU/UF',
    ];
    return $map[$status] ?? 'SPU/UF';
}

// Configurações do Supabase (Insira os mesmos dados usados no Render)
// Prioriza variáveis de ambiente; caso contrário, pergunta no terminal.
// Variáveis aceitas: SUPABASE_PGHOST, SUPABASE_PGPORT, SUPABASE_PGDATABASE,
//                     SUPABASE_PGUSER, SUPABASE_PGPASSWORD
$pgHost = getenv('SUPABASE_PGHOST');
$pgPort = getenv('SUPABASE_PGPORT') ?: '6543';
$pgDb   = getenv('SUPABASE_PGDATABASE') ?: 'postgres';
$pgUser = getenv('SUPABASE_PGUSER');
$pgPass = getenv('SUPABASE_PGPASSWORD');

if (!$pgHost) {
    echo "--- Configurações de Conexão com o Supabase ---\n";
    $pgHost = readline("Host do Supabase (ex: aws-1-us-west-2.pooler.supabase.com): ");
    $pgPort = readline("Porta (pressione Enter para usar 6543): ") ?: '6543';
    $pgDb   = readline("Nome do Banco (pressione Enter para usar postgres): ") ?: 'postgres';
    $pgUser = readline("Usuário (ex: postgres.btmpxettyjbtkfkcfmmu): ");
    $pgPass = readline("Senha do Banco (Supabase): ");
}

echo "\nConectando aos bancos de dados...\n";

try {
    // 1. Conexão SQLite
    $sqlite = new PDO("sqlite:$sqlitePath");
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "✔ Conectado ao SQLite local.\n";

    // 2. Conexão PostgreSQL (Supabase)
    $dsn = "pgsql:host=$pgHost;port=$pgPort;dbname=$pgDb;sslmode=require";
    $postgres = new PDO($dsn, $pgUser, $pgPass);
    $postgres->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $postgres->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "✔ Conectado ao Supabase PostgreSQL.\n";
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage() . "\n");
}

// Obter tabelas do SQLite
$stmt = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "\nIniciando cópia de dados para " . count($tables) . " tabelas...\n";

// Confirmação de segurança: a migração TRUNCA todas as tabelas no Supabase
$force = getenv('SUPABASE_FORCE') === '1' || in_array('--force', $argv ?? []);
if (!$force) {
    echo "\n⚠ ATENÇÃO: TODAS as tabelas do Supabase serão TRUNCADAS (CASCADE) e\n";
    echo "recopiadas do SQLite local. Os dados atuais no Supabase serão APAGADOS.\n";
    echo "Para pular esta confirmação, defina SUPABASE_FORCE=1 ou use --force.\n";
    echo "Deseja continuar? (sim/nao): ";
    $confirm = strtolower(trim(fgets(STDIN)));
    if (!in_array($confirm, ['sim', 's', 'yes', 'y'])) {
        die("Abortado. Nenhum dado foi alterado.\n");
    }
}

try {
    // Desabilitar triggers (foreign keys) no PostgreSQL temporariamente
    $postgres->exec("SET session_replication_role = 'replica';");
    echo "✔ Chaves estrangeiras temporariamente desabilitadas no Supabase.\n\n";

    foreach ($tables as $table) {
        echo "Processando tabela: [$table]... ";
        
        // Limpar dados existentes na tabela no PostgreSQL
        $postgres->exec("TRUNCATE TABLE \"$table\" RESTART IDENTITY CASCADE;");

        // Obter dados do SQLite
        $query = $sqlite->query("SELECT * FROM \"$table\"");
        $rows = $query->fetchAll();

        if (count($rows) === 0) {
            echo "Vazia (0 registros).\n";
            continue;
        }

        // Preparar inserção no PostgreSQL
        $columns = array_keys($rows[0]);
        $colList = implode(', ', array_map(fn($c) => "\"$c\"", $columns));
        $valPlaceholders = implode(', ', array_map(fn($c) => ":$c", $columns));

        // Colunas numéricas no PostgreSQL: `''` do SQLite viraria erro de sintaxe numérica.
        $numericCols = [];
        $stmtCols = $postgres->query(
            "SELECT column_name FROM information_schema.columns "
            . "WHERE table_name = " . $postgres->quote($table)
            . " AND data_type IN ('numeric', 'decimal', 'smallint', 'integer', 'bigint', 'real', 'double precision')"
        );
        foreach ($stmtCols->fetchAll(PDO::FETCH_COLUMN) as $c) {
            $numericCols[$c] = true;
        }

        $insertQuery = "INSERT INTO \"$table\" ($colList) VALUES ($valPlaceholders)";
        $insertStmt = $postgres->prepare($insertQuery);

        $postgres->beginTransaction();
        foreach ($rows as $row) {
            // Normalizar valores do SQLite para o PostgreSQL
            foreach ($row as $key => $val) {
                if (!isset($numericCols[$key])) {
                    continue;
                }
                if ($val === null || trim((string) $val) === '') {
                    $row[$key] = null;
                } else {
                    $row[$key] = str_replace(',', '.', trim((string) $val));
                }
            }
            $insertStmt->execute($row);
        }
        $postgres->commit();

        echo "Copiado " . count($rows) . " registros com sucesso.\n";

        // Ajustar sequências de auto-incremento de ID no PostgreSQL (se a tabela tiver coluna 'id')
        if (in_array('id', $columns)) {
            try {
                $seqQuery = "SELECT pg_get_serial_sequence('$table', 'id') as seq";
                $seqRow = $postgres->query($seqQuery)->fetch();
                if ($seqRow && $seqRow['seq']) {
                    $seqName = $seqRow['seq'];
                    $postgres->exec("SELECT setval('$seqName', COALESCE((SELECT MAX(id) FROM \"$table\"), 1), (SELECT MAX(id) FROM \"$table\") IS NOT NULL)");
                }
            } catch (Exception $e) {
                // Silencia se não houver sequência associada
            }
        }
    }

    // Reabilitar triggers no PostgreSQL
    $postgres->exec("SET session_replication_role = 'origin';");
    echo "\n✔ Chaves estrangeiras reabilitadas no Supabase.\n";

    // ----------------------------------------------------------------
    // Cria as 3 tabelas do frontend (gerenciadas via REST API do Supabase,
    // não existem nas migrations do Laravel).
    // ----------------------------------------------------------------
    $frontendTables = ['tabela_requerimentos', 'tabela_status_fluxo', 'tabela_foco'];
    foreach ($frontendTables as $ft) {
        $postgres->exec("CREATE TABLE IF NOT EXISTS \"$ft\" (
            id              BIGSERIAL PRIMARY KEY,
            numero_requerimento TEXT NOT NULL,
            dados_json      JSONB,
            updated_at      TIMESTAMPTZ DEFAULT NOW()
        )");
        // Constraint única idempotente
        $postgres->exec("DO \$\$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = '{$ft}_numero_requerimento_key') THEN
                ALTER TABLE \"$ft\" ADD CONSTRAINT \"{$ft}_numero_requerimento_key\" UNIQUE (numero_requerimento);
            END IF;
        END \$\$;");
        echo "  ✔ Tabela \"$ft\" pronta.\n";
    }

    // ----------------------------------------------------------------
    // Sincronizar tabela_requerimentos do frontend (Painel Gerencial/Kanban)
    // Insere apenas os requerimentos que ainda não existem lá (não sobrescreve
    // os dados ricos/já sincronizados dos que já estavam presentes).
    // ----------------------------------------------------------------
    echo "\nSincronizando tabela_requerimentos (frontend)...\n";
    try {
        $availReqCols = [];
        foreach ($postgres->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tabela_requerimentos'")->fetchAll(PDO::FETCH_COLUMN) as $c) {
            $availReqCols[$c] = true;
        }

        $existingReqs = array_fill_keys(
            $postgres->query("SELECT numero_requerimento FROM tabela_requerimentos")->fetchAll(PDO::FETCH_COLUMN),
            true
        );

        $stmtReq = $sqlite->query(
            "SELECT r.*, p.status_atual, p.uf, p.municipio "
            . "FROM requerimentos r LEFT JOIN processos p ON p.numero_requerimento = r.numero_requerimento "
            . "ORDER BY r.numero_requerimento"
        );
        $localReqs = $stmtReq->fetchAll(PDO::FETCH_ASSOC);

        $novos = 0;
        foreach ($localReqs as $r) {
            $pk = $r['numero_requerimento'];
            if (isset($existingReqs[$pk])) {
                continue;
            }
            $dadosJson = [
                'tipo_requerimento' => $r['tipo_requerimento'] ?? null,
                'data_req' => $r['data_hora_recebimento'] ?? null,
                'processo_sei' => $r['nup_sei'] ?? null,
                'cpf_cnpj' => $r['cpf_cnpj_requerente'] ?? null,
                'interessado' => $r['nome_requerente'] ?? null,
                'telefone' => $r['contato_requerente'] ?? null,
                'cpf_cnpj_rep' => $r['cpf_cnpj_representante'] ?? null,
                'nome_rep' => $r['nome_representante'] ?? null,
                'telefone_rep' => $r['contato_representante'] ?? null,
                'projeto_prioritario' => $r['projeto_prioritario'] ?? null,
                'prioridade_legal' => $r['prioridade_legal'] ?? null,
                'uf' => $r['uf'] ?? (strlen((string) $pk) >= 2 ? strtoupper(substr($pk, 0, 2)) : null),
                'municipio' => $r['municipio'] ?? null,
                'regime_requerido' => $r['tipo_requerimento'] ?? null,
            ];

            $payload = [
                'numero_requerimento' => $pk,
                'dados_json' => json_encode($dadosJson, JSON_UNESCAPED_UNICODE),
            ];
            if (isset($availReqCols['documentos_anexados'])) {
                $payload['documentos_anexados'] = json_decode($r['documentos_anexados'] ?? '[]', true) ?? [];
            }
            if (isset($availReqCols['status'])) {
                $payload['status'] = $r['status_atual'] ?? null;
            }
            $payload['updated_at'] = date('Y-m-d H:i:s');

            $reqCols = array_keys($payload);
            $reqColList = implode(', ', array_map(fn($c2) => "\"$c2\"", $reqCols));
            $reqPh = implode(', ', array_map(fn($c2) => ":$c2", $reqCols));
            $postgres->prepare("INSERT INTO tabela_requerimentos ($reqColList) VALUES ($reqPh)")->execute($payload);
            $novos++;
        }
        echo "  tabela_requerimentos: $novos novos registros inseridos (total no Supabase: "
            . $postgres->query("SELECT COUNT(*) FROM tabela_requerimentos")->fetchColumn() . ").\n";
    } catch (Exception $e) {
        echo "  ⚠ Erro ao sincronizar tabela_requerimentos: " . $e->getMessage() . "\n";
    }

    // ----------------------------------------------------------------
    // Sincronizar tabela_status_fluxo do frontend (Painel Gerencial/Kanban)
    // Insere apenas os processos que ainda não existem lá, com o status atual
    // do Laravel; não sobrescreve os fluxos já manipulados manualmente.
    // ----------------------------------------------------------------
    echo "\nSincronizando tabela_status_fluxo (frontend)...\n";
    try {
        $availFluxoCols = [];
        foreach ($postgres->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tabela_status_fluxo'")->fetchAll(PDO::FETCH_COLUMN) as $c) {
            $availFluxoCols[$c] = true;
        }

        $existingFluxo = array_fill_keys(
            $postgres->query("SELECT numero_requerimento FROM tabela_status_fluxo")->fetchAll(PDO::FETCH_COLUMN),
            true
        );

        $stmtFluxo = $sqlite->query(
            "SELECT numero_requerimento, status_atual FROM processos ORDER BY numero_requerimento"
        );
        $novosFluxo = 0;
        foreach ($stmtFluxo->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $pk = $p['numero_requerimento'];
            if (isset($existingFluxo[$pk])) {
                continue;
            }
            $status = $p['status_atual'] ?? 'Aguardando Análise';
            $fluxoJson = [
                'status' => $status,
                'status_geral' => $status,
                'checkpoint' => $status,
                'instancia' => perfilDoStatus($status),
            ];
            $payloadFluxo = [
                'numero_requerimento' => $pk,
                'dados_json' => json_encode($fluxoJson, JSON_UNESCAPED_UNICODE),
            ];
            if (isset($availFluxoCols['updated_at'])) {
                $payloadFluxo['updated_at'] = date('Y-m-d H:i:s');
            }
            $fluxoCols = array_keys($payloadFluxo);
            $fluxoColList = implode(', ', array_map(fn($c2) => "\"$c2\"", $fluxoCols));
            $fluxoPh = implode(', ', array_map(fn($c2) => ":$c2", $fluxoCols));
            $postgres->prepare("INSERT INTO tabela_status_fluxo ($fluxoColList) VALUES ($fluxoPh)")->execute($payloadFluxo);
            $novosFluxo++;
        }
        echo "  tabela_status_fluxo: $novosFluxo novos registros inseridos (total no Supabase: "
            . $postgres->query("SELECT COUNT(*) FROM tabela_status_fluxo")->fetchColumn() . ").\n";
    } catch (Exception $e) {
        echo "  ⚠ Erro ao sincronizar tabela_status_fluxo: " . $e->getMessage() . "\n";
    }

    // Resumo final das principais tabelas
    echo "\n--- Resumo no Supabase ---\n";
    foreach (['processos', 'requerimentos', 'users', 'tramites'] as $chave) {
        $n = $postgres->query("SELECT COUNT(*) FROM \"$chave\"")->fetchColumn();
        echo "  $chave: $n\n";
    }

    echo "🎉 Migração concluída com sucesso absoluto!\n";

} catch (Exception $e) {
    if ($postgres->inTransaction()) {
        $postgres->rollBack();
    }
    // Garantir que reabilita mesmo em caso de erro
    try {
        $postgres->exec("SET session_replication_role = 'origin';");
    } catch (Exception $sec) {}
    
    echo "\n❌ Erro durante a migração: " . $e->getMessage() . "\n";
}
