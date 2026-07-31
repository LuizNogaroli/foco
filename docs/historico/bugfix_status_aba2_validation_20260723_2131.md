# Interação: Correção do Bug de Mudança de Status (Aba 2) e Validações
**Data:** 23/07/2026

## Contexto do Problema Reportado
O usuário relatou: "Aba 2, salvar e enviar não está mudando o status". A investigação técnica constatou que não havia uma, mas duas falhas silenciosas combinadas impedindo a operação:
1. **Ausência de Feedback Visual:** O formulário da aba sofria erros normais de validação bloqueantes do Laravel no Back-End. O Laravel abortava a mudança do status e redirecionava à aba, porém as views não possuíam o renderizador para apresentar as mensagens de erro (`@if($errors->any())`). O usuário via apenas um recarregamento estático.
2. **Double-Write Interrompido:** O painel de Kanban do projeto continuava conectado ao Supabase (`tabela_status_fluxo`). A ponte de atualização via Javascript havia quebrado pois dependia de botões que foram refatorados. Consequentemente, mesmo quando uma tramitação era 100% exitosa no Laravel, ela não era sinalizada ao Supabase Kanban.

## Solução Adotada (Antes e Depois / Rollback)

### 1. Adição de Visibilidade de Erros (Abas 1, 2 e 3)
**Antes:**
As tags `<form>` nativas do Blade estavam desprovidas da captura de exceções do componente Validator do PHP.

**Depois:**
Um bloco `@if($errors->any())` renderizando a `$errors->all()` em alertas vermelhos foi adicionado logo após o `@csrf` nos formulários de `aba1.blade.php`, `aba2.blade.php` e `aba3.blade.php`.

**Plano de Rollback / Desfazer:**
Para reverter, basta apagar o bloco `@if($errors->any())` (com sua `<div>` estilizada) dos três arquivos nas views (`resources/views/processos/abas/`). O formulário voltará a falhar sem alertar o usuário.

---

### 2. Sincronização com Supabase (Fluxo)
**Antes (foco-02-v2.js - Linhas 1599-1604):**
```javascript
      btn.innerHTML = 'Salvando...';
      const sucesso = await executarSalvamentoAba2();
      if (sucesso) {
        formReq2.submit();
      } else {
```

**Depois (foco-02-v2.js):**
```javascript
      btn.innerHTML = 'Salvando...';
      const sucesso = await executarSalvamentoAba2();
      if (sucesso) {
        const processId = localStorage.getItem("CURRENT_PROCESS_ID");
        if (processId && window.parent && typeof window.parent.updateStatusFluxo === 'function') {
            await window.parent.updateStatusFluxo(processId, 4); 
        }
        formReq2.submit();
      } else {
```

**Antes (ProcessoController.php - Linhas 730-754):**
O método interno `syncProcessoStatusToSupabase` via `GuzzleHttp` se restringia unicamente a espelhar dados na `tabela_requerimentos`. A `tabela_status_fluxo` (painel Kanban) ficava alheia.

**Depois (ProcessoController.php):**
Inserido no controlador PHP uma reprodução fidedigna do `workflowMapping` da aplicação JS, com rotinas GET/PATCH para identificar atualizações preexistentes na `tabela_status_fluxo` e postar o avanço correto do status a fim de garantir consistência absoluta caso o Javascript do cliente sofra timeout.

**Plano de Rollback / Desfazer:**
1. Em `public/js/foco-02-v2.js`, excluir a injeção da condicional `if (processId...) await window.parent.updateStatusFluxo(processId, 4);` na escuta de submissão.
2. Em `app/Http/Controllers/ProcessoController.php`, método `syncProcessoStatusToSupabase`, excluir todo o mapeamento e chamadas HTTP REST (`$client->patch...`) logo após o comentário `// Sync com tabela_status_fluxo (Kanban)`, restaurando a função ao seu arranjo primitivo.

## Reflexão Conclusiva do Usuário
"Então chego à conclusão que mesmo num protótipo é melhor começar logo com a stack definitiva, pois senão depois dá trabalho para migrar, o código corre o risco de ficar 'sujo' e ainda nos arriscamos a ter problemas de lógica no processo."
