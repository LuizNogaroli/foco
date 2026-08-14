{{-- Box Coordenação-Geral - Formulário Próprio --}}

<div class="decl-section-title" style="margin-top:0; border-bottom: none;">Manifestação:</div>

<div style="margin-bottom: 12px; font-size: 14px;">
    Com base na análise técnica e nas informações apresentadas, entendo que:
</div>

<div class="decl-opcao-btns">
    <input type="radio" name="decl_{{ $chave }}_opcao" id="decl-{{ $chave }}-radio1" value="favoravel" {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'favoravel' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio1">
        <span class="opcao-icone">✔</span>
        <span>A proposta reúne elementos suficientes para apreciação da Comissão de Destinações Especiais (CDE).</span>
    </label>
</div>

<div class="decl-opcao-btns">
    <input type="radio" name="decl_{{ $chave }}_opcao" id="decl-{{ $chave }}-radio2" value="favoravel_condicionantes" {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'favoravel_condicionantes' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio2">
        <span class="opcao-icone">✔</span>
        <span>A proposta reúne elementos suficientes para apreciação da Comissão de Destinações Especiais (CDE) com condicionantes, conforme abaixo:</span>
    </label>
</div>

<div class="decl-obs-group" id="bloco-{{ $chave }}-condicionantes" style="display: {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'favoravel_condicionantes' ? 'block' : 'none' }};">
    <label for="obs-{{ $chave }}-condicionantes">Condicionantes:</label>
    <textarea id="obs-{{ $chave }}-condicionantes" name="obs_{{ $chave }}_condicionantes" placeholder="• Condicionante 1&#10;• Condicionante 2&#10;• Condicionante n">{{ $dados['obs_'. $chave .'_condicionantes'] ?? '' }}</textarea>
</div>

<div class="decl-opcao-btns">
    <input type="radio" name="decl_{{ $chave }}_opcao" id="decl-{{ $chave }}-radio3" value="nao_favoravel" {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'nao_favoravel' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio3">
        <span class="opcao-icone">✔</span>
        <span>A proposta demanda complementação antes de eventual submissão à CDE.</span>
    </label>
</div>

<div class="decl-section-title">Conclusão:</div>
<div class="decl-opcao-btns">
    <input type="radio" name="decl_{{ $chave }}_conclusao" id="decl-{{ $chave }}-radio4" value="apta_cde" {{ ($dados['decl_'. $chave .'_conclusao'] ?? '') == 'apta_cde' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio4">
        <span class="opcao-icone">✔</span>
        <span>A proposta está apta para apreciação pela Comissão de Destinações Especiais (CDE).</span>
    </label>

    <input type="radio" name="decl_{{ $chave }}_conclusao" id="decl-{{ $chave }}-radio5" value="inapta_cde" {{ ($dados['decl_'. $chave .'_conclusao'] ?? '') == 'inapta_cde' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio5">
        <span class="opcao-icone">✔</span>
        <span>A proposta não está apta para apreciação pela CDE, devendo retornar à unidade de origem para complementação.</span>
    </label>
</div>

<script>
    document.querySelectorAll('input[name="decl_{{ $chave }}_opcao"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const condsDiv = document.getElementById('bloco-{{ $chave }}-condicionantes');
            if (this.value === 'favoravel_condicionantes') {
                condsDiv.style.display = 'block';
            } else {
                condsDiv.style.display = 'none';
            }
        });
    });
</script>
