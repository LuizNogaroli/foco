<script>
    window.INLINE_SOLICITACAO_RIP = @json($dados['solicitacao_criacao_rip'] ?? ($processo->foco?->aba1?->solicitacao_criacao_rip ?? ''));
    window.INLINE_SOLICITACAO_ANEXOS = @json($dados['solicitacao_anexos'] ?? []);
</script>
<link rel="stylesheet" href="{{ asset('css/custom-select.css') }}">
<style>
  .accordion-icon {
    font-size: 0.9rem;
    color: white !important;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-block;
  }
  .active .accordion-icon {
    transform: rotate(90deg);
  }
  .consolidated-box {
    background-color: #f1f5f9;
    border-left: 4px solid #0056b3;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
  }
  .consolidated-box h4 {
    margin-top: 0;
    color: #1e3a5f;
    font-size: 1.1em;
  }
  .consolidated-box p {
    margin: 5px 0;
    font-size: 0.95em;
    color: #475569;
  }
  h4.section-title {
    margin: 24px 0 16px 0;
    color: #0056b3;
    border-bottom: 2px solid #ddd;
    padding-bottom: 8px;
  }

  .acordeao-wrapper {
    margin-top: 16px;
    border: none;
    border-radius: 6px;
    overflow: hidden;
  }
  fieldset { border: none !important; }
  .acordeao-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #1e3a5f;
    cursor: pointer;
    user-select: none;
    gap: 10px;
    transition: background 0.2s;
  }
  .acordeao-header:hover {
    background: #2d5282;
  }
  .acordeao-titulo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 0.97em;
    color: #fff;
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
  }
  .acordeao-badge-ok.visivel {
    display: flex;
  }
  .acordeao-badge-ok::before {
    content: "?";
    font-size: 1em;
  }
  .acordeao-seta {
    font-size: 0.85em;
    color: #fff;
    transition: transform 0.25s;
    flex-shrink: 0;
  }
  .acordeao-wrapper.aberto .acordeao-seta {
    transform: rotate(90deg);
  }
  .acordeao-corpo {
    display: none;
    padding: 20px 20px 24px;
    background: #fff;
    border-top: none;
  }
  .acordeao-wrapper.aberto .acordeao-corpo {
    display: block;
  }
  .decl-intro {
    font-size: 0.93em;
    color: #374151;
    margin-bottom: 16px;
    line-height: 1.6;
  }
  .decl-checks {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
  }
  .decl-check-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 12px;
    border: none;
    border-radius: 5px;
    background: #f8fafc;
    cursor: pointer;
    transition:
      background 0.15s,
      border-color 0.15s;
  }
  .decl-check-item:hover {
    background: #f0f9ff;
    border-color: #93c5fd;
  }
  .decl-check-item input[type="checkbox"] {
    margin-top: 2px;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    accent-color: #16a34a;
    cursor: pointer;
  }
  .decl-check-item span {
    font-size: 0.91em;
    color: #374151;
    line-height: 1.55;
  }
  .decl-btn-assinar {
    margin-top: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .btn-assinar {
    padding: 8px 22px;
    background: #1e3a5f;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 0.93em;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }
  .btn-assinar:hover {
    background: #2d5282;
  }
  .btn-assinar:disabled {
    background: #94a3b8;
    cursor: not-allowed;
  }
  .btn-limpar-decl {
    padding: 8px 16px;
    background: transparent;
    color: #64748b;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    font-size: 0.91em;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-limpar-decl:hover {
    background: #f1f5f9;
    color: #374151;
  }
  .decl-status-assinado {
    display: none;
    align-items: center;
    gap: 6px;
    color: #16a34a;
    font-size: 0.91em;
    font-weight: 600;
  }
  .decl-status-assinado.visivel {
    display: flex;
  }
  .decl-status-assinado::before {
    content: "?";
    font-size: 1.1em;
  }
  .decl-assinado-overlay {
    display: none;
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 5px;
    padding: 14px 16px;
    margin-top: 16px;
    font-size: 0.91em;
    color: #15803d;
    line-height: 1.7;
  }
  .decl-assinado-overlay.visivel {
    display: block;
  }
  .decl-assinado-overlay strong {
    display: block;
    margin-bottom: 6px;
    font-size: 1em;
    color: #166534;
  }
  #btnEnviarSPU {
    background: #1a7a4a;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 8px 18px;
    font-size: 0.95em;
    font-weight: 600;
    cursor: pointer;
    transition:
      background 0.2s,
      box-shadow 0.2s;
    box-shadow: 0 2px 6px rgba(26, 122, 74, 0.25);
  }
  #btnEnviarSPU:hover {
    background: #155f39;
    box-shadow: 0 4px 10px rgba(26, 122, 74, 0.35);
  }
  #btnEnviarSPU:active {
    background: #0f4328;
  }
</style>

<div id="aba3-container" class="form-container">
  <h2>Análise de Viabilidade e Proposta de Destinação</h2>
  <form action="{{ route('processos.tramitar', $processo->id) }}" method="POST" hx-post="{{ route('processos.tramitar', $processo->id) }}" hx-target="#aba3-container" hx-indicator="#form-indicator-aba3" id="form03">
      <div id="form-indicator-aba3" class="htmx-indicator" style="display:none; color: #475569; margin-bottom: 10px;">⏳ Processando...</div>
      @csrf
      <input type="hidden" name="aba_atual" value="3">
      <input type="hidden" name="next_aba" value="index">

            <!-- Bloco Retornar Processo -->
            @if($processo->tramitacao === 'Devolvido')
                @if(isset($respostaDevolucao))
                <div id="blocoRespostaDevolutivaAba3" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                    <h4 style="color: #16a34a; margin-top: 0; border-bottom: 1px solid #86efac; padding-bottom: 8px;">✅ Retornar Processo</h4>
                    <p style="margin-bottom: 10px; font-size: 0.9em; color: #166534;">
                        Justificativa preenchida por <strong>{{ $respostaDevolucao['usuario'] }}</strong> em {{ $respostaDevolucao['data'] }}
                    </p>
                    <div style="width: 100%; min-height: 80px; padding: 15px; border: 1px solid #86efac; border-radius: 4px; background-color: #f8fafc; font-family: inherit; font-size: 14px; color: #334155; white-space: pre-wrap;">{{ $respostaDevolucao['texto'] }}</div>
                </div>
                @else
                <div id="blocoRespostaDevolutivaAba3" class="editavel" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                    <h4 style="color: #16a34a; margin-top: 0; border-bottom: 1px solid #86efac; padding-bottom: 8px;">Retornar Processo</h4>
                    <label for="resposta_devolucao" style="font-weight: bold; color: #166534;">Descreva o que foi corrigido ou complementado nesta etapa (Obrigatório para enviar):</label>
                    <textarea id="resposta_devolucao" name="resposta_devolucao" required style="width: 100%; min-height: 100px; padding: 10px; border: 1px solid #86efac; border-radius: 4px; margin-top: 10px; font-family: inherit; font-size: 14px;">{{ $dados['resposta_devolucao'] ?? '' }}</textarea>
                </div>
                @endif
            @endif

      @if ($errors->any())
          <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
              <h4 style="color: #b91c1c; margin-top: 0; margin-bottom: 10px;">⚠️ Atenção: Não foi possível salvar devido aos seguintes erros:</h4>
              <ul style="color: #991b1b; margin-bottom: 0; padding-left: 20px;">
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif

      <!-- ========== ACCORDIONS DE REVISÃO (ABAS 1 E 2) ========== -->
        <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
          <!-- ABA 1a - Dados do Requerimento -->
          <div class="accordion-item" style="border: none;">
            <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
              <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📋 Dados do Requerimento</span>
              <span class="accordion-icon">▶</span>
            </div>
            <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">
              @php $req = $requerimento ?? null; @endphp
              <div style="display:flex;flex-direction:column;">
                <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Nome do Requerente:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->nome_requerente ?? '-' }}</span></div>
                <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">CPF/CNPJ:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->cpf_cnpj_requerente ?? '-' }}</span></div>
                <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Telefone:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->contato_requerente ?? '-' }}</span></div>
                <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Número do Requerimento:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->numero_requerimento ?? $processo->numero_requerimento }}</span></div>
                <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Data do Requerimento:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->data_hora_recebimento ?? $processo->created_at?->format('d/m/Y') ?? '-' }}</span></div>
                <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Número do Processo SEI:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->nup_sei ?? '-' }}</span></div>
                <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Prioridade Legal:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;color:#ea580c;font-weight:bold;">{{ $req?->prioridade_legal ?? 'Não se aplica' }}</span></div>
                <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Tipo de Requerimento:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->tipo_requerimento ?? $processo->tipo_requerimento ?? '-' }}</span></div>
              </div>
              @if($req?->nome_representante)
              <div style="margin-top:16px; padding-top:16px; border-top:1px dashed #cbd5e1;">
                <div style="font-size:0.85rem; font-weight:700; color:#1e3a5f; margin-bottom:10px;">Representante Legal</div>
                <div style="display:flex;flex-direction:column;">
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Nome:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->nome_representante ?? '-' }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">CPF/CNPJ do Representante:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->cpf_cnpj_representante ?? '-' }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Telefone do Representante:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $req?->contato_representante ?? '-' }}</span></div>
                </div>
              </div>
              @endif
            </div>
          </div>

          <!-- ABA 1b - Indicação do Imóvel -->
          <!-- Bloco de Solicitação de Criação de RIP -->
          <div id="container-solicitacao-criacao-rip" style="display: none; margin-bottom: 12px; width: 100%;"></div>

          <div class="accordion-item" style="border: none;">
            <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
              <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📋 RIP(s) ou Cadastro(s) Mínimo(s)</span>
              <span class="accordion-icon">▶</span>
            </div>
            <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">
              @php
                $focoRips = $processo->foco?->rips ?? collect();
                $focoCadastros = $processo->foco?->cadastrosMinimos ?? collect();
              @endphp
    
              @if($focoRips->isEmpty() && $focoCadastros->isEmpty())
                <div id="rips-aba3-container">
                  <p style="color:#64748b; font-style:italic;">Nenhum RIP ou Cadastro Mínimo associado. Os dados são carregados dinamicamente abaixo.</p>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', async function() {
                  const container = document.getElementById('rips-aba3-container');
                  const processId = window.CURRENT_PROCESS_ID || "{{ $processo->numero_requerimento }}";
                  const SUPA_URL = window.SUPABASE_URL || 'https://btmpxettyjbtkfkcfmmu.supabase.co';
                  const SUPA_KEY = window.SUPABASE_ANON_KEY;
                  console.log('[Aba3-RIP] Iniciando busca. processId:', processId, 'SUPA_URL:', SUPA_URL, 'KEY?', !!SUPA_KEY);
                  if (!processId || !SUPA_URL || !SUPA_KEY) { console.warn('[Aba3-RIP] Variáveis ausentes, abortando.'); return; }
    
                  function buildField(label, value) {
                    return `<div style="display:flex;align-items:baseline;margin-bottom:6px;padding:5px 0;font-size:0.9rem;">
                      <span style="display:flex;width:240px;"><span style="font-weight:600;color:#334155;white-space:nowrap;">${label}</span><span style="flex:1;border-bottom:1px dotted #94a3b8;min-width:10px;"></span><span style="white-space:nowrap;color:#334155;">:</span></span>
                      <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;color:#0f172a;border-radius:3px;">${value || '-'}</span>
                    </div>`;
                  }
    
                  let rips = [], cadastros = [];
                  try {
                    const urlInd = `${SUPA_URL}/rest/v1/tabela_indicacao?select=*&numero_requerimento=eq.${encodeURIComponent(processId)}&limit=1`;
                    console.log('[Aba3-RIP] Consultando tabela_indicacao:', urlInd);
                    const resInd = await fetch(urlInd, { headers: { 'apikey': SUPA_KEY, 'Authorization': `Bearer ${SUPA_KEY}` } });
                    if (resInd.ok) {
                      const rows = await resInd.json();
                      console.log('[Aba3-RIP] Resposta tabela_indicacao:', rows);
                      if (rows?.[0]?.dados_json) {
                        const dj = typeof rows[0].dados_json === 'string' ? JSON.parse(rows[0].dados_json) : rows[0].dados_json;
                        if (dj) { rips = dj.rips || []; cadastros = dj.cadastros_minimos || []; }
                      }
                    } else {
                      console.warn('[Aba3-RIP] tabela_indicacao retornou status:', resInd.status);
                    }
                  } catch(e) { console.error('[Aba3-RIP] Erro ao consultar tabela_indicacao:', e); }
    
                  console.log('[Aba3-RIP] Após tabela_indicacao - rips:', rips.length, 'cadastros:', cadastros.length);
    
                  if (rips.length === 0 && cadastros.length === 0) {
                    try {
                      const reqUrl = `${SUPA_URL}/rest/v1/tabela_requerimentos?select=*&numero_requerimento=eq.${encodeURIComponent(processId)}&limit=1`;
                      console.log('[Aba3-RIP] Fallback: consultando tabela_requerimentos:', reqUrl);
                      const reqRes = await fetch(reqUrl, { headers: { 'apikey': SUPA_KEY, 'Authorization': `Bearer ${SUPA_KEY}` } });
                      if (reqRes.ok) {
                        const reqList = await reqRes.json();
                        console.log('[Aba3-RIP] Resposta tabela_requerimentos:', reqList);
                        if (reqList && reqList.length > 0) {
                          const dj = typeof reqList[0].dados_json === 'string' ? JSON.parse(reqList[0].dados_json) : reqList[0].dados_json;
                          if (dj) { rips = dj.rips || []; cadastros = dj.cadastros_minimos || []; }
                        }
                      } else {
                        console.warn('[Aba3-RIP] tabela_requerimentos retornou status:', reqRes.status);
                      }
                    } catch (e) { console.error('[Aba3-RIP] Erro ao consultar tabela_requerimentos:', e); }
                  }
    
                  console.log('[Aba3-RIP] Resultado final - rips:', rips.length, 'cadastros:', cadastros.length);
                  container.innerHTML = '';
                  if (rips.length === 0 && cadastros.length === 0) {
                    container.innerHTML = '<p style="color:#64748b;font-style:italic;">Nenhum RIP ou Cadastro Mínimo associado a este processo.</p>';
                    return;
                  }
    
                  for (const raw of rips) {
                    const ripObj = typeof raw === 'string' ? { numero_rip: raw } : (raw || {});
                    const rip = ripObj.numero_rip;
                    const destT = ripObj.destinacao_terreno || '';
                    const areaT = ripObj.area_terreno_parcial || '';
                    const destI = ripObj.destinacao_imovel || '';
                    const areaI = ripObj.area_imovel_parcial || '';
                    let dadosSPU = {};
                    try { if (typeof window.fetchSPU === 'function') dadosSPU = await window.fetchSPU(rip); } catch(e) {}
                    function destLine(p, dest, area) {
                      if (!dest) return '';
                      let compl = '';
                      if (dest === 'Parcial' && area) compl = ' — <strong>Metragem:</strong> ' + area + ' m²';
                      return `<div style="margin-bottom:6px;"><span style="font-weight:600;color:#1e293b;">${p}</span><br><span style="color:#166534;">${dest}</span>${compl}</div>`;
                    }
                    const destBox = (destT || destI) ? `<div style="margin-top:10px;padding:10px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:0.9rem;">
                      ${destLine('Qual a área do terreno a ser destinada?', destT, areaT)}
                      ${destLine('Qual a área do imóvel a ser destinada?', destI, areaI)}
                    </div>` : '';
                    const block = document.createElement('div');
                    block.className = 'accordion-item';
                block.style.cssText = 'border:none;margin-bottom:8px;';
                    block.innerHTML = `<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)"><span class="accordion-title" style="font-weight: 600; color: #ffffff;">🏠 Imóvel (RIP): ${rip}</span><span class="accordion-icon">▶</span></div>
                    <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;"><div style="display:flex;flex-direction:column;">
                      ${buildField('Conceituação do Imóvel', dadosSPU.conceituacao)}
                      ${buildField('Tipo de Imóvel', dadosSPU.tipo_imovel)}
                      ${buildField('Natureza do Imóvel', dadosSPU.natureza || dadosSPU.natureza_terreno)}
                      ${buildField('Classificação do Imóvel', dadosSPU.classificacao)}
                      ${(String(dadosSPU.natureza || dadosSPU.natureza_terreno).trim() === 'Urbano') ? buildField('Inscrição Municipal', dadosSPU.inscricao_municipal) : ''}
                      ${(String(dadosSPU.natureza || dadosSPU.natureza_terreno).trim() === 'Rural') ? buildField('CCIR', dadosSPU.ccir) : ''}
                      ${buildField('Condição de Urbanização', dadosSPU.condicao_urbanizacao)}
                      ${buildField('CEP', dadosSPU.cep)}
                      ${buildField('Logradouro', dadosSPU.logradouro || dadosSPU.endereco)}
                      ${buildField('Bairro', dadosSPU.bairro)}
                      ${buildField('Município / UF', (dadosSPU.municipio || '') + ' / ' + (dadosSPU.uf || ''))}
                      ${buildField('Área Total (m²)', dadosSPU.area_total)}
                      ${buildField('Área da União (m²)', dadosSPU.area_uniao || dadosSPU.area_terreno_uniao)}
                      ${buildField('Área Construída Total (m²)', dadosSPU.area_construida_total)}
                      ${buildField('Área Construída Disponível (m²)', dadosSPU.area_construida_disponivel)}
                      ${buildField('Área de Terreno Disponível (m²)', dadosSPU.area_terreno_disponivel)}
                      ${buildField('Benfeitorias', dadosSPU.benfeitorias)}
                      ${buildField('Situação da Incorporação', dadosSPU.situacao_incorporacao || dadosSPU.situacao)}
                      ${buildField('Processo de Incorporação', dadosSPU.processo_incorporacao)}
                      ${buildField('LPM/1831 ou LMEO Homologadas?', dadosSPU.lpm_homologada)}
                      ${buildField('Valor da Avaliação (R$)', dadosSPU.valor_avaliado || dadosSPU.valor_avaliacao)}
                      ${buildField('Data da Avaliação', dadosSPU.data_avaliacao)}
                      ${buildField('Instrumento de Avaliação', dadosSPU.instrumento_avaliacao)}
                    </div>
                    ${destBox}
                    </div>`;
                    container.appendChild(block);
                  }
    
                  cadastros.forEach((cad, idx) => {
                    const block = document.createElement('div');
                    block.className = 'accordion-item';
                block.style.cssText = 'border:none;margin-bottom:8px;';
                    const destT = cad.destinacao_terreno || '';
                    const areaT = cad.area_terreno_parcial || '';
                    const destI = cad.destinacao_imovel || '';
                    const areaI = cad.area_imovel_parcial || '';
                    function destLine(p, dest, area) {
                      if (!dest) return '';
                      let compl = '';
                      if (dest === 'Parcial' && area) compl = ' — <strong>Metragem:</strong> ' + area + ' m²';
                      return `<div style="margin-bottom:6px;"><span style="font-weight:600;color:#1e293b;">${p}</span><br><span style="color:#166534;">${dest}</span>${compl}</div>`;
                    }
                    const destBox = (destT || destI) ? `<div style="margin-top:10px;padding:10px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:0.9rem;">
                      ${destLine('Qual a área do terreno a ser destinada?', destT, areaT)}
                      ${destLine('Qual a área do imóvel a ser destinada?', destI, areaI)}
                    </div>` : '';
                    const lat = cad.latitude || '';
                    const lng = cad.longitude || '';
                    function cadField(l, v) {
                      return `<div><div style="font-weight:600;color:#334155;font-size:0.78rem;margin-bottom:2px;">${l}</div><div style="padding:4px 10px;background:#f1f5f9;border-radius:3px;">${v || '-'}</div></div>`;
                    }
                    function cadFieldFull(l, v) {
                      return `<div style="grid-column:1 / -1;"><div style="font-weight:600;color:#334155;font-size:0.78rem;margin-bottom:2px;">${l}</div><div style="padding:4px 10px;background:#f1f5f9;border-radius:3px;">${v || '-'}</div></div>`;
                    }
                    const mapaHtml = (lat && lng) ? `<div style="width:320px;flex-shrink:0;min-height:260px;"><div id="mapa-cad-a3-${idx}" data-leaflet-map style="width:100%;height:100%;min-height:260px;border:1px solid #cbd5e1;border-radius:6px;"></div></div>` : '';
                    block.innerHTML = `<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                        <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📝 Cadastro Mínimo #${idx+1} (Sem RIP)</span>
                        <span class="accordion-icon">▶</span>
                    </div>
                    <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;"><div style="display:flex;flex-direction:row;gap:15px;align-items:stretch;">
                      <div style="flex:1;min-width:0;"><div style="display:grid;grid-template-columns:1fr 1fr;gap:8px 12px;font-size:0.9rem;">
                        ${cadField('CEP', cad.cep)}
                        ${cadField('Logradouro', cad.logradouro || cad.endereco)}
                        ${cadField('Número', cad.numero)}
                        ${cadField('Complemento', cad.complemento)}
                        ${cadField('Município / UF', (cad.municipio || '') + ' / ' + (cad.uf || ''))}
                        ${cadField('Área (m²)', cad.area || cad.area_m2)}
                        ${cadField('Localização', (lat && lng) ? (lat + ', ' + lng) : (cad.modo_localizacao || '-'))}
                        ${cad.observacoes ? cadFieldFull('Observações', cad.observacoes) : ''}
                      </div>
                      ${destBox}
                      </div>
                      ${mapaHtml}
                    </div></div>`;
                    container.appendChild(block);
                    if (lat && lng && typeof initMapCadastro === 'function') {
                      initMapCadastro('mapa-cad-a3-' + idx, lat, lng);
                    }
                  });
                });
                </script>
              @else
                {{-- RIPs já disponíveis no MySQL --}}
                <div style="display: flex; flex-direction: column; gap: 10px;" id="rips-aba3-mysql">
                  @foreach($focoRips as $rip)
                  <div style="background:white;border:1px solid #cbd5e1;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:8px;">
                    <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                      <span class="accordion-title" style="font-weight: 600; color: #ffffff;">🏠 Imóvel (RIP): {{ $rip->numero_rip }}</span><span class="accordion-icon">▶</span>
                    </div>
                    <div style="padding:16px;display:none;background:#fff;" id="rip-spu-aba3-{{ $loop->index }}">
                      <p style="color:#64748b; font-size: 0.9em; font-style: italic;">Carregando dados SPU...</p>
                    </div>
                  </div>
                  <script>
                    document.addEventListener('DOMContentLoaded', async function() {
                      const el = document.getElementById('rip-spu-aba3-{{ $loop->index }}');
                      let d = {};
                      try { if (typeof window.fetchSPU === 'function') d = await window.fetchSPU('{{ $rip->numero_rip }}'); } catch(e) {}
                      function f(l,v){return `<div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">${l}:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">${v||'-'}</span></div>`;}
                      const destT = '{{ $rip->destinacao_terreno }}';
                      const areaT = '{{ $rip->area_terreno_parcial }}';
                      const destI = '{{ $rip->destinacao_imovel }}';
                      const areaI = '{{ $rip->area_imovel_parcial }}';
                      function destLine(pergunta, dest, area) {
                        if (!dest) return '';
                        let compl = '';
                        if (dest === 'Parcial' && area) compl = ' — <strong>Metragem:</strong> ' + area + ' m²';
                        return `<div style="margin-bottom:6px;"><span style="font-weight:600;color:#1e293b;">${pergunta}</span><br><span style="color:#166534;">${dest}</span>${compl}</div>`;
                      }
                      const destBox = (destT || destI) ? `<div style="margin-top:10px;padding:10px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:0.9rem;">
                        ${destLine('Qual a área do terreno a ser destinada?', destT, areaT)}
                        ${destLine('Qual a área do imóvel a ser destinada?', destI, areaI)}
                      </div>` : '';
                      el.innerHTML = `<div style="display:flex;flex-direction:column;">
                          ${f('Conceituação do Imóvel', d.conceituacao)}
                          ${f('Tipo de Imóvel', d.tipo_imovel)}
                          ${f('Natureza do Imóvel', d.natureza || d.natureza_terreno)}
                          ${f('Classificação do Imóvel', d.classificacao)}
                          ${(String(d.natureza || d.natureza_terreno).trim() === 'Urbano') ? f('Inscrição Municipal', d.inscricao_municipal) : ''}
                          ${(String(d.natureza || d.natureza_terreno).trim() === 'Rural') ? f('CCIR', d.ccir) : ''}
                          ${f('Condição de Urbanização', d.condicao_urbanizacao)}
                        ${f('CEP', d.cep)}
                        ${f('Logradouro', d.logradouro || d.endereco)}
                        ${f('Bairro', d.bairro)}
                        ${f('Município / UF', (d.municipio || '') + ' / ' + (d.uf || ''))}
                        ${f('Área Total (m²)', d.area_total)}
                        ${f('Área da União (m²)', d.area_uniao || d.area_terreno_uniao)}
                        ${f('Área Construída Total (m²)', d.area_construida_total)}
                        ${f('Área Construída Disponível (m²)', d.area_construida_disponivel)}
                        ${f('Área de Terreno Disponível (m²)', d.area_terreno_disponivel)}
                        ${f('Benfeitorias', d.benfeitorias)}
                        ${f('Situação da Incorporação', d.situacao_incorporacao || d.situacao)}
                        ${f('Processo de Incorporação', d.processo_incorporacao)}
                        ${f('LPM/1831 ou LMEO Homologadas?', d.lpm_homologada)}
                        ${f('Valor da Avaliação (R$)', d.valor_avaliado || d.valor_avaliacao)}
                        ${f('Data da Avaliação', d.data_avaliacao)}
                        ${f('Instrumento de Avaliação', d.instrumento_avaliacao)}
                      </div>
                      ${destBox}`;
                    });
                  </script>
                  @endforeach
                  @foreach($focoCadastros as $idx => $cad)
                  @php
                      $cadLat = $cad->latitude ?? '';
                      $cadLng = $cad->longitude ?? '';
                      $ripsVincA3 = $cad->ripsVinculados ?? collect();
                  @endphp
                  <div style="background:white;border:1px solid #cbd5e1;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                      <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📝 Cadastro Mínimo #{{ $idx+1 }} (Sem RIP)</span>
                      @if($ripsVincA3->isNotEmpty())
                      <span style="display:inline-flex;align-items:center;gap:5px;background:#1d4ed8;color:#fff;padding:2px 10px;border-radius:999px;font-size:0.72rem;font-weight:600;margin-left:10px;">🔗 Transformado em RIP @foreach($ripsVincA3 as $rv){{ $loop->first ? '' : ', ' }}{{ $rv->numero_rip }}@endforeach</span>
                      @endif
                      <span class="accordion-icon">▶</span>
                    </div>
                    <div style="padding:16px;display:none;background:#fff;">
                      @if($ripsVincA3->isNotEmpty())
                      <div style="margin-bottom:12px;padding:10px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;font-size:0.9rem;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-size:1.1rem;">🔗</span>
                        <span><strong style="color:#1d4ed8;">Área transformada no RIP:</strong></span>
                        @foreach($ripsVincA3 as $rv)
                        <span style="display:inline-block;background:#dbeafe;color:#1e40af;padding:2px 10px;border-radius:999px;font-weight:600;">{{ $rv->numero_rip }}</span>
                        @endforeach
                      </div>
                      @endif
                      <div style="display:flex;flex-direction:row;gap:15px;align-items:stretch;">
                        <div style="flex:1;min-width:0;">
                          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px 12px;font-size:0.9rem;">
                            @php
                                $cadFields = [
                                    'CEP' => $cad->cep ?? '-',
                                    'Logradouro' => $cad->logradouro ?? '-',
                                    'Número' => $cad->numero ?? '-',
                                    'Complemento' => $cad->complemento ?? '-',
                                    'Município / UF' => ($cad->municipio ?? '-') . ' / ' . ($cad->uf ?? '-'),
                                    'Área (m²)' => $cad->area ?? $cad->area_m2 ?? '-',
                                    'Localização' => $cadLat && $cadLng ? ($cadLat . ', ' . $cadLng) : ($cad->modo_localizacao ?? '-'),
                                ];
                            @endphp
                            @foreach($cadFields as $label => $value)
                            <div>
                              <div style="font-weight:600;color:#334155;font-size:0.78rem;margin-bottom:2px;">{{ $label }}</div>
                              <div style="padding:4px 10px;background:#f1f5f9;border-radius:3px;">{{ $value }}</div>
                            </div>
                            @endforeach
                            @if($cad->observacoes)
                            <div style="grid-column:1 / -1;">
                              <div style="font-weight:600;color:#334155;font-size:0.78rem;margin-bottom:2px;">Observações</div>
                              <div style="padding:4px 10px;background:#f1f5f9;border-radius:3px;">{{ $cad->observacoes }}</div>
                            </div>
                            @endif
                          </div>
                          @if($cad->destinacao_terreno || $cad->destinacao_imovel)
                          <div style="margin-top:10px;padding:10px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:0.9rem;">
                            <div style="margin-bottom:6px;"><span style="font-weight:600;color:#1e293b;">Qual a área do terreno a ser destinada?</span><br><span style="color:#166534;">{{ $cad->destinacao_terreno ?? '-' }}</span>@if($cad->destinacao_terreno === 'Parcial' && $cad->area_terreno_parcial) — <strong>Metragem:</strong> {{ $cad->area_terreno_parcial }} m² @endif</div>
                            <div><span style="font-weight:600;color:#1e293b;">Qual a área do imóvel a ser destinada?</span><br><span style="color:#166534;">{{ $cad->destinacao_imovel ?? '-' }}</span>@if($cad->destinacao_imovel === 'Parcial' && $cad->area_imovel_parcial) — <strong>Metragem:</strong> {{ $cad->area_imovel_parcial }} m² @endif</div>
                          </div>
                          @endif
                        </div>
                        @if($cadLat && $cadLng)
                        <div style="width:320px;flex-shrink:0;min-height:260px;">
                          <div id="mapa-cad-mysql-a3-{{ $loop->index }}" data-leaflet-map style="width:100%;height:100%;min-height:260px;border:1px solid #cbd5e1;border-radius:6px;"></div>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                  <script>
                    document.addEventListener('DOMContentLoaded', function() {
                      if ({{ $cadLat && $cadLng ? 'true' : 'false' }} && typeof initMapCadastro === 'function') {
                        initMapCadastro('mapa-cad-mysql-a3-{{ $loop->index }}', '{{ $cadLat }}', '{{ $cadLng }}');
                      }
                    });
                  </script>
                  @endforeach
                </div>
              @endif
            </div>
          </div>

          <!-- ABA 2 -->
          <div class="accordion-item" style="border: none;">
            <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
              <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📋 Diagnóstico preliminar do imóvel</span>
              <span class="accordion-icon">▶</span>
            </div>
            <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">
              @php
                $aba2 = $processo->foco?->aba2;
              @endphp
              @if(!$aba2)
                <p style="color:#64748b; font-style:italic;">Os dados de caracterização não foram preenchidos ou não foram migrados para a nova estrutura.</p>
              @else
                <div style="display:flex;flex-direction:column;">
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Situação Ocupacional:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $aba2->situacao_ocupacional ?? '-' }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Tempo de Desocupação:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $aba2->tempo_desocupacao ?? '-' }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Data de Conhecimento:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $aba2->data_conhecimento_ocupacao ?? '-' }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Tipo de Uso Atual:</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $aba2->tipo_uso_atual ?? '-' }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Há Incidência Ambiental?</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ is_array($aba2->ha_incidencia) ? implode(', ', $aba2->ha_incidencia) : ($aba2->ha_incidencia ?? '-') }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Há Riscos?</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ is_array($aba2->ha_riscos) ? implode(', ', $aba2->ha_riscos) : ($aba2->ha_riscos ?? '-') }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Há Restrições?</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ is_array($aba2->ha_restricoes) ? implode(', ', $aba2->ha_restricoes) : ($aba2->ha_restricoes ?? '-') }}</span></div>
                  <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;"><span style="width:240px;font-weight:600;color:#334155;">Coordenadas Geográficas (Lat/Long):</span><span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $aba2->latitude ?? '-' }} / {{ $aba2->longitude ?? '-' }}</span></div>
                </div>
              @endif
            </div>
          </div>


        </div>
        <!-- ========================================================= -->

  @php
      $canEditAba3 = false;
      if (isset($perfil) && ($perfil === 'ALL' || $perfil === 'Equipe Destinação')) {
          if (in_array($processo->status_atual, ['Análise de Viabilidade', 'Análise de viabilidade'])) {
              $canEditAba3 = true;
          }
      }
  @endphp
  <fieldset @if(!$canEditAba3) disabled @endif>

        <!-- ========================================================== -->

        <!-- Dados do Destinatário -->
        <h4 class="section-title">Análise do Destinatário</h4>
        <div class="form-group editavel">
          <label style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">CPF/CNPJ regular?
            <span style="display:inline-flex; flex-wrap:wrap; gap:8px;">
              <a href="https://servicos.receita.fazenda.gov.br/servicos/cpf/consultasituacao/consultapublica.asp" target="_blank" rel="noopener noreferrer" style="display:inline-block; padding:4px 10px; background-color:#0284c7; color:#fff; border-radius:6px; font-size:11px; text-decoration:none; font-weight:600;">Consultar CPF</a>
              <a href="https://solucoes.receita.fazenda.gov.br/Servicos/cnpjreva/" target="_blank" rel="noopener noreferrer" style="display:inline-block; padding:4px 10px; background-color:#0284c7; color:#fff; border-radius:6px; font-size:11px; text-decoration:none; font-weight:600;">Consultar CNPJ</a>
            </span>
          </label>
          <div class="radio-group">
            <label class="radio-option"
              ><input
                type="radio"
                name="cpf_cnpj_regular"
                value="Sim"
                required
              {{ isset($dados['cpf_cnpj_regular']) && $dados['cpf_cnpj_regular'] == 'Sim' ? 'checked' : '' }}>
              Sim</label
            >
            <label class="radio-option"
              ><input type="radio" name="cpf_cnpj_regular" value="Não" {{ isset($dados['cpf_cnpj_regular']) && $dados['cpf_cnpj_regular'] == 'Não' ? 'checked' : '' }}>
              Não</label
            >
          </div>
          <!-- Campo de Observações exibido se "Não" -->
          <div
            id="bloco-cpf-cnpj-irregular-obs"
            style="
              display: none;
              flex-direction: column;
              gap: 6px;
              margin-top: 8px;
            "
          >
            <label for="obs_cpf_cnpj_irregular"
              >Observações (CPF/CNPJ irregular):</label
            >
            <textarea
              id="obs_cpf_cnpj_irregular"
              name="obs_cpf_cnpj_irregular"
              placeholder="Descreva as irregularidades identificadas no CPF/CNPJ..."
            >{{ $dados['obs_cpf_cnpj_irregular'] ?? '' }}</textarea>
          </div>
        </div>
        <div class="form-group editavel">
          <label for="campo16">Natureza Jurídica do Destinatário:</label>
          <select id="campo16" name="campo16" data-selected="{{ $dados['campo16'] ?? '' }}">
            <option value="">Selecione a Natureza Jurídica...</option>
            <optgroup label="1. Administração Pública">
              <option value="101-5">
                101-5 - Órgão Público do Poder Executivo Federal
              </option>
              <option value="102-3">
                102-3 - Órgão Público do Poder Executivo Estadual ou do Distrito
                Federal
              </option>
              <option value="103-1">
                103-1 - Órgão Público do Poder Executivo Municipal
              </option>
              <option value="104-0">
                104-0 - Órgão Público do Poder Legislativo Federal
              </option>
              <option value="105-8">
                105-8 - Órgão Público do Poder Legislativo Estadual ou do
                Distrito Federal
              </option>
              <option value="106-6">
                106-6 - Órgão Público do Poder Legislativo Municipal
              </option>
              <option value="107-4">
                107-4 - Órgão Público do Poder Judiciário Federal
              </option>
              <option value="108-2">
                108-2 - Órgão Público do Poder Judiciário Estadual
              </option>
              <option value="110-4">110-4 - Autarquia Federal</option>
              <option value="111-2">
                111-2 - Autarquia Estadual ou do Distrito Federal
              </option>
              <option value="112-0">112-0 - Autarquia Municipal</option>
              <option value="113-9">
                113-9 - Fundação Pública de Direito Público Federal
              </option>
              <option value="114-7">
                114-7 - Fundação Pública de Direito Público Estadual ou do
                Distrito Federal
              </option>
              <option value="115-5">
                115-5 - Fundação Pública de Direito Público Municipal
              </option>
              <option value="116-3">
                116-3 - Órgão Público Autônomo Federal
              </option>
              <option value="117-1">
                117-1 - Órgão Público Autônomo Estadual ou do Distrito Federal
              </option>
              <option value="118-0">
                118-0 - Órgão Público Autônomo Municipal
              </option>
              <option value="119-8">119-8 - Commission Polinacional</option>
              <option value="121-0">
                121-0 - Consórcio Público de Direito Público (Associação
                Pública)
              </option>
              <option value="122-8">
                122-8 - Consórcio Público de Direito Privado
              </option>
              <option value="123-6">123-6 - Estado ou Distrito Federal</option>
              <option value="124-4">124-4 - Município</option>
              <option value="125-2">
                125-2 - Fundação Pública de Direito Privado Federal
              </option>
              <option value="126-0">
                126-0 - Fundação Pública de Direito Privado Estadual ou do
                Distrito Federal
              </option>
              <option value="127-9">
                127-9 - Fundação Pública de Direito Privado Municipal
              </option>
              <option value="128-7">
                128-7 - Fundo Público da Administração Indireta Federal
              </option>
              <option value="129-5">
                129-5 - Fundo Público da Administração Indireta Estadual ou do
                Distrito Federal
              </option>
              <option value="130-9">
                130-9 - Fundo Público da Administração Indireta Municipal
              </option>
              <option value="131-7">
                131-7 - Fundo Público da Administração Direta Federal
              </option>
              <option value="132-5">
                132-5 - Fundo Público da Administração Direta Estadual ou do
                Distrito Federal
              </option>
              <option value="133-3">
                133-3 - Fundo Público da Administração Direta Municipal
              </option>
              <option value="134-1">134-1 - União</option>
            </optgroup>
            <optgroup label="2. Entidades Empresariais">
              <option value="201-1">201-1 - Empresa Pública</option>
              <option value="203-8">203-8 - Sociedade de Economia Mista</option>
              <option value="204-6">204-6 - Sociedade Anônima Aberta</option>
              <option value="205-4">205-4 - Sociedade Anônima Fechada</option>
              <option value="206-2">
                206-2 - Sociedade Empresária Limitada
              </option>
              <option value="207-0">
                207-0 - Sociedade Empresária em Nome Coletivo
              </option>
              <option value="208-9">
                208-9 - Sociedade Empresária em Comandita Simples
              </option>
              <option value="209-7">
                209-7 - Sociedade Empresária em Comandita por Ações
              </option>
              <option value="212-7">
                212-7 - Sociedade em Conta de Participação
              </option>
              <option value="213-5">213-5 - Empresário (Individual)</option>
              <option value="214-3">214-3 - Cooperativa</option>
              <option value="215-1">215-1 - Consórcio de Sociedades</option>
              <option value="216-0">216-0 - Grupo de Sociedades</option>
              <option value="217-8">
                217-8 - Estabelecimento, no Brasil, de Sociedade Estrangeira
              </option>
              <option value="219-4">
                219-4 - Estabelecimento, no Brasil, de Empresa Binacional
                Argentino-Brasileira
              </option>
              <option value="221-6">
                221-6 - Empresa Domiciliada no Exterior
              </option>
              <option value="222-4">222-4 - Clube/Fundo de Investimento</option>
              <option value="223-2">223-2 - Sociedade Simples Pura</option>
              <option value="224-0">224-0 - Sociedade Simples Limitada</option>
              <option value="225-9">
                225-9 - Sociedade Simples em Nome Coletivo
              </option>
              <option value="226-7">
                226-7 - Sociedade Simples em Comandita Simples
              </option>
              <option value="227-5">227-5 - Empresa Binacional</option>
              <option value="228-3">228-3 - Consórcio de Empregadores</option>
              <option value="229-1">229-1 - Consórcio Simples</option>
              <option value="230-5">
                230-5 - Empresa Individual de Responsabilidade Limitada (de
                Natureza Empresária)
              </option>
              <option value="231-3">
                231-3 - Empresa Individual de Responsabilidade Limitada (de
                Natureza Simples)
              </option>
              <option value="232-1">
                232-1 - Sociedade Unipessoal de Advogados
              </option>
              <option value="233-0">233-0 - Cooperativas de Consumo</option>
              <option value="234-8">
                234-8 - Empresa Simples de Inovação - Inova Simples
              </option>
              <option value="235-6">235-6 - Investidor Não Residente</option>
            </optgroup>
            <optgroup label="3. Entidades sem Fins Lucrativos">
              <option value="303-4">
                303-4 - Serviço Notarial e Registral (Cartório)
              </option>
              <option value="306-9">306-9 - Fundação Privada</option>
              <option value="307-7">307-7 - Serviço Social Autônomo</option>
              <option value="308-5">308-5 - Condomínio Edilício</option>
              <option value="310-7">
                310-7 - Comissão de Conciliação Prévia
              </option>
              <option value="311-5">
                311-5 - Entidade de Mediação e Arbitragem
              </option>
              <option value="313-1">313-1 - Entidade Sindical</option>
              <option value="320-4">
                320-4 - Estabelecimento, no Brasil, de Fundação ou Associação
                Estrangeiras
              </option>
              <option value="321-2">
                321-2 - Fundação ou Associação Domiciliada no Exterior
              </option>
              <option value="322-0">322-0 - Organização Religiosa</option>
              <option value="323-9">323-9 - Comunidade Indígena</option>
              <option value="324-7">324-7 - Fundo Privado</option>
              <option value="325-5">
                325-5 - Órgão de Direção Nacional de Partido Político
              </option>
              <option value="326-3">
                326-3 - Órgão de Direção Regional de Partido Político
              </option>
              <option value="327-1">
                327-1 - Órgão de Direção Local de Partido Político
              </option>
              <option value="328-0">
                328-0 - Comitê Financeiro de Partido Político
              </option>
              <option value="329-8">
                329-8 - Frente Plebiscitária ou Referendária
              </option>
              <option value="330-1">330-1 - Organização Social (OS)</option>
              <option value="331-0">331-0 - Demais Condomínios</option>
              <option value="332-8">
                332-8 - Plano de Benefícios de Previdência Complementar Fechada
              </option>
              <option value="399-9">399-9 - Associação Privada</option>
            </optgroup>
            <optgroup label="4. Pessoas Físicas">
              <option value="401-4">
                401-4 - Empresa Individual Imobiliária
              </option>
              <option value="402-2">402-2 - Segurado Especial</option>
              <option value="408-1">408-1 - Contribuinte individual</option>
              <option value="409-0">
                409-0 - Candidato a Cargo Político Eletivo
              </option>
              <option value="411-1">411-1 - Leiloeiro</option>
              <option value="412-0">
                412-0 - Produtor Rural (Pessoa Física)
              </option>
            </optgroup>
            <optgroup label="5. Organizações Internacionais">
              <option value="501-0">501-0 - Organização Internacional</option>
              <option value="502-9">
                502-9 - Representação Diplomática Estrangeira
              </option>
              <option value="503-7">
                503-7 - Outras Instituições Extraterritoriais
              </option>
            </optgroup>
          </select>
        </div>

        <!-- Destinação Ativa -->
        <div class="form-group editavel">
          <label
            >Possui destinações ativas
            <span
              class="hint-badge"
              data-hint="Verifique se há contratos vigentes no SPUnet ou em sistemas legados."
              >?</span
            ></label
          >
          <div class="radio-group">
            <label class="radio-option"
              ><input
                type="radio"
                name="destinacao_ativa"
                value="Sim"
                onclick="
                  var b=document.getElementById('bloco-destinacao-ativa');
                  b.style.display='flex';
                  document.getElementById('contratos_destinacao_ativa').placeholder='Dados de destinações/contratos/instrumentos já formalizados pelo destinatário com a SPU (dados vindos do MGC/módulo de Destinação)';
                "
              {{ isset($dados['destinacao_ativa']) && $dados['destinacao_ativa'] == 'Sim' ? 'checked' : '' }}>
              Sim</label
            >
            <label class="radio-option"
              ><input
                type="radio"
                name="destinacao_ativa"
                value="Não"
                onclick="
                  document.getElementById(
                    'bloco-destinacao-ativa',
                  ).style.display = 'none'
                "
              {{ isset($dados['destinacao_ativa']) && $dados['destinacao_ativa'] == 'Não' ? 'checked' : '' }}>
              Não</label
            >
          </div>
          <div
            id="bloco-destinacao-ativa"
            style="
              display: none;
              margin-top: 10px;
              flex-direction: column;
              gap: 8px;
            "
          >
            <label for="contratos_destinacao_ativa"
              >Contratos/Instrumentos relacionados à destinação ativa:</label
            >
            <textarea
              id="contratos_destinacao_ativa"
              name="contratos_destinacao_ativa"
              rows="4"
              placeholder="Dados de destinações/contratos/instrumentos já formalizados pelo destinatário com a SPU (dados vindos do MGC/módulo de Destinação)"
            >{{ $dados['contratos_destinacao_ativa'] ?? '' }}</textarea>
          </div>
        </div>

        <!-- Pendências Contratuais -->
        <div class="form-group editavel">
          <label>Há pendências contratuais identificadas?</label>
          <div
            class="radio-group"
            style="display: flex; gap: 32px; margin-bottom: 10px"
          >
            <label class="radio-option"
              ><input
                type="radio"
                name="ha_pendencias_contratuais"
                value="Sim"
              {{ isset($dados['ha_pendencias_contratuais']) && $dados['ha_pendencias_contratuais'] == 'Sim' ? 'checked' : '' }}>
              Sim</label
            >
            <label class="radio-option"
              ><input
                type="radio"
                name="ha_pendencias_contratuais"
                value="Não"
              {{ isset($dados['ha_pendencias_contratuais']) && $dados['ha_pendencias_contratuais'] == 'Não' ? 'checked' : '' }}>
              Não</label
            >
          </div>

          <div
            id="bloco-pendencias-itens"
            style="display: none; margin-top: 10px"
          >
            <label
              >Selecione as pendências identificadas (Ativas ou não):</label
            >
            <div
              class="checkbox-group"
              style="
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-top: 8px;
              "
            >
              <label class="checkbox-option"
                ><input
                  type="checkbox"
                  name="pendencias[]"
                  value="Inadimplência financeira"
                {{ isset($dados['pendencias']) && in_array('Inadimplência financeira', (array)$dados['pendencias']) ? 'checked' : '' }}>
                Inadimplência financeira</label
              >
              <label class="checkbox-option"
                ><input
                  type="checkbox"
                  name="pendencias[]"
                  value="Pendências contratuais"
                {{ isset($dados['pendencias']) && in_array('Pendências contratuais', (array)$dados['pendencias']) ? 'checked' : '' }}>
                Pendências contratuais</label
              >
              <label class="checkbox-option"
                ><input
                  type="checkbox"
                  name="pendencias[]"
                  value="Irregularidade cadastral"
                {{ isset($dados['pendencias']) && in_array('Irregularidade cadastral', (array)$dados['pendencias']) ? 'checked' : '' }}>
                Irregularidade cadastral</label
              >
              <label class="checkbox-option"
                ><input
                  type="checkbox"
                  name="pendencias[]"
                  value="Uso em desacordo com o autorizado"
                {{ isset($dados['pendencias']) && in_array('Uso em desacordo com o autorizado', (array)$dados['pendencias']) ? 'checked' : '' }}>
                Uso em desacordo com o autorizado</label
              >
              <label class="checkbox-option"
                ><input type="checkbox" name="pendencias[]" value="Outras" {{ isset($dados['pendencias']) && in_array('Outras', (array)$dados['pendencias']) ? 'checked' : '' }}>
                Outras</label
              >
            </div>
          </div>

          <div
            id="bloco-obs-pendencias"
            style="
              display: none;
              margin-top: 10px;
              flex-direction: column;
              gap: 6px;
            "
          >
            <label for="obs_pendencias">Observações sobre pendências:</label>
            <textarea
              id="obs_pendencias"
              name="obs_pendencias"
              rows="3"
              placeholder="Descreva as pendências identificadas..."
            >{{ $dados['obs_pendencias'] ?? '' }}</textarea>
          </div>
        </div>

        <!-- Capacidade Financeira -->
        <h4 class="section-title">Capacidade Financeira</h4>
        <div class="form-group editavel">
          <label for="capacidade_fin"
            >Capacidade orçamentária/financeira demonstrada:</label
          >
          <select id="capacidade_fin" name="capacidade_fin" required data-selected="{{ $dados['capacidade_fin'] ?? '' }}">
            <option value="">Selecione...</option>
            <option value="Demonstrada formalmente">Demonstrada formalmente</option>
            <option value="Não demonstrada">Não demonstrada</option>
            <option value="Não se aplica">Não se aplica</option>
          </select>
        </div>

        <!-- Ações judiciais ou órgãos de controle -->
        <h4 class="section-title">Ações judiciais ou órgãos de controle</h4>
        <div class="form-group editavel">
          <label>NUP SEI:</label>
          <input
            type="text"
            id="nup_sei"
            name="nup_sei"
            readonly
            style="background: #f1f5f9; color: #475569"
          / value="{{ $dados['nup_sei'] ?? '' }}">
        </div>
        <div class="form-group editavel">
          <label>Tipo de Processo:</label>
          <input
            type="text"
            id="tipo_processo"
            name="tipo_processo"
            readonly
            style="background: #f1f5f9; color: #475569"
          / value="{{ $dados['tipo_processo'] ?? '' }}">
        </div>
        <div class="form-group editavel">
          <label>Resumo:</label>
          <input
            type="text"
            id="resumo_acao"
            name="resumo_acao"
            readonly
            style="background: #f1f5f9; color: #475569"
          / value="{{ $dados['resumo_acao'] ?? '' }}">
        </div>
        <div class="form-group editavel">
          <label>Descrição:</label>
          <textarea
            id="descricao_acao"
            name="descricao_acao"
            rows="4"
            readonly
            style="background: #f1f5f9; color: #475569; resize: none"
          >{{ $dados['descricao_acao'] ?? '' }}</textarea>
        </div>

        <!-- Dados de Área e Valor do Imóvel a ser Destinado -->
        <h4 class="section-title">Dados de Área e Valor do Imóvel a ser Destinado:</h4>
        <div
          class="form-group editavel"
          style="
            padding: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
          "
        >
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px">
            <div class="form-group">
              <label for="area_total_imovel">Área total do imóvel a ser destinada (m²):</label>
              <input
                type="text"
                id="area_total_imovel"
                name="area_total_imovel"
                placeholder="Ex: 5.000,00"
                autocomplete="off"
                readonly
              / value="{{ $dados['area_total_imovel'] ?? '' }}">
            </div>
            <div class="form-group">
              <label for="valor_total_imovel"
                >Valor total da área a ser destinada (R$):</label
              >
              <input
                type="text"
                id="valor_total_imovel"
                name="valor_total_imovel"
                placeholder="Ex: 1.500.000,00"
                autocomplete="off"
                readonly
              / value="{{ $dados['valor_total_imovel'] ?? '' }}">
            </div>
          </div>
          <div
            style="
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 15px;
              margin-top: 10px;
              border-top: 1px dashed #cbd5e1;
              padding-top: 10px;
              display: none;
            "
          >
          </div>
          <div class="form-group" style="margin-top: 15px">
            <label>Há necessidade de mudanças de área ou valor?</label>
            <div class="radio-group">
              <label class="radio-option">
                <input type="radio" name="necessidade_mudanca" value="Sim" onclick="document.getElementById('bloco-justificativa-mudanca').style.display='block'"> Sim
              </label>
              <label class="radio-option">
                <input type="radio" name="necessidade_mudanca" value="Não" onclick="document.getElementById('bloco-justificativa-mudanca').style.display='none'"> Não
              </label>
            </div>
            <div id="bloco-justificativa-mudanca" style="display: none; margin-top: 8px;">
              <label for="justificativa_mudanca">Justificativa da mudança:</label>
              <textarea id="justificativa_mudanca" name="justificativa_mudanca" rows="3" placeholder="Justifique o pedido de mudança..."></textarea>
            </div>
          </div>
        </div>

        <!-- Custos de Manutenção para a SPU -->
        <h4 class="section-title">Custos de Manutenção para a SPU</h4>
        <div class="form-group editavel">
          <label>Imóvel gera custos de manutenção para a SPU?</label>
          <div class="radio-group">
            <label class="radio-option"
              ><input
                type="radio"
                name="custos_manutencao"
                value="Sim"
                onclick="
                  document.getElementById(
                    'bloco-custos-manutencao',
                  ).style.display = 'block'
                "
              {{ isset($dados['custos_manutencao']) && $dados['custos_manutencao'] == 'Sim' ? 'checked' : '' }}>
              Sim</label
            >
            <label class="radio-option"
              ><input
                type="radio"
                name="custos_manutencao"
                value="Não"
               
                onclick="
                  document.getElementById(
                    'bloco-custos-manutencao',
                  ).style.display = 'none'
                "
              {{ isset($dados['custos_manutencao']) && $dados['custos_manutencao'] == 'Não' ? 'checked' : '' }}>
              Não</label
            >
          </div>
          <div
            id="bloco-custos-manutencao"
            style="display: none; margin-top: 10px"
          >
            <label for="custos_valor">Valor Anual:</label>
            <input
              type="text"
              id="custos_valor"
              name="custos_valor"
              class="mask-moeda"
              placeholder="R$ 0,00"
            / value="{{ $dados['custos_valor'] ?? '' }}">
          </div>
        </div>

        <!-- Outros Interessados -->
        <h4 class="section-title">Outros Interessados</h4>
        <div class="form-group editavel">
          <label>Há outros interessados no imóvel?</label>
          <div class="radio-group">
            <label class="radio-option"
              ><input
                type="radio"
                name="outros_interessados"
                value="Sim"
                onclick="
                  document.getElementById(
                    'bloco-outros-interessados',
                  ).style.display = 'block'
                "
              {{ isset($dados['outros_interessados']) && $dados['outros_interessados'] == 'Sim' ? 'checked' : '' }}>
              Sim</label
            >
            <label class="radio-option"
              ><input
                type="radio"
                name="outros_interessados"
                value="Não"
               
                onclick="
                  document.getElementById(
                    'bloco-outros-interessados',
                  ).style.display = 'none'
                "
              {{ isset($dados['outros_interessados']) && $dados['outros_interessados'] == 'Não' ? 'checked' : '' }}>
              Não</label
            >
          </div>
          <div
            id="bloco-outros-interessados"
            style="display: none; margin-top: 10px"
          >
            <label for="obs_outros_interessados"
              >Observações sobre outros interessados:</label
            >
            <textarea
              id="obs_outros_interessados"
              name="obs_outros_interessados"
              rows="3"
              placeholder="Descreva os outros interessados identificados..."
            >{{ $dados['obs_outros_interessados'] ?? '' }}</textarea>
          </div>
        </div>

        <!-- Proposta de Destinação e Impactos -->
        <h4 class="section-title">Proposta de Destinação e Impactos</h4>
        <div class="form-group editavel">
          <label for="campo51"
            >Tipo de procedimento pretendido:
            <span class="hint-semaforo"
              ><span
                class="hint-icon"
                data-hint-tipo="verde"
                data-hint="Selecione o tipo de procedimento administrativo que origina esta análise de destinação."
                role="tooltip"
                tabindex="0"
                aria-label="Ajuda: Tipo de procedimento"
                >?</span
              ></span
            >
          </label>
          <select id="campo51" name="tipo_procedimento" required data-selected="{{ $dados['tipo_procedimento'] ?? '' }}">
            <option value="">Selecione...</option>
            <option value="Nova destinação">Nova destinação</option>
            <option value="Regularização de uso">Regularização de uso</option>
          </select>
          <span class="error-msg" id="err51" style="display: none"
            >Selecione o tipo de procedimento.</span
          >

          <!-- SEÇÃO CONDICIONAL: Contratos Anteriores (Dinamico) -->
          <div
            id="bloco_contratos_anteriores"
            style="
              display: none;
              margin-top: 15px;
              padding-top: 15px;
              border-top: 1px dashed #cbd5e1;
            "
          >
            <h3
              style="
                text-align: left;
                margin-bottom: 12px;
                color: #1e3a5f;
                font-size: 0.95rem;
                font-weight: bold;
              "
            >
              Contratos anteriores
            </h3>
            <div
              id="contratos_anteriores_container"
              style="display: flex; flex-wrap: wrap; gap: 10px"
            >
              <!-- Carregado dinamicamente -->
            </div>
          </div>

          <div id="bloco51_obs" style="display: none; margin-top: 8px">
            <label for="campo51_obs">Informações complementares:</label>
            <textarea
              id="campo51_obs"
              name="campo51_obs"
              placeholder="Informações complementares ao tipo de procedimento..."
            >{{ $dados['campo51_obs'] ?? '' }}</textarea>
          </div>
        </div>

        <div class="form-group editavel">
          <label for="campo52"
            >Tipo de uso imobiliário pretendido:
            <span class="hint-semaforo"
              ><span
                class="hint-icon"
                data-hint-tipo="verde"
                data-hint="Selecione o tipo de uso imobiliário pretendido conforme Tabela Taxonmica RESOLUO COMGC/SPU/MGI N 3/2025."
                role="tooltip"
                tabindex="0"
                aria-label="Ajuda: Tipo de uso imobiliário pretendido"
                >?</span
              ></span
            >
          </label>
          <select
            id="campo52"
            name="tipo_uso_imobiliario"
            required
            data-no-custom
           data-selected="{{ $dados['tipo_uso_imobiliario'] ?? '' }}">
            <option value="">Selecione...</option>
            <option value="0101">
              01.01 Uso administrativo e representativo
            </option>
            <option value="0102">
              01.02 Uso para agropecuária, aquicultura, produção florestal e
              pesca
            </option>
            <option value="0103">
              01.03 Uso ambiental e dos recursos naturais
            </option>
            <option value="0104">
              01.04 Uso cultural, esportivo e de lazer
            </option>
            <option value="0105">01.05 Uso em eventos de curta duração</option>
            <option value="0106">01.06 Uso habitacional</option>
            <option value="0107">
              01.07 Uso industrial, comercial ou de prestação de serviços
            </option>
            <option value="0108">
              01.08 Uso em infraestrutura de serviços públicos
            </option>
            <option value="0109">
              01.09 Uso em infraestrutura de transportes
            </option>
            <option value="0110">
              01.10 Uso multifinalitário em projeto de requalificação urbana
            </option>
            <option value="0111">
              01.11 Uso por povos originários e comunidades tradicionais
            </option>
            <option value="0112">
              01.12 Uso em servio de ensino, pesquisa e extensão
            </option>
            <option value="0113">
              01.13 Uso em servio socioassistêncial e de cidadania
            </option>
            <option value="0114">01.14 Uso em serviços de saúde</option>
            <option value="0115">01.15 Uso residencial para servidor</option>
            <option value="0116">
              01.16 Uso para segurança pública e defesa nacional
            </option>
            <option value="0117">01.17 Uso religioso</option>
            <option value="0118">01.18 Sem informação</option>
            <option value="0119">01.19 Sem uso definido/vinculação</option>
          </select>
          <span class="error-msg" id="err52" style="display: none"
            >Selecione o tipo de uso imobiliário.</span
          >
        </div>

        <div class="form-group editavel">
          <label for="campo53"
            >Tipo de uso específico pretendido:
            <span class="hint-semaforo"
              ><span
                class="hint-icon"
                data-hint-tipo="verde"
                data-hint="Selecione o tipo de uso específico pretendido conforme Tabela Taxonmica RESOLUO COMGC/SPU/MGI N 3/2025."
                role="tooltip"
                tabindex="0"
                aria-label="Ajuda: Tipo de uso específico pretendido"
                >?</span
              ></span
            >
          </label>
          <select
            id="campo53"
            name="tipo_uso_especifico"
            required
            data-no-custom
           data-selected="{{ $dados['tipo_uso_especifico'] ?? '' }}">
            <option value="">
              Selecione primeiro o tipo de uso imobiliário...
            </option>
          </select>
          <span class="error-msg" id="err53" style="display: none"
            >Selecione o tipo de uso específico.</span
          >
        </div>

        <div class="form-group editavel">
          <label
            >Há previsão de modificação do terreno, aterro ou retirada de
            material, que acresça ou diminua a área?
            <span class="hint-semaforo"
              ><span
                class="hint-icon"
                data-hint-tipo="verde"
                data-hint="Indique se a destinação proposta implica modificações físicas no terreno ou na edificação."
                role="tooltip"
                tabindex="0"
                aria-label="Ajuda: Modificação do terreno/edificação"
                >?</span
              ></span
            >
          </label>
          <div class="radio-group" id="group-campo54">
            <label class="radio-option"
              ><input
                type="radio"
                name="campo54"
                value="Sim"
                onclick="
                  document.getElementById('bloco54').style.display = 'block'
                "
              {{ isset($dados['campo54']) && $dados['campo54'] == 'Sim' ? 'checked' : '' }}>
              Sim</label
            >
            <label class="radio-option"
              ><input
                type="radio"
                name="campo54"
                value="Não"
                onclick="
                  document.getElementById('bloco54').style.display = 'none'
                "
              {{ isset($dados['campo54']) && $dados['campo54'] == 'Não' ? 'checked' : '' }}>
              Não</label
            >
            <label class="radio-option"
              ><input
                type="radio"
                name="campo54"
                value="Sem informação"
                onclick="
                  document.getElementById('bloco54').style.display = 'none'
                "
              {{ isset($dados['campo54']) && $dados['campo54'] == 'Sem informação' ? 'checked' : '' }}>
              Sem informação</label
            >
          </div>
          <span class="error-msg" id="err54" style="display: none"
            >Selecione uma opção.</span
          >
          <div id="bloco54" style="display: none; margin-top: 8px">
            <label for="campo54_desc"
              >Descrição da modificação prevista:
              <span class="hint-semaforo"
                ><span
                  class="hint-icon"
                  data-hint-tipo="verde"
                  data-hint="Descreva as modificações físicas previstas no terreno ou na edificação."
                  role="tooltip"
                  tabindex="0"
                  aria-label="Ajuda: Descrição da modificação"
                  >?</span
                ></span
              >
            </label>
            <textarea
              id="campo54_desc"
              name="campo54_desc"
              placeholder="Descreva as modificações previstas no terreno ou na edificação..."
            >{{ $dados['campo54_desc'] ?? '' }}</textarea>
          </div>
        </div>

        <div class="form-group editavel">
          <label for="campo55"
            >Compatibilidade urbanística:
            <span class="hint-semaforo"
              ><span
                class="hint-icon"
                data-hint-tipo="verde"
                data-hint="Avalie a compatibilidade do uso proposto com as normas urbanísticas municipais vigentes (Plano Diretor, zoneamento, etc.)."
                role="tooltip"
                tabindex="0"
                aria-label="Ajuda: Compatibilidade urbanística"
                >?</span
              ></span
            >
          </label>
          <select id="campo55" name="compatibilidade_urbanistica" required data-selected="{{ $dados['compatibilidade_urbanistica'] ?? '' }}">
            <option value="" selected>Selecione...</option>
            <option value="Compatível">
              Compatível: uso proposto está em conformidade com o zoneamento e
              normas urbanísticas vigentes
            </option>
            <option value="Compatível com restrições">
              Compatível com restrições: uso admitido com condicionantes ou
              mediante aprovação específica
            </option>
            <option value="Incompatível">
              Incompatível: uso contrário às normas urbanísticas vigentes
            </option>
            <option value="Não foi possível verificar">
              Não foi possível verificar: ausência de informações ou acesso às
              normas municipais
            </option>
          </select>
          <span class="error-msg" id="err55" style="display: none"
            >Selecione a compatibilidade urbanística.</span
          >
          <div id="bloco55_obs" style="display: none; margin-top: 8px">
            <label for="campo55_obs">Observações complementares:</label>
            <textarea
              id="campo55_obs"
              name="campo55_obs"
              placeholder="Observações sobre a compatibilidade urbanística..."
            >{{ $dados['campo55_obs'] ?? '' }}</textarea>
          </div>
        </div>


        <div class="form-group editavel">
          <label>Há vinculação com o programa 'Imóvel da Gente'?</label>
          <div class="radio-group" id="group-imovel-gente-radio">
            <label class="radio-option"><input type="radio" name="vinculo_imovel_gente" value="Sim" onclick="document.getElementById('bloco-linha-programa').style.display='flex'"> Sim</label>
            <label class="radio-option"><input type="radio" name="vinculo_imovel_gente" value="Não" onclick="document.getElementById('bloco-linha-programa').style.display='none'" checked> Não</label>
          </div>
          <div id="bloco-linha-programa" style="display: none; margin-top: 8px; flex-direction: column; gap: 6px;">
            <label for="linha_programa">Linha do Programa:</label>
            <select id="linha_programa" name="linha_programa">
              <option value="">Selecione a linha...</option>
              <option value="LINHA 1 - HABITAÇÃO">LINHA 1 - HABITAÇÃO</option>
              <option value="LINHA 2 – REURB">LINHA 2 – REURB</option>
              <option value="LINHA 3 - POLÍT. PÚBLICAS">LINHA 3 - POLÍT. PÚBLICAS</option>
              <option value="LINHA 4 - MÚLTIPLOS USOS">LINHA 4 - MÚLTIPLOS USOS</option>
            </select>
          </div>
        </div>

        <div class="form-group editavel">
          <label>Os beneficiários são famílias ou indivíduos?</label>
          <div class="radio-group">
            <label class="radio-option"><input type="radio" name="tipo_beneficiario" value="Famílias"> Famílias</label>
            <label class="radio-option"><input type="radio" name="tipo_beneficiario" value="Indivíduos"> Indivíduos</label>
            <label class="radio-option"><input type="radio" name="tipo_beneficiario" value="Não há informação"> Não há informação</label>
          </div>
        </div>

        <div class="form-group editavel">
          <label for="campo59"
            >Número estimado de beneficiários:
            <span class="hint-semaforo"
              ><span
                class="hint-icon"
                data-hint-tipo="verde"
                data-hint="Informe o nmero estimado de pessoas ou famlias beneficiadas diretamente pela destinação proposta."
                role="tooltip"
                tabindex="0"
                aria-label="Ajuda: Nmero estimado de beneficirios"
                >?</span
              ></span
            >
          </label>
          <input
            type="number"
            id="campo59"
            name="num_beneficiarios"
            placeholder="0"
            min="0"
            autocomplete="off"
            value="{{ $dados['num_beneficiarios'] ?? '' }}"
          />
          <span class="error-msg" id="err59" style="display: none"
            >Informe o nmero de beneficirios (mnimo 0).</span
          >
        </div>

        <div class="form-group editavel">
          <label for="campo511">
            Regime de destinação proposto:
            <span class="hint-semaforo">
              <span
                class="hint-icon"
                data-hint-tipo="verde"
                data-hint="Selecione o regime de destinação proposto conforme Tabela RESOLUO COMGC/SPU/MGI N 3/2025."
                role="tooltip"
                tabindex="0"
                aria-label="Ajuda: Regime de destinação proposto"
                >?</span
              >
            </span>
          </label>

          <select
            id="campo511"
            name="regime_destinacao"
            required
            data-no-custom
           data-selected="{{ $dados['regime_destinacao'] ?? '' }}">
            <option value="">Selecione um regime...</option>
            <option value="Acordo de Cooperação Técnica para Regularização Fundiária Urbana (ACT-Reurb)">Acordo de Cooperação Técnica para Regularização Fundiária Urbana (ACT-Reurb)</option>
            <option value="Aforamento gratuito">Aforamento gratuito</option>
            <option value="Aforamento oneroso">Aforamento oneroso</option>
            <option value="Arrendamento">Arrendamento</option>
            <option value="Autorização de obras">Autorização de obras</option>
            <option value="Autorização de passagem gratuita">Autorização de passagem gratuita</option>
            <option value="Autorização de passagem onerosa">Autorização de passagem onerosa</option>
            <option value="Autorização de uso para fins comerciais">Autorização de uso para fins comerciais</option>
            <option value="Autorização de uso sustentável">Autorização de uso sustentável</option>
            <option value="Cessão de uso em condições especiais">Cessão de uso em condições especiais</option>
            <option value="Cessão de uso gratuita">Cessão de uso gratuita</option>
            <option value="Cessão de uso onerosa">Cessão de uso onerosa</option>
            <option value="Cessão de uso provisória">Cessão de uso provisória</option>
            <option value="Concessão de Direito de Superfície Gratuita">Concessão de Direito de Superfície Gratuita</option>
            <option value="Concessão de Direito de Superfície Onerosa">Concessão de Direito de Superfície Onerosa</option>
            <option value="Concessão de Direito Real de Laje Gratuita">Concessão de Direito Real de Laje Gratuita</option>
            <option value="Concessão de Direito Real de Laje Onerosa">Concessão de Direito Real de Laje Onerosa</option>
            <option value="Concessão de Direito Real de Uso Gratuita">Concessão de Direito Real de Uso Gratuita</option>
            <option value="Concessão de Direito Real de Uso Onerosa">Concessão de Direito Real de Uso Onerosa</option>
            <option value="Concessão de uso especial para fins de moradia (CUEM)">Concessão de uso especial para fins de moradia (CUEM)</option>
            <option value="Dação em pagamento">Dação em pagamento</option>
            <option value="Declaração de Interesse do Serviço Publico">Declaração de Interesse do Serviço Publico</option>
            <option value="Doação">Doação</option>
            <option value="Entrega">Entrega</option>
            <option value="Entrega provisória">Entrega provisória</option>
            <option value="Guarda Provisória">Guarda Provisória</option>
            <option value="Inscrição de ocupação">Inscrição de ocupação</option>
            <option value="Integralização de cotas em Fundo de Investimento Imobiliário">Integralização de cotas em Fundo de Investimento Imobiliário</option>
            <option value="Investidura">Investidura</option>
            <option value="Locação para terceiros">Locação para terceiros</option>
            <option value="Permissão de uso para eventos de curta duração">Permissão de uso para eventos de curta duração</option>
            <option value="Permissão de uso para fins residenciais">Permissão de uso para fins residenciais</option>
            <option value="Permuta">Permuta</option>
            <option value="Promessa de compra e venda">Promessa de compra e venda</option>
            <option value="Remição do foro">Remição do foro</option>
            <option value="Transferência de gestão de orlas e praias">Transferência de gestão de orlas e praias</option>
            <option value="Transferência de direito real de uso para Reurb-S">Transferência de direito real de uso para Reurb-S</option>
            <option value="Transferência de propriedade para fins de Reurb-S">Transferência de propriedade para fins de Reurb-S</option>
            <option value="Transferência gratuita da posse">Transferência gratuita da posse</option>
            <option value="Transferência onerosa da posse">Transferência onerosa da posse</option>
            <option value="Venda">Venda</option>
          </select>

          <span class="error-msg" id="err511" style="display: none">
            Selecione o regime de destinação.
          </span>

          <div id="bloco511_obs" style="display: none; margin-top: 8px">
            <label for="campo511_obs">Observações complementares:</label>
            <textarea
              id="campo511_obs"
              name="campo511_obs"
              placeholder="Observações sobre o regime de destinação proposto..."
            >{{ $dados['campo511_obs'] ?? '' }}</textarea>
          </div>
        </div>

        @if($processo->tramitacao !== 'Devolvido')
        <!-- ========== ACCORDION DEVOLUÇÃO ========== -->
        <style>
          .accordion-container-dev { display: flex; flex-direction: column; gap: 15px; margin-bottom: 25px; }
          .accordion-item-dev { border: 1px solid #fda4af; border-radius: 8px; overflow: hidden; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
          .accordion-header-dev { padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 1.1em; background: #fff1f2; color: #be123c; border-left: 5px solid #e11d48; transition: filter 0.2s; }
          .accordion-header-dev:hover { filter: brightness(0.95); }
          .accordion-body-dev { display: none; padding: 20px; border-top: 1px solid #fda4af; }
          .accordion-body-dev.active-dev { display: block; background: #fff1f2; }
          .accordion-icon-dev { font-size: 1.2em; color: #be123c !important; transition: transform 0.3s; }
          .active-dev .accordion-icon-dev { transform: rotate(90deg); }
        </style>
        <div class="accordion-container-dev">
          <div class="accordion-item-dev">
            <div class="accordion-header-dev" onclick="this.nextElementSibling.classList.toggle('active-dev'); this.classList.toggle('active-dev');">
              <span>⚠️ Devolver Processo</span>
              <span class="accordion-icon-dev">▶</span>

            </div>
            <div class="accordion-body-dev editavel">
              <label for="motivo_devolucao_rapida" style="color: #9f1239; font-weight: bold; font-size: 0.9em; display: block; margin-bottom: 5px;">Motivo (Obrigatório):</label>
              <textarea id="motivo_devolucao_rapida" name="motivo_devolucao" placeholder="Justifique a devolução..." style="width: 100%; min-height: 80px; padding: 8px; border: 1px solid #fecdd3; border-radius: 4px; margin-bottom: 15px; font-family: inherit; box-sizing: border-box;"></textarea>

              <p style="margin-top: 0; margin-bottom: 10px; color: #9f1239; font-weight: bold;">Para qual fase o processo deve retornar?</p>
              <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <button type="button" 
                        onclick="enviarDevolucao(1)"
                        class="btnEnviarDevolucaoRapida" 
                        style="flex: 1; min-width: 200px; background-color: #be123c; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s; box-shadow: 0 2px 4px rgba(190, 18, 60, 0.2);">
                    🔙 Indicação do Imóvel
                </button>
                <button type="button" 
                        onclick="enviarDevolucao(2)"
                        class="btnEnviarDevolucaoRapida" 
                        style="flex: 1; min-width: 200px; background-color: #9f1239; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s; box-shadow: 0 2px 4px rgba(159, 18, 57, 0.2);">
                    🔙 Diagnóstico do Imóvel
                </button>
              </div>
            </div>
            
            <script>
            function enviarDevolucao(aba) {
                const motivo = document.getElementById('motivo_devolucao_rapida').value;
                if(!motivo.trim()) {
                    alert("O motivo da devolução é obrigatório. Por favor, justifique antes de enviar.");
                    return;
                }
                
                const botoes = document.querySelectorAll('.btnEnviarDevolucaoRapida');
                botoes.forEach(b => { b.disabled = true; b.style.opacity = '0.7'; b.innerHTML = '⏳ Devolvendo...'; });

                fetch("{{ route('processos.devolver', $processo->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "HX-Request": "true"
                    },
                    body: JSON.stringify({
                        aba: aba,
                        motivo_devolucao: motivo
                    })
                }).then(res => {
                    if(res.ok && res.headers.has('HX-Redirect')) {
                        window.location.href = res.headers.get('HX-Redirect');
                    } else if(res.redirected) {
                        window.location.href = res.url;
                    } else {
                        window.location.href = "{{ route('processos.index') }}";
                    }
                }).catch(err => {
                    console.error("Erro na devolução:", err);
                    alert("Ocorreu um erro ao devolver o processo.");
                    botoes.forEach(b => { b.disabled = false; b.style.opacity = '1'; });
                });
            }
            </script>
          </div>
        </div>
        @endif

        <!-- btnConfirmarAprovacao removido pois submissão é via Salvar e Enviar -->

        <!-- Botão Salvar e Enviar -->
        <!-- Bloco Resposta à Devolutiva -->
        @if($processo->tramitacao === 'Devolvido')
        <div id="blocoRespostaDevolutivaAba3" class="editavel" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin-top: 30px; margin-bottom: 10px;">
            <h4 style="color: #16a34a; margin-top: 0; border-bottom: 1px solid #86efac; padding-bottom: 8px;">Resposta à Devolutiva</h4>
            <label for="resposta_devolucao" style="font-weight: bold; color: #166534;">Descreva o que foi corrigido ou complementado nesta etapa (Obrigatório para enviar):</label>
            <textarea id="resposta_devolucao" name="resposta_devolucao" required style="width: 100%; min-height: 100px; padding: 10px; border: 1px solid #86efac; border-radius: 4px; margin-top: 10px; font-family: inherit; font-size: 14px;">{{ $dados['resposta_devolucao'] ?? '' }}</textarea>
        </div>
        @endif

        <script>
        window._saveDraft = async function() {
            const form = document.getElementById('form03');
            if (!form) return;
            
            const btn = document.querySelector('button[onclick*="_saveDraft()"]');
            const origText = btn ? btn.innerHTML : "Salvar Rascunho";
            if(btn) btn.innerHTML = "⏳ Salvando...";
            
            const formData = new FormData(form);
            const dataObj = {};
            for (let [key, value] of formData.entries()) {
                if (key === '_token' || key === 'aba_atual' || key === 'next_aba') continue;
                
                // Remove o sufixo [] para bater com a estrutura esperada no backend/Blade
                const cleanKey = key.endsWith('[]') ? key.slice(0, -2) : key;
                
                if (dataObj[cleanKey] !== undefined) {
                    if (!Array.isArray(dataObj[cleanKey])) dataObj[cleanKey] = [dataObj[cleanKey]];
                    dataObj[cleanKey].push(value);
                } else {
                    dataObj[cleanKey] = value;
                }
            }
            
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
            const processId = "{{ $processo->id }}";
            
            try {
                const res = await fetch('/draft/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        processo_id: processId,
                        aba: "3",
                        data: dataObj
                    })
                });
                
                if (res.ok) {
                    alert("Rascunho da Aba 3 salvo com sucesso!");
                } else {
                    alert("Erro ao salvar rascunho. Tente novamente.");
                }
            } catch(e) {
                console.error("Erro no rascunho:", e);
                alert("Erro de conexão ao salvar rascunho.");
            }
            
            if(btn) btn.innerHTML = origText;
        }
        </script>
        <div style="display: flex; flex-direction: row; justify-content: center; gap: 15px; width: 100%; max-width: 50%; margin: 30px auto 30px auto; border-top: 1px dashed #ccc; padding-top: 30px;">
            <button type="button" class="btn-action" style="width: 48%; font-size: 1.2em; padding: 16px; background-color: #64748b; border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease;" onclick="if(typeof window._saveDraft === 'function') window._saveDraft();">💾 Salvar Rascunho</button>
            <button type="submit" class="btn-action" style="width: 48%; font-size: 1.2em; padding: 16px; background-color: #0284c7; border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease;">💾 Salvar e Enviar</button>
        </div>

      </fieldset>
    </form>
</div>
    <script src="{{ asset('js/fetch_acoes.js') }}"></script>
    <script src="{{ asset('js/fetch_spu.js') }}"></script>
    <script src="{{ asset('js/formulario.js') }}"></script>
    <script src="{{ asset('js/hints.js') }}"></script>
    <script src="{{ asset('js/custom-select.js') }}"></script>
    <script>
      // Controle condicional das pendências contratuais
      function checkPendenciasContratuais() {
        const checkedRadio = document.querySelector(
          'input[name="ha_pendencias_contratuais"]:checked',
        );
        const blocoItens = document.getElementById("bloco-pendencias-itens");
        const blocoObs = document.getElementById("bloco-obs-pendencias");

        if (!checkedRadio) return;

        if (checkedRadio.value === "Sim") {
          if (blocoItens) blocoItens.style.display = "block";
          const temSelecionado = Array.from(
            document.querySelectorAll('input[name="pendencias[]"]'),
          ).some((cb) => cb.checked);
          if (blocoObs)
            blocoObs.style.display = temSelecionado ? "flex" : "none";
        } else {
          if (blocoItens) blocoItens.style.display = "none";
          if (blocoObs) blocoObs.style.display = "none";
          document
            .querySelectorAll('input[name="pendencias[]"]')
            .forEach((cb) => (cb.checked = false));
          const txt = document.getElementById("obs_pendencias");
          if (txt) txt.value = "";
        }
      }

      document.addEventListener("DOMContentLoaded", () => {
        document
          .querySelectorAll('input[name="ha_pendencias_contratuais"]')
          .forEach((radio) => {
            radio.addEventListener("change", checkPendenciasContratuais);
          });
        document
          .querySelectorAll('input[name="pendencias[]"]')
          .forEach((cb) => {
            cb.addEventListener("change", checkPendenciasContratuais);
          });
        setTimeout(checkPendenciasContratuais, 500);
      });

      document
        .getElementById("form03")
        .addEventListener("submit", function (e) {
          const form = e.target;
          if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
          }
        });
    </script>
    <script src="{{ asset('js/sync.js') }}"></script>
    <script>
      function toggleBloco(id, mostrar) {
        const el = document.getElementById(id);
        if (el) el.style.display = mostrar ? "flex" : "none";
      }

      function limparErro(el, idErr) {
        const err = document.getElementById(idErr);
        if (err) err.style.display = "none";
        if (el) el.style.borderColor = "";
      }

      const usoEspecificoMap = {
        "0101": [
          [
            "010101",
            "01.01.01  Sede ou escritório de órgão ou entidade pública federal",
          ],
          [
            "010102",
            "01.01.02  Sede ou escritório de órgão ou entidade pública estadual ou distrital",
          ],
          [
            "010103",
            "01.01.03  Sede ou escritório de órgão ou entidade pública municipal",
          ],
          ["010104", "01.01.04  Representação diplomática ou consular"],
          ["010105", "01.01.05  Sede de organismo internacional"],
          ["010199", "01.01.99  Outro uso administrativo e representativo"],
        ],
        "0102": [
          ["010201", "01.02.01  Agricultura"],
          ["010202", "01.02.02  Pecuária"],
          ["010203", "01.02.03  Aquicultura"],
          ["010204", "01.02.04  Produção florestal"],
          ["010205", "01.02.05  Pesca"],
          [
            "010299",
            "01.02.99  Outro uso agropecuário, aquicultura, florestal ou pesqueiro",
          ],
        ],
        "0103": [
          ["010301", "01.03.01  Unidade de conservação"],
          ["010302", "01.03.02  área de preservação permanente"],
          ["010303", "01.03.03  Reserva legal"],
          [
            "010304",
            "01.03.04  área de reflorestamento ou recuperação ambiental",
          ],
          ["010305", "01.03.05  área de manejo de recursos naturais"],
          ["010399", "01.03.99  Outro uso ambiental e de recursos naturais"],
        ],
        "0104": [
          [
            "010401",
            "01.04.01  Equipamento cultural (museu, teatro, biblioteca, etc.)",
          ],
          [
            "010402",
            "01.04.02  Equipamento esportivo (estádio, ginásio, quadra, etc.)",
          ],
          ["010403", "01.04.03  área de lazer e recreação pública"],
          ["010404", "01.04.04  Parque urbano ou área verde pública"],
          ["010499", "01.04.99  Outro uso cultural, esportivo ou de lazer"],
        ],
        "0105": [
          ["010501", "01.05.01  Evento cultural"],
          ["010502", "01.05.02  Evento esportivo"],
          ["010503", "01.05.03  Evento comercial ou feira"],
          ["010504", "01.05.04  Evento institucional ou cívico"],
          ["010599", "01.05.99  Outro uso em evento de curta duração"],
        ],
        "0106": [
          ["010601", "01.06.01  Habitação de interesse social (HIS)"],
          ["010602", "01.06.02  Habitação de mercado popular"],
          [
            "010603",
            "01.06.03  Habitação para segmentos específicos (idosos, pessoas com deficiência, etc.)",
          ],
          [
            "010604",
            "01.06.04  Habitação em assentamento informal consolidado",
          ],
          ["010699", "01.06.99  Outro uso habitacional"],
        ],
        "0107": [
          ["010701", "01.07.01  Uso industrial"],
          ["010702", "01.07.02  Uso comercial varejista"],
          ["010703", "01.07.03  Uso comercial atacadista"],
          ["010704", "01.07.04  Prestação de serviços"],
          ["010705", "01.07.05  Uso misto (comercial/serviços/residencial)"],
          [
            "010799",
            "01.07.99  Outro uso industrial, comercial ou de serviços",
          ],
        ],
        "0108": [
          ["010801", "01.08.01  Abastecimento de água"],
          ["010802", "01.08.02  Esgotamento sanitário"],
          ["010803", "01.08.03  Energia elétrica"],
          ["010804", "01.08.04  Gás canalizado"],
          ["010805", "01.08.05  Telecomunicações"],
          ["010806", "01.08.06  Resíduos sólidos"],
          [
            "010899",
            "01.08.99  Outro uso em infraestrutura de serviços públicos",
          ],
        ],
        "0109": [
          ["010901", "01.09.01  Rodovia"],
          ["010902", "01.09.02  Ferrovia"],
          ["010903", "01.09.03  Porto ou instalação portuária"],
          ["010904", "01.09.04  Aeroporto ou aeródromo"],
          ["010905", "01.09.05  Hidrovia"],
          [
            "010906",
            "01.09.06  Mobilidade urbana (metrô, BRT, ciclovia, etc.)",
          ],
          ["010999", "01.09.99  Outro uso em infraestrutura de transportes"],
        ],
        "0110": [
          ["011001", "01.10.01  Projeto de requalificação urbana integrada"],
          ["011002", "01.10.02  Operação urbana consorciada"],
          [
            "011003",
            "01.10.03  Projeto de revitalização de área portuária ou industrial",
          ],
          [
            "011099",
            "01.10.99  Outro uso multifinalitário em requalificação urbana",
          ],
        ],
        "0111": [
          ["011101", "01.11.01  Terra indígena"],
          ["011102", "01.11.02  Território quilombola"],
          [
            "011103",
            "01.11.03  área de comunidade tradicional (ribeirinhos, extrativistas, etc.)",
          ],
          [
            "011199",
            "01.11.99  Outro uso por povos originários ou comunidades tradicionais",
          ],
        ],
        "0112": [
          ["011201", "01.12.01  Instituição de ensino federal"],
          ["011202", "01.12.02  Instituição de ensino estadual ou municipal"],
          ["011203", "01.12.03  Instituição de pesquisa"],
          ["011204", "01.12.04  Centro de extensão ou capacitação"],
          ["011299", "01.12.99  Outro uso em ensino, pesquisa e extensão"],
        ],
        "0113": [
          [
            "011301",
            "01.13.01  Centro de assistência social (CRAS, CREAS, etc.)",
          ],
          ["011302", "01.13.02  Abrigo ou albergue"],
          ["011303", "01.13.03  Centro de cidadania ou atendimento ao cidadão"],
          ["011304", "01.13.04  Entidade filantrópica ou sem fins lucrativos"],
          ["011399", "01.13.99  Outro uso socioassistêncial e de cidadania"],
        ],
        "0114": [
          ["011401", "01.14.01  Hospital ou unidade hospitalar"],
          ["011402", "01.14.02  Unidade básica de saúde (UBS/UPA)"],
          ["011403", "01.14.03  Laboratório ou centro de pesquisa em saúde"],
          ["011499", "01.14.99  Outro uso em serviços de saúde"],
        ],
        "0115": [
          ["011501", "01.15.01  Residência funcional de servidor federal"],
          ["011502", "01.15.02  Alojamento coletivo de servidores"],
          ["011599", "01.15.99  Outro uso residencial para servidor"],
        ],
        "0116": [
          ["011601", "01.16.01  Instalação militar"],
          ["011602", "01.16.02  Delegacia, presídio ou unidade de custódia"],
          ["011603", "01.16.03  Corpo de bombeiros"],
          ["011604", "01.16.04  Base ou infraestrutura de defesa nacional"],
          [
            "011699",
            "01.16.99  Outro uso em segurança pública e defesa nacional",
          ],
        ],
        "0117": [
          ["011701", "01.17.01  Templo, igreja ou espaço de culto religioso"],
          [
            "011702",
            "01.17.02  Cemitério ou crematório de vinculação religiosa",
          ],
          ["011799", "01.17.99  Outro uso religioso"],
        ],
        "0118": [["011800", "01.18.00  Sem informação"]],
        "0119": [["011900", "01.19.00  Sem uso definido/vinculação"]],
      };

      function toggleObs56() {
        const checks = document.querySelectorAll(
          '#campo56-checks input[type="checkbox"]',
        );
        const temSelecionado = Array.from(checks).some((cb) => cb.checked);
        toggleBloco("bloco56_obs", temSelecionado);
      }

      function toggleObs57() {
        const checks = document.querySelectorAll(
          '#campo57-checks input[type="checkbox"]',
        );
        const temSelecionado = Array.from(checks).some((cb) => cb.checked);
        toggleBloco("bloco57_obs", temSelecionado);
      }
      function marcarInvalido(el, errId) {
        if (el) el.style.borderColor = "#dc2626";
        const errEl = document.getElementById(errId);
        if (errEl) errEl.style.display = "block";
      }

      function popularCampo53(valor) {
        const campo53 = document.getElementById("campo53");
        campo53.innerHTML = "";
        campo53.disabled = true;

        if (!valor || !usoEspecificoMap[valor]) {
          const opt = document.createElement("option");
          opt.value = "";
          opt.textContent = "Selecione primeiro o tipo de uso imobiliário...";
          campo53.appendChild(opt);
          return;
        }

        const optDefault = document.createElement("option");
        optDefault.value = "";
        optDefault.textContent = "Selecione o tipo de uso específico...";
        campo53.appendChild(optDefault);

        usoEspecificoMap[valor].forEach(([value, label]) => {
          const opt = document.createElement("option");
          opt.value = value;
          opt.textContent = label;
          campo53.appendChild(opt);
        });

        campo53.disabled = false;
      }

      function validarFormulario() {
        let valido = true;
        let primeiro = null;

        document
          .querySelectorAll(".error-msg")
          .forEach((el) => (el.style.display = "none"));
        document
          .querySelectorAll('select,input[type="number"]')
          .forEach((el) => (el.style.borderColor = ""));

        function falha(el, errId) {
          marcarInvalido(el, errId);
          if (!primeiro) primeiro = el;
          valido = false;
        }

        const c51 = document.getElementById("campo51");
        if (!c51.value) falha(c51, "err51");

        const c52 = document.getElementById("campo52");
        if (!c52.value) falha(c52, "err52");

        const c53 = document.getElementById("campo53");
        if (c52.value && !c53.disabled && !c53.value) falha(c53, "err53");

        if (!document.querySelector('input[name="campo54"]:checked')) {
          falha(document.querySelector('input[name="campo54"]'), "err54");
        }

        const c55 = document.getElementById("campo55");
        if (!c55.value) falha(c55, "err55");

        [
          "campo56_radio",
          "campo57_radio",
          "campo58_radio",
          "campo510_radio",
        ].forEach((name) => {
          if (!document.querySelector(`input[name="${name}"]:checked`)) {
            falha(
              document.querySelector(`input[name="${name}"]`),
              "err" + name.replace("campo", "").replace("_radio", ""),
            );
          }
        });

        const c59 = document.getElementById("campo59");
        if (
          c59.value === "" ||
          isNaN(Number(c59.value)) ||
          Number(c59.value) < 0
        )
          falha(c59, "err59");

        const c511 = document.getElementById("campo511");

        const valorCampo511 =
          c511 && c511.selectedIndex > 0
            ? c511.options[c511.selectedIndex].value.trim()
            : "";

        if (!valorCampo511) {
          falha(c511, "err511");
        } else {
          c511.style.borderColor = "";

          const err511 = document.getElementById("err511");
          if (err511) {
            err511.style.display = "none";
          }
        }
      }

      function focarPrimeiro(el) {
        if (!el) return;
        el.scrollIntoView({ behavior: "smooth", block: "center" });
        setTimeout(() => {
          try {
            el.focus();
          } catch (_) {}
        }, 300);
      }

      document.addEventListener("DOMContentLoaded", function () {
        document
          .querySelectorAll("select:not([data-no-custom])")
          .forEach((el) => {
            if (typeof initCustomSelect === "function") initCustomSelect(el);
          });

        // Carregar ações judiciais da tabela_acoes
        (async function () {
          // Mock manual solicitado pelo usuário
          const mockAcoes = {
            nup_sei: "90849.008382/2026-88",
            tipo_processo: "Ação Civil Pública",
            resumo: "ACP da Associação Quilombola Kulumbu do Patuazinho busca proteção territorial e conclusão da regularização fundiária.",
            descricao: "Trata-se de Ação Civil Pública ajuizada pela Associação Quilombola Kulumbu do Patuazinho em face da União, do Instituto Nacional de Colonização e Reforma Agrária (INCRA) e da Fundação Cultural Palmares, visando à proteção territorial, cultural e ambiental da comunidade quilombola, bem como à conclusão do procedimento administrativo de regularização fundiária (Processo INCRA nº 54350.000408/2010-11)."
          };

          document.getElementById("nup_sei").value = mockAcoes.nup_sei;
          document.getElementById("tipo_processo").value = mockAcoes.tipo_processo;
          document.getElementById("resumo_acao").value = mockAcoes.resumo;
          document.getElementById("descricao_acao").value = mockAcoes.descricao;
        })();

        // Carregar dados de contratos anteriores da tabela_spu
        let contratosAnterioresData = [];
        (async function () {
          const processId =
            localStorage.getItem("CURRENT_PROCESS_ID") || window.processId;
          console.log(
            `[foco-03] ProcessId para contratos: "${processId}", fetchSPU existe: ${typeof window.fetchSPU}`,
          );
          if (!processId || typeof window.fetchSPU !== "function") {
            console.warn("[foco-03] ProcessId ou fetchSPU não disponível");
            return;
          }

          // 1. Resolve o RIP real a partir do processId (requerimento)
          let rip = null;
          const SUPA_URL =
            window.SUPABASE_URL ||
            (window.parent && window.parent.SUPABASE_URL);
          const SUPA_KEY =
            window.SUPABASE_ANON_KEY ||
            (window.parent && window.parent.SUPABASE_ANON_KEY);
          if (SUPA_URL && SUPA_KEY) {
            try {
              const urlInd = `${SUPA_URL}/rest/v1/tabela_indicacao?select=dados_json&numero_requerimento=eq.${processId}`;
              const resInd = await fetch(urlInd, {
                headers: {
                  apikey: SUPA_KEY,
                  Authorization: `Bearer ${SUPA_KEY}`,
                },
              });
              if (resInd.ok) {
                const rows = await resInd.json();
                if (
                  rows[0] &&
                  rows[0].dados_json &&
                  rows[0].dados_json.rips &&
                  rows[0].dados_json.rips.length > 0
                ) {
                  rip = rows[0].dados_json.rips[0];
                  console.log(`[foco-03] RIP resolvido para contratos: ${rip}`);
                }
              }
            } catch (e) {
              console.warn(
                "[foco-03] Erro ao buscar RIP na tabela_indicacao para contratos:",
                e,
              );
            }
          }

          // Fallback se não resolvido
          if (!rip) {
            rip = processId;
            console.log(
              `[foco-03] Usando processId como RIP fallback para contratos: ${rip}`,
            );
          }

          try {
            const dadosSPU = await window.fetchSPU(rip);
            console.log(
              "[foco-03] Resultado fetchSPU para contratos:",
              dadosSPU,
            );
            if (dadosSPU && dadosSPU.contratos_anteriores) {
              contratosAnterioresData = dadosSPU.contratos_anteriores;
              renderContratosAnteriores();
            }
          } catch (e) {
            console.error("[foco-03] Erro ao carregar dados SPU:", e);
          }
        })();



        function renderContratosAnteriores() {
          const container = document.getElementById(
            "contratos_anteriores_container",
          );
          if (!container) return;
          container.innerHTML = "";
          if (
            !contratosAnterioresData ||
            contratosAnterioresData.length === 0
          ) {
            container.innerHTML =
              '<span style="font-size: 0.85rem; color: #64748b;">Nenhum contrato anterior registrado para este imóvel.</span>';
            return;
          }
          contratosAnterioresData.forEach((c) => {
            const card = document.createElement("div");
            card.style.cssText =
              "background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 8px; font-size: 0.85rem; color: #334155; box-shadow: 0 1px 2px rgba(0,0,0,0.05); flex: 1; min-width: 200px;";
            card.innerHTML = `
        <strong style="display: block; color: #1e293b; margin-bottom: 2px;">${c.numero}</strong>
        <span style="font-size: 0.8em; color: #64748b;">Vigência: ${c.data_inicio} a ${c.data_fim}</span>
      `;
            container.appendChild(card);
          });
        }

        // Lógica de exibição das observações de CPF/CNPJ irregular
        function checkCpfCnpjRegular() {
          const checked = document.querySelector(
            'input[name="cpf_cnpj_regular"]:checked',
          );
          const bloco = document.getElementById("bloco-cpf-cnpj-irregular-obs");
          if (bloco) {
            const exibir = checked && checked.value === "Não";
            bloco.style.display = exibir ? "flex" : "none";
            if (!exibir) {
              const txt = document.getElementById("obs_cpf_cnpj_irregular");
              if (txt) txt.value = "";
            }
          }
        }
        document
          .querySelectorAll('input[name="cpf_cnpj_regular"]')
          .forEach((radio) => {
            radio.addEventListener("change", checkCpfCnpjRegular);
          });
        setTimeout(checkCpfCnpjRegular, 500);

        document
          .getElementById("campo51")
          .addEventListener("change", function () {
            toggleBloco("bloco51_obs", !!this.value);
            limparErro(this, "err51");
          });

        document
          .getElementById("campo52")
          .addEventListener("change", function () {
            limparErro(this, "err52");
            limparErro(document.getElementById("campo53"), "err53");
            popularCampo53(this.value);
          });

        document
          .getElementById("campo53")
          .addEventListener("change", function () {
            limparErro(this, "err53");
          });

        document.querySelectorAll('input[name="campo54"]').forEach((radio) => {
          radio.addEventListener("change", function () {
            toggleBloco("bloco54", this.value === "Sim");
            if (this.value !== "Sim") {
              const desc = document.getElementById("campo54_desc");
              if (desc) desc.value = "";
            }
            limparErro(null, "err54");
          });
        });

        document
          .getElementById("campo55")
          .addEventListener("change", function () {
            limparErro(this, "err55");
            toggleBloco("bloco55_obs", !!this.value);
          });

        [
          { radio: "campo56_radio", grupo: "group-campo56", err: "err56" },
          { radio: "campo57_radio", grupo: "group-campo57", err: "err57" },
          { radio: "campo58_radio", grupo: "group-campo58", err: "err58" },
          { radio: "campo510_radio", grupo: "group-campo510", err: "err510" },
        ].forEach((cfg) => {
          document
            .querySelectorAll(`input[name="${cfg.radio}"]`)
            .forEach((radio) => {
              radio.addEventListener("change", function () {
                const exibir = this.value === "Sim";
                toggleBloco(cfg.grupo, exibir);

                if (!exibir) {
                  document
                    .querySelectorAll(
                      `#${cfg.grupo} input[type="checkbox"], #${cfg.grupo} input[type="radio"]`,
                    )
                    .forEach((cb) => (cb.checked = false));
                  document
                    .querySelectorAll(`#${cfg.grupo} textarea`)
                    .forEach((t) => (t.value = ""));
                  document
                    .querySelectorAll(`#${cfg.grupo} select`)
                    .forEach((s) => (s.value = ""));
                }

                limparErro(null, cfg.err);
              });
            });
        });

        const campo511 = document.getElementById("campo511");

        if (campo511) {
          campo511.addEventListener("change", function () {
            const temValor =
              this.selectedIndex > 0 &&
              this.options[this.selectedIndex].value.trim() !== "";

            toggleBloco("bloco511_obs", temValor);

            if (temValor) {
              this.style.borderColor = "";

              const err511 = document.getElementById("err511");
              if (err511) {
                err511.style.display = "none";
              }
            } else {
              marcarInvalido(this, "err511");
            }
          });

          campo511.addEventListener("input", function () {
            this.dispatchEvent(new Event("change", { bubbles: true }));
          });
        }

        // Restaura selects com data-selected (rascunho)
        document.querySelectorAll('#form03 select[data-selected]').forEach(function(sel) {
          var val = sel.getAttribute('data-selected');
          if (val) {
            sel.value = val;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
          }
        });
        // Restaura radio-groups e seus blocos condicionais
        [
          { name: 'campo54', bloco: 'bloco54' },
          { name: 'campo56_radio', bloco: 'group-campo56' },
          { name: 'campo57_radio', bloco: 'group-campo57' },
          { name: 'campo58_radio', bloco: 'group-campo58' },
          { name: 'campo510_radio', bloco: 'group-campo510' }
        ].forEach(function(cfg) {
          var checked = document.querySelector('input[name="' + cfg.name + '"]:checked');
          if (checked) {
            checked.dispatchEvent(new Event('change', { bubbles: true }));
          }
        });
      });

      // Listener obsoleto de salvamento removido para permitir submissão HTMX nativa
      </script>

<script>
// Carregar solicitação de criação de RIP
setTimeout(async () => {
  let solicitacao = window.INLINE_SOLICITACAO_RIP || "";

  if (!solicitacao) {
    const processId = localStorage.getItem('CURRENT_PROCESS_ID');
    if (processId && typeof window.carregarIndicacoes === 'function') {
      try {
        const registro = await window.carregarIndicacoes(processId);
        if (registro && registro.dados_json && registro.dados_json.solicitacao_criacao_rip) {
          solicitacao = registro.dados_json.solicitacao_criacao_rip;
        }
      } catch(e) {
        console.warn('[aba3] Erro ao carregar solicitação do Supabase:', e);
      }
    }
  }

  if (solicitacao) {
    const container = document.getElementById('container-solicitacao-criacao-rip');
    if (!container) return;
    const anexos = window.INLINE_SOLICITACAO_ANEXOS || [];
    let anexosHtml = '';
    if (anexos.length > 0) {
      anexosHtml = anexos.map(a =>
        `<div style="display:flex;align-items:center;gap:6px;padding:4px 10px;background:#fff;border-radius:4px;border:1px solid #f3f4f6;margin-top:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
          <a href="${a.base64}" target="_blank" download="${a.nome}" style="color:#1e3a5f;text-decoration:underline;font-size:13px;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${a.nome}">${a.nome}</a>
        </div>`
      ).join('');
    }
    container.style.display = "block";
    container.innerHTML = `
      <div class="card mb-4" style="border: 1px solid #fbcfe8; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); background-color: #fdf2f8;">
        <div style="padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0; border-bottom: 1px solid #fbcfe8; background-color: #fce7f3; color: #9d174d;">
          <h4 style="margin: 0; font-weight: bold; font-size: 1.1em;">🔔 Solicitação de Criação de RIP</h4>
        </div>
        <div style="padding: 16px;">
          <div style="text-align: left; margin: 0;">
            <label style="display:block; margin-bottom: 5px; font-weight: 600; color: #9d174d;">Justificativa enviada ao setor de cadastro:</label>
            <textarea readonly style="width: 100%; border: 1px solid #fbcfe8; padding: 8px; border-radius: 4px; background-color: #fff; color: #334155; resize: vertical; font-family: inherit; font-size: 14px;" rows="4">${solicitacao}</textarea>
          </div>
          ${anexosHtml ? `<div style="margin-top:12px;"><label style="display:block;margin-bottom:5px;font-weight:600;color:#9d174d;font-size:13px;">Anexos:</label>${anexosHtml}</div>` : ''}
        </div>
      </div>
    `;
  }
}, 1000);
</script>
  </body>
</html>

