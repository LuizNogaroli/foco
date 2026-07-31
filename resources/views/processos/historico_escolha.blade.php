<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Modelo - {{ $processo->numero_requerimento }}</title>
    <link rel="stylesheet" href="{{ asset('css/styles-forms.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #1e293b; }

        .choice-container { width: 100%; max-width: 900px; margin: 60px auto; padding: 0 20px; }
        .choice-header { text-align: center; margin-bottom: 40px; }
        .choice-header h1 { font-size: 1.5rem; color: #1e3a5f; font-weight: 700; margin-bottom: 4px; }
        .choice-header .choice-sub { font-size: 0.95rem; color: #64748b; }
        .choice-header .choice-back { display: inline-block; margin-top: 14px; padding: 8px 20px; background: #1e3a5f; color: #fff; border-radius: 6px; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: background 0.2s; }
        .choice-header .choice-back:hover { background: #2d5282; }

        .choice-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (min-width: 900px) { .choice-grid { grid-template-columns: 1fr 1fr 1fr 1fr; } }

        .choice-card { background: #fff; border-radius: 12px; padding: 32px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s; text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; gap: 16px; border: 2px solid transparent; }
        .choice-card:hover:not(.choice-card-disabled) { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); border-color: #1e3a5f; }
        .choice-card-disabled { opacity: 0.5; cursor: not-allowed; border-color: #e2e8f0; }

        .choice-icon { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; }
        .choice-icon-a { background: #e0f2fe; color: #0369a1; }
        .choice-icon-b { background: #f0fdf4; color: #15803d; }
        .choice-icon-c { background: #fef3c7; color: #92400e; }
        .choice-icon-d { background: #f3e8ff; color: #7c3aed; }
        .choice-icon-e { background: #fef3c7; color: #b45309; }
        .choice-icon-f { background: #e0f2fe; color: #0369a1; }
        .choice-icon-g { background: #fce7f3; color: #be185d; }

        .choice-card h2 { font-size: 1.15rem; font-weight: 700; color: #1e3a5f; }
        .choice-card p { font-size: 0.9rem; color: #64748b; line-height: 1.5; }

        .choice-badge { display: inline-block; padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; }
        .choice-badge-active { background: #dcfce7; color: #166534; }
        .choice-badge-pending { background: #fef3c7; color: #92400e; }

        @media (max-width: 640px) { .choice-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="choice-container">
        <div class="choice-header">
            <h1>Escolher Modelo de Histórico</h1>
            <div class="choice-sub">Requerimento nº {{ $processo->numero_requerimento }} &mdash; {{ $processo->status_atual }}</div>
            <a href="{{ route('processos.index') }}" class="choice-back">← Voltar ao Painel</a>
        </div>

        <div class="choice-grid">
            <a href="{{ route('processos.historico', $processo->id) }}" class="choice-card">
                <div class="choice-icon choice-icon-a">A</div>
                <h2>Modelo A</h2>
                <p>Visualização cronológica atual com detalhes por aba, pareceres e manifestações.</p>
                <span class="choice-badge choice-badge-active">Ativo</span>
            </a>

            <a href="{{ route('processos.historico.modelo-b', $processo->id) }}" class="choice-card">
                <div class="choice-icon choice-icon-b">B</div>
                <h2>Modelo B</h2>
                <p>Linha do tempo vertical compacta com detalhes em modal.</p>
                <span class="choice-badge choice-badge-active">Ativo</span>
            </a>

            <a href="{{ route('processos.historico.modelo-c', $processo->id) }}" class="choice-card">
                <div class="choice-icon choice-icon-c">C</div>
                <h2>Modelo C</h2>
                <p>KanBan com colunas por perfil responsável e cards resumidos.</p>
                <span class="choice-badge choice-badge-active">Ativo</span>
            </a>

            <a href="{{ route('processos.historico.modelo-d', $processo->id) }}" class="choice-card">
                <div class="choice-icon choice-icon-d">D</div>
                <h2>Modelo D</h2>
                <p>Grafo do fluxo de estados percorrido pelo processo.</p>
                <span class="choice-badge choice-badge-active">Ativo</span>
            </a>

            <a href="{{ route('processos.historico.modelo-e', $processo->id) }}" class="choice-card">
                <div class="choice-icon choice-icon-e">E</div>
                <h2>Modelo E</h2>
                <p>Diagrama BPMN com gateway de devolução, swimlane do ciclo e convergência.</p>
                <span class="choice-badge choice-badge-active">Ativo</span>
            </a>

            <a href="{{ route('processos.historico.modelo-f', $processo->id) }}" class="choice-card">
                <div class="choice-icon choice-icon-f">F</div>
                <h2>Modelo F</h2>
                <p>Colunas por passagem: cada devolução abre uma nova coluna à direita.</p>
                <span class="choice-badge choice-badge-active">Ativo</span>
            </a>

            <a href="{{ route('processos.historico.modelo-g', $processo->id) }}" class="choice-card">
                <div class="choice-icon choice-icon-g">G</div>
                <h2>Modelo G</h2>
                <p>Matriz abas × perfis × passagens. Visão tabular de todas as etapas.</p>
                <span class="choice-badge choice-badge-active">Ativo</span>
            </a>
        </div>
    </div>
</body>
</html>
