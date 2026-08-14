<script>
    window.INLINE_SOLICITACAO_RIP = @json($dados['solicitacao_criacao_rip'] ?? ($processo->foco?->aba1?->solicitacao_criacao_rip ?? ''));
    window.INLINE_SOLICITACAO_ANEXOS = @json($dados['solicitacao_anexos'] ?? []);
    window.INLINE_DOCUMENTOS_ABA2 = @json($dados['documentos_aba2'] ?? []);
</script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<style>
  .custom-empty-select { background-color: #ffffff !important; border: 1px solid #3b82f6 !important; box-shadow: 0 0 4px rgba(59,130,246,0.3) !important; }
  .switch { position: relative; display: inline-block; width: 34px; height: 20px; }
  .switch input { opacity: 0; width: 0; height: 0; }
  .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #22c55e; transition: .4s; }
  .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
  input:checked + .slider { background-color: #ef4444; }
  input:checked + .slider:before { transform: translateX(14px); }
  .slider.round { border-radius: 20px; }
  .slider.round:before { border-radius: 50%; }
  .edit-toggle { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: bold; cursor: pointer; margin: 0; }
  .toggle-label-left { color: #16a34a; transition: opacity 0.3s; opacity: 1; }
  .toggle-label-right { color: #dc2626; transition: opacity 0.3s; opacity: 0.4; }
  .edit-toggle:has(input:checked) .toggle-label-left { opacity: 0.4; }
  .edit-toggle:has(input:checked) .toggle-label-right { opacity: 1; }

  #map { height: 500px; width: 100%; border: 1px solid #ddd; border-radius: 8px; margin: 10px 0; z-index: 1; }
  .cep-row { display: flex; gap: 10px; align-items: flex-start; }
  #cep-info { margin-top: 5px; font-size: 0.85em; min-height: 1.2em; }
  .cep-info-ok { color: #28a745; font-weight: bold; }
  .cep-info-erro { color: #dc3545; font-weight: bold; }
  .btn-search { padding: 10px 15px; height: 42px; white-space: nowrap; }
  .lista-geo { list-style: none; padding: 0; margin-top: 15px; }
  .lista-geo li { background: #f8f9fa; border: 1px solid #e2e8f0; padding: 10px 15px; margin-bottom: 8px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; }
  
  .consolidated-box {
      background-color: #f1f5f9;
      border-left: 4px solid #0056b3;
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
  }
  .consolidated-box h4 { margin-top: 0; color: #1e3a5f; font-size: 1.1em; }
  .consolidated-box p { margin: 5px 0; font-size: 0.95em; color: #475569; }

  /* MODAL GEO */
  .geo-modal-overlay {
      display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 100000;
      align-items: center; justify-content: center;
  }
  .geo-modal-content {
      background: #fff; width: 90%; max-width: 900px; border-radius: 12px;
      display: flex; flex-direction: column; overflow: hidden;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  }
  .geo-modal-header {
      padding: 15px 20px; background: #1e3a5f; color: white;
      display: flex; justify-content: space-between; align-items: center;
  }
  .geo-modal-header h3 { margin: 0; font-size: 1.1em; }
  .geo-modal-close {
      background: none; border: none; color: white; font-size: 1.5em; cursor: pointer;
  }
  .geo-modal-body { padding: 20px; flex: 1; }
  .geo-modal-footer {
      padding: 15px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;
      display: flex; justify-content: flex-end; gap: 10px;
  }
  #modal-map { height: 500px; width: 100%; border: 1px solid #ddd; border-radius: 8px; }

  .campo-alterado {
      background-color: #fffacd !important;
      border-color: #f59e0b !important;
  }
  .valor-original-hint {
      display: block;
      font-size: 11px;
      color: #64748b;
      margin-top: 3px;
      font-style: italic;
  }
</style>
<div class="form-container">

    <h2>Diagnóstico do Imóvel</h2>

    @if($errors->any())
        <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ef4444;">
            <strong>Atenção:</strong>
            <ul style="margin-top: 8px; margin-bottom: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ========== ACCORDIONS ABA 1 (SOMENTE LEITURA) ========== -->
    <div class="accordion-container" style="margin-bottom: 25px; display: flex; flex-direction: column; gap: 15px;">

      <!-- Aba 1a: Dados do Requerimento -->
      <div class="accordion-item" id="acc_aba1a" style="border: none;">
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

      <!-- Aba 1b: Indicação do Imóvel -->
      <div class="accordion-item" id="acc_aba1b" style="border: none;">
        <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
          <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📍 RIP(s) ou Cadastro(s) Mínimo(s)</span>
          <span class="accordion-icon">▶</span>
        </div>
        <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">
          @php
            $focoRips = $processo->foco?->rips ?? collect();
            $focoCadastros = $processo->foco?->cadastrosMinimos ?? collect();

            if ($focoRips->isEmpty()) {
                $draftAba1 = \App\Models\FocoDraft::where('processo_id', $processo->id)
                    ->where('user_id', auth()->id())
                    ->where('aba', '1')
                    ->first();
                
                if ($draftAba1) {
                    $draftRips = $draftAba1->data['rips'] ?? $draftAba1->data['rips[]'] ?? [];
                    if (!is_array($draftRips)) $draftRips = [$draftRips];
                    
                    if (!empty($draftRips)) {
                        $focoRips = collect($draftRips)->filter()->map(function($rip) {
                            if (is_string($rip)) $rip = json_decode($rip, true) ?? [];
                            if (!is_array($rip)) $rip = ['numero_rip' => $rip];
                            return (object) $rip;
                        });
                    }
                }
            }

            if ($focoCadastros->isEmpty()) {
                $draftAba1 = $draftAba1 ?? \App\Models\FocoDraft::where('processo_id', $processo->id)
                    ->where('user_id', auth()->id())
                    ->where('aba', '1')
                    ->first();
                    
                if ($draftAba1) {
                    $draftCads = $draftAba1->data['cadastros_minimos'] ?? $draftAba1->data['cadastros_minimos[]'] ?? [];
                    if (!empty($draftCads)) {
                        $cads = is_array($draftCads) ? $draftCads : json_decode($draftCads, true) ?? [];
                        if (!is_array($cads)) $cads = [$cads]; // fallback if it was a single string that didn't decode to array
                        
                        $focoCadastros = collect($cads)->map(function($cad) {
                            if (is_string($cad)) $cad = json_decode($cad, true) ?? [];
                            return (object) $cad;
                        });
                    }
                }
            }
          @endphp

          @if($focoRips->isEmpty() && $focoCadastros->isEmpty())
            <div id="rips-aba2-container">
              <p style="color:#64748b; font-style:italic;">Nenhum RIP ou Cadastro Mínimo associado. Os dados são carregados dinamicamente abaixo.</p>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', async function() {
              const container = document.getElementById('rips-aba2-container');
              const processId = window.CURRENT_PROCESS_ID;
              const SUPA_URL = window.SUPABASE_URL;
              const SUPA_KEY = window.SUPABASE_ANON_KEY;
              if (!processId || !SUPA_URL || !SUPA_KEY) return;

              function buildField(label, value) {
                return `<div style="display:flex;align-items:baseline;margin-bottom:6px;padding:5px 0;font-size:0.9rem;">
                  <span style="display:flex;width:240px;"><span style="font-weight:600;color:#334155;white-space:nowrap;">${label}</span><span style="flex:1;border-bottom:1px dotted #94a3b8;min-width:10px;"></span><span style="white-space:nowrap;color:#334155;">:</span></span>
                  <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;color:#0f172a;border-radius:3px;">${value || '-'}</span>
                </div>`;
              }

              let rips = [], cadastros = [];
              let conceituacao = '';
              try {
                const urlInd = `${SUPA_URL}/rest/v1/tabela_indicacao?select=*&numero_requerimento=eq.${encodeURIComponent(processId)}&limit=1`;
                const resInd = await fetch(urlInd, { headers: { 'apikey': SUPA_KEY, 'Authorization': `Bearer ${SUPA_KEY}` } });
                if (resInd.ok) {
                  const rows = await resInd.json();
                  if (rows?.[0]?.dados_json) {
                    const dj = typeof rows[0].dados_json === 'string' ? JSON.parse(rows[0].dados_json) : rows[0].dados_json;
                    if (dj) {
                      rips = dj.rips || [];
                      cadastros = dj.cadastros_minimos || [];
                      conceituacao = dj.conceituacao_imovel || '';
                    }
                  }
                }
              } catch(e) { console.warn('Erro indicacao:', e); }

              if (rips.length === 0 && cadastros.length === 0 && !conceituacao) {
                try {
                  const reqUrl = `${SUPA_URL}/rest/v1/tabela_requerimentos?select=*&numero_requerimento=eq.${encodeURIComponent(processId)}&limit=1`;
                  const reqRes = await fetch(reqUrl, { headers: { 'apikey': SUPA_KEY, 'Authorization': `Bearer ${SUPA_KEY}` } });
                  if (reqRes.ok) {
                    const reqList = await reqRes.json();
                    if (reqList && reqList.length > 0) {
                      const dj = typeof reqList[0].dados_json === 'string' ? JSON.parse(reqList[0].dados_json) : reqList[0].dados_json;
                      if (dj) {
                        rips = dj.rips || [];
                        cadastros = dj.cadastros_minimos || [];
                      }
                    }
                  }
                } catch (e) {
                  console.warn('Erro tabela_requerimentos:', e);
                }
              }

              console.log('aba2 fallback: processId =', processId, 'rips found =', rips);


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
                const block = document.createElement('div');
                block.className = 'accordion-item';
                block.style.cssText = 'border:none;margin-bottom:8px;';
                function destLine(p, dest, area) {
                  if (!dest) return '';
                  let compl = '';
                  if (dest === 'Parcial' && area) compl = ' — <strong>Metragem:</strong> ' + area + ' m²';
                  return `<div style="margin-bottom:6px;"><span style="font-weight:600;color:#1e293b;">${p}</span><br><span style="color:#166534;">${dest}</span>${compl}</div>`;
                }
                const destBox = (destT || destI) ? `<div style="margin-top:10px;padding:10px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:0.9rem;">
                  ${destLine('Qual a área do terreno a ser destinada?', destT, areaT)}
                  ${destLine('Qual a área construída a ser destinada?', destI, areaI)}
                </div>` : '';
                block.innerHTML = `<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                    <span class="accordion-title" style="font-weight: 600; color: #ffffff;">🏠 Imóvel (RIP): ${rip}</span>
                    <span class="accordion-icon">▶</span>
                </div>
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
                  ${destLine('Qual a área construída a ser destinada?', destI, areaI)}
                </div>` : '';
                const lat = cad.latitude || '';
                const lng = cad.longitude || '';
                function cadField(l, v) {
                  return `<div><div style="font-weight:600;color:#334155;font-size:0.78rem;margin-bottom:2px;">${l}</div><div style="padding:4px 10px;background:#f1f5f9;border-radius:3px;">${v || '-'}</div></div>`;
                }
                function cadFieldFull(l, v) {
                  return `<div style="grid-column:1 / -1;"><div style="font-weight:600;color:#334155;font-size:0.78rem;margin-bottom:2px;">${l}</div><div style="padding:4px 10px;background:#f1f5f9;border-radius:3px;">${v || '-'}</div></div>`;
                }
                const mapaHtml = (lat && lng) ? `<div style="width:320px;flex-shrink:0;min-height:260px;"><div id="mapa-cad-${idx}" data-leaflet-map style="width:100%;height:100%;min-height:260px;border:1px solid #cbd5e1;border-radius:6px;"></div></div>` : '';
                block.innerHTML = `<div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                    <span class="accordion-title" style="font-weight: 600; color: #ffffff;">📝 Cadastro Mínimo #${idx+1} (Sem RIP)</span>
                    <div style="display:inline-flex; gap: 8px; margin-left: 15px;">
                      <button type="button" onclick="abrirModalRipVinculado('js-${idx}'); event.stopPropagation();" style="padding: 2px 8px; font-size: 0.75rem; background: #e0f2fe; color: #0369a1; border: none; border-radius: 4px; cursor: pointer;">Incluir RIP</button>
                      <button type="button" style="padding: 2px 8px; font-size: 0.75rem; background: #f1f5f9; color: #475569; border: none; border-radius: 4px; cursor: pointer;">Prosseguir sem RIP</button>
                    </div>
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
                  <div id="rips-vinculados-cad-js-${idx}" style="margin-top:10px; display:flex; flex-direction:column; gap:6px;"></div>
                  </div>
                  ${mapaHtml}
                </div></div>`;
                container.appendChild(block);
                if (lat && lng && typeof initMapCadastro === 'function') {
                  initMapCadastro('mapa-cad-' + idx, lat, lng);
                }
              });
            });
            </script>
          @else
            {{-- RIPs já disponíveis no MySQL --}}
            <div style="display: flex; flex-direction: column; gap: 10px;" id="rips-aba2-mysql">
              @foreach($focoRips as $rip)
              <div style="background:white;border:1px solid #cbd5e1;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                  <span>🏠 Imóvel (RIP): {{ $rip->numero_rip }}</span>
                  <span class="accordion-icon">▶</span>
                </div>
                <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;" id="rip-spu-aba2-{{ $loop->index }}">
                  <p style="color:#64748b;font-style:italic;font-size:0.85rem;">Carregando dados do SPU...</p>
                </div>
              </div>
              <script>
                document.addEventListener('DOMContentLoaded', async function() {
                  const el = document.getElementById('rip-spu-aba2-{{ $loop->index }}');
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
                    ${destLine('Qual a área construída a ser destinada?', destI, areaI)}
                    </div>` : '';
                  el.innerHTML = `<div>
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
              @foreach($focoCadastros as $cad)
              @php
                  $cadLat = $cad->latitude ?? '';
                  $cadLng = $cad->longitude ?? '';
              @endphp
              <div style="background:white;border:1px solid #cbd5e1;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                  <span>📝 Cadastro Mínimo #{{ $loop->index+1 }} (Sem RIP)</span>
                  <div style="display:inline-flex; gap: 8px; margin-left: 15px;">
                    <button type="button" style="padding: 2px 8px; font-size: 0.75rem; background: #e0f2fe; color: #0369a1; border: none; border-radius: 4px; cursor: pointer;" onclick="abrirModalRipVinculado('{{ $cad->id ?? 'js-' . $loop->index }}'); event.stopPropagation();">Incluir RIP</button>
                    <button type="button" style="padding: 2px 8px; font-size: 0.75rem; background: #f1f5f9; color: #475569; border: none; border-radius: 4px; cursor: pointer;">Prosseguir sem RIP</button>
                  </div>
                  <span class="accordion-icon">▶</span>
                </div>
                <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">
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
                      <div id="rips-vinculados-cad-{{ $cad->id ?? 'js-' . $loop->index }}" style="margin-top:10px; display:flex; flex-direction:column; gap:6px;">
                        @foreach(($cad->ripsVinculados ?? collect()) as $ripV)
                        <div style="background:#eff6ff;border:1px solid #bfdbfe;padding:8px 12px;border-radius:6px;font-size:0.85rem;">
                          <strong style="color:#1d4ed8;">🔗 RIP Vinculado:</strong> {{ $ripV->numero_rip }}
                          @if($ripV->destinacao_terreno)
                          <div style="margin-top:4px;color:#334155;"><strong>Destinação do terreno:</strong> {{ $ripV->destinacao_terreno }}@if($ripV->destinacao_terreno === 'Parcial' && $ripV->area_terreno_parcial) — {{ $ripV->area_terreno_parcial }} m² @endif</div>
                          @endif
                          @if($ripV->destinacao_imovel)
                          <div style="color:#334155;"><strong>Destinação do imóvel:</strong> {{ $ripV->destinacao_imovel }}@if($ripV->destinacao_imovel === 'Parcial' && $ripV->area_imovel_parcial) — {{ $ripV->area_imovel_parcial }} m² @endif</div>
                          @endif
                        </div>
                        @endforeach
                      </div>
                    </div>
                    @if($cadLat && $cadLng)
                    <div style="width:320px;flex-shrink:0;min-height:260px;">
                      <div id="mapa-cad-mysql-{{ $loop->index }}" data-leaflet-map style="width:100%;height:100%;min-height:260px;border:1px solid #cbd5e1;border-radius:6px;"></div>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  if ({{ $cadLat && $cadLng ? 'true' : 'false' }} && typeof initMapCadastro === 'function') {
                    initMapCadastro('mapa-cad-mysql-{{ $loop->index }}', '{{ $cadLat }}', '{{ $cadLng }}');
                  }
                });
              </script>
              @endforeach
            </div>
          @endif

          <div style="margin-top:15px; padding:12px; background:#fff1f2; border:1px solid #fda4af; border-radius:6px; font-size:0.9rem;">
            <div style="font-weight:600; color:#9f1239; margin-bottom:5px;">Há inconsistências cadastrais?</div>
            <div style="display:flex; gap:15px; margin-bottom:8px;">
              <label style="cursor:pointer;"><input type="radio" name="rip_alteracao_sim_nao" value="Sim" onclick="document.getElementById('bloco-alteracao-rip').style.display='block'"> Sim</label>
              <label style="cursor:pointer;"><input type="radio" name="rip_alteracao_sim_nao" value="Não" onclick="document.getElementById('bloco-alteracao-rip').style.display='none'"> Não</label>
            </div>
            <div id="bloco-alteracao-rip" style="display:none;">
              <textarea name="rip_alteracao_descricao" style="width:100%; padding:8px; border:1px solid #fda4af; border-radius:4px; font-family:inherit; font-size:0.9rem;" placeholder="Descreva as alterações necessárias..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ======================================================= -->

    @php
        $simuladoCookie = request()->cookie('perfil_simulado');
        $user = auth()->user();
        $isAdmin = $user && ($user->hasRole('Administrador') || $user->hasRole('Direção'));
        
        $canEditAba2 = false;
        if ($simuladoCookie === 'ALL' || $simuladoCookie === 'CARACTERIZACAO') {
            $canEditAba2 = true;
        } elseif (!$simuladoCookie && $isAdmin) {
            $canEditAba2 = true;
        } elseif ($user && $user->hasRole('Equipe Caracterização')) {
            $canEditAba2 = true;
        }
    @endphp
    <fieldset @if(!$canEditAba2) disabled @endif>
    <form action="{{ route('processos.tramitar', $processo->id) }}" method="POST" hx-post="{{ route('processos.tramitar', $processo->id) }}" hx-target="#aba2-container" hx-indicator="#form-indicator-aba2" id="form02">
      @csrf
        <div id="form-indicator-aba2" class="htmx-indicator" style="display:none; color: #475569; margin-bottom: 10px;">⏳ Processando...</div>
        @csrf
        <input type="hidden" name="aba_atual" value="2">
        <input type="hidden" name="next_aba" value="index">
        <div id="hidden-rips-vinculados"></div>

            <!-- Bloco Retornar Processo -->
            @if($processo->tramitacao === 'Devolvido')
                @if(isset($respostaDevolucao))
                <div id="blocoRespostaDevolutivaAba2" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                    <h4 style="color: #16a34a; margin-top: 0; border-bottom: 1px solid #86efac; padding-bottom: 8px;">✅ Retornar Processo</h4>
                    <p style="margin-bottom: 10px; font-size: 0.9em; color: #166534;">
                        Justificativa preenchida por <strong>{{ $respostaDevolucao['usuario'] }}</strong> em {{ $respostaDevolucao['data'] }}
                    </p>
                    <div style="width: 100%; min-height: 80px; padding: 15px; border: 1px solid #86efac; border-radius: 4px; background-color: #f8fafc; font-family: inherit; font-size: 14px; color: #334155; white-space: pre-wrap;">{{ $respostaDevolucao['texto'] }}</div>
                </div>
                @else
                <div id="blocoRespostaDevolutivaAba2" class="editavel" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                    <h4 style="color: #16a34a; margin-top: 0; border-bottom: 1px solid #86efac; padding-bottom: 8px;">Retornar Processo</h4>
                    <label for="resposta_devolucao" style="font-weight: bold; color: #166534;">Descreva o que foi corrigido ou complementado nesta etapa (Obrigatório para enviar):</label>
                    <textarea id="resposta_devolucao" name="resposta_devolucao" required style="width: 100%; min-height: 100px; padding: 10px; border: 1px solid #86efac; border-radius: 4px; margin-top: 10px; font-family: inherit; font-size: 14px;">{{ $dados['resposta_devolucao'] ?? '' }}</textarea>
                </div>
                @endif
            @endif

      <!-- ========== ACCORDION INDICAÇÕES ========== -->
<style>
  fieldset { border: none !important; }
  .custom-empty-select { background-color: #ffffff !important; border: 1px solid #3b82f6 !important; box-shadow: 0 0 4px rgba(59,130,246,0.3) !important; }

          .accordion-container {
              display: flex;
              flex-direction: column;
              gap: 15px;
              margin-bottom: 25px;
          }
          .accordion-item {
              border: none;
              border-radius: 8px;
              overflow: hidden;
              background: #fff;
              box-shadow: 0 2px 4px rgba(0,0,0,0.05);
          }
          .accordion-header {
              padding: 15px 20px;
              cursor: pointer;
              display: flex;
              justify-content: space-between;
              align-items: center;
              font-weight: bold;
              font-size: 1.1em;
              transition: background 0.2s;
          }
          .accordion-header.type-rip {
              background: #eff6ff;
              color: #1e40af;
              border-left: 5px solid #3b82f6;
          }
          .accordion-header.type-cadastro {
              background: #f0fdf4;
              color: #166534;
              border-left: 5px solid #22c55e;
          }
          .accordion-header:hover {
              filter: brightness(0.95);
          }
          .accordion-body {
              display: none;
              padding: 20px;
              border-top: none;
          }
          .accordion-body.active {
              display: block;
          }
          .accordion-icon {
              font-size: 0.9rem;
              transition: transform 0.3s;
              color: white !important;
          }
          .type-rip .accordion-icon, .type-cadastro .accordion-icon {
              color: #1e3a5f !important;
          }
          .active .accordion-icon {
              transform: rotate(90deg);
          }
          /* Ocultar botões de consulta/edição conforme solicitado */
          .edit-toggle {
              display: none !important;
          }
          
          /* Estilo para placeholders e textos de campos vazios em vermelho */
          .empty-spu-field:disabled,
          select.empty-spu-field:disabled {
              color: #dc2626 !important;
              -webkit-text-fill-color: #dc2626 !important;
              opacity: 1;
          }
          .empty-spu-field::placeholder {
              color: #dc2626 !important;
              opacity: 1;
          }
      </style>

      <!-- Bloco de Solicitação de Criação de RIP -->
      <div id="container-solicitacao-criacao-rip" style="display: none; margin-bottom: 12px; width: 100%;"></div>

      <!-- Tags visuais dos RIPs associados (removido por redundância) -->
      <div id="listaRIPsAssociados" style="display:none !important; flex-wrap:wrap; gap:8px; margin-bottom:12px; padding:8px 0;"></div>

      <div id="accordion-indicacoes" class="accordion-container">
          <div id="msg-sem-indicacoes" style="text-align: center; padding: 30px; color: #64748b; display:none;">
              <i>Nenhuma indicação cadastrada na Aba 1.</i>
          </div>
      </div>
      <!-- ========== FIM ACCORDION INDICAÇÕES ========== -->


        
            <div id="global-sections-container">
          
          <!-- ==================== OCUPAÇÃO ==================== -->
          <div id="secao-ocupacao">
              <h4 style="margin: 24px 0 16px 0; color: #0056b3; border-bottom: 2px solid #ddd; padding-bottom: 8px;">
                Ocupação
              </h4>

              <!-- Situação ocupacional -->
              <div class="form-group editavel">
                <label>Situação ocupacional:</label>
                <div class="radio-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; align-items: center;">
                  <label class="radio-option">
                    <input type="radio" name="situacao_ocupacional" value="Desocupado" {{ isset($dados['situacao_ocupacional']) && $dados['situacao_ocupacional'] == 'Desocupado' ? 'checked' : '' }} required />
                    Desocupado
                  </label>
                  <label class="radio-option">
                    <input type="radio" name="situacao_ocupacional" value="Ocupado regularmente" {{ isset($dados['situacao_ocupacional']) && $dados['situacao_ocupacional'] == 'Ocupado regularmente' ? 'checked' : '' }} />
                    Ocupado regularmente
                  </label>
                  <label class="radio-option">
                    <input type="radio" name="situacao_ocupacional" value="Ocupado irregularmente" {{ isset($dados['situacao_ocupacional']) && $dados['situacao_ocupacional'] == 'Ocupado irregularmente' ? 'checked' : '' }} />
                    Ocupado irregularmente
                  </label>
                  <label class="radio-option">
                    <input type="radio" name="situacao_ocupacional" value="Não há informação" {{ isset($dados['situacao_ocupacional']) && $dados['situacao_ocupacional'] == 'Não há informação' ? 'checked' : '' }} />
                    Não há informação
                  </label>
                </div>

                <!-- Campos exibidos quando Desocupado -->
                <div id="bloco-desocupado" style="display: none; flex-direction: column; gap: 6px; margin-top: 8px;">
                  <label for="campo-tempo-desocupacao">Tempo de desocupação:</label>
                  @php
                    $dt = $dados['tempo_desocupacao'] ?? '';
                    $mesAno = '';
                    if (preg_match('/^\d{4}-\d{2}$/', $dt)) {
                      $mesAno = $dt;
                    } elseif (preg_match('/^(\d{2})\/(\d{4})$/', $dt, $m)) {
                      $mesAno = $m[2] . '-' . $m[1];
                    } elseif (preg_match('/^(\d{4})$/', $dt, $m)) {
                      $mesAno = $m[1] . '-01';
                    }
                  @endphp
                  <input type="month" id="campo-tempo-desocupacao" name="tempo_desocupacao" value="{{ $mesAno }}" />

                  <label for="obs-desocupado">Observações:</label>
                  <textarea id="obs-desocupado" name="obs_desocupado" placeholder="Observações sobre a desocupação...">{{ $dados['obs_desocupado'] ?? '' }}</textarea>
                </div>

                <!-- Campos exibidos quando Ocupado regularmente ou Ocupado irregularmente -->
                <div id="bloco-ocupado" style="display: none; flex-direction: column; gap: 6px; margin-top: 8px;">
                  <label for="campo-data-conhecimento-ocupacao">Data de conhecimento da ocupação:</label>
                  @php
                    $dtOcup = $dados['data_conhecimento_ocupacao'] ?? '';
                    $mesAnoOcup = '';
                    if (preg_match('/^\d{4}-\d{2}$/', $dtOcup)) {
                      $mesAnoOcup = $dtOcup;
                    } elseif (preg_match('/^(\d{2})\/(\d{4})$/', $dtOcup, $m)) {
                      $mesAnoOcup = $m[2] . '-' . $m[1];
                    } elseif (preg_match('/^(\d{4})$/', $dtOcup, $m)) {
                      $mesAnoOcup = $m[1] . '-01';
                    }
                  @endphp
                  <input type="month" id="campo-data-conhecimento-ocupacao" name="data_conhecimento_ocupacao" value="{{ $mesAnoOcup }}" />

                  <label for="obs-ocupado">Observações:</label>
                  <textarea id="obs-ocupado" name="obs_ocupado" placeholder="Informar dados do ocupante e indicar se a ocupação é parcial ou integral.">{{ $dados['obs_ocupado'] ?? '' }}</textarea>
                </div>
              </div>

              <!-- Campos exibidos apenas quando Ocupado regularmente ou Ocupado irregularmente -->
              <div id="bloco-uso-atual" style="display: none; flex-direction: column; gap: 0;">
                <!-- Uso imobiliário atual -->
                <div class="form-group editavel">
                  <label for="campo32">Uso imobiliário atual:</label>
                  <select id="campo32" name="tipo_uso_atual" data-selected="{{ $dados['tipo_uso_atual'] ?? '' }}"
                          hx-get="/api/vocacoes?selected={{ $dados['tipo_uso_especifico_atual'] ?? '' }}" 
                          hx-target="#campo33" 
                          hx-trigger="load, change">
                    <option value="">Selecione...</option>
                    <option value="0101" {{ (isset($dados['tipo_uso_atual']) && $dados['tipo_uso_atual'] == '0101') ? 'selected' : '' }}>01.01 Uso administrativo e representativo</option>
                    <option value="0102" {{ (isset($dados['tipo_uso_atual']) && $dados['tipo_uso_atual'] == '0102') ? 'selected' : '' }}>01.02 Uso para agropecuária, aquicultura, produção florestal e pesca</option>
                    <option value="0103" {{ (isset($dados['tipo_uso_atual']) && $dados['tipo_uso_atual'] == '0103') ? 'selected' : '' }}>01.03 Uso ambiental e dos recursos naturais</option>
                    <option value="0104" {{ (isset($dados['tipo_uso_atual']) && $dados['tipo_uso_atual'] == '0104') ? 'selected' : '' }}>01.04 Uso cultural, esportivo e de lazer</option>
                    <option value="0106" {{ (isset($dados['tipo_uso_atual']) && $dados['tipo_uso_atual'] == '0106') ? 'selected' : '' }}>01.06 Uso habitacional</option>
                    <option value="0111" {{ (isset($dados['tipo_uso_atual']) && $dados['tipo_uso_atual'] == '0111') ? 'selected' : '' }}>01.11 Uso por povos originários e comunidades tradicionais</option>
                  </select>
                </div>

                <!-- Uso específico atual -->
                <div class="form-group editavel">
                  <label for="campo33">Uso específico atual:</label>
                  <select id="campo33" name="tipo_uso_especifico_atual">
                    <option value="">Selecione primeiro o uso imobiliário atual...</option>
                  </select>
                </div>
              </div>
          </div>

          <!-- ==================== INCIDÊNCIA AMBIENTAL ==================== -->
          <div id="secao-incidencia-ambiental">
              <h4 style="margin: 24px 0 16px 0; color: #0056b3; border-bottom: 2px solid #ddd; padding-bottom: 8px;">
                Incidência ambiental
              </h4>

              <div class="form-group editavel">
                <label>Há incidência ambiental identificada?</label>
                <div id="group-pergunta-incidencia" class="checkbox-group" style="display:flex; flex-direction:row; gap:32px; flex-wrap:wrap; margin-bottom:10px; align-items:center;">
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_incidencia[]" value="Sim" {{ isset($dados['ha_incidencia']) && in_array('Sim', (array)$dados['ha_incidencia']) ? 'checked' : '' }} /> Sim</label>
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_incidencia[]" value="Não" {{ isset($dados['ha_incidencia']) && in_array('Não', (array)$dados['ha_incidencia']) ? 'checked' : '' }} /> Não</label>
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_incidencia[]" value="Não há informação suficiente" {{ isset($dados['ha_incidencia']) && in_array('Não há informação suficiente', (array)$dados['ha_incidencia']) ? 'checked' : '' }} /> Não há informação suficiente</label>
                </div>

                <div id="bloco-incidencia-itens" style="display:none;">
                  <label style="margin-top:4px;">Incidências verificadas:</label>
                  <div class="checkbox-group" id="group-incidencia">
                    <label class="checkbox-option">
                      <input type="checkbox" name="incidencia_ambiental[]" value="APP" {{ isset($dados['incidencia_ambiental']) && in_array('APP', (array)$dados['incidencia_ambiental']) ? 'checked' : '' }} />
                      APP — Área de Preservação Permanente
                      <span class="hint-semaforo">
                        <span class="hint-icon" data-hint="Área protegida por legislação específica com restrições severas." data-hint-tipo="vermelho">?</span>
                      </span>
                    </label>
                    <label class="checkbox-option">
                      <input type="checkbox" name="incidencia_ambiental[]" value="Unidade de Conservação" {{ isset($dados['incidencia_ambiental']) && in_array('Unidade de Conservação', (array)$dados['incidencia_ambiental']) ? 'checked' : '' }} />
                      Unidade de Conservação Federal, Estadual ou Municipal
                      <span class="hint-semaforo">
                        <span class="hint-icon" data-hint="Sujeito a regime próprio de proteção." data-hint-tipo="amarelo">?</span>
                      </span>
                    </label>
                    <label class="checkbox-option">
                      <input type="checkbox" name="incidencia_ambiental[]" value="Área de risco" {{ isset($dados['incidencia_ambiental']) && in_array('Área de risco', (array)$dados['incidencia_ambiental']) ? 'checked' : '' }} />
                      Área de risco — geotécnica, inundação, etc.
                    </label>
                    <label class="checkbox-option">
                      <input type="checkbox" name="incidencia_ambiental[]" value="Área contaminada" {{ isset($dados['incidencia_ambiental']) && in_array('Área contaminada', (array)$dados['incidencia_ambiental']) ? 'checked' : '' }} />
                      Área contaminada — passivo ambiental
                    </label>
                    <label class="checkbox-option">
                      <input type="checkbox" name="incidencia_ambiental[]" value="Outra situação ambiental" {{ isset($dados['incidencia_ambiental']) && in_array('Outra situação ambiental', (array)$dados['incidencia_ambiental']) ? 'checked' : '' }} />
                      Outra situação ambiental
                    </label>
                  </div>

                  <div id="bloco-obs-incidencia" style="display: none; flex-direction: column; gap: 6px; margin-top: 8px;">
                    <label for="obs_incidencia_ambiental">Observações sobre incidência ambiental:</label>
                    <textarea id="obs_incidencia_ambiental" name="obs_incidencia_ambiental" placeholder="Descreva informações complementares sobre incidência ambiental...">{{ $dados['obs_incidencia_ambiental'] ?? '' }}</textarea>
                  </div>
                </div>
              </div>
          </div>

          <!-- ==================== RISCOS ==================== -->
          <div id="secao-riscos">
              <h4 style="margin: 24px 0 16px 0; color: #0056b3; border-bottom: 2px solid #ddd; padding-bottom: 8px;">
                Riscos
              </h4>

              <div class="form-group editavel">
                <label>Há riscos identificado?</label>
                <div id="group-pergunta-riscos" class="checkbox-group" style="display:flex; flex-direction:row; gap:32px; flex-wrap:wrap; margin-bottom:10px; align-items:center;">
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_riscos[]" value="Sim" {{ isset($dados['ha_riscos']) && in_array('Sim', (array)$dados['ha_riscos']) ? 'checked' : '' }} /> Sim</label>
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_riscos[]" value="Não" {{ isset($dados['ha_riscos']) && in_array('Não', (array)$dados['ha_riscos']) ? 'checked' : '' }} /> Não</label>
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_riscos[]" value="Não há informação suficiente" {{ isset($dados['ha_riscos']) && in_array('Não há informação suficiente', (array)$dados['ha_riscos']) ? 'checked' : '' }} /> Não há informação suficiente</label>
                </div>

                <div id="bloco-riscos-itens" style="display:none;">
                  <label style="margin-top:4px;">Riscos verificados:</label>
                  <div class="checkbox-group" id="group-riscos">
                      <label class="checkbox-option">
                        <input type="checkbox" name="riscos[]" value="Risco de invasão/esbulho" {{ isset($dados['riscos']) && in_array('Risco de invasão/esbulho', (array)$dados['riscos']) ? 'checked' : '' }} />
                        Risco de invasão/esbulho
                      </label>
                      <label class="checkbox-option">
                        <input type="checkbox" name="riscos[]" value="Risco à segurança/saúde pública" {{ isset($dados['riscos']) && in_array('Risco à segurança/saúde pública', (array)$dados['riscos']) ? 'checked' : '' }} />
                        Risco à segurança/saúde pública
                      </label>
                      <label class="checkbox-option">
                        <input type="checkbox" name="riscos[]" value="Risco estrutural ou de desabamento" {{ isset($dados['riscos']) && in_array('Risco estrutural ou de desabamento', (array)$dados['riscos']) ? 'checked' : '' }} />
                        Risco estrutural ou de desabamento
                      </label>
                      <label class="checkbox-option">
                        <input type="checkbox" name="riscos[]" value="Risco de depredação, vandalismo ou deterioração" {{ isset($dados['riscos']) && in_array('Risco de depredação, vandalismo ou deterioração', (array)$dados['riscos']) ? 'checked' : '' }} />
                        Risco de depredação, vandalismo ou deterioração
                      </label>
                      <label class="checkbox-option">
                        <input type="checkbox" name="riscos[]" value="Outro risco identificado" {{ isset($dados['riscos']) && in_array('Outro risco identificado', (array)$dados['riscos']) ? 'checked' : '' }} />
                        Outro risco identificado
                      </label>
                  </div>

                  <div id="bloco-obs-riscos" style="display: none; flex-direction: column; gap: 6px; margin-top: 8px;">
                    <label for="obs-riscos">Observações sobre riscos:</label>
                    <textarea id="obs-riscos" name="obs_riscos" placeholder="Descreva informações complementares sobre os riscos verificados...">{{ $dados['obs_riscos'] ?? '' }}</textarea>
                  </div>
                </div>
              </div>
          </div>

          <!-- ==================== RESTRIÇÕES ==================== -->
          <div id="secao-restricoes">
              <h4 style="margin: 24px 0 16px 0; color: #0056b3; border-bottom: 2px solid #ddd; padding-bottom: 8px;">
                Condições Específicas do Imóvel
              </h4>

              <div class="form-group editavel">
                <label>Há restrições e condições limitadoras?</label>
                <div id="group-pergunta-restricoes" class="checkbox-group" style="display:flex; flex-direction:row; gap:32px; flex-wrap:wrap; margin-bottom:10px; align-items:center;">
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_restricoes[]" value="Sim" {{ isset($dados['ha_restricoes']) && in_array('Sim', (array)$dados['ha_restricoes']) ? 'checked' : '' }} /> Sim</label>
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_restricoes[]" value="Não" {{ isset($dados['ha_restricoes']) && in_array('Não', (array)$dados['ha_restricoes']) ? 'checked' : '' }} /> Não</label>
                  <label class="checkbox-option" style="display:inline-flex; align-items:center; gap:6px; margin:0;"><input type="checkbox" name="ha_restricoes[]" value="Não há informação suficiente" {{ isset($dados['ha_restricoes']) && in_array('Não há informação suficiente', (array)$dados['ha_restricoes']) ? 'checked' : '' }} /> Não há informação suficiente</label>
                </div>

                <div id="bloco-restricoes-itens" style="display:none;">
                  <label style="margin-top:4px;">Restrições verificadas:</label>
                  <div class="checkbox-group" id="group-restricoes">
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Faixa de fronteira" {{ isset($dados['restricoes']) && in_array('Faixa de fronteira', (array)$dados['restricoes']) ? 'checked' : '' }} /> Faixa de fronteira</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Faixa de segurança" {{ isset($dados['restricoes']) && in_array('Faixa de segurança', (array)$dados['restricoes']) ? 'checked' : '' }} /> Faixa de segurança</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Faixa de domínio Ferrovia/Rodovia" {{ isset($dados['restricoes']) && in_array('Faixa de domínio Ferrovia/Rodovia', (array)$dados['restricoes']) ? 'checked' : '' }} /> Faixa de domínio Ferrovia/Rodovia</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Faixa de 100 metros ao longo da costa marítima" {{ isset($dados['restricoes']) && in_array('Faixa de 100 metros ao longo da costa marítima', (array)$dados['restricoes']) ? 'checked' : '' }} /> Faixa de 100 metros ao longo da costa marítima</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Circunferência de 1.320 metros em torno de instalações militares" {{ isset($dados['restricoes']) && in_array('Circunferência de 1.320 metros em torno de instalações militares', (array)$dados['restricoes']) ? 'checked' : '' }} /> Circunferência de 1.320 metros em torno de instalações militares</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Terra indígena" {{ isset($dados['restricoes']) && in_array('Terra indígena', (array)$dados['restricoes']) ? 'checked' : '' }} /> Terra indígena</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Território quilombola ou área de comunidade tradicional" {{ isset($dados['restricoes']) && in_array('Território quilombola ou área de comunidade tradicional', (array)$dados['restricoes']) ? 'checked' : '' }} /> Território quilombola ou área de comunidade tradicional</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Zona/Área de Interesse Social (ZEIS)" {{ isset($dados['restricoes']) && in_array('Zona/Área de Interesse Social (ZEIS)', (array)$dados['restricoes']) ? 'checked' : '' }} /> Zona/Área de Interesse Social — ZEIS</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Área Non Aedificandi" {{ isset($dados['restricoes']) && in_array('Área Non Aedificandi', (array)$dados['restricoes']) ? 'checked' : '' }} /> Área Non Aedificandi</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Restrição de uso/ocupação incidente sobre o imóvel" {{ isset($dados['restricoes']) && in_array('Restrição de uso/ocupação incidente sobre o imóvel', (array)$dados['restricoes']) ? 'checked' : '' }} /> Restrição de uso/ocupação incidente sobre o imóvel</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Tombado como patrimônio histórico, artístico e/ou cultural" {{ isset($dados['restricoes']) && in_array('Tombado como patrimônio histórico, artístico e/ou cultural', (array)$dados['restricoes']) ? 'checked' : '' }} /> Tombado como patrimônio histórico, artístico e/ou cultural</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Poligonal de Porto Organizado" {{ isset($dados['restricoes']) && in_array('Poligonal de Porto Organizado', (array)$dados['restricoes']) ? 'checked' : '' }} /> Poligonal de Porto Organizado</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Área operacional da RFFSA" {{ isset($dados['restricoes']) && in_array('Área operacional da RFFSA', (array)$dados['restricoes']) ? 'checked' : '' }} /> Área operacional da RFFSA</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Localizada em loteamento" {{ isset($dados['restricoes']) && in_array('Localizada em loteamento', (array)$dados['restricoes']) ? 'checked' : '' }} /> Localizada em loteamento</label>
                      <label class="checkbox-option"><input type="checkbox" name="restricoes[]" value="Outra restrição identificada" {{ isset($dados['restricoes']) && in_array('Outra restrição identificada', (array)$dados['restricoes']) ? 'checked' : '' }} /> Outra restrição identificada</label>
                  </div>

                  <div id="bloco-obs-restricoes" style="display: none; flex-direction: column; gap: 6px; margin-top: 16px;">
                    <label for="obs-restricoes">Observações sobre as restrições:</label>
                    <textarea id="obs-restricoes" name="obs_restricoes" placeholder="Descreva informações complementares sobre as restrições verificadas...">{{ $dados['obs_restricoes'] ?? '' }}</textarea>
                  </div>
                </div>
              </div>
          </div>

          <!-- ========== GEOLOCALIZAÇÃO ========== -->
          <div id="secao-geolocalizacao" style="margin-top: 24px; margin-bottom: 24px;">
              <h4 style="margin: 0 0 16px 0; color: #0056b3; border-bottom: 2px solid #ddd; padding-bottom: 8px;">
                Geolocalização
              </h4>
              
              <input type="hidden" id="cep" name="geo_cep" value="{{ $dados['geo_cep'] ?? '' }}">
              <input type="hidden" id="latitude" name="latitude" value="{{ $dados['latitude'] ?? '' }}">
              <input type="hidden" id="longitude" name="longitude" value="{{ $dados['longitude'] ?? '' }}">

              <div class="form-group" style="background: transparent; border: none; padding: 0; display: flex; justify-content: center; margin-top: 15px;">
                  <button type="button" id="btn-open-geo-modal" class="btn-primary" style="padding: 12px 24px; font-size: 1.1em; width: auto; font-weight: bold; background-color: #0284c7; color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                      🗺️ Abrir mapa...
                  </button>
              </div>
          </div>

      </div>

      <!-- MODAL GEO -->
      <div id="geoModal" class="geo-modal-overlay">
          <div class="geo-modal-content">
              <div class="geo-modal-header">
                  <h3>Demarcação Geográfica</h3>
                  <button type="button" class="geo-modal-close" onclick="fecharGeoModal()">×</button>
              </div>
              <div class="geo-modal-body">
                  <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                      <input type="text" id="modal-search-input" placeholder="Buscar endereço ou CEP..." style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                      <button type="button" class="btn-secondary" onclick="buscarNoModal()">🔍 Buscar no Mapa</button>
                  </div>
                  <div id="modal-map"></div>
                  <div style="margin-top: 10px; font-size: 0.85em; color: #64748b;">
                      💡 Dica: Utilize as ferramentas de desenho no canto superior direito do mapa para marcar o ponto ou a área exata.
                  </div>
              </div>
              <div class="geo-modal-footer">
                  <button type="button" class="btn-secondary" onclick="fecharGeoModal()">Cancelar</button>
                  <button type="button" class="btn-primary" onclick="salvarGeoModal()">Salvar Coordenadas</button>
              </div>
          </div>
      </div>

      


      

      <!-- Observações e Documentos da Aba 2 (Geral) -->
      <div style="margin-top: 30px; margin-bottom: 20px;">
          <!-- Observações (Geral) -->
          <div class="form-group editavel" style="margin-bottom: 20px;">
              <label style="font-weight: bold; display: block; margin-bottom: 5px; font-size: 0.95em; color: #1e1b4b;">Observações da Caracterização:</label>
              <textarea name="observacoes_aba2" id="observacoes_aba2" rows="4" placeholder="Insira aqui as observações gerais da análise desta aba..." style="width: 100%; border: 1px solid #cbd5e1; padding: 10px; border-radius: 4px; font-size: 0.95em; background: #ffffff; color: #1e293b; resize: vertical;">{{ $dados['observacoes_aba2'] ?? '' }}</textarea>
          </div>

          <!-- Documentos e Links Anexados -->
          <div class="form-group editavel">
              <label style="font-weight: bold; display: block; margin-bottom: 10px; font-size: 0.95em; color: #1e1b4b;">Documentos e Links Anexados</label>
              <div id="documentos-list-aba2" class="documentos-container">
                  <!-- Lista de documentos injetada aqui pelo dinâmico -->
              </div>
              <button type="button" class="btn btn-outline-success btn-sm mt-2" id="btnAdicionarDocAba2">+ Adicionar link/documento</button>
          </div>
      </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAdicionarDocAba2 = document.getElementById('btnAdicionarDocAba2');
        const documentosListAba2 = document.getElementById('documentos-list-aba2');
        
        if (btnAdicionarDocAba2 && documentosListAba2) {
            btnAdicionarDocAba2.addEventListener('click', function() {
                const docRow = document.createElement('div');
                docRow.style.cssText = 'display: flex; gap: 10px; margin-top: 10px; align-items: center;';
                docRow.innerHTML = `
                    <input type="file" style="display:none;" onchange="this.nextElementSibling.value = this.files[0].name">
                    <input type="text" name="documentos_aba2[]" placeholder="Cole o link ou clique para selecionar arquivo" style="flex: 1; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; outline: none; cursor: pointer;" onclick="if(!this.value) this.previousElementSibling.click()">
                    <button type="button" class="btn btn-outline-danger btn-sm" style="padding: 6px 10px;" onclick="this.parentElement.remove()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                `;
                documentosListAba2.appendChild(docRow);
            });

            // Restaura documentos salvos no rascunho
            (window.INLINE_DOCUMENTOS_ABA2 || []).forEach(function(val) {
                if (!val) return;
                const docRow = document.createElement('div');
                docRow.style.cssText = 'display: flex; gap: 10px; margin-top: 10px; align-items: center;';
                docRow.innerHTML = `
                    <input type="file" style="display:none;">
                    <input type="text" name="documentos_aba2[]" value="${val.replace(/"/g, '&quot;')}" style="flex: 1; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; outline: none;">
                    <button type="button" class="btn btn-outline-danger btn-sm" style="padding: 6px 10px;" onclick="this.parentElement.remove()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                `;
                documentosListAba2.appendChild(docRow);
            });
        }
    });
</script>



      <!-- Botões Principais Empilhados -->
      <div style="display: flex; flex-direction: row; justify-content: center; gap: 15px; width: 100%; max-width: 50%; margin: 30px auto 0 auto; border-top: 1px solid #ccc; padding-top: 30px;">
          <button type="button" class="btn-action" style="width: 48%; font-size: 1.2em; padding: 16px; background-color: #64748b; border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease;" onclick="if(typeof window._saveDraft === 'function') window._saveDraft();">💾 Salvar Rascunho</button>
          <button type="submit" class="btn-action" style="width: 48%; font-size: 1.2em; padding: 16px; background-color: #0284c7; border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s ease;">💾 Salvar e Enviar</button>
      </div>

    </form>
    </fieldset>
  </div>

    <!-- Modal Aprovação Aba 2 -->
    <div id="modalAprovacaoAba2" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:4000; align-items:center; justify-content:center;">
        <div style="background:white; padding:30px; border-radius:12px; max-width:1000px; width:95%; box-shadow:0 10px 25px rgba(0,0,0,0.3); text-align:left; position:relative; border-top: 8px solid #28a745; max-height: 90vh; overflow-y: auto;">
            <button id="btnFecharModalAprovacao" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; color:#64748b;">&times;</button>
            <h3 style="color:#1e3a5f; margin-top:0; margin-bottom:15px; font-size:20px; font-weight:700;">Conferência e Aprovação - Aba 2</h3>
            <p style="font-size:0.9em; color:#64748b; margin-bottom:20px;">Por favor, revise o resumo dos dados abaixo e preencha sua manifestação.</p>
            
            <div id="containerRelatorioAprovacao" style="width: 100%; max-height: 400px; border: 1px solid #ccc; border-radius: 4px; overflow-y: auto; margin-bottom: 20px; padding: 20px; background: #fff;">
                <div id="loadingRelatorio" style="text-align: center; padding: 20px; font-weight: bold; color: #1e3a5f;">
                    Carregando resumo dos dados...
                </div>
                <div id="conteudoRelatorioAprovacao" style="display: none;" class="report-container">
                    <!-- O resumo será injetado dinamicamente via JS -->
                </div>
            </div>
            
            <div style="background: #f8fafc; padding: 20px; border-radius: 6px; border-left: 4px solid #1a7a4a; margin-bottom: 20px;">
                <h4 style="margin: 0 0 10px 0; color: #1e3a5f; font-size: 16px;">Declaração</h4>
                <p style="font-size: 14px; color: #334155; line-height: 1.5; margin-bottom: 15px; background: #fff; padding: 10px; border: 1px solid #e2e8f0; border-radius: 4px;">
                    Declaro que as informações consignadas neste formulário foram inseridas com base nos dados disponíveis nos sistemas oficiais, nos documentos constantes do processo e nas verificações realizadas no âmbito desta unidade, estando compatíveis com os elementos analisados.
                </p>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: bold; color: #1e3a5f; font-size: 15px; margin-bottom: 15px;">
                    <input type="checkbox" id="chkAprovarAba2" style="width: 20px; height: 20px; cursor: pointer;">
                    Estou de acordo e aprovo as informações apresentadas.
                </label>

                <div style="margin-top: 15px;">
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 5px; font-size: 14px;">Observações da Manifestação:</label>
                    <textarea id="txtObservacoesAba2" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; font-size: 14px; resize: vertical;" placeholder="Registre eventuais ressalvas, condicionantes, inconsistências identificadas ou orientações..."></textarea>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button id="btnCancelarAprovacao" style="padding: 10px 20px; background: #e2e8f0; color: #475569; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Voltar e Editar</button>
                <button id="btnConfirmarAprovacao" style="padding: 10px 20px; background: #166534; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;" disabled>✅ Concluir Manifestação</button>
            </div>
        </div>
    </div>

    <!-- Modal Incluir RIP Vinculado (Cadastro Mínimo) -->
    <div id="modalInserirRipVinculado" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:5000; align-items:center; justify-content:center;">
        <div style="background:white; padding:30px; border-radius:12px; max-width:600px; width:90%; box-shadow:0 10px 25px rgba(0,0,0,0.3); position:relative; border-top: 8px solid #1e3a5f;">
            <button id="btnFecharModalRipVinculado" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; color:#64748b;">&times;</button>
            <h2 style="margin-top:0; color:#1e3a5f; font-size:20px; text-align: left; margin-bottom: 20px;">Incluir RIP Vinculado</h2>

            <div style="margin-bottom: 20px; text-align: left;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e3a5f;">Número do RIP:</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="inputNumeroRipVinculado" style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;" placeholder="Digite o RIP...">
                    <button type="button" id="btnPesquisarRipVinculado" class="btn-inst btn-inst-primary" style="padding: 10px 20px; font-size: 0.9rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Pesquisar
                    </button>
                </div>
                <span id="errRipVinculadoNaoEncontrado" style="display:none; color:#dc2626; font-size:0.85em; margin-top:5px; font-weight:600;"></span>
            </div>

            <div id="dadosRipVinculadoPesquisado" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px; font-size: 0.9rem; color: #334155; text-align: left;">
                <h3 style="margin-top: 0; color: #1e3a5f; font-size: 16px; margin-bottom: 12px; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px;">Dados do Imóvel</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div style="grid-column: 1 / -1;"><strong>Endereço:</strong> <span id="ripVinculadoEndereco">-</span></div>
                    <div><strong>Bairro:</strong> <span id="ripVinculadoBairro">-</span></div>
                    <div><strong>CEP:</strong> <span id="ripVinculadoCep">-</span></div>
                    <div><strong>Município:</strong> <span id="ripVinculadoMunicipio">-</span></div>
                    <div><strong>UF:</strong> <span id="ripVinculadoUf">-</span></div>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; margin-bottom: 20px; text-align: left;">
                <h4 style="margin-top: 0; color: #1e3a5f; margin-bottom: 15px; font-size: 15px; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px;">Destinação de Área</h4>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #1e293b; font-size: 14px;">Qual a área do terreno a ser destinada?</label>
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <label style="cursor: pointer;"><input type="radio" name="destinacao_terreno_rip_vinculado" value="Integral" checked> Integral</label>
                        <label style="cursor: pointer;"><input type="radio" name="destinacao_terreno_rip_vinculado" value="Parcial"> Parcial</label>

                        <div id="containerAreaTerrenoParcialRipVinculado" style="display: none; align-items: center; gap: 8px; margin-left: 10px;">
                            <label style="font-size: 13px; color: #475569;">Metragem:</label>
                            <input type="number" id="modalAreaTerrenoRipVinculado" placeholder="Ex: 500" style="width: 120px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; outline: none;"> <span style="color: #64748b;">m²</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #1e293b; font-size: 14px;">Qual a área construída a ser destinada?</label>
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <label style="cursor: pointer;"><input type="radio" name="destinacao_imovel_rip_vinculado" value="Integral" checked> Integral</label>
                        <label style="cursor: pointer;"><input type="radio" name="destinacao_imovel_rip_vinculado" value="Parcial"> Parcial</label>

                        <div id="containerAreaImovelParcialRipVinculado" style="display: none; align-items: center; gap: 8px; margin-left: 10px;">
                            <label style="font-size: 13px; color: #475569;">Metragem:</label>
                            <input type="number" id="modalAreaImovelRipVinculado" placeholder="Ex: 150" style="width: 120px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; outline: none;"> <span style="color: #64748b;">m²</span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; gap: 10px;">
                <button type="button" id="btnCancelarRipVinculado" class="btn-inst btn-inst-outline" style="flex: 1;">Cancelar</button>
                <button type="button" id="btnMaisRipVinculado" class="btn-inst btn-inst-outline" style="flex: 1.5; font-size: 0.85rem; padding: 10px 5px;">Inserir + 1 RIP</button>
                <button type="button" id="btnSalvarRipVinculado" class="btn-inst btn-inst-primary" style="flex: 1;">Inserir</button>
            </div>
        </div>
    </div>

    <script>
    function initModalRipVinculado() {
        const modalRipVinculado = document.getElementById('modalInserirRipVinculado');
        const btnFecharModalRipVinculado = document.getElementById('btnFecharModalRipVinculado');
        const btnCancelarRipVinculado = document.getElementById('btnCancelarRipVinculado');
        const btnSalvarRipVinculado = document.getElementById('btnSalvarRipVinculado');
        const btnMaisRipVinculado = document.getElementById('btnMaisRipVinculado');
        const btnPesquisarRipVinculado = document.getElementById('btnPesquisarRipVinculado');
        const inputNumeroRipVinculado = document.getElementById('inputNumeroRipVinculado');
        if (!modalRipVinculado) return;

        window.__cadastroRipVinculadoAlvo = null;

        function mostrarErroRipVinculado(msg) {
            const el = document.getElementById('errRipVinculadoNaoEncontrado');
            if (el) { el.textContent = msg; el.style.display = 'block'; }
        }
        function limparErroRipVinculado() {
            const el = document.getElementById('errRipVinculadoNaoEncontrado');
            if (el) el.style.display = 'none';
        }
        function fecharModalRipVinculado() {
            if (modalRipVinculado) modalRipVinculado.style.display = 'none';
            const dadosBox = document.getElementById('dadosRipVinculadoPesquisado');
            if (dadosBox) dadosBox.style.display = 'none';
            limparErroRipVinculado();
        }

        window.abrirModalRipVinculado = function(alvo) {
            window.__cadastroRipVinculadoAlvo = alvo;
            if (modalRipVinculado) modalRipVinculado.style.display = 'flex';
            if (inputNumeroRipVinculado) { inputNumeroRipVinculado.value = ''; inputNumeroRipVinculado.style.borderColor = ''; }
            limparErroRipVinculado();
            const dadosBox = document.getElementById('dadosRipVinculadoPesquisado');
            if (dadosBox) dadosBox.style.display = 'none';
        };

        function obterDestinacoesRipVinculado() {
            const destTerreno = document.querySelector('input[name="destinacao_terreno_rip_vinculado"]:checked')?.value || 'Integral';
            const areaTerreno = destTerreno === 'Parcial' ? (document.getElementById('modalAreaTerrenoRipVinculado')?.value || '') : '';
            const destImovel = document.querySelector('input[name="destinacao_imovel_rip_vinculado"]:checked')?.value || 'Integral';
            const areaImovel = destImovel === 'Parcial' ? (document.getElementById('modalAreaImovelRipVinculado')?.value || '') : '';
            return { destTerreno, areaTerreno, destImovel, areaImovel };
        }

        function resetarDestinacoesRipVinculado() {
            document.querySelectorAll('input[name="destinacao_terreno_rip_vinculado"]').forEach((r, idx) => r.checked = idx === 0);
            document.querySelectorAll('input[name="destinacao_imovel_rip_vinculado"]').forEach((r, idx) => r.checked = idx === 0);
            if (document.getElementById('modalAreaTerrenoRipVinculado')) document.getElementById('modalAreaTerrenoRipVinculado').value = '';
            if (document.getElementById('modalAreaImovelRipVinculado')) document.getElementById('modalAreaImovelRipVinculado').value = '';
            if (document.getElementById('containerAreaTerrenoParcialRipVinculado')) document.getElementById('containerAreaTerrenoParcialRipVinculado').style.display = 'none';
            if (document.getElementById('containerAreaImovelParcialRipVinculado')) document.getElementById('containerAreaImovelParcialRipVinculado').style.display = 'none';
        }

        function adicionarRipVinculadoNaLista(rip, spuData, destT, areaT, destI, areaI) {
            const alvo = window.__cadastroRipVinculadoAlvo;
            if (!alvo) return;
            let containerId = 'rips-vinculados-cad-' + alvo;
            let focoCadastroMinimoId = alvo;
            if (String(alvo).indexOf('js-') === 0) {
                containerId = 'rips-vinculados-cad-' + alvo;
                focoCadastroMinimoId = null;
            }
            const container = document.getElementById(containerId);
            if (!container) return;

            const ripObj = {
                numero_rip: rip,
                foco_cadastro_minimo_id: focoCadastroMinimoId,
                destinacao_terreno: destT,
                area_terreno_parcial: areaT,
                destinacao_imovel: destI,
                area_imovel_parcial: areaI
            };
            const dadosJsonStr = JSON.stringify(ripObj);
            const dadosJsonEscaped = dadosJsonStr.replace(/"/g, '&quot;');

            const div = document.createElement('div');
            div.className = 'rip-vinculado-item';
            div.style.cssText = 'background:#eff6ff;border:1px solid #bfdbfe;padding:8px 12px;border-radius:6px;font-size:0.85rem;';
            div.innerHTML = `
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                    <div style="flex:1;"><strong style="color:#1d4ed8;">🔗 RIP Vinculado:</strong> ${rip}</div>
                    <span style="cursor:pointer;color:#ef4444;font-size:18px;font-weight:bold;" onclick="window.removerRipVinculado(this, '${dadosJsonEscaped}')">&times;</span>
                </div>
                <div style="margin-top:4px;color:#334155;">
                    <div><strong>Destinação do terreno:</strong> ${destT}${destT === 'Parcial' && areaT ? ' — ' + areaT + ' m²' : ''}</div>
                    <div><strong>Destinação do imóvel:</strong> ${destI}${destI === 'Parcial' && areaI ? ' — ' + areaI + ' m²' : ''}</div>
                </div>
            `;
            container.appendChild(div);

            const formHidden = document.getElementById('hidden-rips-vinculados');
            const inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = 'rips_vinculados[]';
            inputHidden.value = dadosJsonStr;
            if (formHidden) formHidden.appendChild(inputHidden);
            else container.appendChild(inputHidden);
        }

        window.removerRipVinculado = function(el, jsonStr) {
            const item = el.closest('.rip-vinculado-item');
            if (item) item.remove();
            const formHidden = document.getElementById('hidden-rips-vinculados');
            const scope = formHidden || document;
            const all = scope.querySelectorAll('input[name="rips_vinculados[]"]');
            all.forEach(function(inp) {
                if (inp.value === jsonStr) inp.remove();
            });
        };

        if (btnFecharModalRipVinculado) btnFecharModalRipVinculado.addEventListener('click', fecharModalRipVinculado);
        if (btnCancelarRipVinculado) btnCancelarRipVinculado.addEventListener('click', fecharModalRipVinculado);
        if (inputNumeroRipVinculado) inputNumeroRipVinculado.addEventListener('input', limparErroRipVinculado);

        document.querySelectorAll('input[name="destinacao_terreno_rip_vinculado"]').forEach(function(r) {
            r.addEventListener('change', function() {
                const container = document.getElementById('containerAreaTerrenoParcialRipVinculado');
                if (container) container.style.display = (r.value === 'Parcial') ? 'flex' : 'none';
            });
        });
        document.querySelectorAll('input[name="destinacao_imovel_rip_vinculado"]').forEach(function(r) {
            r.addEventListener('change', function() {
                const container = document.getElementById('containerAreaImovelParcialRipVinculado');
                if (container) container.style.display = (r.value === 'Parcial') ? 'flex' : 'none';
            });
        });

        if (btnPesquisarRipVinculado) {
            btnPesquisarRipVinculado.addEventListener('click', async () => {
                limparErroRipVinculado();
                const rip = inputNumeroRipVinculado ? inputNumeroRipVinculado.value.trim() : '';
                if (rip === '') return;
                const originalText = btnPesquisarRipVinculado.innerHTML;
                btnPesquisarRipVinculado.innerHTML = 'Pesquisando...';
                btnPesquisarRipVinculado.disabled = true;
                const spuData = await window.fetchSPU(rip);
                const existe = spuData && (spuData.numero_rip || spuData.cep);
                btnPesquisarRipVinculado.innerHTML = originalText;
                btnPesquisarRipVinculado.disabled = false;
                const dadosBox = document.getElementById('dadosRipVinculadoPesquisado');
                if (!existe) {
                    mostrarErroRipVinculado('RIP não encontrado na tabela_spu!');
                    if (inputNumeroRipVinculado) inputNumeroRipVinculado.style.borderColor = '#dc2626';
                    if (dadosBox) dadosBox.style.display = 'none';
                    return;
                }
                if (inputNumeroRipVinculado) inputNumeroRipVinculado.style.borderColor = '#22c55e';
                if (dadosBox) {
                    dadosBox.style.display = 'block';
                    document.getElementById('ripVinculadoEndereco').textContent = spuData.logradouro || '-';
                    document.getElementById('ripVinculadoCep').textContent = spuData.cep || '-';
                    document.getElementById('ripVinculadoBairro').textContent = spuData.bairro || '-';
                    document.getElementById('ripVinculadoMunicipio').textContent = spuData.municipio || '-';
                    document.getElementById('ripVinculadoUf').textContent = spuData.uf || '-';
                }
            });
        }

        async function inserirRipVinculado(manterAberto) {
            limparErroRipVinculado();
            const rip = inputNumeroRipVinculado ? inputNumeroRipVinculado.value.trim() : '';
            if (rip === '') return;
            const spuData = await window.fetchSPU(rip);
            const existe = spuData && (spuData.numero_rip || spuData.cep);
            if (!existe) {
                mostrarErroRipVinculado('RIP não encontrado na tabela_spu!');
                if (inputNumeroRipVinculado) inputNumeroRipVinculado.style.borderColor = '#dc2626';
                return;
            }
            if (inputNumeroRipVinculado) inputNumeroRipVinculado.style.borderColor = '';
            const { destTerreno, areaTerreno, destImovel, areaImovel } = obterDestinacoesRipVinculado();
            adicionarRipVinculadoNaLista(rip, spuData, destTerreno, areaTerreno, destImovel, areaImovel);
            resetarDestinacoesRipVinculado();
            if (manterAberto) {
                if (inputNumeroRipVinculado) { inputNumeroRipVinculado.value = ''; inputNumeroRipVinculado.focus(); }
                const dadosBox = document.getElementById('dadosRipVinculadoPesquisado');
                if (dadosBox) dadosBox.style.display = 'none';
            } else {
                fecharModalRipVinculado();
            }
        }

        if (btnSalvarRipVinculado) btnSalvarRipVinculado.addEventListener('click', () => inserirRipVinculado(false));
        if (btnMaisRipVinculado) btnMaisRipVinculado.addEventListener('click', () => inserirRipVinculado(true));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModalRipVinculado);
    } else {
        initModalRipVinculado();
    }
    document.addEventListener('htmx:afterSwap', function(e) {
        if (e.target && e.target.id === 'aba2-container') initModalRipVinculado();
    });
    </script>

