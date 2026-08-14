<link rel="stylesheet" href="{{ asset('css/report.css') }}">
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
.decl-assinado-overlay { display: none; background: #f0fdf4; border: 1px solid #86efac; border-radius: 5px; padding: 14px 16px; margin-top: 16px; font-size: 0.91em; color: #15803d; line-height: 1.7; }
.decl-assinado-overlay.visivel { display: block; }
.decl-info-nota { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 5px; padding: 10px 14px; font-size: 0.88em; color: #1e40af; margin-bottom: 14px; line-height: 1.5; }
.secao-ativa { border: 2px solid #2563eb !important; }
.secao-ativa .acordeao-header { background: #dbeafe !important; color: #1e3a5f !important; }
.secao-ativa .acordeao-titulo { color: #1e3a5f !important; }
.secao-ativa .acordeao-seta { color: #1e3a5f !important; }
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
.decl-sublabel { font-weight: 600; font-size: 0.92em; color: #1e3a5f; margin: 16px 0 4px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
.decl-bloco-condicional { display: none; }
.decl-bloco-condicional.visivel { display: block; }
.acordeao-badge-ok {
  display: none;
  align-items: center;
  gap: 4px;
  background: #15803d;
  color: #ffffff;
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
</style>

<div class="form-container">
    <h2>Manifestações</h2>

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


    {{-- RESUMO DAS ABAS ANTERIORES --}}
    <div style="margin-bottom: 30px;">
      <h3 style="color: #1e3a5f; margin-bottom: 20px; font-weight: 700;">Resumo dos Dados do Processo</h3>

      @if(!empty($dados1) || !empty($dados2) || !empty($dados3))
        @if(!empty($dados1))
        <div class="acordeao-wrapper">
          <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="background: #1e3a5f;">
            <div class="acordeao-titulo">📋 Dados do Requerimento</div>
            <span class="acordeao-seta">▶</span>
          </div>
          <div class="acordeao-corpo" style="padding: 15px; background: #fff;">
            <div class="report-container">
              @include('processos.abas.resumos.aba1a')
            </div>
          </div>
        </div>

        <div class="acordeao-wrapper">
          <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="background: #1e3a5f;">
            <div class="acordeao-titulo">📋 RIP(s) ou Cadastro(s) Mínimo(s)</div>
            <span class="acordeao-seta">▶</span>
          </div>
          <div class="acordeao-corpo" style="padding: 15px; background: #fff;">
            <div class="report-container">
              @include('processos.abas.resumos.aba1b')
            </div>
          </div>
        </div>
        @endif

        @if(!empty($dados2))
        <div class="acordeao-wrapper">
          <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="background: #1e3a5f;">
            <div class="acordeao-titulo">📋 Diagnóstico preliminar do imóvel</div>
            <span class="acordeao-seta">▶</span>
          </div>
          <div class="acordeao-corpo" style="padding: 15px; background: #fff;">
            <div class="report-container">
              @include('processos.abas.resumos.aba2')
            </div>
          </div>
        </div>
        @endif

        @if(!empty($dados3))
        <div class="acordeao-wrapper">
          <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')" style="background: #1e3a5f;">
            <div class="acordeao-titulo">📋 Análise de Viabilidade</div>
            <span class="acordeao-seta">▶</span>
          </div>
          <div class="acordeao-corpo" style="padding: 15px; background: #fff;">
            <div class="report-container">
              <h4 style="margin-top: 0; color: #1e3a5f; font-size: 1.1em; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 15px;">Análise do Destinatário</h4>
              @include('processos.abas.resumos.aba3_analise')

              <h4 style="margin-top: 25px; color: #1e3a5f; font-size: 1.1em; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 15px;">Proposta de Destinação</h4>
              @include('processos.abas.resumos.aba3_proposta')
            </div>
          </div>
        </div>
        @endif
      @else
        <div style="text-align:center; padding:30px; color:#64748b; background:#f8fafc; border-radius:8px; border:1px dashed #cbd5e1;">
          Nenhum dado das abas anteriores foi encontrado. Preencha as abas 1, 2 ou 3 primeiro.
        </div>
      @endif
    </div>
    {{-- FIM RESUMO DAS ABAS ANTERIORES --}}


    <form action="{{ route('processos.tramitar', $processo->id) }}" method="POST" hx-post="{{ route('processos.tramitar', $processo->id) }}" hx-target="#aba7-container" hx-indicator="#form-indicator" id="form07">
        @csrf
        <div id="form-indicator" class="htmx-indicator" style="display:none; color: #475569; margin-bottom: 10px;">⏳ Processando...</div>
        <input type="hidden" name="next_aba" value="index">

        @php
            $status = $processo->status_atual;

            // Determinar qual secao esta ativa
            $secoes = [
                'chefia' => ['status' => 'Validação - Chefia', 'perfil' => 'Chefia', 'label' => 'Chefia', 'assinatura' => 'assinatura_chefia'],
                'coordenacao' => ['status' => 'Validação - Coordenação', 'perfil' => 'Coordenação', 'label' => 'Coordenação SPU/UF', 'assinatura' => 'assinatura_coordenacao'],
                'superintendencia' => ['status' => 'Deliberação - Superintendência', 'perfil' => 'Superintendência', 'label' => 'Superintendência', 'assinatura' => 'assinatura_superintendencia'],
                'equipe_cg' => ['status' => 'Conformidade Prévia', 'perfil' => 'Equipe C.G.', 'label' => 'Equipe C.G.', 'assinatura' => 'assinatura_equipe_cg'],
                'coordenacao_geral' => ['status' => 'Validação - Coordenação-Geral', 'perfil' => 'Coordenação-Geral', 'label' => 'Coordenação-Geral', 'assinatura' => 'assinatura_coordenacao_geral'],
                'direcao' => ['status' => 'Validação - Direção', 'perfil' => 'Direção', 'label' => 'Direção', 'assinatura' => 'assinatura_direcao'],
                'cde' => ['status' => 'Deliberação - CDE', 'perfil' => 'CDE', 'label' => 'CDE', 'assinatura' => 'assinatura_cde'],
            ];

            $chaveAtiva = null;
            foreach ($secoes as $chave => $s) {
                if ($s['status'] === $status) {
                    $chaveAtiva = $chave;
                    break;
                }
            }
        @endphp

        {{-- SECAO ATIVA --}}
        @foreach($secoes as $chave => $s)
            @if($chave === $chaveAtiva || isset($dados[$s['assinatura'] . '_nome']))
            <div class="acordeao-wrapper {{ $chave === $chaveAtiva ? 'aberto secao-ativa' : '' }}" id="box-{{ $chave }}">
                <div class="acordeao-header" onclick="this.parentElement.classList.toggle('aberto')">
                    <div class="acordeao-titulo">📋 {{ $s['label'] }}</div>
                    @if(isset($dados[$s['assinatura'] . '_nome']))
                        <span class="acordeao-badge-ok visivel">✔ Concluído</span>
                    @else
                        <span class="acordeao-badge-pendente">⏳ Pendente</span>
                    @endif
                    <span class="acordeao-seta">▶</span>
                </div>

                <div class="acordeao-corpo">
                    @php
                        $origemTextoAtiva = null;
                        $map = [
                            'coordenacao' => ['assinatura' => 'assinatura_chefia', 'label' => 'Chefia'],
                            'superintendencia' => ['assinatura' => 'assinatura_coordenacao', 'label' => 'Coordenação SPU/UF'],
                            'equipe_cg' => ['assinatura' => 'assinatura_superintendencia', 'label' => 'Superintendência'],
                            'coordenacao_geral' => ['assinatura' => 'assinatura_equipe_cg', 'label' => 'Equipe C.G.'],
                            'direcao' => ['assinatura' => 'assinatura_coordenacao_geral', 'label' => 'Coordenação-Geral'],
                            'cde' => ['assinatura' => 'assinatura_superintendencia', 'label' => 'Superintendência'],
                        ];
                        
                        if (isset($map[$chave])) {
                            if ($chave === 'cde' && isset($dados['assinatura_direcao_data'])) {
                                $origemTextoAtiva = "Encaminhado por Direção em " . $dados['assinatura_direcao_data'];
                            } else {
                                $ant = $map[$chave];
                                if (isset($dados[$ant['assinatura'] . '_data'])) {
                                    $origemTextoAtiva = "Encaminhado por " . $ant['label'] . " em " . $dados[$ant['assinatura'] . '_data'];
                                }
                            }
                        }
                    @endphp

                    @if($origemTextoAtiva)
                    <div class="decl-origem-stamp">
                        {{ $origemTextoAtiva }}
                    </div>
                    @endif

                    <div class="decl-info-nota" style="margin-bottom: 15px;">
                        Verifique o histórico acima antes de emitir sua manifestação.
                    </div>

                    @if(isset($dados[$s['assinatura'] . '_nome']))
                        {{-- JA ASSINADO --}}
                        <div class="decl-assinado-overlay visivel" style="display:block; margin-bottom: 20px; padding: 15px; border-radius: 6px; background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                            <strong>✔ Manifestação registrada</strong><br>
                            Assinado por: {{ $dados[$s['assinatura'] . '_nome'] }} em {{ $dados[$s['assinatura'] . '_data'] }}
                        </div>
                    @endif
                    
                    {{-- FORMULARIO --}}
                    <fieldset @if(isset($dados[$s['assinatura'] . '_nome']) || ($perfil !== 'ALL' && $s['perfil'] !== $perfil)) disabled @endif>
                        
                        @include('processos.abas.manifestacoes.' . $chave)
                        </fieldset>

                        @if(!isset($dados[$s['assinatura'] . '_nome']))
                        <div class="decl-btn-assinar" style="justify-content: flex-end; margin-top: 15px;">
                            @if($perfil === 'ALL' || $perfil === $s['perfil'])
                                <button type="submit" class="btn-inst btn-inst-outline" style="padding: 8px 22px; font-weight: 600;" onclick="document.getElementById('hidden_acao_aba7_rascunho').value='{{ $chave }}'; document.getElementById('hidden_acao_aba7').value='';">💾 Salvar Rascunho</button>
                                <button type="submit" class="btn-assinar" style="padding: 8px 22px; font-weight: 600;" onclick="document.getElementById('hidden_acao_aba7').value='{{ $chave }}'; document.getElementById('hidden_acao_aba7_rascunho').value='';">📤 Salvar e Enviar</button>
                            @endif
                        </div>
                        @endif
                </div>
            </div>
            @endif
        @endforeach

        <input type="hidden" name="acao_aba7" id="hidden_acao_aba7" value="">
        <input type="hidden" name="acao_aba7_rascunho" id="hidden_acao_aba7_rascunho" value="">
    </form>
</div>

<script>
function toggleRegimeDropdown() {
    const radioNao = document.querySelector('input[name="sup_regime_concorda"][value="nao"]');
    const divSugerido = document.getElementById('div-regime-sugerido');
    if (radioNao && divSugerido) {
        divSugerido.style.display = radioNao.checked ? 'block' : 'none';
    }
}

(function() {
    const form07 = document.getElementById('form07');
    if (!form07) return;

    form07.onsubmit = function(e) {
        // Se for rascunho, ignora validação
        const rascunhoVal = document.getElementById('hidden_acao_aba7_rascunho')?.value || '';
        if (rascunhoVal !== '') {
            return true;
        }

        let isValid = true;
        let msgErro = 'Por favor, selecione uma opção para todos os itens obrigatórios da manifestação.';
        
        // Validação customizada para garantir que todos os grupos de radio da seção ativa foram preenchidos
        const activeSection = document.querySelector('.secao-ativa');
        if (activeSection) {
            const radioGroups = new Set();
            activeSection.querySelectorAll('input[type="radio"]').forEach(radio => radioGroups.add(radio.name));
            
            for (let name of radioGroups) {
                if (!activeSection.querySelector(`input[name="${name}"]:checked`)) {
                    isValid = false;
                    break;
                }
            }

            // Box Equipe C.G.: "Salvar e Enviar" exige os campos obrigatórios preenchidos
            if (activeSection.id === 'box-equipe_cg' && isValid) {
                // Observações de cada seção do checklist só são obrigatórias se houver "Não" na seção
                const obsCampos = activeSection.querySelectorAll('textarea[name^="obs_chk_"]');
                for (let obs of obsCampos) {
                    const grupoObs = obs.closest('.decl-obs-group');
                    const secao = grupoObs ? grupoObs.parentElement : null;
                    const temNao = secao
                        ? secao.querySelectorAll('input[type="radio"][value="Não"]:checked').length > 0
                        : false;
                    if (temNao && (!obs.value || obs.value.trim() === '')) {
                        isValid = false;
                        msgErro = 'Preencha as observações das seções em que algum item foi marcado como "Não".';
                        break;
                    }
                }
                // Condicionantes só é obrigatória quando a opção "com condicionantes" estiver visível
                if (isValid) {
                    const conds = document.getElementById('obs-equipe-cg-condicionantes');
                    if (conds && conds.offsetParent !== null && (!conds.value || conds.value.trim() === '')) {
                        isValid = false;
                        msgErro = 'Preencha as condicionantes antes de enviar a manifestação.';
                    }
                }
            }
        }

        if (!isValid || !form07.checkValidity()) {
            if (e) e.preventDefault();
            if (isValid) form07.reportValidity();
            else alert(msgErro);
            
            // Limpar os hiddens para não travar próximos submits
            const hAcao = document.getElementById('hidden_acao_aba7');
            const hRasc = document.getElementById('hidden_acao_aba7_rascunho');
            if (hAcao) hAcao.value = '';
            if (hRasc) hRasc.value = '';
            return false;
        }
    };
})();
</script>
