# Correção da Atribuição de Action e Method na Tag Form da Aba 7

**Arquivo Modificado:** `resources/views/processos/abas/aba7.blade.php`

## Motivo da Alteração
O usuário reportou que ao clicar em "Salvar e Enviar" na Aba 7, a tela sofria apenas um refresh (recarregamento) e o status do processo não era alterado.

Investigando o log do servidor (`laravel.log`), constatou-se que a rota `ProcessoController@tramitar` **nunca chegava a ser acionada** durante o clique.

**Causa Raiz:**
A tag `<form id="form07">` continha apenas a diretiva HTMX `hx-post="{{ route('processos.tramitar', $processo->id) }}"`, porém **carecia dos atributos HTML nativos `action` e `method="POST"`**.
Quando o HTMX não interceptava a submissão (por conta do carregamento dinâmico ou por falha no seletor `hx-target="#aba7-container"`), o navegador realizava uma submissão de formulário HTML nativa. Como a tag `<form>` não possuía `action` nem `method`, o navegador fazia uma requisição `GET` para a URL da própria página atual (`/processos/{id}?aba=7`), o que causava o comportamento de apenas recarregar a tela sem jamais disparar o POST para o controller.

Além disso, o evento `DOMContentLoaded` que encapsulava a validação JS não era executado quando a Aba 7 era carregada dinamicamente via AJAX/HTMX.

**Solução:**
1. Adicionados os atributos nativos `action="{{ route('processos.tramitar', $processo->id) }}"` e `method="POST"` na tag `<form id="form07">`.
2. Refatorado o script de validação JS para uma IIFE de execução imediata que associa diretamente o handler `form07.onsubmit`, funcionando perfeitamente em carregamentos dinâmicos.

## 1. Estado Anterior (Antes)
```html
<form hx-post="{{ route('processos.tramitar', $processo->id) }}" hx-target="#aba7-container" hx-indicator="#form-indicator" id="form07">
```

## 2. Estado Novo (Depois)
```html
<form action="{{ route('processos.tramitar', $processo->id) }}" method="POST" hx-post="{{ route('processos.tramitar', $processo->id) }}" hx-target="#aba7-container" hx-indicator="#form-indicator" id="form07">
```

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/aba7.blade.php`.
2. Remova os atributos `action` e `method="POST"` da tag `<form id="form07">`.
3. Restaure o script JS para o formato de listener `DOMContentLoaded`.
