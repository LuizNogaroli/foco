# Bypass de Validação HTML5 no Botão de Devolução via HTMX

**Arquivo Modificado:** `resources/views/processos/abas/aba3.blade.php`

## Motivo da Alteração
O usuário reportou novamente que o botão de "Indicação do Imóvel" dentro do container de devolução na Aba 3 não funcionava.
Após análise do comportamento do HTMX, constatou-se que o botão, apesar de ter o `type="button"`, encontra-se dentro do escopo do formulário principal da Aba 3 (`form03`).
O HTMX (a partir da versão 1.9) verifica nativamente a validação do HTML5 do formulário que envolve o botão antes de disparar o `hx-post`. 
Como a Aba 3 possui campos obrigatórios (`required`) que o usuário, intencionalmente, ainda não havia preenchido (pois estava devolvendo o processo precocemente por falta de RIP), o formulário era considerado "inválido".
Consequentemente, o HTMX abortava a requisição silenciosamente.

A correção consistiu em adicionar o atributo `formnovalidate` nos botões de devolução para instruir o HTMX (e o navegador) a ignorar a validação dos demais campos da aba e enviar o request imediatamente.

## 1. Estado Anterior (Antes)
```html
                <button type="button" 
                        hx-post="{{ route('processos.devolver', $processo->id) }}" 
                        hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'
                        hx-vals='{"aba": 1}' 
```

## 2. Estado Novo (Depois)
```html
                <button type="button" formnovalidate
                        hx-post="{{ route('processos.devolver', $processo->id) }}" 
                        hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'
                        hx-vals='{"aba": 1}' 
```

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/aba3.blade.php`.
2. Localize os botões de devolução (`btnEnviarDevolucaoRapida`).
3. Remova o atributo `formnovalidate` da tag `<button>`.
