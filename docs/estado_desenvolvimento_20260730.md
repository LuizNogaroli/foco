# Estado do Desenvolvimento — 30/07/2026

## Implementações e Correções Realizadas nesta Sessão

1. **Remoção de Cadastro Mínimo Duplicado (Aba 2):**
   - Corrigida a renderização duplicada do Cadastro Mínimo na Aba 2. O Javascript legado (`foco-02-v2.js`) injetava o bloco duplicado ao final da página (`accordion-indicacoes`). Essa chamada foi comentada pois a página já o renderiza dinamicamente e com CSS correto dentro do container pai (`acc_aba1b`).

2. **Refatoração Completa do Fluxo de Rascunho (Aba 1, Aba 2, Aba 3):**
   - **Correção Geral de Validação:** Removida a exigência de campos obrigatórios (`required`) ao clicar no botão "Salvar Rascunho". Isso agora permite o salvamento de formulários preenchidos parcialmente (como é o propósito de um rascunho). A validação rigorosa continua ativa no botão final "Salvar e Enviar".
   - **Aba 2 e Aba 3 migrados para `/draft/save` (Laravel):** Substituída a lógica legada do Supabase em background por chamadas assíncronas via *AJAX* direcionadas à tabela `foco_drafts` no MySQL do Laravel.
   - **Remoção de Sufixos `[]` de Checkboxes:** Ajustada a serialização de FormData para remover o sufixo `[]` dos nomes de campos multiseleção para bater com o padrão Blade esperado.
   - **Carga de Rascunhos do Banco (Laravel):** Alterado o método `show` no `ProcessoController` do Laravel para buscar ativamente o rascunho correspondente à aba do usuário logado na tabela `foco_drafts` e fundi-lo com `$dados`. Isso faz os dados salvos reaparecerem na tela ao atualizar a página.
   - **Extração Dinâmica de ID do Processo:** Substituída a dependência instável de `localStorage` para ler o ID do processo; agora ele é extraído da URL do formulário (`actionUrl`).
   - **Feedback Visual (Alerts):** Padronizada a exibição do alerta de sucesso ("Rascunho salvo com sucesso!") nas Abas 1, 2 e 3 após a conclusão das requisições assíncronas.
   - **Correção da Vinculação do Rascunho (Aba 1):** Restaurada a atribuição global `window._saveDraft` que havia sido omitida na refatoração, fazendo o botão "Salvar Rascunho" da Aba 1 voltar a ser executado no clique.

3. **Exibição do CEP e Endereço do RIP (Aba 1):**
   - Alterada a função `adicionarRipNaLista` e o fluxo de listeners no `foco-01.js` para consultar o banco de dados SPUnet via `fetchSPU` e incluir no card formatado do RIP o **Logradouro, Município/UF e o CEP** do imóvel cadastrado, permitindo a confirmação visual pelo técnico.

4. **Correção de Flicker no Título (Aba 1):**
   - Comentada a instrução Javascript que forçava a sobreposição do título da página a partir de dados em cache no Supabase. O título carregado originalmente via Blade (`$requerimento->tipo_requerimento`) agora permanece estático e correto.
