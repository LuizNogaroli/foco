{{-- Box Equipe C.G. - Checklist + Manifestação --}}

<div class="decl-section-title" style="margin-top:0; border-bottom: none;">Conformidade Prévia</div>

@php
    $checklist = [
        'Documental' => [
            '1' => 'Documentos de identificação (PF/PJ) anexados e válidos?',
            '2' => 'Se PJ, documento de designação do representante legal anexado e válido?',
            '3' => 'Se estrangeiro ou PJ com capital estrangeiro, indicado a necessidade de autorização da Secretária ou Ministra?',
            '4' => 'Prioridade legal (se aplicável) devidamente comprovada?',
            '5' => 'Conformidade Atendida?'
        ],
        'Imóvel' => [
            '7' => 'Áreas informadas (total, União, destinada) consistentes com o requerimento, SPUnet/SIAPA e documentos anexados?',
            '8' => 'Geolocalização informada compativel com os documentos apresentados?',
            '9' => 'Situação de ocupação da área apontada?',
            '10' => 'Matrícula registral atualizada (se existente) anexada?',
            '11' => 'Benfeitorias declaradas e documentadas?',
            '12' => 'Há passivos jurídicos, ambientais ou administrativos sobre o imóvel?',
            '13' => 'Conformidade Atendida?'
        ],
        'Regime e Uso' => [
            '15' => 'Regime selecionado compatível com a natureza do interessado e uso pretendido?',
            '17' => 'Tipo de uso imobiliário e específico compatível com a finalidade declarada?',
            '18' => 'Recursos financeiros para implantação declarados?',
            '19' => 'Interesse Público e Social demonstrado?',
            '20' => 'Conformidade Atendida?'
        ]
    ];
@endphp

@foreach($checklist as $secao => $itens)
    <div style="margin-bottom: 20px;">
        <div class="decl-section-title" style="font-size: 1.1em; color: #1e3a5f;">{{ $secao }}</div>
        @foreach($itens as $id => $pergunta)
            <div class="checklist-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; border-bottom: 1px solid #f1f5f9; padding: 5px; border-radius: 4px; transition: background-color 0.2s;">
                <label style="font-weight: 500; font-size: 0.95em; margin: 0;">{{ $id }}. {{ $pergunta }}</label>
                <div style="display: flex; gap: 15px;">
                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; margin: 0; padding: 2px 6px; border-radius: 4px; {{ ($dados['chk_' . $id] ?? '') == 'Sim' ? 'background: #dcfce7; font-weight: bold; color: #166534;' : '' }}">
                        <input type="radio" name="chk_{{ $id }}" value="Sim" {{ ($dados['chk_' . $id] ?? '') == 'Sim' ? 'checked' : '' }}> Sim
                    </label>
                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; margin: 0; padding: 2px 6px; border-radius: 4px; {{ ($dados['chk_' . $id] ?? '') == 'Não' ? 'background: #fee2e2; font-weight: bold; color: #991b1b;' : '' }}">
                        <input type="radio" name="chk_{{ $id }}" value="Não" {{ ($dados['chk_' . $id] ?? '') == 'Não' ? 'checked' : '' }}> Não
                    </label>
                </div>
            </div>
        @endforeach
        <style>
            .checklist-row:hover { background-color: #f1f5f9; }
        </style>
        <div class="decl-obs-group">
            <label for="obs_chk_{{ strtolower(str_replace(' ', '_', $secao)) }}">Observações / Justificativa:</label>
            <textarea id="obs_chk_{{ strtolower(str_replace(' ', '_', $secao)) }}" name="obs_chk_{{ strtolower(str_replace(' ', '_', $secao)) }}" placeholder="Observações...">{{ $dados['obs_chk_' . strtolower(str_replace(' ', '_', $secao))] ?? '' }}</textarea>
        </div>
    </div>
@endforeach

<div style="margin-bottom: 20px;">
    <div class="decl-section-title" style="font-size: 1.1em; color: #1e3a5f;">Manifestação:</div>

    <div class="decl-opcao-btns">
        <input type="radio" name="decl_equipe_cg_opcao" id="decl-equipe-cg-radio1" value="favoravel" {{ ($dados['decl_equipe_cg_opcao'] ?? '') == 'favoravel' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="decl-equipe-cg-radio1">
            <span class="opcao-icone">✔</span>
            <span>As informações sustentam deliberação favorável.</span>
        </label>

        <input type="radio" name="decl_equipe_cg_opcao" id="decl-equipe-cg-radio2" value="favoravel_condicionantes" {{ ($dados['decl_equipe_cg_opcao'] ?? '') == 'favoravel_condicionantes' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="decl-equipe-cg-radio2">
            <span class="opcao-icone">✔</span>
            <span>As informações sustentam deliberação com condicionantes.</span>
        </label>
    </div>

    <div class="decl-obs-group" id="bloco-equipe-cg-condicionantes" style="display: {{ ($dados['decl_equipe_cg_opcao'] ?? '') == 'favoravel_condicionantes' ? 'block' : 'none' }};">
        <label for="obs-equipe-cg-condicionantes">Condicionantes:</label>
        <textarea id="obs-equipe-cg-condicionantes" name="obs_equipe_cg_condicionantes" placeholder="XXX campo texto XXX">{{ $dados['obs_equipe_cg_condicionantes'] ?? '' }}</textarea>
    </div>

    <div class="decl-opcao-btns">
        <input type="radio" name="decl_equipe_cg_opcao" id="decl-equipe-cg-radio3" value="nao_favoravel" {{ ($dados['decl_equipe_cg_opcao'] ?? '') == 'nao_favoravel' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="decl-equipe-cg-radio3">
            <span class="opcao-icone">✔</span>
            <span>As informações não sustentam deliberação favorável no momento.</span>
        </label>
    </div>

    <div class="decl-section-title">Conclusão:</div>
    <div class="decl-opcao-btns">
        <input type="radio" name="decl_equipe_cg_conclusao" id="decl-equipe-cg-radio4" value="apta_cde" {{ ($dados['decl_equipe_cg_conclusao'] ?? '') == 'apta_cde' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="decl-equipe-cg-radio4">
            <span class="opcao-icone">✔</span>
            <span>A proposta está apta para apreciação pela Comissão de Destinações Especiais (CDE).</span>
        </label>

        <input type="radio" name="decl_equipe_cg_conclusao" id="decl-equipe-cg-radio5" value="inapta_cde" {{ ($dados['decl_equipe_cg_conclusao'] ?? '') == 'inapta_cde' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="decl-equipe-cg-radio5">
            <span class="opcao-icone">✔</span>
            <span>A proposta não está apta para apreciação pela CDE, devendo retornar à unidade de origem para complementação.</span>
        </label>
    </div>

    <script>
        document.querySelectorAll('input[name="decl_equipe_cg_opcao"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const condsDiv = document.getElementById('bloco-equipe-cg-condicionantes');
                if (this.value === 'favoravel_condicionantes') {
                    condsDiv.style.display = 'block';
                } else {
                    condsDiv.style.display = 'none';
                }
            });
        });
    </script>
</div>
