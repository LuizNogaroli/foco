{{-- Box Superintendência - Formulário Próprio --}}

{{-- Regime de destinação --}}
<div style="margin-bottom: 20px;">
    <div class="decl-section-title">Regime de destinação</div>
    <div style="background: #e0f2fe; padding: 10px; border-radius: 6px; margin-bottom: 10px; font-size: 13px;">
        ℹ Regime proposto pelo analista: <strong>{{ $dados3['regime_destinacao'] ?? ($dados3['proposta_destinacao']['regime_destinacao'] ?? 'Não informado') }}</strong>
    </div>
    <div class="decl-opcao-btns">
        <input type="radio" name="sup_regime_concorda" id="sup-reg-sim" value="sim" {{ ($dados['sup_regime_concorda'] ?? '') == 'sim' ? 'checked' : '' }} onchange="toggleRegimeDropdown()">
        <label class="decl-opcao-btn-label" for="sup-reg-sim"><span class="opcao-icone">✔</span><span>Concordo.</span></label>

        <input type="radio" name="sup_regime_concorda" id="sup-reg-nao" value="nao" {{ ($dados['sup_regime_concorda'] ?? '') == 'nao' ? 'checked' : '' }} onchange="toggleRegimeDropdown()">
        <label class="decl-opcao-btn-label" for="sup-reg-nao"><span class="opcao-icone">✔</span><span>Não concordo.</span></label>
    </div>

    <div id="div-regime-sugerido" style="display: {{ ($dados['sup_regime_concorda'] ?? '') == 'nao' ? 'block' : 'none' }}; margin-top: 10px;">
        <label class="decl-sublabel">Selecione o regime de destinação desejado:</label>
        <select name="sup_regime_novo" class="form-control" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e1;">
            <option value="">Selecione...</option>
            <option value="Aforamento" {{ ($dados['sup_regime_novo'] ?? '') == 'Aforamento' ? 'selected' : '' }}>Aforamento</option>
            <option value="Alienação/Venda" {{ ($dados['sup_regime_novo'] ?? '') == 'Alienação/Venda' ? 'selected' : '' }}>Alienação/Venda</option>
            <option value="Cessão de Direito Real de Uso (CDRU)" {{ ($dados['sup_regime_novo'] ?? '') == 'Cessão de Direito Real de Uso (CDRU)' ? 'selected' : '' }}>Cessão de Direito Real de Uso (CDRU)</option>
            <option value="Cessão de Uso em Condições Especiais (CUCE)" {{ ($dados['sup_regime_novo'] ?? '') == 'Cessão de Uso em Condições Especiais (CUCE)' ? 'selected' : '' }}>Cessão de Uso em Condições Especiais (CUCE)</option>
            <option value="Cessão de Uso Gratuita" {{ ($dados['sup_regime_novo'] ?? '') == 'Cessão de Uso Gratuita' ? 'selected' : '' }}>Cessão de Uso Gratuita</option>
            <option value="Cessão de Uso Onerosa" {{ ($dados['sup_regime_novo'] ?? '') == 'Cessão de Uso Onerosa' ? 'selected' : '' }}>Cessão de Uso Onerosa</option>
            <option value="Concessão de Direito Real de Uso (CDRU)" {{ ($dados['sup_regime_novo'] ?? '') == 'Concessão de Direito Real de Uso (CDRU)' ? 'selected' : '' }}>Concessão de Direito Real de Uso (CDRU)</option>
            <option value="Doação" {{ ($dados['sup_regime_novo'] ?? '') == 'Doação' ? 'selected' : '' }}>Doação</option>
            <option value="Entrega" {{ ($dados['sup_regime_novo'] ?? '') == 'Entrega' ? 'selected' : '' }}>Entrega</option>
            <option value="Guarda Provisória" {{ ($dados['sup_regime_novo'] ?? '') == 'Guarda Provisória' ? 'selected' : '' }}>Guarda Provisória</option>
            <option value="Inscrição de Ocupação" {{ ($dados['sup_regime_novo'] ?? '') == 'Inscrição de Ocupação' ? 'selected' : '' }}>Inscrição de Ocupação</option>
            <option value="Permissão de Uso" {{ ($dados['sup_regime_novo'] ?? '') == 'Permissão de Uso' ? 'selected' : '' }}>Permissão de Uso</option>
            <option value="Termo de Autorização de Uso (TAU)" {{ ($dados['sup_regime_novo'] ?? '') == 'Termo de Autorização de Uso (TAU)' ? 'selected' : '' }}>Termo de Autorização de Uso (TAU)</option>
        </select>
    </div>
</div>

{{-- Deliberação --}}
<div style="margin-bottom: 20px;">
    <div class="decl-section-title">Deliberação</div>
    <div class="decl-opcao-btns">
        <input type="radio" name="sup_deliberacao" id="sup-delib-1" value="favoravel" {{ ($dados['sup_deliberacao'] ?? '') == 'favoravel' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="sup-delib-1"><span class="opcao-icone">✔</span><span>Favorável à proposta.</span></label>

        <input type="radio" name="sup_deliberacao" id="sup-delib-2" value="favoravel_ressalvas" {{ ($dados['sup_deliberacao'] ?? '') == 'favoravel_ressalvas' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="sup-delib-2"><span class="opcao-icone">✔</span><span>Favorável, com ressalvas.</span></label>

        <input type="radio" name="sup_deliberacao" id="sup-delib-3" value="complementacao" {{ ($dados['sup_deliberacao'] ?? '') == 'complementacao' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="sup-delib-3"><span class="opcao-icone">✔</span><span>Necessária complementação de informações/documentos.</span></label>

        <input type="radio" name="sup_deliberacao" id="sup-delib-4" value="cancelamento" {{ ($dados['sup_deliberacao'] ?? '') == 'cancelamento' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="sup-delib-4"><span class="opcao-icone">✔</span><span>Cancelamento.</span></label>
    </div>
</div>

<div class="decl-obs-group" style="margin-bottom: 20px;">
    <label for="obs_superintendencia">Observações:</label>
    <textarea id="obs_superintendencia" name="obs_superintendencia" placeholder="Registre eventuais ressalvas, condicionantes ou orientações. (Obrigatório em caso de Complementação, Cancelamento ou Novo Regime)">{{ $dados['obs_superintendencia'] ?? '' }}</textarea>
</div>

{{-- Competência --}}
<div style="margin-bottom: 20px; border-top: 1px dashed #bae6fd; padding-top: 15px;">
    <div class="decl-section-title" style="margin-top:0; border-bottom: none;">O processo deve ser submetido à CDE?</div>
    <div class="decl-opcao-btns">
        <input type="radio" name="sup_competencia" id="sup-comp-1" value="nao" {{ ($dados['sup_competencia'] ?? '') == 'nao' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="sup-comp-1"><span class="opcao-icone">✔</span><span>Não.</span></label>

        <input type="radio" name="sup_competencia" id="sup-comp-2" value="sim" {{ ($dados['sup_competencia'] ?? '') == 'sim' ? 'checked' : '' }}>
        <label class="decl-opcao-btn-label" for="sup-comp-2"><span class="opcao-icone">✔</span><span>Sim.</span></label>
    </div>
</div>
