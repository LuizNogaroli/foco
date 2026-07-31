<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - {{ $processo->numero_requerimento }}</title>
    <link rel="stylesheet" href="{{ asset('css/styles-forms.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #1e293b; }

        .hist-container { width: 100%; max-width: 1400px; margin: 40px auto; padding: 0 20px; }
        .hist-header { text-align: center; margin-bottom: 30px; }
        .hist-header h1 { font-size: 1.5rem; color: #1e3a5f; font-weight: 700; margin-bottom: 4px; }
        .hist-header .hist-sub { font-size: 0.95rem; color: #64748b; }
        .hist-header .hist-back { display: inline-block; margin-top: 14px; padding: 8px 20px; background: #1e3a5f; color: #fff; border-radius: 6px; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: background 0.2s; }
        .hist-header .hist-back:hover { background: #2d5282; }

        .hist-empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95em; }
        .report-container { max-width: 100% !important; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
</head>
<body>
    <div class="hist-container">
        <div class="hist-header">
            <h1>Histórico de Movimentações</h1>
            <div class="hist-sub">Requerimento nº {{ $processo->numero_requerimento }} &mdash; {{ $processo->status_atual }}</div>
            <a href="{{ url()->previous() }}" class="hist-back">← Voltar ao Painel</a>
        </div>

        @include('processos.abas.partials.timeline')

    </div>

    <script>
        const ProcessoID = {{ $processo->id }};
    </script>
</body>
</html>
