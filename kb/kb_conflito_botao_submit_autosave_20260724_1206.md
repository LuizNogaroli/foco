# Conflito de Manipulação de DOM e Formulários com Autosave

**Data:** 2026-07-24
**Contexto:** Laravel 11, Blade, Manipulação de DOM via JS (`autosave.js`)

## O Problema
Scripts genéricos de "Autosave/Draft Restore" que restauram estado de formulários iterando sobre chaves de um payload guardado (`document.querySelectorAll('[name="..."]')`) podem, acidentalmente, buscar botões de submit (`<button type="submit" name="acao">`) caso o atributo `name` desses botões coincida com alguma chave gravada no banco de dados.
Se o script executar a atribuição `element.value = savedValue` no botão, ele altera silenciosamente o valor enviado pelo botão no momento do clique. Isso compromete o fluxo do formulário.

## Sintoma no Laravel
O Controller valida uma rota esperando campos específicos (ex: `decl_coordenacao_opcao`) com base na etapa reportada pelo botão de submit (`$acao = $request->input('acao_aba7')`). Como o botão submetido teve seu valor corrompido para o nome de outra etapa (ex: `"chefia"`), o Controller processa a condicional errada e aciona mensagens de erro de validação (através do `withErrors()`) reportando que campos daquela outra etapa estão ausentes. Isso causa muita confusão, pois o usuário preencheu corretamente a interface de sua etapa atual, mas o backend avaliou a regra da etapa antiga.

## Solução Adotada
A exclusão definitiva da funcionalidade de `autosave.js` no projeto atual, substituída por um botão de "Salvar Rascunho" manual, garantindo que submissões parciais passem pelo fluxo tradicional do formulário em vez de sincronização via AJAX/DOM no background.

**Prevenção:** Em projetos onde a funcionalidade genérica de autosave precisa ser mantida, é estritamente obrigatório adicionar condicionais (guards) bloqueando interações com tags `<button>` durante a restauração de dados:
```javascript
if (el.tagName === 'BUTTON') return;
```
