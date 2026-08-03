# Lista de Pendências — 03/08/2026

Abaixo estão listadas as tarefas restantes (TODO list) identificadas no encerramento desta sessão:

---

## 🔴 Alta Prioridade

- [ ] **Executar a migration em produção/homologação:**
  - `php artisan migrate`
  - Migration: `2026_08_03_214501_add_destinacao_fields_to_foco_rips_table.php`
  - Adiciona: `destinacao_terreno`, `area_terreno_parcial`, `destinacao_imovel`, `area_imovel_parcial` à tabela `foco_rips`.

- [ ] **Validar em tela os campos de Destinação do RIP:**
  - Abrir modal "Inserir RIP" na Aba 1.
  - Confirmar que os radios Integral/Parcial aparecem para Terreno e Imóvel.
  - Confirmar que o campo de metragem exibe/esconde ao selecionar "Parcial".
  - Salvar um processo com RIP e confirmar que os valores são persistidos no banco.

---

## 🟡 Média Prioridade

- [ ] **Confirmar em tela o novo nome "Diagnóstico do Imóvel":**
  - Acessar o kanban e verificar se um processo neste status exibe "Diagnóstico do Imóvel".
  - Verificar o título `<h2>` da Aba 2 ao abrir um processo com este status.
  - Verificar o botão "🔙 Diagnóstico do Imóvel" na Aba 3 (devolução).

- [ ] **Confirmar em tela o Histórico Modelo A:**
  - Acessar `http://127.0.0.1:8000/processos/215/historico` e validar exibição dos trâmites.

---

## 🟢 Baixa Prioridade / Backlog

- [ ] **Ajustes finos na UI (a definir pelo usuário):**
  - O usuário pode solicitar refinamentos no box "Devolução Resolvida" e outros pontos do fluxo.

- [ ] **Falhas pré-existentes na suíte de testes (4):**
  - `AuthenticationTest`, `EmailVerificationTest`, `RegistrationTest`, `ExampleTest`
  - Causa: esperam redirecionamento para `/dashboard`, mas o app redireciona para `/`.
  - Não relacionadas às mudanças desta sessão.

- [ ] **Avaliar registro de trâmite em `abrir()`:**
  - O método `abrir()` não cria trâmite. Avaliar se a abertura do processo deve aparecer no histórico.

- [ ] **Dados de RIP para processos existentes:**
  - Processos já cadastrados antes da migration não terão os campos `destinacao_terreno`/`destinacao_imovel` preenchidos (ficarão `null`). Avaliar se há necessidade de backfill.
