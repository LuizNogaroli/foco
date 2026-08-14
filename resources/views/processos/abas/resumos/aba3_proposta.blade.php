@php
    $proposta = $dados3['proposta_destinacao'] ?? $dados3 ?? [];
@endphp
<div style="display:flex;flex-direction:column;">
    <!-- Proposta de Destinação e Impactos -->
    <div style="font-size:0.95rem; font-weight:700; color:#1e3a5f; margin-bottom:10px;">Proposta de Destinação e Impactos</div>
    
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Tipo de Procedimento:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['tipo_procedimento'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Observações do Procedimento:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['campo51_obs'] ?? '-' }}</span>
    </div>

    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Tipo de Uso (Imobiliário):</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['tipo_uso_imobiliario'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Uso Específico:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['tipo_uso_especifico'] ?? '-' }}</span>
    </div>

    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Regime de Destinação Proposto:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['regime_destinacao'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Observações adicionais (Regime):</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['campo511_obs'] ?? '-' }}</span>
    </div>

    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Previsão de modificação física?</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['campo54'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Descrição da modificação:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['campo54_desc'] ?? '-' }}</span>
    </div>

    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Compatibilidade urbanística:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['compatibilidade_urbanistica'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Observações compatibilidade:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['campo55_obs'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Vinculação com o programa Imóvel da Gente?</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['campo56_radio'] ?? '-' }}</span>
    </div>
    @if(($proposta['campo56_radio'] ?? '') === 'Sim')
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Linha do programa:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['linha_programa'] ?? '-' }}</span>
    </div>
    @endif


    <div style="margin-top:16px; padding-top:16px; border-top:1px dashed #cbd5e1; margin-bottom: 10px;">
        <div style="font-size:0.95rem; font-weight:700; color:#1e3a5f; margin-bottom:10px;">Impactos Esperados</div>
    </div>

    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Expectativa de impacto social?</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['campo58_radio'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Impacto Social esperado:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['impacto_social'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Tipo de beneficiário:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['tipo_beneficiario'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Número estimado de beneficiários:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['num_beneficiarios'] ?? '-' }}</span>
    </div>
    <div style="display:flex;align-items:baseline;margin-bottom:6px;font-size:0.9rem;">
        <span style="width:300px;font-weight:600;color:#334155;">Observações Impacto Social:</span>
        <span style="flex:1;margin-left:6px;padding:3px 10px;background:#f1f5f9;border-radius:3px;">{{ $proposta['impacto_social_obs'] ?? '-' }}</span>
    </div>
</div>
