# Histórico e Walkthrough: Implementação do HTMX na Aba 7
**Data:** 26/07/2026

## O que foi alterado:
A refatoração gradual para introdução do HTMX foi concluída com sucesso. Todo o fluxo de aprovação e deliberação da Aba 7 agora opera com a fluidez de uma Single Page Application (SPA), sem a necessidade de recarregamentos completos de tela.

1. **Instalação e Disponibilização do HTMX:** O HTMX foi instalado via NPM e importado nativamente no arquivo `resources/js/app.js`. Configuramos a injeção automática do Token CSRF usando o listener `htmx:configRequest`.
2. **Controller Refatorado:** O `ProcessoController@show` agora detecta o header `HX-Request` para retornar apenas o HTML parcial da Aba 7.
3. **Mágica na Interface (`aba7.blade.php`):** A tag padrão `<form>` foi modernizada usando os atributos `hx-post`, `hx-target` e `hx-indicator`.

---

## Registo de Reversibilidade (Rollback / Undo)

### 1. Estado Anterior (Antes)
- **`app.js`:** Continha apenas a inicialização do AlpineJS.
- **`ProcessoController@show` (linha 362):** Retornava obrigatoriamente `view('processos.show', ...)` para a Aba 7.
- **`show.blade.php`:** Não importava o `app.js` com o Vite e possuía o include simples: `@include('processos.abas.aba7')`.
- **`aba7.blade.php` (linha 10):**
```html
<form method="POST" action="{{ route('processos.tramitar', $processo->id) }}" id="form07">
    @csrf
```

### 2. Estado Novo (Depois)
- **`app.js`:** Importa `htmx.org` e adiciona o token CSRF.
- **`ProcessoController@show`:**
```php
if ($request->header('HX-Request') && $aba == 7) {
    return view('processos.abas.aba7', compact(...));
}
return view('processos.show', compact(...));
```
- **`show.blade.php`:** Adicionado `@vite(['resources/js/app.js'])` no cabeçalho e encapsulamento em container:
```html
<div id="aba7-container">
    @include('processos.abas.aba7', ['processo' => $processo, 'dados' => $dados])
</div>
```
- **`aba7.blade.php`:**
```html
<form hx-post="{{ route('processos.tramitar', $processo->id) }}" hx-target="#aba7-container" hx-indicator="#form-indicator" id="form07">
    @csrf
    <div id="form-indicator" class="htmx-indicator" style="display:none; color: #475569; margin-bottom: 10px;">⏳ Processando...</div>
```

### 3. Plano de Rollback / Desfazer
Para reverter a implementação do HTMX na Aba 7 e retornar ao formato legado, execute os seguintes passos:
1. **Reverter a Interface (`aba7.blade.php`):** Substituir a tag `<form hx-post="..." hx-target="#aba7-container" ...>` pela tradicional `<form method="POST" action="...route...">`. E remover a div `<div id="form-indicator">`.
2. **Reverter a Visão Principal (`show.blade.php`):** Remover a `<div id="aba7-container">` deixando apenas o `@include('processos.abas.aba7')`.
3. **Reverter o Controller:** No `app/Http/Controllers/ProcessoController.php` (método `show`), deletar o bloco condicional `if ($request->header('HX-Request') && $aba == 7) { return view(...); }`.
4. **Reverter Vite (Opcional):** Se o HTMX não for mais desejado em lugar nenhum, remover `@vite(['resources/js/app.js'])` de `show.blade.php` e desfazer a importação do HTMX no `resources/js/app.js`, rodando `npm run build` novamente.
