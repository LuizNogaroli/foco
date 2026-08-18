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
