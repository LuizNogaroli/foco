<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico (Modelo G) - {{ $processo->numero_requerimento }}</title>
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

        /* ---- Matrix scrollable wrapper ---- */
        .matrix-scroll {
            overflow-x: auto;
            padding-bottom: 16px;
        }

        /* ---- Matrix table ---- */
        .matrix {
            border-collapse: separate;
            border-spacing: 0;
            min-width: 100%;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        /* Sticky first column (row labels) */
        .matrix th:first-child,
        .matrix td:first-child {
            position: sticky;
            left: 0;
            z-index: 3;
            background: #fff;
        }
        .matrix thead th:first-child {
            z-index: 5;
        }

        /* Header */
        .matrix thead th {
            background: #1e3a5f;
            color: #fff;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 12px 14px;
            text-align: center;
            white-space: nowrap;
            border-bottom: 2px solid #2d5282;
        }
        .matrix thead th:first-child {
            text-align: left;
            min-width: 200px;
            background: #1e3a5f;
        }
        .matrix thead th .pass-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }
        .matrix thead th .pass-num {
            font-size: 1.1rem;
        }
        .matrix thead th .pass-badge {
            font-size: 0.6rem;
            background: rgba(255,255,255,0.15);
            padding: 1px 8px;
            border-radius: 99px;
        }
        .matrix thead th .dev-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            color: #fca5a5;
        }

        /* Row labels column */
        .matrix tbody th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.82rem;
            color: #1e293b;
            padding: 10px 14px;
            text-align: left;
            white-space: nowrap;
            border-bottom: 1px solid #e2e8f0;
            border-right: 2px solid #e2e8f0;
        }
        .matrix tbody th .row-icon {
            margin-right: 6px;
        }
        .matrix tbody th .row-label {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 400;
        }

        /* Cells */
        .matrix tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #f1f5f9;
            text-align: center;
            vertical-align: middle;
            min-width: 140px;
            max-width: 180px;
        }
        .matrix tbody td:last-child {
            border-right: none;
        }

        /* Empty cell */
        .cell-empty {
            color: #e2e8f0;
            font-size: 0.7rem;
        }

        /* Cell types */
        .cell-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            padding: 4px 6px;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .cell-content:hover {
            transform: scale(1.05);
        }

        .cell-salva {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }
        .cell-salva .cell-icon { font-size: 1rem; }
        .cell-salva .cell-action { font-size: 0.7rem; font-weight: 600; color: #1e40af; }
        .cell-salva .cell-date { font-size: 0.65rem; color: #60a5fa; }

        .cell-manifestacao {
            background: #f0fdfa;
            border: 1px solid #99f6e4;
        }
        .cell-manifestacao .cell-icon { font-size: 1rem; }
        .cell-manifestacao .cell-action { font-size: 0.7rem; font-weight: 600; color: #0f766e; }
        .cell-manifestacao .cell-date { font-size: 0.65rem; color: #5eead4; }

        .cell-devolucao {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        .cell-devolucao .cell-icon { font-size: 1rem; }
        .cell-devolucao .cell-action { font-size: 0.7rem; font-weight: 700; color: #dc2626; }
        .cell-devolucao .cell-date { font-size: 0.65rem; color: #fca5a5; }

        .cell-recebido {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .cell-recebido .cell-icon { font-size: 1rem; }
        .cell-recebido .cell-action { font-size: 0.7rem; font-weight: 600; color: #16a34a; }
        .cell-recebido .cell-date { font-size: 0.65rem; color: #86efac; }

        .cell-resolucao {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-width: 2px;
        }
        .cell-resolucao .cell-icon { font-size: 1rem; }
        .cell-resolucao .cell-action { font-size: 0.7rem; font-weight: 700; color: #15803d; }
        .cell-resolucao .cell-date { font-size: 0.65rem; color: #86efac; }

        .cell-sistema {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
        }
        .cell-sistema .cell-icon { font-size: 1rem; }
        .cell-sistema .cell-action { font-size: 0.7rem; font-weight: 600; color: #475569; }
        .cell-sistema .cell-date { font-size: 0.65rem; color: #94a3b8; }

        /* Devolution connector row */
        .matrix tbody tr.dev-control {
            background: #fef2f2;
        }
        .matrix tbody tr.dev-control td {
            border-bottom: 2px solid #fecaca;
            padding: 8px 10px;
        }
        .dev-control-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #dc2626;
        }
        .dev-control-cell .dev-arrow {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .dev-control-cell .dev-arrow .line {
            width: 30px; height: 2px; background: #ef4444;
        }

        /* Legend */
        .matrix-legend {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 14px 20px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .matrix-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #475569;
        }
        .legend-swatch {
            width: 14px; height: 14px;
            border-radius: 3px;
            border: 1px solid;
            flex-shrink: 0;
        }
        .legend-swatch-salva { background: #eff6ff; border-color: #bfdbfe; }
        .legend-swatch-manifestacao { background: #f0fdfa; border-color: #99f6e4; }
        .legend-swatch-devolucao { background: #fef2f2; border-color: #fecaca; }
        .legend-swatch-resolucao { background: #f0fdf4; border-color: #86efac; }

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
    </style>
</head>
<body>
    <div class="hist-container">
        <div class="hist-header">
            <h1>Histórico de Movimentações <span style="font-weight:400;font-size:0.9rem;color:#64748b;">— Modelo G (Matriz)</span></h1>
            <div class="hist-sub">Requerimento nº {{ $processo->numero_requerimento }} &mdash; {{ $processo->status_atual }}</div>
            <div style="margin-top:14px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('processos.historico.escolha', $processo->id) }}" class="hist-back" style="background:#475569;">← Escolher Modelo</a>
                <a href="{{ route('processos.index') }}" class="hist-back">← Voltar ao Painel</a>
            </div>
        </div>

        @if(empty($historicoTramites) || $historicoTramites->count() === 0)
            <div class="hist-empty">Nenhum evento registrado no histórico ainda.</div>
        @else
            <div class="matrix-legend">
                <span class="matrix-legend-item"><span class="legend-swatch legend-swatch-salva"></span> Salvos</span>
                <span class="matrix-legend-item"><span class="legend-swatch legend-swatch-manifestacao"></span> Manifestações</span>
                <span class="matrix-legend-item"><span class="legend-swatch legend-swatch-devolucao"></span> Devoluções</span>
                <span class="matrix-legend-item"><span class="legend-swatch legend-swatch-resolucao"></span> Resoluções</span>
            </div>

            <div class="matrix-scroll">
                <table class="matrix">
                    <thead>
                        <tr>
                            <th>Etapa / Perfil</th>
                            @foreach($columns as $colIdx => $col)
                                <th>
                                    @php $isDevCol = ($col['endReason'] ?? null) && !$loop->last; @endphp
                                    @if($isDevCol)
                                        <div class="dev-header">
                                            <span>{{ $colIdx + 1 }}ª Passagem</span>
                                            <span class="pass-badge">⬇ desvio</span>
                                        </div>
                                    @else
                                        <div class="pass-header">
                                            <span class="pass-num">{{ $colIdx + 1 }}ª</span>
                                            <span class="pass-badge">Passagem</span>
                                        </div>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rowDefs as $rowKey => $rowDef)
                            <tr>
                                <th>
                                    <span class="row-icon">{{ $rowDef['icon'] }}</span>
                                    <span class="row-label">{{ $rowDef['label'] }}</span>
                                </th>
                                @foreach($columns as $colIdx => $col)
                                    @php
                                        $tramite = $matrix[$colIdx][$rowKey] ?? null;
                                    @endphp
                                    <td>
                                        @if($tramite)
                                            @php
                                                $acao = $tramite->acao;
                                                $dataAcao = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y H:i');
                                                $usuario = $tramite->usuario ? $tramite->usuario->name : 'Sistema';
                                                $isSalva = in_array($acao, ['Aba 1 Salva', 'Aba 2 Salva', 'Aba 3 Salva', 'Aba 1 Alterada', 'Aba 2 Alterada', 'Aba 3 Alterada', 'Atualização']);
                                                $isManifestacao = !$isSalva && !in_array($acao, ['Devolvido', 'Recebido', 'Devolução Resolvida']);
                                                $isDevolucao = ($acao === 'Devolvido');
                                                $isRecebido = ($acao === 'Recebido');
                                                $isResolucao = ($acao === 'Devolução Resolvida');

                                                if ($isSalva) {
                                                    $cellType = 'cell-salva';
                                                    $icon = '✅';
                                                    $shortAction = str_contains($acao, 'Alterada') ? 'Alterado' : 'Salvo';
                                                } elseif ($isManifestacao) {
                                                    $cellType = 'cell-manifestacao';
                                                    $icon = '📝';
                                                    $shortAction = 'Manifestação';
                                                } elseif ($isDevolucao) {
                                                    $cellType = 'cell-devolucao';
                                                    $icon = '⚠️';
                                                    $shortAction = 'Devolução';
                                                } elseif ($isRecebido) {
                                                    $cellType = 'cell-recebido';
                                                    $icon = '📥';
                                                    $shortAction = 'Recebido';
                                                } elseif ($isResolucao) {
                                                    $cellType = 'cell-resolucao';
                                                    $icon = '✅';
                                                    $shortAction = 'Resolvido';
                                                } else {
                                                    $cellType = 'cell-sistema';
                                                    $icon = '⚙️';
                                                    $shortAction = $acao;
                                                }

                                                $modalId = 'modal-' . $tramite->id;
                                            @endphp
                                            <div class="cell-content {{ $cellType }}" onclick="abrirModal('{{ $modalId }}')" title="{{ $acao }} - {{ $dataAcao }}">
                                                <span class="cell-icon">{{ $icon }}</span>
                                                <span class="cell-action">{{ $shortAction }}</span>
                                                <span class="cell-date">{{ \Carbon\Carbon::parse($tramite->created_at)->format('d/m') }}</span>
                                            </div>
                                        @else
                                            <span class="cell-empty">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px; text-align:center; font-size:0.78rem; color:#94a3b8;">
                Cada coluna representa uma passagem completa. Devoluções criam uma nova coluna à direita.
                Clique em uma célula para ver detalhes.
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
                                <strong>✔ Manifestação registrada</strong><br>
                                @if($prefix === 'superintendencia')
                                    Deliberação: {{ ucfirst(str_replace('_', ' ', $dadosSnapshot['sup_deliberacao'] ?? '')) }}<br>
                                    @if(($dadosSnapshot['sup_regime_concorda'] ?? '') === 'nao')
                                        Regime sugerido: {{ $dadosSnapshot['sup_regime_novo'] ?? 'Nenhum' }}<br>
                                    @endif
                                    Observações: {{ $dadosSnapshot['obs_superintendencia'] ?? 'Nenhuma observação' }}
                                @elseif($prefix === 'cde')
                                    Deliberação: {{ ucfirst(str_replace('_', ' ', $dadosSnapshot['cde_deliberacao'] ?? '')) }}<br>
                                    Observações: {{ $dadosSnapshot['obs_cde'] ?? 'Nenhuma observação' }}
                                @else
                                    Parecer: {{ ($dadosSnapshot['decl_'.$prefix.'_opcao'] ?? '') == 'suficiente' ? 'Suficiente' : 'Insuficiente' }}<br>
                                    Observações: {{ $dadosSnapshot['obs_'.$prefix] ?? 'Nenhuma observação' }}
                                @endif
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
