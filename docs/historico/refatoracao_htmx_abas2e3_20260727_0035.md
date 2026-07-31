# Refatoração HTMX nas Abas 2 e 3

## 1. Estado Anterior (Antes)
- **Aba 2 (Diagnóstico Preliminar):** O formulário executava submissões tradicionais (`<form method="POST">`) e a lógica do select "Uso Específico" dependia de um imenso array JSON (`usoEspecificoMap`) carregado diretamente no frontend. Sempre que o formulário era salvo, toda a página (`processos.show`) precisava ser recarregada.
- **Aba 3 (Análise de Viabilidade):** O formulário dependia de um script robusto (`foco-03.js` / `fetch_spu.js`) que enviava `fetch` calls diretamente para a tabela Supabase usando chaves de API expostas no frontend. Havia mensagens "fake" simulando atrasos de rede e botões que interceptavam o submit via Javascript.
- **Redirecionamento:** O Controller dependia de lógica de fallback inconsistente onde a falha na leitura da próxima aba redirecionava para a própria aba ou tentava pular para a próxima aba ignorando o fluxo natural.

## 2. Estado Novo (Depois)
- **Aba 2:** A tag do formulário foi atualizada para utilizar `<form hx-post="..." hx-target="#aba2-container" hx-indicator="...">`. A injeção de JSON no HTML foi completamente removida. O campo de "Uso Específico" (`campo33`) agora busca as opções dinamicamente no backend via HTMX (`hx-get="/api/vocacoes"`).
- **Aba 3:** O `<form>` agora utiliza `hx-post` e é perfeitamente submetido pela rota unificada `processos.tramitar` no backend Laravel. As chamadas `fetch` diretas e inseguras para o Supabase no frontend foram removidas, e a API Laravel centraliza as regras de negócio antes de atualizar o Supabase.
- **Redirecionamento Unificado:** Em `ProcessoController::tramitar`, sempre que o botão "Salvar e Enviar" for acionado nas abas 1, 2 ou 3, o backend emite ou um `HX-Redirect` ou um código `302` limpo para a rota raiz `processos.index` (Painel de Requerimentos), consolidando o fluxo. A lógica do botão `devolver` também respeita essa nova estrutura.

## 3. Plano de Rollback / Desfazer
Para reverter todas as modificações introduzidas por esta refatoração:

**Passo 1: Frontend (Abas 2 e 3)**
- **resources/views/processos/abas/aba2.blade.php**: Remover `hx-post`, `hx-target` e `hx-indicator` do `id="form02"`. Reverter para `<form method="POST" action="...">`. Retornar a tag `<script>` contendo a variável `usoEspecificoMap` com todos os JSONs de mapeamento das vocações.
- **resources/views/processos/abas/aba3.blade.php**: Remover `hx-post` do `id="form03"`. Descomentar ou retornar a inclusão dos arquivos `js/sync.js`, `js/foco-03.js` e as divs de simulação visual (loading fake).

**Passo 2: Backend (Controller e Rotas)**
- **app/Http/Controllers/ProcessoController.php**: Remover o método `getVocacoes` (usado pela API da aba 2). Modificar o método `tramitar`, removendo a linha de força bruta (`$nextAba = 'index';`) e restaurando a lógica antiga de `$nextAba = $abaAtual + 1`.
- **routes/web.php**: Remover a rota `Route::get('/api/vocacoes', [ProcessoController::class, 'getVocacoes']);`.
