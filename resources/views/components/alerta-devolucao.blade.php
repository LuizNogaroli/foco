@props(['devolucao', 'jaRecebido' => false, 'aba' => null, 'processoId' => null])

<div class="alerta-devolucao" style="margin-bottom: 20px; background-color: #fef2f2; border: 2px solid #ef4444; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.1);">
    <div class="alerta-devolucao-header" onclick="toggleDevolucaoAccordion(this)" style="background-color: #ef4444; color: white; padding: 12px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 1.1em;">
        <span style="display: flex; align-items: center; gap: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            Processo Devolvido
        </span>
        <svg class="alerta-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.3s; transform: rotate(90deg);">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </div>
    
    <div class="alerta-devolucao-content" style="padding: 20px; display: block; color: #7f1d1d;">
        <div style="margin-bottom: 15px; display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <strong>Origem da devolução:</strong><br>
                {{ $devolucao->etapa ?? 'Setor Anterior' }} 
                @if($devolucao->usuario)
                    <span style="opacity: 0.8; font-size: 0.9em;">({{ $devolucao->usuario->name }})</span>
                @endif
            </div>
            <div style="flex: 1; min-width: 200px;">
                <strong>Data da devolução:</strong><br>
                {{ $devolucao->created_at ? $devolucao->created_at->format('d/m/Y H:i:s') : '-' }}
            </div>
        </div>
        
        <div style="background-color: #fee2e2; padding: 15px; border-radius: 6px; border-left: 4px solid #dc2626; margin-bottom: 15px;">
            <strong style="display: block; margin-bottom: 8px;">Justificativa / Observações:</strong>
            <p style="margin: 0; white-space: pre-wrap; font-size: 1.05em; line-height: 1.5;">{{ $devolucao->justificativa ?? 'Nenhuma justificativa fornecida.' }}</p>
        </div>

        @if(!$jaRecebido && $processoId)
            <div id="btn-receber-wrapper" style="display: flex; justify-content: flex-end; margin-top: 15px;">
                <button type="button" onclick="receberDevolucao()" style="background-color: #dc2626; color: white; border: none; border-radius: 6px; padding: 10px 20px; font-weight: bold; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Estou Ciente / Receber
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleDevolucaoAccordion(header) {
        const content = header.nextElementSibling;
        const icon = header.querySelector('.alerta-icon');
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(90deg)';
        }
    }

    function receberDevolucao() {
        const btnWrapper = document.getElementById('btn-receber-wrapper');
        const originalHtml = btnWrapper.innerHTML;
        btnWrapper.innerHTML = '<span style="color: #991b1b; font-weight: bold;">Processando...</span>';

        fetch("{{ route('processos.receber-devolucao', $processoId ?? 0) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                aba: '{{ $aba ?? "" }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                btnWrapper.innerHTML = '<span style="color: #15803d; font-weight: bold; display: flex; align-items: center; gap: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Recebimento registrado com sucesso!</span>';
            } else {
                btnWrapper.innerHTML = originalHtml;
                alert('Erro ao registrar recebimento.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnWrapper.innerHTML = originalHtml;
            alert('Erro ao registrar recebimento.');
        });
    }
</script>
