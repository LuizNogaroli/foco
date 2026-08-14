<strong style="color:#0f766e;">✔ Manifestação registrada</strong><br>
@if($prefix === 'superintendencia')
    Deliberação: {{ ucfirst(str_replace('_', ' ', $dadosSnapshot['sup_deliberacao'] ?? '')) }}<br>
    @if(($dadosSnapshot['sup_regime_concorda'] ?? '') === 'nao')
        Regime sugerido: {{ $dadosSnapshot['sup_regime_novo'] ?? 'Nenhum' }}<br>
    @endif
    Observações: {{ $dadosSnapshot['obs_superintendencia'] ?? 'Nenhuma observação' }}
@elseif($prefix === 'cde')
    Deliberação: {{ ucfirst(str_replace('_', ' ', $dadosSnapshot['cde_deliberacao'] ?? '')) }}<br>
    Observações: {{ $dadosSnapshot['obs_cde'] ?? 'Nenhuma observação' }}
@elseif($prefix === 'equipe_cg')
    @php
        $opcaoEquipe = $dadosSnapshot['decl_equipe_cg_opcao'] ?? '';
        $opcaoEquipeTexto = $opcaoEquipe === 'favoravel' ? 'Favorável'
            : ($opcaoEquipe === 'favoravel_condicionantes' ? 'Favorável com condicionantes'
            : ($opcaoEquipe === 'nao_favoravel' ? 'Não favorável no momento' : 'Não informado'));
        $conclusaoEquipe = ($dadosSnapshot['decl_equipe_cg_conclusao'] ?? '') === 'apta_cde' ? 'Apta para apreciação pela CDE'
            : (($dadosSnapshot['decl_equipe_cg_conclusao'] ?? '') === 'inapta_cde' ? 'Não apta para apreciação pela CDE' : '');
    @endphp
    Manifestação: {{ $opcaoEquipeTexto }}<br>
    @if($opcaoEquipe === 'favoravel_condicionantes')
        Condicionantes: {{ $dadosSnapshot['obs_equipe_cg_condicionantes'] ?? 'Nenhuma' }}<br>
    @endif
    @if($conclusaoEquipe)
        Conclusão: {{ $conclusaoEquipe }}<br>
    @endif
@elseif($prefix === 'coordenacao_geral')
    @php
        $opcaoCg = $dadosSnapshot['decl_coordenacao_geral_opcao'] ?? '';
        $opcaoCgTexto = $opcaoCg === 'favoravel' ? 'A proposta reúne elementos suficientes para apreciação da CDE'
            : ($opcaoCg === 'favoravel_condicionantes' ? 'A proposta reúne elementos suficientes para apreciação da CDE com condicionantes'
            : ($opcaoCg === 'nao_favoravel' ? 'A proposta demanda complementação antes de eventual submissão à CDE' : 'Não informado'));
        $conclusaoCg = ($dadosSnapshot['decl_coordenacao_geral_conclusao'] ?? '') === 'apta_cde' ? 'Apta para apreciação pela CDE'
            : (($dadosSnapshot['decl_coordenacao_geral_conclusao'] ?? '') === 'inapta_cde' ? 'Não apta para apreciação pela CDE' : '');
    @endphp
    Manifestação: {{ $opcaoCgTexto }}<br>
    @if($opcaoCg === 'favoravel_condicionantes')
        Condicionantes: {{ $dadosSnapshot['obs_coordenacao_geral_condicionantes'] ?? 'Nenhuma' }}<br>
    @endif
    @if($conclusaoCg)
        Conclusão: {{ $conclusaoCg }}<br>
    @endif
@elseif($prefix === 'direcao')
    @php
        $opcaoDirecao = $dadosSnapshot['decl_direcao_opcao'] ?? '';
        $opcaoDirecaoTexto = $opcaoDirecao === 'apta_cde' ? 'A Proposta apta para submissão à CDE'
            : ($opcaoDirecao === 'restituir_spuf' ? 'Necessário restituir o processo à SPU/UF para complementação'
            : ($opcaoDirecao === 'diligencia' ? 'Necessário diligência específica antes da submissão ao CDE'
            : ($opcaoDirecao === 'suficiente' ? 'Suficiente'
            : ($opcaoDirecao === 'insuficiente' ? 'Insuficiente' : 'Não informado'))));
    @endphp
    Manifestação: {{ $opcaoDirecaoTexto }}<br>
    Encaminhe-se conforme deliberado
@else
    @php
        $opcaoPadrao = $dadosSnapshot['decl_'.$prefix.'_opcao'] ?? '';
        $opcaoPadraoTexto = $opcaoPadrao === 'favoravel' ? 'Favorável'
            : ($opcaoPadrao === 'favoravel_condicionantes' ? 'Favorável com condicionantes'
            : ($opcaoPadrao === 'nao_favoravel' ? 'Não favorável no momento'
            : ($opcaoPadrao === 'suficiente' ? 'Suficiente'
            : ($opcaoPadrao === 'insuficiente' ? 'Insuficiente' : 'Não informado'))));
        $conclusaoPadrao = ($dadosSnapshot['decl_'.$prefix.'_conclusao'] ?? '') === 'apta_cde' ? 'Apta para apreciação pela CDE'
            : (($dadosSnapshot['decl_'.$prefix.'_conclusao'] ?? '') === 'inapta_cde' ? 'Não apta para apreciação pela CDE' : '');
        $obsPadrao = $dadosSnapshot['obs_'.$prefix.'_condicionantes'] ?? ($dadosSnapshot['obs_'.$prefix] ?? '');
    @endphp
    Manifestação: {{ $opcaoPadraoTexto }}<br>
    @if(($opcaoPadrao === 'favoravel_condicionantes' || $opcaoPadrao === 'insuficiente') && $obsPadrao)
        Observações: {{ $obsPadrao }}<br>
    @endif
    @if($conclusaoPadrao)
        Conclusão: {{ $conclusaoPadrao }}<br>
    @endif
@endif
