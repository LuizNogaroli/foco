{{-- Box Direção - Formulário Próprio --}}

<div class="decl-section-title" style="margin-top:0; border-bottom: none;">Manifestação:</div>

<div style="margin-bottom: 12px; font-size: 14px;">
    Com base nas informações prestadas no formulário e na análise anterior, considero:
</div>

<div class="decl-opcao-btns">
    <input type="radio" name="decl_{{ $chave }}_opcao" id="decl-{{ $chave }}-radio1" value="apta_cde" {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'apta_cde' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio1">
        <span class="opcao-icone">✔</span>
        <span>A Proposta apta para submissão à Comissão de Destinações Especiais (CDE).</span>
    </label>
</div>

<div class="decl-opcao-btns">
    <input type="radio" name="decl_{{ $chave }}_opcao" id="decl-{{ $chave }}-radio2" value="restituir_spuf" {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'restituir_spuf' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio2">
        <span class="opcao-icone">✔</span>
        <span>Necessário restituir o processo à SPU/UF para complementação.</span>
    </label>
</div>

<div class="decl-opcao-btns">
    <input type="radio" name="decl_{{ $chave }}_opcao" id="decl-{{ $chave }}-radio3" value="diligencia" {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'diligencia' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio3">
        <span class="opcao-icone">✔</span>
        <span>Necessário diligência específica antes da submissão ao CDE.</span>
    </label>
</div>

<div style="margin-top: 16px; font-size: 14px; color: #374151;">
    Encaminhe-se conforme deliberado
</div>
