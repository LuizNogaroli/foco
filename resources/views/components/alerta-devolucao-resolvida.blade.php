@props(['resolucao'])

<div class="alerta-devolucao" style="margin-bottom: 20px; background-color: #f0fdf4; border: 2px solid #16a34a; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(22, 163, 74, 0.1);">
    <div style="background-color: #16a34a; color: white; padding: 12px 20px; font-weight: bold; font-size: 1.1em; display: flex; align-items: center; gap: 10px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        Devolução Resolvida
    </div>
    <div style="padding: 20px; color: #166534;">
        <div style="margin-bottom: 15px; display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <strong>Resolvido por:</strong><br>
                {{ $resolucao->usuario ? $resolucao->usuario->name : 'Sistema' }}
            </div>
            <div style="flex: 1; min-width: 200px;">
                <strong>Data da resolução:</strong><br>
                {{ $resolucao->created_at ? $resolucao->created_at->format('d/m/Y H:i:s') : '-' }}
            </div>
        </div>
        <div style="background-color: #dcfce7; padding: 15px; border-radius: 6px; border-left: 4px solid #16a34a;">
            <strong style="display: block; margin-bottom: 8px;">Resolução / Ajustes realizados:</strong>
            <p style="margin: 0; white-space: pre-wrap; font-size: 1.05em; line-height: 1.5;">{{ $resolucao->justificativa ?? 'Nenhuma resolução informada.' }}</p>
        </div>
    </div>
</div>
