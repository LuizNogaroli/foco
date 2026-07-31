# Correção de Conflito de Formulário Aba 7 e Exclusão do Autosave

## Contexto
O usuário relatava que, ao preencher a manifestação da Coordenação na Aba 7 e clicar em "Salvar e Enviar", o sistema retornava um erro de validação do Laravel informando que a opção "Suficiente/Insuficiente" era obrigatória, mesmo a opção estando marcada.

## Diagnóstico
Após aplicar um log global no `ProcessoController@tramitar`, identificamos que o payload POST enviado pelo navegador continha o campo `acao_aba7` com o valor `"chefia"`, em vez de `"coordenacao"`. Isso ocorria porque:
1. O script legado `autosave.js` estava em operação.
2. Em um acesso anterior, foi gerado um rascunho enquanto a etapa ativa era a Chefia.
3. Ao carregar a tela para a Coordenação, o `autosave.js` puxava os dados do rascunho anterior.
4. O `autosave.js` procurava por qualquer elemento no DOM com `name="acao_aba7"` e, de forma inadvertida, sobrescrevia a propriedade `value` do botão `<button name="acao_aba7" value="coordenacao">` para `"chefia"`.
5. Como resultado, ao submeter o formulário, a ação enviada ao Controller não condizia com o escopo ativo.

## Resolução
A pedido do usuário, o sistema de `autosave.js` foi completamente descontinuado, priorizando o salvamento manual ("Salvar Rascunho") para dar maior sensação de controle ao usuário final.

### Estado Anterior (Antes)
No arquivo `resources/views/processos/show.blade.php`:
```html
<script src="{{ asset('js/autosave.js') }}"></script>
```

### Estado Novo (Depois)
O arquivo `public/js/autosave.js` foi fisicamente deletado do servidor.
No arquivo `resources/views/processos/show.blade.php`:
```html
<!-- autosave.js removido a pedido do usuario -->
```

### Plano de Rollback / Desfazer
Para reverter a exclusão do autosave:
1. Recriar o arquivo `public/js/autosave.js` a partir do histórico de commits do repositório Git ou de um backup anterior.
2. No arquivo `resources/views/processos/show.blade.php`, substituir a linha do comentário:
   ```html
   <!-- autosave.js removido a pedido do usuario -->
   ```
   por:
   ```html
   <script src="{{ asset('js/autosave.js') }}"></script>
   ```
3. Caso deseje manter o `autosave.js` ativo sem o bug, deve-se adicionar uma trava no `restoreData` do script para ignorar tags `<button>`.
