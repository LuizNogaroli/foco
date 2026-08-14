<style>
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
.acordeao-badge-ok {
  display: none;
  align-items: center;
  gap: 4px;
  background: #dcfce7;
  color: #16a34a;
  font-size: 0.82em;
  font-weight: 700;
  padding: 2px 9px;
  border-radius: 20px;
  border: 1px solid #86efac;
  white-space: nowrap;
  margin-right: 50px;
}
.acordeao-badge-ok.visivel { display: flex; }
.acordeao-badge-pendente {
  display: flex;
  align-items: center;
  gap: 4px;
  background: #fef3c7;
  color: #92400e;
  font-size: 0.82em;
  font-weight: 700;
  padding: 2px 9px;
  border-radius: 20px;
  border: 1px solid #fde68a;
  white-space: nowrap;
  margin-right: 50px;
}
.acordeao-badge-pendente.oculto { display: none; }
.acordeao-seta { font-size: 0.85em; color: #ffffff; transition: transform 0.25s; flex-shrink: 0; }
.acordeao-wrapper.aberto .acordeao-seta { transform: rotate(90deg); }
.acordeao-corpo { display: none; padding: 20px 20px 24px; background: #fff; border-top: 1px solid #e2e8f0; }
.acordeao-wrapper.aberto .acordeao-corpo { display: block; }
.acordeao-corpo fieldset { border: none; padding: 0; margin: 0; }
.decl-opcao-btns { display: flex; flex-direction: column; gap: 10px; margin: 14px 0 6px; }
.decl-opcao-btns > input[type="radio"] { display: none; }
.decl-opcao-btn-label { display: flex; align-items: flex-start; gap: 12px; padding: 11px 14px; border: none; border-radius: 7px; cursor: pointer; background: #f8fafc; transition: 0.18s; font-size: 0.93em; color: #1e293b; line-height: 1.5; }
.decl-opcao-btn-label:hover { background: #eff6ff; }
.opcao-icone { flex-shrink: 0; width: 20px; height: 20px; border-radius: 50%; border: 2px solid #94a3b8; background: #fff; display: flex; align-items: center; justify-content: center; transition: 0.18s; margin-top: 1px; font-size: 0.75em; color: transparent; }
.decl-opcao-btns > input[type="radio"]:checked + .decl-opcao-btn-label { border-color: #1e3a5f; background: #eff6ff; box-shadow: 0 0 0 3px rgba(30,58,95,0.10); font-weight: 500; }
.decl-opcao-btns > input[type="radio"]:checked + .decl-opcao-btn-label .opcao-icone { border-color: #1e3a5f; background: #1e3a5f; color: #fff; }
.decl-obs-group { margin-top: 16px; }
.decl-obs-group label { display: block; font-size: 0.92em; font-weight: 600; color: #374151; margin-bottom: 5px; }
.decl-obs-group textarea { width: 100%; min-height: 80px; border: none; background: #f1f5f9; border-radius: 5px; padding: 10px; font-size: 0.91em; resize: vertical; color: #1e293b; outline: none; }
.decl-obs-group textarea:focus { box-shadow: 0 0 0 2px #cbd5e1; }
.decl-btn-assinar { margin-top: 20px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.btn-assinar { padding: 8px 22px; background: #1e3a5f; color: #fff; border: none; border-radius: 5px; font-size: 0.93em; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-assinar:hover:not(:disabled) { background: #2d5282; }
.btn-assinar:disabled { background: #94a3b8; cursor: not-allowed; }
.btn-devolver { padding: 8px 22px; background: #be123c; color: #fff; border: none; border-radius: 5px; font-size: 0.93em; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-devolver:hover:not(:disabled) { background: #9f1239; }
.decl-assinado-overlay { display: none; background: #f0fdf4; border: 1px solid #86efac; border-radius: 5px; padding: 14px 16px; margin-top: 16px; font-size: 0.91em; color: #15803d; line-height: 1.7; }
.decl-assinado-overlay.visivel { display: block; }
.decl-pergunta-inline { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; margin: 8px 0 4px; font-size: 0.94em; color: #1e293b; }
.decl-radio-inline { display: flex; align-items: center; gap: 6px; cursor: pointer; }
.decl-radio-inline input { width: 15px; height: 15px; accent-color: #1e3a5f; }
.decl-radio-group { display: flex; flex-direction: column; gap: 8px; margin: 8px 0 4px; }
.decl-radio-item { display: flex; align-items: flex-start; gap: 10px; font-size: 0.93em; color: #1e293b; cursor: pointer; line-height: 1.5; }
.decl-radio-item input { margin-top: 3px; width: 16px; height: 16px; flex-shrink: 0; accent-color: #1e3a5f; }
.decl-sublabel { font-weight: 600; font-size: 0.92em; color: #1e3a5f; margin: 16px 0 4px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
.decl-bloco-condicional { display: none; }
.decl-bloco-condicional.visivel { display: block; }
.decl-info-nota { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 5px; padding: 10px 14px; font-size: 0.88em; color: #1e40af; margin-bottom: 14px; line-height: 1.5; }
.secao-ativa { border: 2px solid #2563eb !important; }
.secao-ativa .acordeao-header { background: #dbeafe !important; color: #1e3a5f !important; }
.secao-ativa .acordeao-titulo { color: #1e3a5f !important; }
.secao-ativa .acordeao-seta { color: #1e3a5f !important; }
iframe.resumo-frame { width: 100%; height: 72vh; min-height: 520px; border: none; display: block; background: #fff; }
.hist-empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95em; }
.decl-origem-stamp {
    background: #f1f5f9;
    border-left: 4px solid #2563eb;
    padding: 10px 14px;
    margin-bottom: 20px;
    font-size: 0.9em;
    color: #0f172a;
    font-weight: 600;
    border-radius: 0 6px 6px 0;
}
.decl-section-title {
    font-weight: 700;
    font-size: 0.98em;
    color: #1e3a5f;
    margin: 20px 0 10px;
    padding-bottom: 6px;
    border-bottom: 1px solid #e2e8f0;
}
</style>

<div style="margin-bottom: 30px;" id="timeline-container">
    <h3 style="color: #1e3a5f; margin-bottom: 20px; font-weight: 700;">Histórico Cronológico do Processo</h3>

    @if(empty($historicoTramites) || count($historicoTramites) === 0)
        <div class="hist-empty" style="text-align:center; padding:30px; color:#64748b; background:#f8fafc; border-radius:8px; border:1px dashed #cbd5e1;">Nenhum evento registrado no histórico ainda.</div>
    @else
        @foreach($historicoTramites as $index => $tramite)
            @php
                $acao = $tramite->acao;
                $dataAcao = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y H:i');
                $etapaOrigem = $tramite->etapa ?? 'Sistema';
                $dadosSnapshot = is_array($tramite->dados_snapshot) ? $tramite->dados_snapshot : json_decode($tramite->dados_snapshot, true) ?? [];
                $usuario = $tramite->usuario ? $tramite->usuario->name : 'Sistema';
            @endphp

            @php
                $isAba1 = str_contains($acao, 'Aba 1') || str_contains($etapaOrigem, 'Aba 1');
                $isAba2 = str_contains($acao, 'Aba 2') || str_contains($etapaOrigem, 'Aba 2');
                $isAba3 = str_contains($acao, 'Aba 3') || str_contains($etapaOrigem, 'Aba 3');
                $isSalvaOrAtualizacao = in_array($acao, ['Aba 1 Salva', 'Aba 2 Salva', 'Aba 3 Salva', 'Aba 1 Alterada', 'Aba 2 Alterada', 'Aba 3 Alterada', 'Atualização']);
            @endphp

            @if($isSalvaOrAtualizacao && ($isAba1 || $isAba2 || $isAba3))
                @php
                    $isAlteracao = str_contains($acao, 'Alterada');
                    $baseTitle = '📋 Dados';
                    if ($isAba1) $baseTitle = '📋 Dados do Requerimento';
                    if ($isAba2) $baseTitle = '📋 Diagnóstico preliminar do imóvel';
                    if ($isAba3) $baseTitle = '📋 Análise do Destinatário';
                    
                    $dados1 = $isAba1 ? $dadosSnapshot : [];
                    $dados2 = $isAba2 ? $dadosSnapshot : [];
                    $dados3 = $isAba3 ? $dadosSnapshot : [];
                    $rips = $dadosSnapshot['rips'] ?? [];
                    $cadastros = $dadosSnapshot['cadastros_minimos'] ?? [];
                @endphp

                @if(!empty($dadosSnapshot['resposta_devolucao']))
                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                        <h4 style="color: #16a34a; margin-top: 0; margin-bottom: 8px; font-size: 0.95em;">✅ Retornar Processo (Ajuste em resposta à devolução)</h4>
                        <div style="margin: 0; color: #166534; font-size: 0.92em; line-height: 1.5; white-space: pre-wrap;">{{ $dadosSnapshot['resposta_devolucao'] }}</div>
                    </div>
                @endif

                @if($isAba1)
                    <div class="acordeao-wrapper" style="{{ $isAlteracao ? 'border-color: #f59e0b;' : '' }}">
                        <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $isAlteracao ? 'background: #d97706;' : '' }}">
                            <div class="acordeao-titulo">
                                📋 Dados do Requerimento 
                                @if($isAlteracao) <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 4px; font-size: 0.75em; margin-left: 10px;">Alteração</span> @endif
                            </div>
                            <span class="acordeao-seta">▶</span>
                        </div>
                        <div class="acordeao-corpo" style="padding: 15px; background: #fff;">
                            <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 0.9em;">
                                <strong>Registro:</strong> 👤 {{ $usuario }} - 🕒 {{ $dataAcao }}
                            </div>
                            <div class="report-container">
                                @include('processos.abas.resumos.aba1a')
                            </div>
                        </div>
                    </div>

                    <div class="acordeao-wrapper" style="{{ $isAlteracao ? 'border-color: #f59e0b;' : '' }}">
                        <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $isAlteracao ? 'background: #d97706;' : '' }}">
                            <div class="acordeao-titulo">
                                📋 RIP(s) ou Cadastro(s) Mínimo(s)
                                @if($isAlteracao) <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 4px; font-size: 0.75em; margin-left: 10px;">Alteração</span> @endif
                            </div>
                            <span class="acordeao-seta">▶</span>
                        </div>
                        <div class="acordeao-corpo" style="padding: 15px; background: #fff;">
                            <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 0.9em;">
                                <strong>Registro:</strong> 👤 {{ $usuario }} - 🕒 {{ $dataAcao }}
                            </div>
                            <div class="report-container">
                                @include('processos.abas.resumos.aba1b')
                            </div>
                        </div>
                    </div>
                @elseif($isAba2)
                    <div class="acordeao-wrapper" style="{{ $isAlteracao ? 'border-color: #f59e0b;' : '' }}">
                        <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $isAlteracao ? 'background: #d97706;' : '' }}">
                            <div class="acordeao-titulo">
                                📋 Diagnóstico preliminar do imóvel
                                @if($isAlteracao) <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 4px; font-size: 0.75em; margin-left: 10px;">Alteração</span> @endif
                            </div>
                            <span class="acordeao-seta">▶</span>
                        </div>
                        <div class="acordeao-corpo" style="padding: 15px; background: #fff;">
                            <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 0.9em;">
                                <strong>Registro:</strong> 👤 {{ $usuario }} - 🕒 {{ $dataAcao }}
                            </div>
                            <div class="report-container">
                                @include('processos.abas.resumos.aba2')
                            </div>
                        </div>
                    </div>
                @elseif($isAba3)
                    <div class="acordeao-wrapper" style="{{ $isAlteracao ? 'border-color: #f59e0b;' : '' }}">
                        <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="{{ $isAlteracao ? 'background: #d97706;' : '' }}">
                            <div class="acordeao-titulo">
                                📋 Análise de Viabilidade
                                @if($isAlteracao) <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 4px; font-size: 0.75em; margin-left: 10px;">Alteração</span> @endif
                            </div>
                            <span class="acordeao-seta">▶</span>
                        </div>
                        <div class="acordeao-corpo" style="padding: 15px; background: #fff;">
                            <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 0.9em;">
                                <strong>Registro:</strong> 👤 {{ $usuario }} - 🕒 {{ $dataAcao }}
                            </div>
                            <div class="report-container">
                                <h4 style="margin-top: 0; color: #1e3a5f; font-size: 1.1em; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 15px;">Análise do Destinatário</h4>
                                @include('processos.abas.resumos.aba3_analise')
                                
                                <h4 style="margin-top: 25px; color: #1e3a5f; font-size: 1.1em; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 15px;">Proposta de Destinação</h4>
                                @include('processos.abas.resumos.aba3_proposta')
                            </div>
                        </div>
                    </div>
                @endif

            @elseif($acao === 'Devolvido')
                <div class="acordeao-wrapper" style="border-color: #ef4444;">
                    <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="background: #ef4444;">
                        <div class="acordeao-titulo" style="color: #fff;">
                            ⚠️ Devolução - Origem: {{ $etapaOrigem }}
                        </div>
                        <span class="acordeao-seta" style="color: #fff;">▶</span>
                    </div>
                    <div class="acordeao-corpo" style="padding: 15px; background: #fef2f2;">
                        <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #fecaca; color: #991b1b; font-size: 0.9em;">
                            <strong>Registro:</strong> 👤 {{ $usuario }} - 🕒 {{ $dataAcao }}
                        </div>
                        <p style="margin: 0; color: #991b1b; font-size: 0.95em;"><strong>Motivo da Devolução:</strong></p>
                        <div style="margin-top: 8px; padding: 12px; background: #fee2e2; border-radius: 6px; color: #7f1d1d; font-size: 0.92em; line-height: 1.5;">
                            {{ $tramite->justificativa }}
                        </div>
                    </div>
                </div>

            @elseif($acao === 'Recebido')
                <div style="margin-bottom: 20px; display: flex; justify-content: center;">
                    <div style="background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 20px; padding: 6px 20px; font-size: 0.85em; font-weight: bold; color: #475569; display: inline-flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Recebido por {{ $usuario }} em {{ $dataAcao }}
                    </div>
                </div>

            @elseif($acao === 'Devolução Resolvida')
                <div class="acordeao-wrapper" style="border-color: #16a34a;">
                    <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="background: #16a34a;">
                        <div class="acordeao-titulo" style="color: #fff;">
                            ✅ Devolução Resolvida
                        </div>
                        <span class="acordeao-seta" style="color: #fff;">▶</span>
                    </div>
                    <div class="acordeao-corpo" style="padding: 15px; background: #f0fdf4;">
                        <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #bbf7d0; color: #166534; font-size: 0.9em;">
                            <strong>Registro:</strong> 👤 {{ $usuario }} - 🕒 {{ $dataAcao }}
                        </div>
                        <p style="margin: 0; color: #166534; font-size: 0.95em;"><strong>Resolução:</strong></p>
                        <div style="margin-top: 8px; padding: 12px; background: #dcfce7; border-radius: 6px; color: #14532d; font-size: 0.92em; line-height: 1.5;">
                            {{ $tramite->justificativa }}
                        </div>
                    </div>
                </div>

            @else
                @php
                    $prefixMap = [
                        'Chefia'            => 'chefia',
                        'Coordenação SPU/UF' => 'coordenacao',
                        'Superintendência'  => 'superintendencia',
                        'Equipe C.G.'       => 'equipe_cg',
                        'Coordenação-Geral' => 'coordenacao_geral',
                        'Direção'           => 'direcao',
                        'CDE'               => 'cde'
                    ];

                    $prefix = $prefixMap[$etapaOrigem] ?? null;

                    // Se a etapaOrigem não for um perfil válido (ex: 'Aba 7' genérico), tentamos descobrir
                    // qual perfil assinou neste trâmite em específico.
                    if (!$prefix) {
                        $tramiteDataStr = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y');
                        
                        // 1. Tentar casar pela data da assinatura contida no snapshot
                        foreach ($prefixMap as $label => $pref) {
                            $sigData = $dadosSnapshot['assinatura_' . $pref . '_data'] ?? '';
                            if ($sigData && str_contains($sigData, $tramiteDataStr)) {
                                $prefix = $pref;
                                $etapaOrigem = $label;
                            }
                        }

                        // 2. Fallback: buscar na ordem hierárquica reversa (CDE -> Chefia)
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
                    <div class="acordeao-wrapper" style="border-color: #0d9488;">
                        <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="background: #0f766e;">
                            <div class="acordeao-titulo" style="color: #fff;">
                                📝 Manifestação - {{ $etapaOrigem }}
                            </div>
                            <span class="acordeao-seta" style="color: #fff;">▶</span>
                        </div>
                        <div class="acordeao-corpo" style="padding: 15px; background: #f0fdfa;">
                            <div style="margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #99f6e4; color: #0f766e; font-size: 0.9em;">
                                <strong>Registro:</strong> 👤 {{ $dadosSnapshot['assinatura_'.$prefix.'_nome'] }} &mdash; 🕒 {{ $dadosSnapshot['assinatura_'.$prefix.'_data'] ?? $dataAcao }}
                            </div>
                            <div class="decl-assinado-overlay visivel" style="display:block; margin-top:0; border-color:#5eead4; background:#ccfbf1;">
                                @include('processos.abas.partials.carimbo_manifestacao', ['dadosSnapshot' => $dadosSnapshot, 'prefix' => $prefix])
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
    @endif
</div>
<hr style="margin: 30px 0; border: 0; border-top: 1px solid #e2e8f0;">
