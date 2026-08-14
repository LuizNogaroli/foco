<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico (Modelo F) - {{ $processo->numero_requerimento }}</title>
    <link rel="stylesheet" href="{{ asset('css/styles-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #1e293b; }

        .hist-container { width: 100%; max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .hist-header { text-align: center; margin-bottom: 30px; }
        .hist-header h1 { font-size: 1.5rem; color: #1e3a5f; font-weight: 700; margin-bottom: 4px; }
        .hist-header .hist-sub { font-size: 0.95rem; color: #64748b; }
        .hist-header .hist-back { display: inline-block; margin-top: 14px; padding: 8px 20px; background: #1e3a5f; color: #fff; border-radius: 6px; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: background 0.2s; }
        .hist-header .hist-back:hover { background: #2d5282; }
        .hist-empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95em; }

        /* ---- Scrollable columns container ---- */
        .cols-scroll {
            overflow-x: auto;
            padding-bottom: 16px;
        }
        .cols-wrapper {
            display: flex;
            gap: 0;
            align-items: stretch;
            min-width: min-content;
        }

        /* ---- Individual column ---- */
        .tl-column {
            flex: 0 0 320px;
            min-width: 280px;
            background: #fff;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .tl-column + .tl-column {
            margin-left: 0;
        }

        /* Column header */
        .col-header {
            padding: 14px 16px;
            background: #1e3a5f;
            border-radius: 10px 10px 0 0;
            text-align: center;
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .col-header .pass-badge {
            background: rgba(255,255,255,0.15);
            border-radius: 99px;
            padding: 2px 10px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Column body (timeline) */
        .col-body {
            padding: 20px 16px 16px 36px;
            flex: 1;
            position: relative;
        }
        .col-body .tl-spine {
            position: absolute;
            left: 18px;
            top: 20px;
            bottom: 20px;
            width: 3px;
            background: #cbd5e1;
            border-radius: 3px;
        }

        /* Gateway connector at bottom of column */
        .col-gateway {
            flex-shrink: 0;
            padding: 12px 16px 16px;
            text-align: center;
            border-top: 1px dashed #fecaca;
            background: #fef2f2;
            border-radius: 0 0 10px 10px;
            position: relative;
        }
        .col-gateway-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .col-gateway-arrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 1.2rem;
            color: #ef4444;
            font-weight: 700;
        }
        .col-gateway-arrow .arrow-line {
            width: 40px;
            height: 3px;
            background: #ef4444;
            border-radius: 3px;
            position: relative;
        }
        .col-gateway-arrow .arrow-line::after {
            content: '';
            position: absolute;
            right: -2px;
            top: -5px;
            border-left: 8px solid #ef4444;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
        }

        /* ---- Timeline items inside column ---- */
        .tl-item { position: relative; margin-bottom: 18px; }
        .tl-item:last-child { margin-bottom: 0; }

        .tl-dot { position: absolute; left: -22px; top: 14px; width: 14px; height: 14px; border-radius: 50%; background: #1e3a5f; border: 3px solid #fff; box-shadow: 0 0 0 2px #1e3a5f; z-index: 1; }
        .tl-dot-green { background: #16a34a; box-shadow: 0 0 0 2px #16a34a; }
        .tl-dot-red { background: #ef4444; box-shadow: 0 0 0 2px #ef4444; }
        .tl-dot-teal { background: #0d9488; box-shadow: 0 0 0 2px #0d9488; }
        .tl-dot-amber { background: #d97706; box-shadow: 0 0 0 2px #d97706; }
        .tl-dot-gray { background: #94a3b8; box-shadow: 0 0 0 2px #94a3b8; }

        .tl-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            transition: box-shadow 0.2s;
            cursor: pointer;
        }
        .tl-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

        .tl-time { font-size: 0.7rem; color: #94a3b8; font-weight: 600; margin-bottom: 2px; }
        .tl-title { font-size: 0.85rem; font-weight: 700; color: #1e3a5f; margin-bottom: 1px; }
        .tl-sub { font-size: 0.75rem; color: #64748b; margin-bottom: 4px; }
        .tl-footer { display: flex; align-items: center; justify-content: space-between; gap: 6px; }
        .tl-author { font-size: 0.72rem; color: #94a3b8; }
        .tl-btn { padding: 3px 10px; border: none; border-radius: 4px; font-size: 0.72rem; font-weight: 600; cursor: pointer; background: #e2e8f0; color: #1e293b; }
        .tl-btn:hover { background: #cbd5e1; }

        /* Devolution card highlight */
        .tl-card-devolucao { border-color: #fecaca; background: #fef2f2; }
        .tl-card-resolucao { border-color: #bbf7d0; background: #f0fdf4; }

        /* Column divider / connector */
        .col-connector {
            flex: 0 0 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-width: 50px;
        }
        .col-connector::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: #ef4444;
        }
        .col-connector::after {
            content: '▶';
            position: absolute;
            right: -2px;
            top: 50%;
            transform: translateY(-50%);
            color: #ef4444;
            font-size: 1rem;
            z-index: 1;
        }

        /* ---- Modal (reused from other models) ---- */
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
    </style>
</head>
<body>
    <div class="hist-container">
        <div class="hist-header">
            <h1>Histórico de Movimentações <span style="font-weight:400;font-size:0.9rem;color:#64748b;">— Modelo F (Colunas por Passagem)</span></h1>
            <div class="hist-sub">Requerimento nº {{ $processo->numero_requerimento }} &mdash; {{ $processo->status_atual }}</div>
            <div style="margin-top:14px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('processos.historico.escolha', $processo->id) }}" class="hist-back" style="background:#475569;">← Escolher Modelo</a>
                <a href="{{ route('processos.index') }}" class="hist-back">← Voltar ao Painel</a>
            </div>
        </div>

        @if(empty($historicoTramites) || $historicoTramites->count() === 0)
            <div class="hist-empty">Nenhum evento registrado no histórico ainda.</div>
        @else
            <div class="cols-scroll">
                <div class="cols-wrapper">
                    @foreach($columns as $colIdx => $col)
                        @php $isLast = ($colIdx === $totalCols - 1); @endphp

                        <div class="tl-column" style="{{ $col['endReason'] ?? null ? 'border-color: #fecaca;' : '' }}">
                            <div class="col-header" style="{{ $col['endReason'] ?? null ? 'background: #dc2626;' : '' }}">
                                <span class="pass-badge">{{ $colIdx + 1 }}ª</span>
                                <span>Passagem</span>
                                @if($col['endReason'] ?? null)
                                    <span style="font-size:0.7rem; background:rgba(255,255,255,0.2); padding:1px 8px; border-radius:99px;">⬇ desvio</span>
                                @endif
                            </div>

                            <div class="col-body">
                                <div class="tl-spine"></div>

                                @foreach($col['items'] as $tramite)
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

                                        if ($acao === 'Devolvido') {
                                            $dotClass = 'tl-dot-red';
                                            $title = '⚠️ Devolução';
                                            $sub = "Origem: {$etapaOrigem}";
                                            $cardExtra = 'tl-card-devolucao';
                                        } elseif ($acao === 'Recebido') {
                                            $dotClass = 'tl-dot-green';
                                            $title = '✅ Recebido';
                                            $sub = "Recebido por {$usuario}";
                                            $cardExtra = '';
                                        } elseif ($acao === 'Devolução Resolvida') {
                                            $dotClass = 'tl-dot-green';
                                            $title = '✅ Devolução Resolvida';
                                            $sub = '';
                                            $cardExtra = 'tl-card-resolucao';
                                        } elseif ($isSalvaOrAtualizacao) {
                                            $dotClass = $isAba3 ? 'tl-dot-amber' : 'tl-dot';
                                            $label = 'Dados salvos';
                                            if ($isAba1) $label = '📋 Dados Requerimento';
                                            if ($isAba2) $label = '📋 Diagnóstico';
                                            if ($isAba3) $label = '📋 Análise Destinatário';
                                            $title = $label;
                                            $sub = '';
                                            $cardExtra = '';
                                        } else {
                                            $dotClass = 'tl-dot-teal';
                                            $prefix = $prefixMap[$etapaOrigem] ?? null;
                                            if (!$prefix) {
                                                $tramiteDataStr = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y');
                                                foreach ($prefixMap as $l => $p) {
                                                    $sigData = $dadosSnapshot['assinatura_' . $p . '_data'] ?? '';
                                                    if ($sigData && str_contains($sigData, $tramiteDataStr)) { $prefix = $p; $etapaOrigem = $l; }
                                                }
                                                if (!$prefix || !isset($dadosSnapshot['assinatura_' . $prefix . '_nome'])) {
                                                    foreach (array_reverse($prefixMap, true) as $l => $p) {
                                                        if (isset($dadosSnapshot['assinatura_' . $p . '_nome'])) { $prefix = $p; $etapaOrigem = $l; break; }
                                                    }
                                                }
                                            }
                                            $title = '📝 Manifestação';
                                            $sub = $etapaOrigem;
                                            $cardExtra = '';
                                        }

                                        $modalId = 'modal-' . $tramite->id;
                                    @endphp

                                    <div class="tl-item">
                                        <div class="tl-dot {{ $dotClass }}"></div>
                                        <div class="tl-card {{ $cardExtra }}" onclick="abrirModal('{{ $modalId }}')">
                                            <div class="tl-time">{{ $dataAcao }}</div>
                                            <div class="tl-title">{{ $title }}</div>
                                            @if($sub)
                                                <div class="tl-sub">{{ $sub }}</div>
                                            @endif
                                            <div class="tl-footer">
                                                <span class="tl-author">👤 {{ $usuario }}</span>
                                                <button class="tl-btn" onclick="event.stopPropagation(); abrirModal('{{ $modalId }}')">🔍</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($col['endReason'] ?? null)
                                <div class="col-gateway">
                                    <div class="col-gateway-label">◆ Desvio de fluxo</div>
                                    <div class="col-gateway-arrow">
                                        <span>Devolução</span>
                                        <span class="arrow-line"></span>
                                        <span style="font-size:1.4rem;">→</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(!$isLast && ($col['endReason'] ?? null))
                            <div class="col-connector"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div style="margin-top:20px; text-align:center; font-size:0.8rem; color:#94a3b8;">
                Cada coluna representa uma passagem completa pelo fluxo.
                Devoluções criam uma nova coluna à direita.
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
