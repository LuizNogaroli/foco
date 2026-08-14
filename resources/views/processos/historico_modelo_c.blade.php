<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico (Modelo C) - {{ $processo->numero_requerimento }}</title>
    <link rel="stylesheet" href="{{ asset('css/styles-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #1e293b; }

        .hist-container { width: 100%; max-width: 100%; margin: 40px auto; padding: 0 20px; }
        .hist-header { text-align: center; margin-bottom: 30px; }
        .hist-header h1 { font-size: 1.5rem; color: #1e3a5f; font-weight: 700; margin-bottom: 4px; }
        .hist-header .hist-sub { font-size: 0.95rem; color: #64748b; }
        .hist-header .hist-back { display: inline-block; margin-top: 14px; padding: 8px 20px; background: #1e3a5f; color: #fff; border-radius: 6px; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: background 0.2s; }
        .hist-header .hist-back:hover { background: #2d5282; }
        .hist-empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95em; }

        .kanban { display: flex; gap: 16px; overflow-x: auto; padding-bottom: 20px; align-items: flex-start; min-height: 400px; }
        .kanban-col { flex: 0 0 220px; min-width: 220px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; }
        .kanban-col-header { padding: 12px 14px; font-weight: 700; font-size: 0.82rem; color: #fff; border-radius: 10px 10px 0 0; text-align: center; letter-spacing: 0.3px; }
        .kanban-col-body { padding: 10px; display: flex; flex-direction: column; gap: 8px; min-height: 60px; }

        .kanban-card { background: #fff; border-radius: 8px; padding: 10px 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: box-shadow 0.2s; cursor: pointer; }
        .kanban-card:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .kanban-card-time { font-size: 0.7rem; color: #94a3b8; font-weight: 600; margin-bottom: 3px; }
        .kanban-card-title { font-size: 0.82rem; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
        .kanban-card-author { font-size: 0.72rem; color: #64748b; }
        .kanban-card-footer { margin-top: 6px; display: flex; justify-content: flex-end; }
        .kanban-card-btn { padding: 3px 10px; border: none; border-radius: 4px; font-size: 0.7rem; font-weight: 600; cursor: pointer; background: #e2e8f0; color: #1e293b; transition: background 0.2s; }
        .kanban-card-btn:hover { background: #cbd5e1; }

        .col-cor-0 .kanban-col-header { background: #1e3a5f; }
        .col-cor-1 .kanban-col-header { background: #2563eb; }
        .col-cor-2 .kanban-col-header { background: #0d9488; }
        .col-cor-3 .kanban-col-header { background: #7c3aed; }
        .col-cor-4 .kanban-col-header { background: #d97706; }
        .col-cor-5 .kanban-col-header { background: #dc2626; }
        .col-cor-6 .kanban-col-header { background: #0891b2; }
        .col-cor-7 .kanban-col-header { background: #4f46e5; }
        .col-cor-8 .kanban-col-header { background: #be185d; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: flex-start; overflow-y: auto; padding: 30px 15px; }
        .modal-overlay.aberto { display: flex; }
        .modal-content { background: #fff; border-radius: 12px; max-width: 1000px; width: 100%; padding: 24px; position: relative; margin: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .modal-close { position: sticky; top: 0; float: right; background: none; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; padding: 4px 8px; line-height: 1; z-index: 1; }
        .modal-close:hover { color: #1e293b; }
        .modal-content .report-container { max-width: 100% !important; }

        .acordeao-wrapper {
          margin-bottom: 20px;
          border: 1px solid #cbd5e1;
          border-radius: 8px;
          overflow: hidden;
          box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .acordeao-header {
          display: flex;
          align-items: center;
          justify-content: flex-start;
          padding: 12px 16px;
          background: #1e3a5f;
          cursor: pointer;
          user-select: none;
          gap: 10px;
          transition: background 0.2s;
        }
        .acordeao-header:hover { background: #2d5282; }
        .acordeao-titulo {
          display: flex;
          align-items: center;
          gap: 10px;
          font-weight: bold;
          font-size: 1.1em;
          color: #ffffff;
          flex: 1;
        }
        .acordeao-seta { font-size: 0.85em; color: #ffffff; transition: transform 0.25s; flex-shrink: 0; }
        .acordeao-wrapper.aberto .acordeao-seta { transform: rotate(90deg); }
        .acordeao-corpo { display: none; padding: 20px 20px 24px; background: #fff; border-top: 1px solid #e2e8f0; }
        .acordeao-wrapper.aberto .acordeao-corpo { display: block; }
        .acordeao-corpo fieldset { border: none; padding: 0; margin: 0; }
        iframe.resumo-frame { width: 100%; height: 72vh; min-height: 520px; border: none; display: block; background: #fff; }
    </style>
</head>
<body>
    <div class="hist-container">
        <div class="hist-header">
            <h1>Histórico de Movimentações <span style="font-weight:400;font-size:0.9rem;color:#64748b;">— Modelo C (KanBan)</span></h1>
            <div class="hist-sub">Requerimento nº {{ $processo->numero_requerimento }} &mdash; {{ $processo->status_atual }}</div>
            <div style="margin-top:14px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('processos.historico.escolha', $processo->id) }}" class="hist-back" style="background:#475569;">← Escolher Modelo</a>
                <a href="{{ route('processos.index') }}" class="hist-back">← Voltar ao Painel</a>
            </div>
        </div>

        @if(empty($historicoTramites) || count($historicoTramites) === 0)
            <div class="hist-empty">Nenhum evento registrado no histórico ainda.</div>
        @else
            @php
                $colIndex = 0;
                $colCores = ['0','1','2','3','4','5','6','7','8'];
                $colHasItems = array_filter($colunas, fn($items) => count($items) > 0);
            @endphp
            <div class="kanban">
                @foreach($colunas as $perfil => $tramites)
                    @if(count($tramites) === 0) @continue @endif
                    @php
                        $cor = $colCores[$colIndex % count($colCores)];
                        $colIndex++;
                    @endphp
                    <div class="kanban-col col-cor-{{ $cor }}">
                        <div class="kanban-col-header">{{ $perfil }} <span style="font-weight:400;opacity:0.8;">({{ count($tramites) }})</span></div>
                        <div class="kanban-col-body">
                            @foreach($tramites as $tramite)
                                @php
                                    $dataAcao = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y H:i');
                                    $usuario = $tramite->usuario ? $tramite->usuario->name : 'Sistema';
                                    $acao = $tramite->acao;
                                    $etapa = $tramite->etapa ?? '';

                                    $label = $acao;
                                    if ($acao === 'Devolvido') $label = '⚠️ Devolução';
                                    elseif ($acao === 'Recebido') $label = '✅ Recebido';
                                    elseif ($acao === 'Devolução Resolvida') $label = '✅ Devolução Resolvida';
                                    elseif (in_array($acao, ['Aba 1 Salva', 'Aba 1 Alterada'])) $label = '📋 Dados Requerimento';
                                    elseif (in_array($acao, ['Aba 2 Salva', 'Aba 2 Alterada'])) $label = '📋 Diagnóstico';
                                    elseif (in_array($acao, ['Aba 3 Salva', 'Aba 3 Alterada'])) $label = '📋 Análise Destinatário';
                                    elseif (str_contains($acao, 'Manifestação') || !in_array($acao, ['Devolvido', 'Recebido', 'Devolução Resolvida', 'Aba 1 Salva', 'Aba 2 Salva', 'Aba 3 Salva', 'Aba 1 Alterada', 'Aba 2 Alterada', 'Aba 3 Alterada', 'Atualização'])) $label = '📝 Manifestação';
                                @endphp
                                <div class="kanban-card" onclick="abrirModal('modal-{{ $tramite->id }}')">
                                    <div class="kanban-card-time">{{ $dataAcao }}</div>
                                    <div class="kanban-card-title">{{ $label }}</div>
                                    <div class="kanban-card-author">👤 {{ $usuario }}</div>
                                    <div class="kanban-card-footer">
                                        <button class="kanban-card-btn" onclick="event.stopPropagation();abrirModal('modal-{{ $tramite->id }}')">🔍 Detalhes</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @foreach($historicoTramites as $tramite)
        @php
            $acao = $tramite->acao;
            $dataAcao = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y H:i');
            $etapaOrigem = $tramite->etapa ?? 'Sistema';
            $dadosSnapshot = is_array($tramite->dados_snapshot) ? $tramite->dados_snapshot : json_decode($tramite->dados_snapshot, true) ?? [];
            $usuario = $tramite->usuario ? $tramite->usuario->name : 'Sistema';

            $isAba1 = str_contains($acao, 'Aba 1') || str_contains($etapaOrigem, 'Aba 1');
            $isAba2 = str_contains($acao, 'Aba 2') || str_contains($etapaOrigem, 'Aba 2');
            $isAba3 = str_contains($acao, 'Aba 3') || str_contains($etapaOrigem, 'Aba 3');
            $isSalvaOrAtualizacao = in_array($acao, ['Aba 1 Salva', 'Aba 2 Salva', 'Aba 3 Salva', 'Aba 1 Alterada', 'Aba 2 Alterada', 'Aba 3 Alterada', 'Atualização']);

            $prefixMap = [
                'Chefia'            => 'chefia',
                'Coordenação SPU/UF' => 'coordenacao',
                'Superintendência'  => 'superintendencia',
                'Equipe C.G.'       => 'equipe_cg',
                'Coordenação-Geral' => 'coordenacao_geral',
                'Direção'           => 'direcao',
                'CDE'               => 'cde'
            ];

            $modalId = 'modal-' . $tramite->id;
        @endphp
        <div id="{{ $modalId }}" class="modal-overlay">
            <div class="modal-content">
                <button class="modal-close" onclick="fecharModal('{{ $modalId }}')">&times;</button>
                <div style="margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid #e2e8f0;">
                    <h2 style="font-size:1.2rem; color:#1e3a5f; margin-bottom:4px;">{{ $acao }}</h2>
                    <div style="font-size:0.88rem; color:#64748b;">👤 {{ $usuario }} — 🕒 {{ $dataAcao }}</div>
                </div>

                @if($isSalvaOrAtualizacao && ($isAba1 || $isAba2 || $isAba3))
                    @php
                        $isAlteracao = str_contains($acao, 'Alterada');
                        $d1 = $isAba1 ? $dadosSnapshot : [];
                        $d2 = $isAba2 ? $dadosSnapshot : [];
                        $d3 = $isAba3 ? $dadosSnapshot : [];
                        $r = $dadosSnapshot['rips'] ?? [];
                        $c = $dadosSnapshot['cadastros_minimos'] ?? [];
                        $altStyle = $isAlteracao ? 'border-color:#f59e0b;' : '';
                        $altBg = $isAlteracao ? 'background:#d97706;' : '';
                    @endphp

                    @if(!empty($dadosSnapshot['resposta_devolucao']))
                        <div style="background-color:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:15px; margin-bottom:20px;">
                            <h4 style="color:#16a34a; margin:0 0 8px; font-size:0.95em;">✅ Retornar Processo (Ajuste em resposta à devolução)</h4>
                            <div style="color:#166534; font-size:0.92em; line-height:1.5; white-space:pre-wrap;">{{ $dadosSnapshot['resposta_devolucao'] }}</div>
                        </div>
                    @endif

                    @if($isAba1)
                        <div class="acordeao-wrapper" style="{{ $altStyle }}">
                            <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $altBg }}">
                                <div class="acordeao-titulo">📋 Dados do Requerimento {!! $isAlteracao ? '<span style="background:#fef3c7;color:#b45309;padding:2px 8px;border-radius:4px;font-size:0.75em;margin-left:10px;">Alteração</span>' : '' !!}</div>
                                <span class="acordeao-seta">▶</span>
                            </div>
                            <div class="acordeao-corpo" style="padding:15px;background:#fff;">
                                @php
                                    $dados1 = $d1;
                                    $rips = $r;
                                    $cadastros = $c;
                                @endphp
                                <div class="report-container">@include('processos.abas.resumos.aba1a')</div>
                            </div>
                        </div>
                        <div class="acordeao-wrapper" style="{{ $altStyle }}">
                            <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $altBg }}">
                                <div class="acordeao-titulo">📋 RIP(s) ou Cadastro(s) Mínimo(s) {!! $isAlteracao ? '<span style="background:#fef3c7;color:#b45309;padding:2px 8px;border-radius:4px;font-size:0.75em;margin-left:10px;">Alteração</span>' : '' !!}</div>
                                <span class="acordeao-seta">▶</span>
                            </div>
                            <div class="acordeao-corpo" style="padding:15px;background:#fff;">
                                @php
                                    $dados1 = $d1;
                                    $rips = $r;
                                    $cadastros = $c;
                                @endphp
                                <div class="report-container">@include('processos.abas.resumos.aba1b')</div>
                            </div>
                        </div>
                    @elseif($isAba2)
                        <div class="acordeao-wrapper" style="{{ $altStyle }}">
                            <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $altBg }}">
                                <div class="acordeao-titulo">📋 Diagnóstico preliminar do imóvel {!! $isAlteracao ? '<span style="background:#fef3c7;color:#b45309;padding:2px 8px;border-radius:4px;font-size:0.75em;margin-left:10px;">Alteração</span>' : '' !!}</div>
                                <span class="acordeao-seta">▶</span>
                            </div>
                            <div class="acordeao-corpo" style="padding:15px;background:#fff;">
                                @php $dados2 = $d2; @endphp
                                <div class="report-container">@include('processos.abas.resumos.aba2')</div>
                            </div>
                        </div>
                    @elseif($isAba3)
                        <div class="acordeao-wrapper" style="{{ $altStyle }}">
                            <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $altBg }}">
                                <div class="acordeao-titulo">📋 Análise de Viabilidade {!! $isAlteracao ? '<span style="background:#fef3c7;color:#b45309;padding:2px 8px;border-radius:4px;font-size:0.75em;margin-left:10px;">Alteração</span>' : '' !!}</div>
                                <span class="acordeao-seta">▶</span>
                            </div>
                            <div class="acordeao-corpo" style="padding:15px;background:#fff;">
                                @php $dados3 = $d3; @endphp
                                <div class="report-container">
                                    <h4 style="margin-top:0;color:#1e3a5f;font-size:1.1em;border-bottom:2px solid #e2e8f0;padding-bottom:5px;margin-bottom:15px;">Análise do Destinatário</h4>
                                    @include('processos.abas.resumos.aba3_analise')
                                    <h4 style="margin-top:25px;color:#1e3a5f;font-size:1.1em;border-bottom:2px solid #e2e8f0;padding-bottom:5px;margin-bottom:15px;">Proposta de Destinação</h4>
                                    @include('processos.abas.resumos.aba3_proposta')
                                </div>
                            </div>
                        </div>
                    @endif

                @elseif($acao === 'Devolvido')
                    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:16px;">
                        <p style="margin:0 0 8px; color:#991b1b; font-size:0.95em;"><strong>Motivo da Devolução:</strong></p>
                        <div style="padding:12px; background:#fee2e2; border-radius:6px; color:#7f1d1d; font-size:0.92em; line-height:1.5;">{{ $tramite->justificativa }}</div>
                    </div>

                @elseif($acao === 'Recebido')
                    <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; padding:16px; text-align:center; font-size:0.95em; color:#475569;">
                        Processo recebido por <strong>{{ $usuario }}</strong> em {{ $dataAcao }}.
                    </div>

                @elseif($acao === 'Devolução Resolvida')
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:16px;">
                        <p style="margin:0 0 8px; color:#166534; font-size:0.95em;"><strong>Resolução da Devolução:</strong></p>
                        <div style="padding:12px; background:#dcfce7; border-radius:6px; color:#14532d; font-size:0.92em; line-height:1.5;">{{ $tramite->justificativa }}</div>
                    </div>

                @else
                    @php
                        $prefix = $prefixMap[$etapaOrigem] ?? null;
                        if (!$prefix) {
                            $tramiteDataStr = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y');
                            foreach ($prefixMap as $label => $pref) {
                                $sigData = $dadosSnapshot['assinatura_' . $pref . '_data'] ?? '';
                                if ($sigData && str_contains($sigData, $tramiteDataStr)) { $prefix = $pref; $etapaOrigem = $label; break; }
                            }
                            if (!$prefix || !isset($dadosSnapshot['assinatura_' . $prefix . '_nome'])) {
                                foreach (array_reverse($prefixMap, true) as $label => $pref) {
                                    if (isset($dadosSnapshot['assinatura_' . $pref . '_nome'])) { $prefix = $pref; $etapaOrigem = $label; break; }
                                }
                            }
                        }
                    @endphp
                    @if($prefix && isset($dadosSnapshot['assinatura_'.$prefix.'_nome']))
                        <div style="background:#f0fdfa; border:1px solid #99f6e4; border-radius:8px; padding:16px;">
                            <div style="margin-bottom:10px; color:#0f766e; font-size:0.9em;"><strong>Assinado por:</strong> {{ $dadosSnapshot['assinatura_'.$prefix.'_nome'] }}</div>
                            <div style="background:#ccfbf1; border:1px solid #5eead4; border-radius:6px; padding:14px; color:#0f766e; font-size:0.92em; line-height:1.6;">
                                @include('processos.abas.partials.carimbo_manifestacao', ['dadosSnapshot' => $dadosSnapshot, 'prefix' => $prefix])
                            </div>
                        </div>
                    @else
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; color:#64748b; text-align:center;">Detalhes não disponíveis para este evento.</div>
                    @endif
                @endif

                <div style="margin-top:20px; text-align:right;">
                    <button class="tl-btn" onclick="fecharModal('{{ $modalId }}')" style="padding:5px 14px;border:none;border-radius:5px;font-size:0.8rem;font-weight:600;cursor:pointer;background:#e2e8f0;color:#1e293b;">Fechar</button>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function abrirModal(id) { document.getElementById(id).classList.add('aberto'); document.body.style.overflow = 'hidden'; }
        function fecharModal(id) { document.getElementById(id).classList.remove('aberto'); document.body.style.overflow = ''; }
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.modal-overlay.aberto').forEach(function(m) { if (e.target === m) { m.classList.remove('aberto'); document.body.style.overflow = ''; } });
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { document.querySelectorAll('.modal-overlay.aberto').forEach(function(m) { m.classList.remove('aberto'); }); document.body.style.overflow = ''; }
        });
    </script>
</body>
</html>
