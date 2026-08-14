{{-- Box Coordenação - Formulário Próprio --}}

<div class="decl-section-title" style="margin-top:0; border-bottom: none;">Manifestação:</div>

<div class="decl-opcao-btns">
    <input type="radio" name="decl_{{ $chave }}_opcao" id="decl-{{ $chave }}-radio1" value="suficiente" {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'suficiente' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio1">
        <span class="opcao-icone">✔</span>
        <span>Os elementos constantes do formulário são suficientes para apreciação dos aspectos de mérito da destinação proposta.</span>
    </label>

    <input type="radio" name="decl_{{ $chave }}_opcao" id="decl-{{ $chave }}-radio2" value="insuficiente" {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'insuficiente' ? 'checked' : '' }}>
    <label class="decl-opcao-btn-label" for="decl-{{ $chave }}-radio2">
        <span class="opcao-icone">✔</span>
        <span><strong>NÃO</strong> há elementos suficientes para apreciação dos aspectos de mérito da destinação proposta, sendo necessária a complementação das informações.</span>
    </label>
</div>

<div class="decl-obs-group" id="bloco-{{ $chave }}-obs" style="display: {{ ($dados['decl_'. $chave .'_opcao'] ?? '') == 'insuficiente' ? 'block' : 'none' }};">
    <label for="obs-{{ $chave }}">Observações:</label>
    <textarea id="obs-{{ $chave }}" name="obs_{{ $chave }}" placeholder="Registre eventuais ressalvas, inconsistências identificadas ou orientações para complementação da proposta.">{{ $dados['obs_'. $chave .''] ?? '' }}</textarea>
</div>

<script>
    document.querySelectorAll('input[name="decl_{{ $chave }}_opcao"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const obsDiv = document.getElementById('bloco-{{ $chave }}-obs');
            if (this.value === 'insuficiente') {
                obsDiv.style.display = 'block';
            } else {
                obsDiv.style.display = 'none';
            }
        });
    });
</script>
