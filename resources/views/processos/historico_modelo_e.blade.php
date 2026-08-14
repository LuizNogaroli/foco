<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico (Modelo E) - {{ $processo->numero_requerimento }}</title>
    <link rel="stylesheet" href="{{ asset('css/styles-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #1e293b; }

        .hist-container { width: 100%; max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .hist-header { text-align: center; margin-bottom: 30px; }
        .hist-header h1 { font-size: 1.5rem; color: #1e3a5f; font-weight: 700; margin-bottom: 4px; }
        .hist-header .hist-sub { font-size: 0.95rem; color: #64748b; }
        .hist-header .hist-back { display: inline-block; margin-top: 14px; padding: 8px 20px; background: #1e3a5f; color: #fff; border-radius: 6px; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: background 0.2s; }
        .hist-header .hist-back:hover { background: #2d5282; }
        .hist-empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95em; }

        /* ---- Main Timeline Spine (for main events) ---- */
        .tl { position: relative; padding-left: 40px; }
        .tl::before { content: ''; position: absolute; left: 15px; top: 8px; bottom: 8px; width: 3px; background: #cbd5e1; border-radius: 3px; }

        .tl-item { position: relative; margin-bottom: 20px; }
        .tl-dot { position: absolute; left: -28px; top: 18px; width: 16px; height: 16px; border-radius: 50%; background: #1e3a5f; border: 3px solid #fff; box-shadow: 0 0 0 2px #1e3a5f; z-index: 1; }
        .tl-dot-green { background: #16a34a; box-shadow: 0 0 0 2px #16a34a; }
        .tl-dot-red { background: #ef4444; box-shadow: 0 0 0 2px #ef4444; }
        .tl-dot-teal { background: #0d9488; box-shadow: 0 0 0 2px #0d9488; }
        .tl-dot-amber { background: #d97706; box-shadow: 0 0 0 2px #d97706; }

        .tl-card { background: #fff; border-radius: 10px; padding: 16px 20px; box-shadow: 0 1px 6px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; transition: box-shadow 0.2s; }
        .tl-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.1); }

        .tl-time { font-size: 0.78rem; color: #64748b; font-weight: 600; margin-bottom: 4px; }
        .tl-title { font-size: 0.95rem; font-weight: 700; color: #1e3a5f; margin-bottom: 2px; }
        .tl-sub { font-size: 0.85rem; color: #475569; margin-bottom: 8px; }
        .tl-footer { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .tl-author { font-size: 0.8rem; color: #94a3b8; }
        .tl-btn { padding: 5px 14px; border: none; border-radius: 5px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: background 0.2s; background: #e2e8f0; color: #1e293b; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .tl-btn:hover { background: #cbd5e1; }

        /* ---- BPMN Cycle Block ---- */
        .cycle-block {
            display: grid;
            grid-template-columns: 130px 1fr;
            gap: 0;
            margin-bottom: 28px;
            position: relative;
        }

        /* Left spine: gateway + converge */
        .cycle-spine {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 0;
            position: relative;
        }

        .gateway {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        .gateway-diamond {
            width: 48px;
            height: 48px;
            background: #fef2f2;
            border: 3px solid #ef4444;
            transform: rotate(45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            position: relative;
        }
        .gateway-diamond-inner {
            transform: rotate(-45deg);
            font-size: 1.3rem;
            font-weight: 700;
            color: #dc2626;
        }
        .gateway-label {
            margin-top: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .spine-middle {
            flex: 1;
            width: 3px;
            background: linear-gradient(to bottom, #fca5a5, #fbbf24);
            position: relative;
            min-height: 40px;
            margin: 6px 0;
        }
        .spine-middle::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: repeating-linear-gradient(
                to bottom,
                #94a3b8 0px,
                #94a3b8 6px,
                transparent 6px,
                transparent 12px
            );
        }

        .converge {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        .converge-diamond {
            width: 44px;
            height: 44px;
            background: #f0fdf4;
            border: 3px solid #16a34a;
            transform: rotate(45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .converge-diamond-inner {
            transform: rotate(-45deg);
            font-size: 1.3rem;
            color: #16a34a;
        }
        .converge-label {
            margin-top: 6px;
            font-size: 0.65rem;
            font-weight: 700;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        /* Arrow from gateway to swimlane */
        .arrow-to-swimlane {
            position: absolute;
            top: 26px;
            left: 120px;
            width: 28px;
            height: 3px;
            background: #ef4444;
            z-index: 3;
        }
        .arrow-to-swimlane::after {
            content: '';
            position: absolute;
            right: -2px;
            top: -6px;
            border-left: 10px solid #ef4444;
            border-top: 7px solid transparent;
            border-bottom: 7px solid transparent;
        }

        /* Arrow from swimlane to converge */
        .arrow-from-swimlane {
            position: absolute;
            bottom: 26px;
            left: 120px;
            width: 28px;
            height: 3px;
            background: #16a34a;
            z-index: 3;
        }
        .arrow-from-swimlane::after {
            content: '';
            position: absolute;
            left: -2px;
            top: -6px;
            border-right: 10px solid #16a34a;
            border-top: 7px solid transparent;
            border-bottom: 7px solid transparent;
        }

        /* Right: Swimlane / Pool */
        .cycle-swimlane {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 2px 12px rgba(245, 158, 11, 0.15);
        }

        .swimlane-header {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            padding: 12px 18px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #b45309;
        }
        .swimlane-header .badge-cycle {
            background: rgba(255,255,255,0.2);
            border-radius: 99px;
            padding: 2px 10px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .swimlane-body {
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .swimlane-event {
            background: #fff;
            border-radius: 8px;
            padding: 12px 14px;
            border: 1px solid #fde68a;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: box-shadow 0.2s;
            cursor: pointer;
        }
        .swimlane-event:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.08); }

        .swimlane-event-time { font-size: 0.72rem; color: #94a3b8; font-weight: 600; }
        .swimlane-event-title { font-size: 0.88rem; font-weight: 700; color: #1e3a5f; margin: 2px 0; }
        .swimlane-event-author { font-size: 0.78rem; color: #64748b; }
        .swimlane-event-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 6px; }

        .swimlane-footer {
            background: #fef3c7;
            border-top: 1px solid #fde68a;
            padding: 10px 18px;
            font-size: 0.78rem;
            color: #92400e;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            font-weight: 600;
        }

        /* ---- Modal ---- */
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
        .secao-ativa { border: 2px solid #2563eb !important; }
        .secao-ativa .acordeao-header { background: #dbeafe !important; color: #1e3a5f !important; }
        .secao-ativa .acordeao-titulo { color: #1e3a5f !important; }
        .secao-ativa .acordeao-seta { color: #1e3a5f !important; }

        /* ---- Legend ---- */
        .bpmn-legend {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 14px 20px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .bpmn-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #475569;
        }
        .legend-diamond {
            width: 20px; height: 20px;
            transform: rotate(45deg);
            border-radius: 3px;
            display: inline-block;
            flex-shrink: 0;
        }
        .legend-diamond-red { background: #fef2f2; border: 2px solid #ef4444; }
        .legend-diamond-green { background: #f0fdf4; border: 2px solid #16a34a; }
        .legend-pool {
            width: 24px; height: 16px;
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 2px solid #f59e0b;
            border-radius: 3px;
            display: inline-block;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="hist-container">
        <div class="hist-header">
            <h1>Histórico de Movimentações <span style="font-weight:400;font-size:0.9rem;color:#64748b;">— Modelo E (BPMN)</span></h1>
            <div class="hist-sub">Requerimento nº {{ $processo->numero_requerimento }} &mdash; {{ $processo->status_atual }}</div>
            <div style="margin-top:14px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('processos.historico.escolha', $processo->id) }}" class="hist-back" style="background:#475569;">← Escolher Modelo</a>
                <a href="{{ route('processos.index') }}" class="hist-back">← Voltar ao Painel</a>
            </div>
        </div>

        @if(empty($blocks) || count($blocks) === 0)
            <div class="hist-empty">Nenhum evento registrado no histórico ainda.</div>
        @else
            <div class="bpmn-legend">
                <span class="bpmn-legend-item"><span class="legend-diamond legend-diamond-red"></span> Gateway exclusivo (devolução)</span>
                <span class="bpmn-legend-item"><span class="legend-pool"></span> Pool / Ciclo de Devolução</span>
                <span class="bpmn-legend-item"><span class="legend-diamond legend-diamond-green"></span> Convergência (retorno ao fluxo)</span>
            </div>

            <div class="tl">
                @php $cycleIndex = 0; @endphp
                @foreach($blocks as $block)
                    @if($block['type'] === 'main')
                        @php
                            $tramite = $block['tramite'];
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

                            if ($acao === 'Devolvido') {
                                $dotClass = 'tl-dot-red';
                                $title = '⚠️ Devolução';
                                $sub = "Origem: {$etapaOrigem}";
                            } elseif ($acao === 'Recebido') {
                                $dotClass = 'tl-dot-green';
                                $title = '✅ Recebido';
                                $sub = "Recebido por {$usuario}";
                            } elseif ($isSalvaOrAtualizacao) {
                                $dotClass = $isAba3 ? 'tl-dot-amber' : 'tl-dot';
                                $label = 'Dados salvos';
                                if ($isAba1) $label = '📋 Dados do Requerimento salvos';
                                if ($isAba2) $label = '📋 Diagnóstico preliminar salvo';
                                if ($isAba3) $label = '📋 Análise do Destinatário salva';
                                $title = $label;
                                $sub = '';
                            } elseif ($acao === 'Devolução Resolvida') {
                                $dotClass = 'tl-dot-green';
                                $title = '✅ Devolução Resolvida';
                                $sub = '';
                            } else {
                                $dotClass = 'tl-dot-teal';
                                $prefix = $prefixMap[$etapaOrigem] ?? null;
                                if (!$prefix) {
                                    $tramiteDataStr = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y');
                                    foreach ($prefixMap as $label => $pref) {
                                        $sigData = $dadosSnapshot['assinatura_' . $pref . '_data'] ?? '';
                                        if ($sigData && str_contains($sigData, $tramiteDataStr)) {
                                            $prefix = $pref;
                                            $etapaOrigem = $label;
                                        }
                                    }
                                    if (!$prefix || !isset($dadosSnapshot['assinatura_' . $prefix . '_nome'])) {
                                        $reversedMap = array_reverse($prefixMap, true);
                                        foreach ($reversedMap as $label => $pref) {
                                            if (isset($dadosSnapshot['assinatura_' . $pref . '_nome'])) {
                                                $prefix = $pref;
                                                $etapaOrigem = $label;
                                                break;
                                            }
                                        }
                                    }
                                }
                                $title = '📝 Manifestação';
                                $sub = $etapaOrigem;
                            }

                            $modalId = 'modal-' . $tramite->id;
                        @endphp

                        <div class="tl-item">
                            <div class="tl-dot {{ $dotClass }}"></div>
                            <div class="tl-card">
                                <div class="tl-time">{{ $dataAcao }}</div>
                                <div class="tl-title">{{ $title }}</div>
                                @if($sub)
                                    <div class="tl-sub">{{ $sub }}</div>
                                @endif
                                <div class="tl-footer">
                                    <span class="tl-author">👤 {{ $usuario }}</span>
                                    <button class="tl-btn" onclick="abrirModal('{{ $modalId }}')">🔍 Ver detalhes</button>
                                </div>
                            </div>
                        </div>
                    @elseif($block['type'] === 'cycle')
                        @php
                            $cycleIdx = $cycleIndex++;
                            $start = $block['start'];
                            $end = $block['end'];
                            $events = $block['events'];
                            $startAcao = $start->acao;
                            $startEtapa = $start->etapa ?? 'Sistema';
                            $startUsuario = $start->usuario ? $start->usuario->name : 'Sistema';
                            $startData = \Carbon\Carbon::parse($start->created_at)->format('d/m/Y H:i');
                        @endphp

                        <div class="cycle-block">
                            <!-- Arrows connecting gateway to swimlane and back -->
                            <div class="arrow-to-swimlane"></div>
                            <div class="arrow-from-swimlane"></div>

                            <!-- Left: BPMN spine -->
                            <div class="cycle-spine">
                                <div class="gateway">
                                    <div class="gateway-diamond">
                                        <div class="gateway-diamond-inner">◆</div>
                                    </div>
                                    <div class="gateway-label">Devolução</div>
                                </div>
                                <div class="spine-middle"></div>
                                <div class="converge">
                                    <div class="converge-diamond">
                                        <div class="converge-diamond-inner">◇</div>
                                    </div>
                                    <div class="converge-label">Retorno</div>
                                </div>
                            </div>

                            <!-- Right: Swimlane -->
                            <div class="cycle-swimlane">
                                <div class="swimlane-header">
                                    <span>🔶 Ciclo de Devolução #{{ $cycleIdx + 1 }}</span>
                                    <span class="badge-cycle">{{ $startEtapa }}</span>
                                </div>
                                <div class="swimlane-body">
                                    <!-- Gateway event (first in cycle) -->
                                    <div class="swimlane-event" onclick="abrirModal('modal-{{ $start->id }}')" style="border-left: 4px solid #ef4444;">
                                        <div class="swimlane-event-time">⚠️ {{ $startData }}</div>
                                        <div class="swimlane-event-title">Devolução</div>
                                        <div class="swimlane-event-author">👤 {{ $startUsuario }} — Origem: {{ $startEtapa }}</div>
                                        <div class="swimlane-event-footer">
                                            <span style="font-size:0.75rem; color:#dc2626; font-weight:600;">🔽 Desvio de fluxo</span>
                                            <button class="tl-btn" onclick="event.stopPropagation(); abrirModal('modal-{{ $start->id }}')">🔍</button>
                                        </div>
                                    </div>

                                    <!-- Internal cycle events -->
                                    @foreach($events as $evt)
                                        @php
                                            $evtAcao = $evt->acao;
                                            $evtData = \Carbon\Carbon::parse($evt->created_at)->format('d/m/Y H:i');
                                            $evtUsuario = $evt->usuario ? $evt->usuario->name : 'Sistema';
                                            $evtDados = is_array($evt->dados_snapshot) ? $evt->dados_snapshot : json_decode($evt->dados_snapshot, true) ?? [];
                                            $evtTitle = $evtAcao;

                                            $isSalva = in_array($evtAcao, ['Aba 1 Salva', 'Aba 2 Salva', 'Aba 3 Salva', 'Aba 1 Alterada', 'Aba 2 Alterada', 'Aba 3 Alterada', 'Atualização']);
                                            if ($isSalva) {
                                                $hasResposta = !empty($evtDados['resposta_devolucao']);
                                                $evtTitle = '📋 ' . $evtAcao . ($hasResposta ? ' (com resposta)' : '');
                                            } elseif ($evtAcao === 'Recebido') {
                                                $evtTitle = '✅ Recebido';
                                            }
                                        @endphp
                                        <div class="swimlane-event" onclick="abrirModal('modal-{{ $evt->id }}')" style="border-left: 4px solid {{ $isSalva ? '#f59e0b' : '#94a3b8' }};">
                                            <div class="swimlane-event-time">{{ $isSalva ? '📝' : '' }} {{ $evtData }}</div>
                                            <div class="swimlane-event-title">{{ $evtTitle }}</div>
                                            <div class="swimlane-event-author">👤 {{ $evtUsuario }}</div>
                                            <div class="swimlane-event-footer">
                                                <span style="font-size:0.72rem;color:#64748b;">{{ $evt->etapa ?? '' }}</span>
                                                <button class="tl-btn" onclick="event.stopPropagation(); abrirModal('modal-{{ $evt->id }}')">🔍</button>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Converge event (last in cycle) -->
                                    @if($end)
                                        @php
                                            $endData = \Carbon\Carbon::parse($end->created_at)->format('d/m/Y H:i');
                                            $endUsuario = $end->usuario ? $end->usuario->name : 'Sistema';
                                        @endphp
                                        <div class="swimlane-event" onclick="abrirModal('modal-{{ $end->id }}')" style="border-left: 4px solid #16a34a; background: #f0fdf4;">
                                            <div class="swimlane-event-time">✅ {{ $endData }}</div>
                                            <div class="swimlane-event-title">Devolução Resolvida</div>
                                            <div class="swimlane-event-author">👤 {{ $endUsuario }}</div>
                                            <div class="swimlane-event-footer">
                                                <span style="font-size:0.75rem; color:#16a34a; font-weight:600;">🔼 Retorno ao fluxo principal</span>
                                                <button class="tl-btn" onclick="event.stopPropagation(); abrirModal('modal-{{ $end->id }}')">🔍</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="swimlane-footer">
                                    <span>↩ Fim do ciclo — convergindo para o fluxo principal</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modals -->
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
                        $dados1 = $isAba1 ? $dadosSnapshot : [];
                        $dados2 = $isAba2 ? $dadosSnapshot : [];
                        $dados3 = $isAba3 ? $dadosSnapshot : [];
                        $rips = $dadosSnapshot['rips'] ?? [];
                        $cadastros = $dadosSnapshot['cadastros_minimos'] ?? [];
                    @endphp

                    @if(!empty($dadosSnapshot['resposta_devolucao']))
                        <div style="background-color:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:15px; margin-bottom:20px;">
                            <h4 style="color:#16a34a; margin:0 0 8px; font-size:0.95em;">✅ Retornar Processo (Ajuste em resposta à devolução)</h4>
                            <div style="color:#166534; font-size:0.92em; line-height:1.5; white-space:pre-wrap;">{{ $dadosSnapshot['resposta_devolucao'] }}</div>
                        </div>
                    @endif

                    @php
                        $altStyle = $isAlteracao ? 'border-color:#f59e0b;' : '';
                        $altBg = $isAlteracao ? 'background:#d97706;' : '';
                        $altTag = $isAlteracao ? '<span style="background:#fef3c7;color:#b45309;padding:2px 8px;border-radius:4px;font-size:0.75em;margin-left:10px;">Alteração</span>' : '';
                    @endphp
                    @if($isAba1)
                        <div class="acordeao-wrapper" style="{{ $altStyle }}">
                            <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $altBg }}">
                                <div class="acordeao-titulo">📋 Dados do Requerimento {!! $altTag !!}</div>
                                <span class="acordeao-seta">▶</span>
                            </div>
                            <div class="acordeao-corpo" style="padding:15px;background:#fff;">
                                <div class="report-container">@include('processos.abas.resumos.aba1a')</div>
                            </div>
                        </div>
                        <div class="acordeao-wrapper" style="{{ $altStyle }}">
                            <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $altBg }}">
                                <div class="acordeao-titulo">📋 RIP(s) ou Cadastro(s) Mínimo(s) {!! $altTag !!}</div>
                                <span class="acordeao-seta">▶</span>
                            </div>
                            <div class="acordeao-corpo" style="padding:15px;background:#fff;">
                                <div class="report-container">@include('processos.abas.resumos.aba1b')</div>
                            </div>
                        </div>
                    @elseif($isAba2)
                        <div class="acordeao-wrapper" style="{{ $altStyle }}">
                            <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $altBg }}">
                                <div class="acordeao-titulo">📋 Diagnóstico preliminar do imóvel {!! $altTag !!}</div>
                                <span class="acordeao-seta">▶</span>
                            </div>
                            <div class="acordeao-corpo" style="padding:15px;background:#fff;">
                                <div class="report-container">@include('processos.abas.resumos.aba2')</div>
                            </div>
                        </div>
                    @elseif($isAba3)
                        <div class="acordeao-wrapper" style="{{ $altStyle }}">
                            <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $altBg }}">
                                <div class="acordeao-titulo">📋 Análise de Viabilidade {!! $altTag !!}</div>
                                <span class="acordeao-seta">▶</span>
                            </div>
                            <div class="acordeao-corpo" style="padding:15px;background:#fff;">
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
                        <div style="padding:12px; background:#fee2e2; border-radius:6px; color:#7f1d1d; font-size:0.92em; line-height:1.5;">
                            {{ $tramite->justificativa }}
                        </div>
                    </div>

                @elseif($acao === 'Recebido')
                    <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; padding:16px; text-align:center; font-size:0.95em; color:#475569;">
                        Processo recebido por <strong>{{ $usuario }}</strong> em {{ $dataAcao }}.
                    </div>

                @elseif($acao === 'Devolução Resolvida')
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:16px;">
                        <p style="margin:0 0 8px; color:#166534; font-size:0.95em;"><strong>Resolução da Devolução:</strong></p>
                        <div style="padding:12px; background:#dcfce7; border-radius:6px; color:#14532d; font-size:0.92em; line-height:1.5;">
                            {{ $tramite->justificativa }}
                        </div>
                    </div>

                @else
                    @php
                        $prefix = $prefixMap[$etapaOrigem] ?? null;
                        if (!$prefix) {
                            $tramiteDataStr = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y');
                            foreach ($prefixMap as $label => $pref) {
                                $sigData = $dadosSnapshot['assinatura_' . $pref . '_data'] ?? '';
                                if ($sigData && str_contains($sigData, $tramiteDataStr)) {
                                    $prefix = $pref;
                                    $etapaOrigem = $label;
                                }
                            }
                            if (!$prefix || !isset($dadosSnapshot['assinatura_' . $prefix . '_nome'])) {
                                $reversedMap = array_reverse($prefixMap, true);
                                foreach ($reversedMap as $label => $pref) {
                                    if (isset($dadosSnapshot['assinatura_' . $pref . '_nome'])) {
                                        $prefix = $pref;
                                        $etapaOrigem = $label;
                                        break;
                                    }
                                }
                            }
                        }
                    @endphp
                    @if($prefix && isset($dadosSnapshot['assinatura_'.$prefix.'_nome']))
                        <div style="background:#f0fdfa; border:1px solid #99f6e4; border-radius:8px; padding:16px;">
                            <div style="margin-bottom:10px; color:#0f766e; font-size:0.9em;">
                                <strong>Assinado por:</strong> {{ $dadosSnapshot['assinatura_'.$prefix.'_nome'] }}
                            </div>
                            <div style="background:#ccfbf1; border:1px solid #5eead4; border-radius:6px; padding:14px; color:#0f766e; font-size:0.92em; line-height:1.6;">
                                @include('processos.abas.partials.carimbo_manifestacao', ['dadosSnapshot' => $dadosSnapshot, 'prefix' => $prefix])
                            </div>
                        </div>
                    @endif
                @endif

                <div style="margin-top:20px; text-align:right;">
                    <button class="tl-btn" onclick="fecharModal('{{ $modalId }}')">Fechar</button>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function abrirModal(id) {
            document.getElementById(id).classList.add('aberto');
            document.body.style.overflow = 'hidden';
        }
        function fecharModal(id) {
            document.getElementById(id).classList.remove('aberto');
            document.body.style.overflow = '';
        }
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.modal-overlay.aberto').forEach(function(modal) {
                if (e.target === modal) {
                    modal.classList.remove('aberto');
                    document.body.style.overflow = '';
                }
            });
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.aberto').forEach(function(modal) {
                    modal.classList.remove('aberto');
                });
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>
