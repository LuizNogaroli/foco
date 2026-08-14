<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico (Modelo D) - {{ $processo->numero_requerimento }}</title>
    <link rel="stylesheet" href="{{ asset('css/styles-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #1e293b; }

        .hist-container { width: 100%; max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .hist-header { text-align: center; margin-bottom: 30px; }
        .hist-header h1 { font-size: 1.5rem; color: #1e3a5f; font-weight: 700; margin-bottom: 4px; }
        .hist-header .hist-sub { font-size: 0.95rem; color: #64748b; }
        .hist-header .hist-back { display: inline-block; margin-top: 14px; padding: 8px 20px; background: #1e3a5f; color: #fff; border-radius: 6px; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: background 0.2s; }
        .hist-header .hist-back:hover { background: #2d5282; }
        .hist-empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95em; }

        .graph { display: flex; flex-direction: column; align-items: center; gap: 0; position: relative; padding: 20px 0; }

        .graph-node { display: flex; flex-direction: column; align-items: center; background: #fff; border-radius: 12px; padding: 16px 24px; border: 2px solid #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.06); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s; min-width: 200px; max-width: 320px; text-align: center; position: relative; z-index: 1; }
        .graph-node:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); border-color: #1e3a5f; }

        .graph-node-tipo-salva { border-color: #2563eb; background: #eff6ff; }
        .graph-node-tipo-salva .graph-node-label { color: #1e40af; }
        .graph-node-tipo-manifestacao { border-color: #0d9488; background: #f0fdfa; }
        .graph-node-tipo-manifestacao .graph-node-label { color: #0f766e; }
        .graph-node-tipo-devolucao { border-color: #ef4444; background: #fef2f2; }
        .graph-node-tipo-devolucao .graph-node-label { color: #991b1b; }
        .graph-node-tipo-recebido { border-color: #16a34a; background: #f0fdf4; }
        .graph-node-tipo-recebido .graph-node-label { color: #166534; }
        .graph-node-tipo-resolucao { border-color: #16a34a; background: #f0fdf4; border-width: 3px; }
        .graph-node-tipo-resolucao .graph-node-label { color: #15803d; }

        .graph-node-time { font-size: 0.72rem; color: #94a3b8; font-weight: 600; margin-bottom: 4px; }
        .graph-node-label { font-size: 0.88rem; font-weight: 700; margin-bottom: 2px; }
        .graph-node-author { font-size: 0.75rem; color: #64748b; }

        .graph-arrow { display: flex; flex-direction: column; align-items: center; padding: 4px 0; position: relative; z-index: 0; }
        .graph-arrow-line { width: 3px; height: 28px; border-radius: 3px; }
        .graph-arrow-line-salva { background: #93c5fd; }
        .graph-arrow-line-manifestacao { background: #5eead4; }
        .graph-arrow-line-devolucao { background: #fca5a5; }
        .graph-arrow-line-recebido { background: #86efac; }
        .graph-arrow-head { width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 10px solid; }
        .graph-arrow-head-salva { border-top-color: #93c5fd; }
        .graph-arrow-head-manifestacao { border-top-color: #5eead4; }
        .graph-arrow-head-devolucao { border-top-color: #fca5a5; }
        .graph-arrow-head-recebido { border-top-color: #86efac; }
        .graph-arrow-label { font-size: 0.65rem; color: #94a3b8; margin-top: 2px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

        .graph-legend { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-bottom: 24px; padding: 14px 20px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; }
        .graph-legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: #475569; }
        .graph-legend-dot { width: 12px; height: 12px; border-radius: 3px; }

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
        iframe.resumo-frame { width: 100%; height: 72vh; min-height: 520px; border: none; display: block; background: #fff; }
    </style>
</head>
<body>
    <div class="hist-container">
        <div class="hist-header">
            <h1>Histórico de Movimentações <span style="font-weight:400;font-size:0.9rem;color:#64748b;">— Modelo D (Fluxo de Estados)</span></h1>
            <div class="hist-sub">Requerimento nº {{ $processo->numero_requerimento }} &mdash; {{ $processo->status_atual }}</div>
            <div style="margin-top:14px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('processos.historico.escolha', $processo->id) }}" class="hist-back" style="background:#475569;">← Escolher Modelo</a>
                <a href="{{ route('processos.index') }}" class="hist-back">← Voltar ao Painel</a>
            </div>
        </div>

        @if(empty($historicoTramites) || count($historicoTramites) === 0)
            <div class="hist-empty">Nenhum evento registrado no histórico ainda.</div>
        @else
            <div class="graph-legend">
                <div class="graph-legend-item"><div class="graph-legend-dot" style="background:#2563eb;"></div> Dados salvos</div>
                <div class="graph-legend-item"><div class="graph-legend-dot" style="background:#0d9488;"></div> Manifestação</div>
                <div class="graph-legend-item"><div class="graph-legend-dot" style="background:#ef4444;"></div> Devolução</div>
                <div class="graph-legend-item"><div class="graph-legend-dot" style="background:#16a34a;"></div> Recebido</div>
            </div>

            <div class="graph">
                @foreach($fluxo['nos'] as $idNo => $no)
                    <div class="graph-node graph-node-tipo-{{ $no['tipo'] }}" onclick="abrirModal('modal-{{ $no['id'] }}')">
                        <div class="graph-node-time">{{ $no['data'] }}</div>
                        <div class="graph-node-label">{{ $no['label'] }}</div>
                        <div class="graph-node-author">👤 {{ $no['usuario'] }}</div>
                    </div>

                    @if(!$loop->last)
                        @php
                            $aresta = $fluxo['arestas'][$loop->index] ?? null;
                            $tipoArrow = $aresta['tipo'] ?? 'salva';
                        @endphp
                        <div class="graph-arrow">
                            <div class="graph-arrow-line graph-arrow-line-{{ $tipoArrow }}"></div>
                            <div class="graph-arrow-head graph-arrow-head-{{ $tipoArrow }}"></div>
                        </div>
                    @endif
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
                    @endphp

                    @if(!empty($dadosSnapshot['resposta_devolucao']))
                        <div style="background-color:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:15px; margin-bottom:20px;">
                            <h4 style="color:#16a34a; margin:0 0 8px; font-size:0.95em;">✅ Retornar Processo</h4>
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
                                <div class="acordeao-titulo">📋 RIP(s) ou Cadastro(s) Mínimo(s) {!! $altTag !!}</div>
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
                                <div class="acordeao-titulo">📋 Diagnóstico preliminar do imóvel {!! $altTag !!}</div>
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
                                <div class="acordeao-titulo">📋 Análise de Viabilidade {!! $altTag !!}</div>
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
