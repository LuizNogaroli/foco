<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Processo;
use App\Models\Foco;
use App\Models\FocoAba1;
use App\Models\FocoAba2;
use App\Models\FocoAba3;
use App\Models\FocoRip;
use App\Models\FocoCadastroMinimo;
use App\Models\EquipeServidor;
use App\Models\Tramite;

class ProcessoController extends Controller
{
    // =============================================
    // MAPA: Status -> Aba correta + proximo status
    // =============================================
    private function getAbaEStatus(string $status, string $perfil): array
    {
        $statusClean = trim($status);

        $map = [
            'Aguardando Análise' => ['aba' => 1, 'status' => 'Indicação do imóvel'],
            'Aguardando análise' => ['aba' => 1, 'status' => 'Indicação do imóvel'],
            
            'Indicação do Imóvel' => ['aba' => 1, 'status' => null],
            'Indicação do imóvel' => ['aba' => 1, 'status' => null],
            
            'Diagnóstico Preliminar' => ['aba' => 2, 'status' => null],
            'Diagnóstico preliminar do imóvel' => ['aba' => 2, 'status' => null],
            
            'Análise de Viabilidade' => ['aba' => 3, 'status' => null],
            'Análise de viabilidade' => ['aba' => 3, 'status' => null],
            
            'Validação - Chefia' => ['aba' => 7, 'status' => null],
            'Validação análise de viabilidade - Chefia' => ['aba' => 7, 'status' => null],
            
            'Validação - Coordenação' => ['aba' => 7, 'status' => null],
            'Validação análise de viabilidade - Coordenação' => ['aba' => 7, 'status' => null],
            
            'Deliberação - Superintendência' => ['aba' => 7, 'status' => null],
            'Deliberação Superintendência' => ['aba' => 7, 'status' => null],
            
            'Validação - Equipe C.G.' => ['aba' => 7, 'status' => null],
            'Conferência análise de viabilidade' => ['aba' => 7, 'status' => null],
            
            'Validação - Coordenação-Geral' => ['aba' => 7, 'status' => null],
            'Validação análise de viabilidade - Coordenação-Geral' => ['aba' => 7, 'status' => null],
            
            'Validação - Direção' => ['aba' => 7, 'status' => null],
            'Validação conferência' => ['aba' => 7, 'status' => null],
            
            'Deliberação - CDE' => ['aba' => 7, 'status' => null],
            'Manifestação CDE' => ['aba' => 7, 'status' => null],
        ];

        return $map[$statusClean] ?? ['aba' => 1, 'status' => null];
    }

    private function perfilPodeOperar(string $status, string $perfil): bool
    {
        $simulado = request()->cookie('perfil_simulado');
        if ($simulado === 'ALL' || (auth()->user() && auth()->user()->hasRole('Administrador'))) {
            return true;
        }

        $map = [
            'Aguardando Análise' => ['Equipe Destinação'],
            'Aguardando análise' => ['Equipe Destinação'],
            
            'Indicação do Imóvel' => ['Equipe Destinação'],
            'Indicação do imóvel' => ['Equipe Destinação'],
            
            'Diagnóstico Preliminar' => ['Equipe Caracterização'],
            'Diagnóstico preliminar do imóvel' => ['Equipe Caracterização'],
            
            'Análise de Viabilidade' => ['Equipe Destinação'],
            'Análise de viabilidade' => ['Equipe Destinação'],
            
            'Validação - Chefia' => ['Chefia'],
            'Validação análise de viabilidade - Chefia' => ['Chefia'],
            
            'Validação - Coordenação' => ['Coordenação'],
            'Validação análise de viabilidade - Coordenação' => ['Coordenação'],
            
            'Deliberação - Superintendência' => ['Superintendência'],
            'Deliberação Superintendência' => ['Superintendência'],
            
            'Validação - Equipe C.G.' => ['Equipe C.G.'],
            'Conferência análise de viabilidade' => ['Equipe C.G.'],
            
            'Validação - Coordenação-Geral' => ['Coordenação-Geral'],
            'Validação análise de viabilidade - Coordenação-Geral' => ['Coordenação-Geral'],
            
            'Validação - Direção' => ['Direção'],
            'Validação conferência' => ['Direção'],
            
            'Deliberação - CDE' => ['CDE'],
            'Manifestação CDE' => ['CDE'],
        ];

        return isset($map[$status]) && in_array($perfil, $map[$status]);
    }

    private function getAbasDoPerfil(string $perfil): array
    {
        $map = [
            'Equipe Destinação'   => [1, 3],
            'Equipe Caracterização' => [2],
            'Chefia'              => [7],
            'Coordenação'         => [7],
            'Superintendência'    => [7],
            'Equipe C.G.'         => [7],
            'Coordenação-Geral'   => [7],
            'Direção'             => [7],
            'CDE'                 => [7],
        ];

        return $map[$perfil] ?? [];
    }

    // =============================================
    // INDEX - Painel de Requerimentos
    // =============================================
    public function index(Request $request)
    {
        $query = Processo::query()->with('requerimento');

        if ($request->filled('uf')) {
            $ufs = (array) $request->uf;
            $query->whereIn('uf', $ufs);
        }

        if ($request->filled('municipio')) {
            $query->where('municipio', 'like', '%' . $request->municipio . '%');
        }

        if ($request->filled('status_atual')) {
            $statusesFiltro = (array) $request->status_atual;
            $query->whereIn('status_atual', $statusesFiltro);
        }

        if ($request->filled('tipo_requerimento')) {
            $tipos = (array) $request->tipo_requerimento;
            $query->whereIn('tipo_requerimento', $tipos);
        }

        // Filtro por interessado (nome do requerente)
        if ($request->filled('interessado')) {
            $query->whereHas('requerimento', function ($q) use ($request) {
                $q->where('nome_requerente', 'like', '%' . $request->interessado . '%');
            });
        }

        // Filtro por RIP
        if ($request->filled('rip')) {
            $query->whereHas('foco.rips', function ($q) use ($request) {
                $q->where('numero_rip', 'like', '%' . $request->rip . '%');
            });
        }

        // Filtro por Região (View Gerencial)
        if ($request->filled('regiao')) {
            $regioesMap = [
                'Norte'       => ['AC','AM','AP','PA','RO','RR','TO'],
                'Nordeste'    => ['AL','BA','CE','MA','PB','PE','PI','RN','SE'],
                'Centro-Oeste'=> ['DF','GO','MS','MT'],
                'Sudeste'     => ['ES','MG','RJ','SP'],
                'Sul'         => ['PR','RS','SC'],
            ];
            $ufsRegiao = [];
            foreach ((array) $request->regiao as $r) {
                if (isset($regioesMap[$r])) {
                    $ufsRegiao = array_merge($ufsRegiao, $regioesMap[$r]);
                }
            }
            if (!empty($ufsRegiao)) {
                $query->whereIn('uf', array_unique($ufsRegiao));
            }
        }

        // Filtro "Meus Processos"
        $meusProcessos = $request->boolean('meus_processos');
        if ($meusProcessos) {
            $perfil = $this->getPerfilAtual();
            $statuses = $this->getStatusesDoPerfil($perfil);
            if (!empty($statuses)) {
                $query->whereIn('status_atual', $statuses);
            }

            // Filtrar por UF com base na equipe do perfil
            $isSimulacao = auth()->user()->hasRole('Direção')
                && request()->cookie('perfil_simulado')
                && request()->cookie('perfil_simulado') !== 'ALL';

            if ($isSimulacao) {
                // Admin simulando: buscar UFs de TODOS os servidores com este perfil
                $ufs = EquipeServidor::where('perfil', $perfil)
                    ->where('ativo', true)
                    ->distinct()
                    ->pluck('uf');
            } else {
                // Usuário normal: buscar UFs do próprio usuário
                $ufs = auth()->user()->equipes()
                    ->where('perfil', $perfil)
                    ->where('ativo', true)
                    ->pluck('uf');
            }

            if ($ufs->isNotEmpty()) {
                if (!$ufs->contains('NAC')) {
                    $query->whereIn('uf', $ufs);
                }
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $processos = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $statuses = [
            'Aguardando Análise',
            'Indicação do Imóvel',
            'Diagnóstico Preliminar',
            'Análise de Viabilidade',
            'Validação - Chefia',
            'Validação - Coordenação',
            'Deliberação - Superintendência',
            'Validação - Equipe C.G.',
            'Validação - Coordenação-Geral',
            'Validação - Direção',
            'Deliberação - CDE',
            'Deliberado - SPU/UF',
            'Deliberado - CDE',
            'Indeferido - SPU/UF',
            'Indeferido - CDE',
            'Concluído - CDE',
            'Cancelado'
        ];
        $statusesPerfil = $this->getStatusesDoPerfil($this->getPerfilAtual());
        
        return view('processos.index', compact('processos', 'statuses', 'statusesPerfil'));
    }

    // =============================================
    // ABRIR - Lupa do painel (muda status + redirect)
    // =============================================
    public function abrir(Processo $processo)
    {
        $perfil = $this->getPerfilAtual();

        // Verificar se o perfil pode operar neste status
        if (!$this->perfilPodeOperar($processo->status_atual, $perfil)) {
            return redirect()->route('processos.index')
                ->with('erro_acesso', 'Você não tem permissão para acessar este processo no status "' . $processo->status_atual . '".');
        }

        $resultado = $this->getAbaEStatus($processo->status_atual, $perfil);

        // Atualiza status se houver transicao definida
        if ($resultado['status'] !== null) {
            $processo->status_atual = $resultado['status'];
            $processo->tramitacao = 'Normal';
            $processo->save();
        }

        return redirect()->route('processos.show', [
            'processo' => $processo->id,
            'aba' => $resultado['aba'],
        ]);
    }

    // =============================================
    // SHOW - Exibe aba do processo
    // =============================================
    public function show(Processo $processo, Request $request)
    {
        $aba = $request->query('aba', 1);

        // Buscar dados do foco (dados canonicos do formulario)
        $foco = $processo->foco;
        $dados = [];

        if ($foco) {
            if ($aba == 1 && $foco->aba1) {
                $dados = $foco->aba1->toArray();
                $dados['rips'] = $foco->rips->pluck('numero_rip')->toArray();
                $dados['cadastros_minimos'] = $foco->cadastrosMinimos->toArray();
            } elseif ($aba == 2 && $foco->aba2) {
                $dados = $foco->aba2->toArray();
            } elseif ($aba == 3 && $foco->aba3) {
                $dados = $foco->aba3->toArray();
            }

            // Disponibiliza a solicitação de criação de RIP da Aba 1 em todas as abas (exceto Aba 7, que carrega do tramite)
            if ($aba != 1 && $aba != 7 && $foco->aba1 && $foco->aba1->solicitacao_criacao_rip) {
                $dados['solicitacao_criacao_rip'] = $foco->aba1->solicitacao_criacao_rip;
            }
        }

        // Fallback: buscar do tramite mais recente (legado)
        if (empty($dados)) {
            $latestTramite = $processo->tramites()->latest()->first();
            $dados = $latestTramite ? $latestTramite->dados_snapshot : [];
        }

        // Carregar rascunho (draft) do Laravel se existir para o usuário atual e aba
        $draft = \App\Models\FocoDraft::where('processo_id', $processo->id)
            ->where('user_id', auth()->id())
            ->where('aba', $aba)
            ->first();
        if ($draft && is_array($draft->data)) {
            $dados = array_merge($dados, $draft->data);
        }

        // Se não estiver na Aba 1, carrega também o draft da Aba 1 para obter
        // solicitacao_criacao_rip e solicitacao_anexos (salvos via "Salvar Rascunho")
        if ($aba != 1) {
            $draftAba1 = \App\Models\FocoDraft::where('processo_id', $processo->id)
                ->where('user_id', auth()->id())
                ->where('aba', '1')
                ->first();
            if ($draftAba1 && is_array($draftAba1->data)) {
                $dados['solicitacao_criacao_rip'] = $draftAba1->data['solicitacao_criacao_rip']
                    ?? $dados['solicitacao_criacao_rip'] ?? '';
                $dados['solicitacao_anexos'] = $draftAba1->data['solicitacao_anexos']
                    ?? $dados['solicitacao_anexos'] ?? [];
            }
        }

        // Buscar dados do portal de servicos
        $requerimento = \App\Models\Requerimento::find($processo->numero_requerimento);

        // Quais abas já foram preenchidas (têm dados salvos)
        $abasPreenchidas = [
            1 => $foco && $foco->aba1,
            2 => $foco && $foco->aba2,
            3 => $foco && $foco->aba3,
        ];

        // Quais abas o perfil atual pode acessar
        $perfil = $this->getPerfilAtual();
        $abasDoPerfil = $this->getAbasDoPerfil($perfil);

        // Se aba == 7 (Visão Painel), precisamos carregar os dados de resumo das abas anteriores
        $dados1 = $dados2 = $dados3 = [];
        $rips = [];
        $cadastros = [];

        if ($aba == 7 && $foco) {
            if ($foco->aba1) {
                $dados1 = $foco->aba1->toArray();
                $rips = $foco->rips->pluck('numero_rip')->toArray();
                $cadastros = $foco->cadastrosMinimos->toArray();
            }
            if ($foco->aba2) {
                $dados2 = $foco->aba2->toArray();
            }
            if ($foco->aba3) {
                $dados3 = $foco->aba3->toArray();
            }

            // Também carrega dados dos rascunhos (drafts) das abas anteriores
            $draft1 = \App\Models\FocoDraft::where('processo_id', $processo->id)
                ->where('user_id', auth()->id())
                ->where('aba', '1')
                ->first();
            if ($draft1 && is_array($draft1->data)) {
                $dados1 = array_merge($dados1, $draft1->data);
            }

            $draft2 = \App\Models\FocoDraft::where('processo_id', $processo->id)
                ->where('user_id', auth()->id())
                ->where('aba', '2')
                ->first();
            if ($draft2 && is_array($draft2->data)) {
                $dados2 = array_merge($dados2, $draft2->data);
            }

            $draft3 = \App\Models\FocoDraft::where('processo_id', $processo->id)
                ->where('user_id', auth()->id())
                ->where('aba', '3')
                ->first();
            if ($draft3 && is_array($draft3->data)) {
                $dados3 = array_merge($dados3, $draft3->data);
            }
        }

        // Ultima devolucao e flag de recebimento (alerta de devolução)
        $ultimaDevolucao = $processo->tramites()
            ->where('acao', 'Devolvido')
            ->orderByDesc('id')
            ->first();
        $jaRecebido = $processo->tramites()
            ->where('acao', 'Recebido')
            ->where('id', '>', $ultimaDevolucao ? $ultimaDevolucao->id : 0)
            ->exists();

        // Ultima resolucao da devolução (box "Devolução Resolvida" nas abas seguintes)
        $ultimaResolucao = $processo->tramites()
            ->where('acao', 'Devolução Resolvida')
            ->where('id', '>', $ultimaDevolucao ? $ultimaDevolucao->id : 0)
            ->orderByDesc('id')
            ->first();

        return view('processos.show', compact('processo', 'aba', 'dados', 'dados1', 'dados2', 'dados3', 'rips', 'cadastros', 'requerimento', 'abasPreenchidas', 'abasDoPerfil', 'perfil', 'ultimaDevolucao', 'jaRecebido', 'ultimaResolucao'));
    }

    // =============================================
    // TRAMITAR - Salvar e Enviar (abas 1-3) ou Assinar (aba 7)
    // =============================================
    public function tramitar(Processo $processo, Request $request)
    {
        $validatedData = $request->except(['_token', 'next_aba', 'aba_atual']);
        $nextAba = $request->input('next_aba', 1);
        $abaAtual = $request->input('aba_atual', null);
        $effectiveAba = $nextAba !== 'index' ? $nextAba : $abaAtual;

        // Criar snapshot para audit trail (tramite)
        $latestTramite = $processo->tramites()->latest()->first();
        $previousData = $latestTramite ? $latestTramite->dados_snapshot : [];
        $newData = array_merge($previousData, $request->except(['_token']));

        // Assinaturas da Aba 7 (apenas se nao for rascunho)
        $acao = $request->input('acao_aba7');
        $isRascunho = $request->filled('acao_aba7_rascunho');
        if ($isRascunho) {
            $acao = $request->input('acao_aba7_rascunho');
        }

        if ($nextAba === 'index' && $acao && !$isRascunho) {
            if ($acao === 'chefia') {
                $newData['assinatura_chefia_nome'] = auth()->user()->name;
                $newData['assinatura_chefia_data'] = now()->format('d/m/Y H:i:s');
            } elseif ($acao === 'coordenacao') {
                $newData['assinatura_coordenacao_nome'] = auth()->user()->name;
                $newData['assinatura_coordenacao_data'] = now()->format('d/m/Y H:i:s');
            } elseif ($acao === 'superintendencia') {
                $newData['assinatura_superintendencia_nome'] = auth()->user()->name;
                $newData['assinatura_superintendencia_data'] = now()->format('d/m/Y H:i:s');
            } elseif ($acao === 'equipe_cg') {
                $newData['assinatura_equipe_cg_nome'] = auth()->user()->name;
                $newData['assinatura_equipe_cg_data'] = now()->format('d/m/Y H:i:s');
            } elseif ($acao === 'coordenacao_geral') {
                $newData['assinatura_coordenacao_geral_nome'] = auth()->user()->name;
                $newData['assinatura_coordenacao_geral_data'] = now()->format('d/m/Y H:i:s');
            } elseif ($acao === 'direcao') {
                $newData['assinatura_direcao_nome'] = auth()->user()->name;
                $newData['assinatura_direcao_data'] = now()->format('d/m/Y H:i:s');
            } elseif ($acao === 'cde') {
                $newData['assinatura_cde_nome'] = auth()->user()->name;
                $newData['assinatura_cde_data'] = now()->format('d/m/Y H:i:s');
            }
        }

        // =============================================
        // Criar tramite(s) - audit trail imutavel
        // =============================================
        $tramitacaoAntes = $processo->tramitacao;
        $usuarioId = auth()->id();

        $perfilLabels = [
            'chefia' => 'Chefia',
            'coordenacao' => 'Coordenação',
            'superintendencia' => 'Superintendência',
            'equipe_cg' => 'Equipe C.G.',
            'coordenacao_geral' => 'Coordenação-Geral',
            'direcao' => 'Direção',
            'cde' => 'CDE',
        ];

        $tramiteAcao = 'Atualização';
        $tramiteEtapa = 'Aba 7';

        if (!$isRascunho && $effectiveAba && $effectiveAba != '7') {
            $abaNum = (int) $effectiveAba;
            $focoExistente = $processo->foco;
            $jaSalva = false;
            if ($abaNum == 1) {
                $jaSalva = $focoExistente && $focoExistente->aba1;
            } elseif ($abaNum == 2) {
                $jaSalva = $focoExistente && $focoExistente->aba2;
            } elseif ($abaNum == 3) {
                $jaSalva = $focoExistente && $focoExistente->aba3;
            }
            $tramiteAcao = $jaSalva ? "Aba {$abaNum} Alterada" : "Aba {$abaNum} Salva";
            $tramiteEtapa = "Preenchimento - Aba {$abaNum}";

            // Retorno do processo em resposta a uma devolução
            if (!empty($validatedData['resposta_devolucao'])) {
                $processo->tramites()->create([
                    'acao' => 'Devolução Resolvida',
                    'etapa' => $tramiteEtapa,
                    'usuario_id' => $usuarioId,
                    'justificativa' => $validatedData['resposta_devolucao'],
                    'dados_snapshot' => $newData,
                ]);
            }
        } elseif (!$isRascunho) {
            $tramiteAcao = 'Manifestação';
            $tramiteEtapa = $perfilLabels[$acao] ?? 'Aba 7';
        }

        $processo->tramites()->create([
            'acao' => $tramiteAcao,
            'etapa' => $tramiteEtapa,
            'usuario_id' => $usuarioId,
            'justificativa' => null,
            'dados_snapshot' => $newData,
        ]);

        // =============================================
        // Salvar na tabela_foco (dados canonicos)
        // =============================================
        if ($effectiveAba && $effectiveAba != '7') {
            $foco = Foco::firstOrCreate(
                ['processo_id' => $processo->id],
                ['aba_salva' => $effectiveAba]
            );

            if ($effectiveAba == '1') {
                $foco->update(['aba_salva' => 1]);
                FocoAba1::updateOrCreate(
                    ['foco_id' => $foco->id],
                    [
                        'conceituacao_imovel' => $validatedData['conceituacao_imovel'] ?? null,
                        'resposta_devolucao' => $validatedData['resposta_devolucao'] ?? null,
                        'solicitacao_criacao_rip' => $validatedData['solicitacao_criacao_rip'] ?? null,
                    ]
                );
                if (isset($validatedData['rips'])) {
                    $foco->rips()->delete();
                    foreach ((array) $validatedData['rips'] as $rip) {
                        if (!empty($rip)) {
                            $foco->rips()->create(['numero_rip' => $rip]);
                        }
                    }
                }
                if (isset($validatedData['cadastros_minimos'])) {
                    $foco->cadastrosMinimos()->delete();
                    $cadastros = is_array($validatedData['cadastros_minimos'])
                        ? $validatedData['cadastros_minimos']
                        : json_decode($validatedData['cadastros_minimos'], true) ?? [];
                    foreach ($cadastros as $cad) {
                        if (is_string($cad)) {
                            $cad = json_decode($cad, true) ?? [];
                        }
                        if (!empty($cad)) {
                            $foco->cadastrosMinimos()->create([
                                'cep' => $cad['cep'] ?? null,
                                'logradouro' => $cad['logradouro'] ?? null,
                                'numero' => $cad['numero'] ?? null,
                                'complemento' => $cad['complemento'] ?? null,
                                'municipio' => $cad['municipio'] ?? null,
                                'uf' => $cad['uf'] ?? null,
                                'area' => $cad['area'] ?? null,
                                'observacoes' => $cad['observacoes'] ?? null,
                            ]);
                        }
                    }
                }
                $processo->status_atual = 'Diagnóstico Preliminar';

            } elseif ($effectiveAba == '2') {
                $foco->update(['aba_salva' => 2]);
                FocoAba2::updateOrCreate(
                    ['foco_id' => $foco->id],
                    [
                        'situacao_ocupacional' => $validatedData['situacao_ocupacional'] ?? null,
                        'tempo_desocupacao' => $validatedData['tempo_desocupacao'] ?? null,
                        'data_conhecimento_ocupacao' => $validatedData['data_conhecimento_ocupacao'] ?? null,
                        'tipo_uso_atual' => $validatedData['tipo_uso_atual'] ?? null,
                        'tipo_uso_especifico_atual' => $validatedData['tipo_uso_especifico_atual'] ?? null,
                        'ha_incidencia' => $validatedData['ha_incidencia'] ?? null,
                        'incidencia_ambiental' => $validatedData['incidencia_ambiental'] ?? null,
                        'ha_riscos' => $validatedData['ha_riscos'] ?? null,
                        'riscos' => $validatedData['riscos'] ?? null,
                        'ha_restricoes' => $validatedData['ha_restricoes'] ?? null,
                        'restricoes' => $validatedData['restricoes'] ?? null,
                        'latitude' => $validatedData['latitude'] ?? null,
                        'longitude' => $validatedData['longitude'] ?? null,
                        'geo_cep' => $validatedData['geo_cep'] ?? null,
                        'observacoes_aba2' => $validatedData['observacoes_aba2'] ?? null,
                    ]
                );
                $processo->status_atual = 'Análise de Viabilidade';

            } elseif ($effectiveAba == '3') {
                $foco->update(['aba_salva' => 3]);
                $dadosAnalise = [
                    'cpf_cnpj_regular' => $validatedData['cpf_cnpj_regular'] ?? null,
                    'obs_cpf_cnpj_irregular' => $validatedData['obs_cpf_cnpj_irregular'] ?? null,
                    'campo16' => $validatedData['campo16'] ?? null,
                    'destinacao_ativa' => $validatedData['destinacao_ativa'] ?? null,
                    'contratos_destinacao_ativa' => $validatedData['contratos_destinacao_ativa'] ?? null,
                    'ha_pendencias_contratuais' => $validatedData['ha_pendencias_contratuais'] ?? null,
                    'pendencias' => $validatedData['pendencias'] ?? null,
                    'obs_pendencias' => $validatedData['obs_pendencias'] ?? null,
                    'capacidade_fin' => $validatedData['capacidade_fin'] ?? null,
                    'custos_manutencao' => $validatedData['custos_manutencao'] ?? null,
                    'custos_valor' => $validatedData['custos_valor'] ?? null,
                    'outros_interessados' => $validatedData['outros_interessados'] ?? null,
                    'obs_outros_interessados' => $validatedData['obs_outros_interessados'] ?? null,
                ];
                $propostaDestinacao = [
                    'tipo_procedimento' => $validatedData['tipo_procedimento'] ?? null,
                    'campo51_obs' => $validatedData['campo51_obs'] ?? null,
                    'tipo_uso_imobiliario' => $validatedData['tipo_uso_imobiliario'] ?? null,
                    'tipo_uso_especifico' => $validatedData['tipo_uso_especifico'] ?? null,
                    'campo54' => $validatedData['campo54'] ?? null,
                    'campo54_desc' => $validatedData['campo54_desc'] ?? null,
                    'compatibilidade_urbanistica' => $validatedData['compatibilidade_urbanistica'] ?? null,
                    'campo55_obs' => $validatedData['campo55_obs'] ?? null,
                    'campo56_radio' => $validatedData['campo56_radio'] ?? null,
                    'campo56' => $validatedData['campo56'] ?? null,
                    'campo56_obs' => $validatedData['campo56_obs'] ?? null,
                    'campo57_radio' => $validatedData['campo57_radio'] ?? null,
                    'campo57' => $validatedData['campo57'] ?? null,
                    'campo57_obs' => $validatedData['campo57_obs'] ?? null,
                    'campo58_radio' => $validatedData['campo58_radio'] ?? null,
                    'impacto_social' => $validatedData['impacto_social'] ?? null,
                    'impacto_social_obs' => $validatedData['impacto_social_obs'] ?? null,
                    'num_beneficiarios' => $validatedData['num_beneficiarios'] ?? null,
                    'campo510_radio' => $validatedData['campo510_radio'] ?? null,
                    'impacto_ambiental' => $validatedData['impacto_ambiental'] ?? null,
                    'impacto_ambiental_obs' => $validatedData['impacto_ambiental_obs'] ?? null,
                    'regime_destinacao' => $validatedData['regime_destinacao'] ?? null,
                    'campo511_obs' => $validatedData['campo511_obs'] ?? null,
                ];
                FocoAba3::updateOrCreate(
                    ['foco_id' => $foco->id],
                    [
                        'dados_analise' => $dadosAnalise,
                        'proposta_destinacao' => $propostaDestinacao,
                    ]
                );
                $processo->status_atual = 'Validação - Chefia';
            }
        } else {
            // =============================================
            // Aba 7 - Fluxo de assinaturas
            // =============================================
            $decisao = $request->input('C_deliberacao');
            
            // Apenas altera status se não for um rascunho
            if (!$isRascunho) {
                if ($acao === 'chefia') {
                    $opcao = $request->input('decl_chefia_opcao');
                    if (empty($opcao)) {
                        return back()->withErrors(['decl_chefia_opcao' => 'É obrigatório selecionar uma das opções (Suficiente ou Insuficiente) antes de enviar.'])->withInput();
                    }
                    if ($opcao === 'suficiente') {
                        $processo->status_atual = 'Validação - Coordenação';
                    } elseif ($opcao === 'insuficiente') {
                        $processo->status_atual = 'Análise de Viabilidade';
                        $processo->tramitacao = 'Devolvido';
                    }
                } elseif ($acao === 'coordenacao') {
                    $opcao = $request->input('decl_coordenacao_opcao');
                    if (empty($opcao)) {
                        return back()->withErrors(['decl_coordenacao_opcao' => 'É obrigatório selecionar uma das opções (Suficiente ou Insuficiente) antes de enviar.'])->withInput();
                    }
                    if ($opcao === 'suficiente') {
                        $processo->status_atual = 'Deliberação - Superintendência';
                    } elseif ($opcao === 'insuficiente') {
                        $processo->status_atual = 'Análise de Viabilidade';
                        $processo->tramitacao = 'Devolvido';
                    }

            } elseif ($acao === 'superintendencia') {
                $sup_deliberacao = $request->input('sup_deliberacao');
                $sup_competencia = $request->input('sup_competencia');
                $sup_regime_concorda = $request->input('sup_regime_concorda');

                if ($sup_deliberacao === 'cancelamento') {
                    $processo->status_atual = 'Cancelado';
                    $processo->tramitacao = 'Cancelado';
                } elseif ($sup_deliberacao === 'complementacao' || $sup_regime_concorda === 'nao') {
                    $processo->status_atual = 'Análise de Viabilidade';
                    $processo->tramitacao = 'Devolvido';
                } else {
                    // Favorável ou Favorável com ressalvas
                    if ($sup_competencia === 'somente_superintendencia') {
                        $processo->status_atual = 'Deliberado - SPU/UF';
                    } else {
                        // precisa_cde ou cde
                        $processo->status_atual = 'Validação - Equipe C.G.';
                    }
                }

            } elseif ($acao === 'equipe_cg') {
                $opcao = $request->input('decl_equipe_cg_opcao');
                if ($opcao === 'insuficiente' && empty($request->input('obs_equipe_cg'))) {
                    return back()->withErrors(['obs_equipe_cg' => 'O campo observações é obrigatório quando a conferência apontar inconsistências/insuficiências.'])->withInput();
                }
                $processo->status_atual = 'Validação - Coordenação-Geral';

            } elseif ($acao === 'coordenacao_geral') {
                $opcao = $request->input('decl_coordenacao_geral_opcao');
                if ($opcao === 'suficiente') {
                    $processo->status_atual = 'Validação - Direção';
                } elseif ($opcao === 'insuficiente') {
                    if (empty($request->input('obs_coordenacao_geral'))) {
                        return back()->withErrors(['obs_coordenacao_geral' => 'O campo observações é obrigatório para devolver o processo.'])->withInput();
                    }
                    $processo->status_atual = 'Análise de Viabilidade';
                    $processo->tramitacao = 'Devolvido';
                }

            } elseif ($acao === 'direcao') {
                $opcao = $request->input('decl_direcao_opcao');
                if ($opcao === 'suficiente') {
                    $processo->status_atual = 'Deliberação - CDE';
                } elseif ($opcao === 'insuficiente') {
                    if (empty($request->input('obs_direcao'))) {
                        return back()->withErrors(['obs_direcao' => 'O campo observações é obrigatório para devolver o processo.'])->withInput();
                    }
                    $processo->status_atual = 'Validação - Coordenação-Geral';
                    $processo->tramitacao = 'Devolvido';
                }

            } elseif ($acao === 'cde') {
                $deliberacao = $request->input('cde_deliberacao');
                $competencia = $request->input('competencia_cde');
                
                // Validação de justicativa se houver opções negativas
                $interesse = $request->input('cde_interesse');
                $destinatario = $request->input('cde_destinatario');
                $regime = $request->input('cde_regime');

                if ($deliberacao === 'indeferir' || $interesse === 'insuficiente' || $destinatario === 'insuficiente' || $regime === 'alterar') {
                    if (empty($request->input('obs_cde'))) {
                        return back()->withErrors(['obs_cde' => 'O campo observações/condicionantes é obrigatório para registrar indeferimento, ressalvas ou novo regime.'])->withInput();
                    }
                }

                if ($deliberacao === 'aprovar') {
                    if ($competencia === 'superintendencia') {
                        $processo->status_atual = 'Deliberado - SPU/UF';
                    } elseif ($competencia === 'cde') {
                        $processo->status_atual = 'Deliberado - CDE';
                    }
                } elseif ($deliberacao === 'indeferir') {
                    if ($competencia === 'superintendencia') {
                        $processo->status_atual = 'Indeferido - SPU/UF';
                    } elseif ($competencia === 'cde') {
                        $processo->status_atual = 'Indeferido - CDE';
                    }
                }
            }
        }
    }

        // Registrar devolução quando a tramitação assumiu "Devolvido"
        if (!$isRascunho && $processo->tramitacao === 'Devolvido' && $tramitacaoAntes !== 'Devolvido') {
            $obsCandidatos = ['obs_chefia', 'obs_coordenacao', 'obs_superintendencia', 'obs_coordenacao_geral', 'obs_direcao', 'obs_cde'];
            $justificativa = null;
            foreach ($obsCandidatos as $campo) {
                if (!empty($validatedData[$campo])) {
                    $justificativa = $validatedData[$campo];
                    break;
                }
            }
            $processo->tramites()->create([
                'acao' => 'Devolvido',
                'etapa' => $tramiteEtapa,
                'usuario_id' => $usuarioId,
                'justificativa' => $justificativa,
                'dados_snapshot' => $newData,
            ]);
        }
        
        $processo->save();
        $this->syncProcessoStatusToSupabase($processo);

        \App\Models\FocoDraft::where('processo_id', $processo->id)
            ->where('user_id', auth()->id())
            ->delete();

        // Redirecionar para o painel ou permanecer na aba
        if ($nextAba === 'index') {
            return redirect()->route('processos.index')
                             ->with('success', 'Processo salvo com sucesso!');
        }

        return redirect()->route('processos.show', ['processo' => $processo->id, 'aba' => $nextAba])
                         ->with('success', 'Formulário salvo com sucesso!');
    }

    // =============================================
    // DEVOLVER - Rota dedicada (abas 1-3)
    // =============================================
    public function devolver(Processo $processo, Request $request)
    {
        $aba = $request->input('aba', 1);
        $etapaOrigem = $processo->status_atual;

        $processo->tramitacao = 'Devolvido';
        if ($aba == 1) {
            $processo->status_atual = 'Indicação do Imóvel';
        } elseif ($aba == 2) {
            $processo->status_atual = 'Diagnóstico Preliminar';
        }
        $processo->save();
        $this->syncProcessoStatusToSupabase($processo);

        $validatedData = $request->except(['_token']);
        $latestTramite = $processo->tramites()->latest()->first();
        $previousData = $latestTramite ? $latestTramite->dados_snapshot : [];
        $newData = array_merge($previousData, $validatedData);

        $processo->tramites()->create([
            'acao' => 'Devolvido',
            'etapa' => $etapaOrigem,
            'usuario_id' => auth()->id(),
            'justificativa' => $request->input('motivo_devolucao'),
            'dados_snapshot' => $newData
        ]);

        return redirect()->route('processos.index')
                         ->with('success', 'Processo devolvido com sucesso!');
    }

    // =============================================
    // RECEBER DEVOLUÇÃO - "Estou Ciente / Receber"
    // =============================================
    public function receberDevolucao(Processo $processo, Request $request)
    {
        $ultimaDevolucao = $processo->tramites()
            ->where('acao', 'Devolvido')
            ->orderByDesc('id')
            ->first();

        $processo->tramitacao = 'Normal';
        $processo->save();
        $this->syncProcessoStatusToSupabase($processo);

        $latestTramite = $processo->tramites()->latest()->first();
        $previousData = $latestTramite ? $latestTramite->dados_snapshot : [];
        $newData = array_merge($previousData, $request->except(['_token']));

        $processo->tramites()->create([
            'acao' => 'Recebido',
            'etapa' => $ultimaDevolucao ? $ultimaDevolucao->etapa : null,
            'usuario_id' => auth()->id(),
            'justificativa' => $ultimaDevolucao ? $ultimaDevolucao->justificativa : null,
            'dados_snapshot' => $newData,
        ]);

        return response()->json(['success' => true]);
    }

    private function syncProcessoStatusToSupabase(Processo $processo)
    {
        try {
            $req = \App\Models\Requerimento::find($processo->numero_requerimento);
            if ($req) {
                $req->update(['status' => $processo->status_atual]);
            }
            $supaUrl = config('services.supabase.url');
            $supaKey = config('services.supabase.key');

            if ($supaUrl && $supaKey) {
                $client = new \GuzzleHttp\Client();
                $client->patch("{$supaUrl}/rest/v1/tabela_requerimentos?numero_requerimento=eq.{$processo->numero_requerimento}", [
                    'headers' => [
                        'apikey' => $supaKey,
                        'Authorization' => "Bearer {$supaKey}",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => ['status' => $processo->status_atual]
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Erro sync Supabase status em tramitar: " . $e->getMessage());
        }
    }

    // =============================================
    // HELPERS
    // =============================================
    private function getPerfilAtual(): string
    {
        $user = auth()->user();
        if (!$user) return '';

        // Só permite simulação de perfil para admin (Direção)
        if ($user->hasRole('Direção') || $user->hasRole('Administrador')) {
            $simulado = request()->cookie('perfil_simulado');
            if ($simulado === 'ALL') {
                return 'ALL';
            }
            if ($simulado) {
                $map = [
                    'DESTINACAO'       => 'Equipe Destinação',
                    'CARACTERIZACAO'   => 'Equipe Caracterização',
                    'CHEFIA'           => 'Chefia',
                    'COORDENACAO'      => 'Coordenação',
                    'SUPERINTENDENCIA' => 'Superintendência',
                    'EQUIPE_CG'        => 'Equipe C.G.',
                    'COORDENACAO_GERAL'=> 'Coordenação-Geral',
                    'DIRETORIA'        => 'Direção',
                    'CDE'              => 'CDE',
                ];
                return $map[$simulado] ?? $simulado;
            }
            return 'ALL'; // Padrão sem simulação para Administrador
        }

        $roles = $user->getRoleNames()->toArray();
        foreach ($roles as $role) {
            return $role;
        }
        return '';
    }

    private function getStatusesDoPerfil(string $perfil): array
    {
        $simulado = request()->cookie('perfil_simulado');
        if ($simulado === 'ALL' || (auth()->user() && auth()->user()->hasRole('Administrador'))) {
            return [
                'Aguardando Análise', 'Aguardando análise',
                'Indicação do Imóvel', 'Indicação do imóvel',
                'Diagnóstico Preliminar', 'Diagnóstico preliminar do imóvel',
                'Análise de Viabilidade', 'Análise de viabilidade',
                'Validação - Chefia', 'Validação análise de viabilidade - Chefia',
                'Validação - Coordenação', 'Validação análise de viabilidade - Coordenação',
                'Deliberação - Superintendência', 'Deliberação Superintendência',
                'Validação - Equipe C.G.', 'Conferência análise de viabilidade',
                'Validação - Coordenação-Geral', 'Validação análise de viabilidade - Coordenação-Geral',
                'Validação - Direção', 'Validação conferência',
                'Deliberação - CDE', 'Manifestação CDE',
                'Deliberado - SPU/UF', 'Deliberado - CDE',
                'Indeferido - SPU/UF', 'Indeferido - CDE',
                'Concluído', 'Devolvido'
            ];
        }

        $map = [
            'Equipe Destinação' => [
                'Aguardando Análise', 'Aguardando análise',
                'Indicação do Imóvel', 'Indicação do imóvel',
                'Análise de Viabilidade', 'Análise de viabilidade',
            ],
            'Equipe Caracterização' => [
                'Diagnóstico Preliminar', 'Diagnóstico preliminar do imóvel',
            ],
            'Chefia' => [
                'Validação - Chefia', 'Validação análise de viabilidade - Chefia',
            ],
            'Coordenação' => [
                'Validação - Coordenação', 'Validação análise de viabilidade - Coordenação',
            ],
            'Superintendência' => [
                'Deliberação - Superintendência', 'Deliberação Superintendência',
                'Deliberado - SPU/UF', 'Indeferido - SPU/UF'
            ],
            'Equipe C.G.' => [
                'Validação - Equipe C.G.', 'Conferência análise de viabilidade',
            ],
            'Coordenação-Geral' => [
                'Validação - Coordenação-Geral', 'Validação análise de viabilidade - Coordenação-Geral',
            ],
            'Direção' => [
                'Validação - Direção', 'Validação conferência',
            ],
            'CDE' => [
                'Deliberação - CDE', 'Manifestação CDE',
                'Deliberado - CDE', 'Indeferido - CDE'
            ],
            'Finalizados' => [
                'Deliberado - SPU/UF', 'Deliberado - CDE', 
                'Indeferido - SPU/UF', 'Indeferido - CDE',
                'Concluído - CDE', 'Cancelado',
            ]
        ];

        return $map[$perfil] ?? [];
    }

    // =============================================
    // HISTÓRICO - Visualização read-only do processo
    // =============================================
    public function historico(Processo $processo)
    {
        $foco = $processo->foco;
        $dados1 = $dados2 = $dados3 = [];
        $rips = [];
        $cadastros = [];

        if ($foco) {
            if ($foco->aba1) {
                $dados1 = $foco->aba1->toArray();
                $rips = $foco->rips->pluck('numero_rip')->toArray();
                $cadastros = $foco->cadastrosMinimos->toArray();
            }
            if ($foco->aba2) {
                $dados2 = $foco->aba2->toArray();
            }
            if ($foco->aba3) {
                $dados3 = $foco->aba3->toArray();
            }
        }

        $requerimento = \App\Models\Requerimento::find($processo->numero_requerimento);

        $historicoTramites = $this->getHistoricoTramites($processo);

        return view('processos.historico', compact('processo', 'dados1', 'dados2', 'dados3', 'rips', 'cadastros', 'requerimento', 'historicoTramites'));
    }

    // =============================================
    // HISTÓRICO - Página de escolha do modelo
    // =============================================
    public function historicoEscolha(Processo $processo)
    {
        return view('processos.historico_escolha', compact('processo'));
    }

    private function getHistoricoTramites(Processo $processo)
    {
        return $processo->tramites()
            ->with('usuario')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    private function isAcaoSalva(string $acao): bool
    {
        return in_array($acao, ['Aba 1 Salva', 'Aba 2 Salva', 'Aba 3 Salva', 'Aba 1 Alterada', 'Aba 2 Alterada', 'Aba 3 Alterada', 'Atualização']);
    }

    private function getPerfilManifestacao(Tramite $tramite): string
    {
        $etapa = $tramite->etapa ?? '';

        $etapaMap = [
            'Chefia' => 'Chefia',
            'Coordenação' => 'Coordenação',
            'Superintendência' => 'Superintendência',
            'Equipe C.G.' => 'Equipe C.G.',
            'Coordenação-Geral' => 'Coordenação-Geral',
            'Direção' => 'Direção',
            'CDE' => 'CDE',
        ];
        foreach ($etapaMap as $chave => $perfil) {
            if (str_contains($etapa, $chave)) {
                return $perfil;
            }
        }

        $prefixMap = [
            'chefia' => 'Chefia',
            'coordenacao' => 'Coordenação',
            'superintendencia' => 'Superintendência',
            'equipe_cg' => 'Equipe C.G.',
            'coordenacao_geral' => 'Coordenação-Geral',
            'direcao' => 'Direção',
            'cde' => 'CDE',
        ];
        $dados = is_array($tramite->dados_snapshot) ? $tramite->dados_snapshot : [];
        foreach (array_reverse($prefixMap, true) as $prefix => $perfil) {
            if (!empty($dados['assinatura_' . $prefix . '_nome'])) {
                return $perfil;
            }
        }

        return '';
    }

    private function getTipoTramite(Tramite $tramite): string
    {
        $acao = $tramite->acao ?? '';

        if ($acao === 'Devolvido') {
            return 'devolucao';
        }
        if ($acao === 'Recebido') {
            return 'recebido';
        }
        if ($acao === 'Devolução Resolvida') {
            return 'resolucao';
        }
        if ($this->isAcaoSalva($acao)) {
            return 'salva';
        }
        return 'manifestacao';
    }

    private function getLabelTramite(Tramite $tramite): string
    {
        $acao = $tramite->acao ?? '';
        $etapa = $tramite->etapa ?? '';

        if ($acao === 'Devolvido') {
            return '⚠️ Devolução';
        }
        if ($acao === 'Recebido') {
            return '✅ Recebido';
        }
        if ($acao === 'Devolução Resolvida') {
            return '✅ Devolução Resolvida';
        }

        if ($this->isAcaoSalva($acao)) {
            if (str_contains($acao, 'Aba 1') || str_contains($etapa, 'Aba 1')) {
                return '📋 Dados do Requerimento';
            }
            if (str_contains($acao, 'Aba 2') || str_contains($etapa, 'Aba 2')) {
                return '📋 Diagnóstico Preliminar';
            }
            if (str_contains($acao, 'Aba 3') || str_contains($etapa, 'Aba 3')) {
                return '📋 Análise de Viabilidade';
            }
            return '📋 Dados salvos';
        }

        $perfil = $this->getPerfilManifestacao($tramite);
        return '📝 ' . ($perfil ?: 'Manifestação');
    }

    // =============================================
    // HISTÓRICO - Modelo B (Timeline Compacta)
    // =============================================
    public function historicoModeloB(Processo $processo)
    {
        $historicoTramites = $this->getHistoricoTramites($processo);

        return view('processos.historico_modelo_b', compact('processo', 'historicoTramites'));
    }

    // =============================================
    // HISTÓRICO - Modelo C (KanBan por Perfil)
    // =============================================
    public function historicoModeloC(Processo $processo)
    {
        $historicoTramites = $this->getHistoricoTramites($processo);

        $colunas = [
            'Equipe Destinação'     => collect(),
            'Equipe Caracterização' => collect(),
            'Chefia'                => collect(),
            'Coordenação'           => collect(),
            'Superintendência'      => collect(),
            'Equipe C.G.'           => collect(),
            'Coordenação-Geral'     => collect(),
            'Direção'               => collect(),
            'CDE'                   => collect(),
            'Sistema'               => collect(),
            'Outros'                => collect(),
        ];

        foreach ($historicoTramites as $tramite) {
            $perfil = $this->getColunaTramite($tramite);
            if (!isset($colunas[$perfil])) {
                $perfil = 'Outros';
            }
            $colunas[$perfil]->push($tramite);
        }

        return view('processos.historico_modelo_c', compact('processo', 'historicoTramites', 'colunas'));
    }

    private function getColunaTramite(Tramite $tramite): string
    {
        $etapa = $tramite->etapa ?? '';
        $dados = is_array($tramite->dados_snapshot) ? $tramite->dados_snapshot : [];

        $etapaMap = [
            'Preenchimento - Aba 1' => 'Equipe Destinação',
            'Preenchimento - Aba 2' => 'Equipe Caracterização',
            'Preenchimento - Aba 3' => 'Equipe Destinação',
            'Validação - Chefia' => 'Chefia',
            'Validação - Coordenação' => 'Coordenação',
            'Deliberação - Superintendência' => 'Superintendência',
            'Validação - Equipe C.G.' => 'Equipe C.G.',
            'Validação - Coordenação-Geral' => 'Coordenação-Geral',
            'Validação - Direção' => 'Direção',
            'Deliberação - CDE' => 'CDE',
            'Manifestação CDE' => 'CDE',
        ];
        foreach ($etapaMap as $chave => $perfil) {
            if (str_contains($etapa, $chave)) {
                return $perfil;
            }
        }

        $prefixMap = [
            'chefia' => 'Chefia',
            'coordenacao' => 'Coordenação',
            'superintendencia' => 'Superintendência',
            'equipe_cg' => 'Equipe C.G.',
            'coordenacao_geral' => 'Coordenação-Geral',
            'direcao' => 'Direção',
            'cde' => 'CDE',
        ];
        foreach ($prefixMap as $prefix => $perfil) {
            if (!empty($dados['assinatura_' . $prefix . '_nome'])) {
                return $perfil;
            }
        }

        if ($tramite->usuario && method_exists($tramite->usuario, 'getRoleNames')) {
            $roles = $tramite->usuario->getRoleNames()->toArray();
            $perfis = ['Equipe Destinação', 'Equipe Caracterização', 'Chefia', 'Coordenação', 'Superintendência', 'Equipe C.G.', 'Coordenação-Geral', 'Direção', 'CDE'];
            foreach ($roles as $role) {
                if (in_array($role, $perfis)) {
                    return $role;
                }
            }
        }

        return $tramite->usuario ? 'Outros' : 'Sistema';
    }

    // =============================================
    // HISTÓRICO - Modelo D (Grafo de Fluxo de Estados)
    // =============================================
    public function historicoModeloD(Processo $processo)
    {
        $historicoTramites = $this->getHistoricoTramites($processo);
        $fluxo = $this->montarFluxoEstados($processo);

        return view('processos.historico_modelo_d', compact('processo', 'historicoTramites', 'fluxo'));
    }

    private function montarFluxoEstados(Processo $processo): array
    {
        $tramites = $this->getHistoricoTramites($processo);

        $nos = [];
        $arestas = [];

        foreach ($tramites as $tramite) {
            $tipo = $this->getTipoTramite($tramite);
            $nos[$tramite->id] = [
                'id' => $tramite->id,
                'tipo' => $tipo,
                'label' => $this->getLabelTramite($tramite),
                'data' => \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y H:i'),
                'usuario' => $tramite->usuario ? $tramite->usuario->name : 'Sistema',
            ];
        }

        $ids = array_keys($nos);
        for ($i = 0; $i < count($ids) - 1; $i++) {
            $tipoAresta = $nos[$ids[$i + 1]]['tipo'];
            if ($tipoAresta === 'resolucao') {
                $tipoAresta = 'recebido';
            }
            $arestas[] = ['tipo' => $tipoAresta];
        }

        return ['nos' => $nos, 'arestas' => $arestas];
    }

    // =============================================
    // HISTÓRICO - Modelo E (BPMN com Gateway e Swimlane)
    // =============================================
    public function historicoModeloE(Processo $processo)
    {
        $historicoTramites = $this->getHistoricoTramites($processo);
        $blocks = $this->montarBlocosHistorico($processo);

        return view('processos.historico_modelo_e', compact('processo', 'historicoTramites', 'blocks'));
    }

    private function montarBlocosHistorico(Processo $processo): array
    {
        $tramites = $this->getHistoricoTramites($processo);
        $blocks = [];
        $i = 0;
        $n = count($tramites);

        while ($i < $n) {
            $tramite = $tramites[$i];

            if (($tramite->acao ?? '') === 'Devolvido') {
                $start = $tramite;
                $end = null;
                $events = [];
                $i++;

                while ($i < $n) {
                    $evt = $tramites[$i];
                    if (($evt->acao ?? '') === 'Devolução Resolvida') {
                        $end = $evt;
                        $i++;
                        break;
                    }
                    $events[] = $evt;
                    $i++;
                }

                $blocks[] = ['type' => 'cycle', 'start' => $start, 'end' => $end, 'events' => $events];
            } else {
                $blocks[] = ['type' => 'main', 'tramite' => $tramite];
                $i++;
            }
        }

        return $blocks;
    }

    // =============================================
    // HISTÓRICO - Modelo F (Colunas por Passagem)
    // =============================================
    public function historicoModeloF(Processo $processo)
    {
        $historicoTramites = $this->getHistoricoTramites($processo);
        $columns = $this->montarColunasPassagem($processo);
        $totalCols = count($columns);

        return view('processos.historico_modelo_f', compact('processo', 'historicoTramites', 'columns', 'totalCols'));
    }

    private function montarColunasPassagem(Processo $processo): array
    {
        $tramites = $this->getHistoricoTramites($processo);
        $columns = [];
        $atual = null;

        foreach ($tramites as $tramite) {
            if ($atual === null) {
                $atual = ['items' => [], 'endReason' => null];
            }

            $atual['items'][] = $tramite;

            if (($tramite->acao ?? '') === 'Devolvido') {
                $atual['endReason'] = $tramite->acao;
                $columns[] = $atual;
                $atual = null;
            }
        }

        if ($atual !== null) {
            $columns[] = $atual;
        }

        if (count($columns) === 0) {
            $columns[] = ['items' => [], 'endReason' => null];
        }

        return $columns;
    }

    // =============================================
    // HISTÓRICO - Modelo G (Matriz Abas × Perfis × Passagens)
    // =============================================
    public function historicoModeloG(Processo $processo)
    {
        $historicoTramites = $this->getHistoricoTramites($processo);
        $columns = $this->montarColunasPassagem($processo);

        $rowDefs = [
            'aba1' => ['icon' => '📋', 'label' => 'Dados do Requerimento'],
            'aba2' => ['icon' => '📋', 'label' => 'Diagnóstico Preliminar'],
            'aba3' => ['icon' => '📋', 'label' => 'Análise de Viabilidade'],
            'chefia' => ['icon' => '🖊️', 'label' => 'Chefia'],
            'coordenacao' => ['icon' => '🖊️', 'label' => 'Coordenação'],
            'superintendencia' => ['icon' => '🖊️', 'label' => 'Superintendência'],
            'equipe_cg' => ['icon' => '🖊️', 'label' => 'Equipe C.G.'],
            'coordenacao_geral' => ['icon' => '🖊️', 'label' => 'Coordenação-Geral'],
            'direcao' => ['icon' => '🖊️', 'label' => 'Direção'],
            'cde' => ['icon' => '🖊️', 'label' => 'CDE'],
            'devolucao' => ['icon' => '⚠️', 'label' => 'Devolução'],
        ];

        $perfilRows = [
            'Chefia' => 'chefia',
            'Coordenação' => 'coordenacao',
            'Superintendência' => 'superintendencia',
            'Equipe C.G.' => 'equipe_cg',
            'Coordenação-Geral' => 'coordenacao_geral',
            'Direção' => 'direcao',
            'CDE' => 'cde',
        ];

        $matrix = [];
        foreach ($columns as $colIdx => $col) {
            $matrix[$colIdx] = [];
            foreach ($col['items'] as $tramite) {
                $rowKey = $this->getRowMatriz($tramite, $perfilRows);
                if ($rowKey !== null) {
                    $matrix[$colIdx][$rowKey] = $tramite;
                }
            }
        }

        return view('processos.historico_modelo_g', compact('processo', 'historicoTramites', 'columns', 'rowDefs', 'matrix'));
    }

    private function getRowMatriz(Tramite $tramite, array $perfilRows): ?string
    {
        $acao = $tramite->acao ?? '';
        $etapa = $tramite->etapa ?? '';

        if ($this->isAcaoSalva($acao)) {
            if (str_contains($acao, 'Aba 1') || str_contains($etapa, 'Aba 1')) {
                return 'aba1';
            }
            if (str_contains($acao, 'Aba 2') || str_contains($etapa, 'Aba 2')) {
                return 'aba2';
            }
            if (str_contains($acao, 'Aba 3') || str_contains($etapa, 'Aba 3')) {
                return 'aba3';
            }
        }

        if ($acao === 'Devolvido' || $acao === 'Devolução Resolvida' || $acao === 'Recebido') {
            return 'devolucao';
        }

        $perfil = $this->getPerfilManifestacao($tramite);
        if ($perfil !== '' && isset($perfilRows[$perfil])) {
            return $perfilRows[$perfil];
        }

        return null;
    }
}
