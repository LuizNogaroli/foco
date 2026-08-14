{{-- Box CDE - Formulário Próprio --}}

{{-- Regime de Destinação --}}
<div style="margin-bottom: 20px;">
    <div class="decl-section-title">Regime de Destinação</div>
    <div style="background: #e0f2fe; padding: 10px; border-radius: 6px; margin-bottom: 10px; font-size: 13px;">
        ℹ Regime proposto pelo analista: <strong>{{ $dados3['regime_destinacao'] ?? ($dados3['proposta_destinacao']['regime_destinacao'] ?? 'Não informado') }}</strong>
    </div>
    <div class="decl-opcao-btns">
        <input type="radio" name="cde_regime" id="cde-reg-1" value="manter" {{ ($dados['cde_regime'] ?? '') == 'manter' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="cde-reg-1"><span class="opcao-icone">✔</span><span>Manter regime proposto</span></label>

        <input type="radio" name="cde_regime" id="cde-reg-2" value="alterar" {{ ($dados['cde_regime'] ?? '') == 'alterar' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="cde-reg-2"><span class="opcao-icone">✔</span><span>Alterar regime</span></label>
    </div>
</div>

{{-- Deliberação Final --}}
<div style="margin-bottom: 20px;">
    <div class="decl-section-title">Deliberação Final</div>
    <div class="decl-opcao-btns">
        <input type="radio" name="cde_deliberacao" id="cde-delib-1" value="aprovar" {{ ($dados['cde_deliberacao'] ?? '') == 'aprovar' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="cde-delib-1"><span class="opcao-icone">✔</span><span>Aprovar a proposta, nos termos apresentados.</span></label>

        <input type="radio" name="cde_deliberacao" id="cde-delib-2" value="indeferir" {{ ($dados['cde_deliberacao'] ?? '') == 'indeferir' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="cde-delib-2"><span class="opcao-icone">✔</span><span>Indeferir a proposta.</span></label>
    </div>
</div>

{{-- Competência --}}
<div style="margin-bottom: 20px;">
    <div class="decl-section-title">Processo de competência da Superintendência ou da CDE?</div>
    <div class="decl-opcao-btns">
        <input type="radio" name="competencia_cde" id="cde-comp-1" value="superintendencia" {{ ($dados['competencia_cde'] ?? '') == 'superintendencia' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="cde-comp-1"><span class="opcao-icone">✔</span><span>Competência da Superintendência</span></label>

        <input type="radio" name="competencia_cde" id="cde-comp-2" value="cde" {{ ($dados['competencia_cde'] ?? '') == 'cde' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="cde-comp-2"><span class="opcao-icone">✔</span><span>Competência da CDE</span></label>
    </div>
</div>

<div class="decl-obs-group" style="margin-bottom: 20px;">
    <label for="obs_cde">Observações/Condicionantes:</label>
    <textarea id="obs_cde" name="obs_cde" placeholder="Descreva as condicionantes ou ressalvas...">{{ $dados['obs_cde'] ?? '' }}</textarea>
</div>
