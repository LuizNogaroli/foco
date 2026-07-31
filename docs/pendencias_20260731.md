# Lista de Pendências — 31/07/2026

Abaixo estão listadas as tarefas restantes (TODO list) identificadas no encerramento desta sessão de desenvolvimento:

- [ ] **Confirmar em tela o Histórico Modelo A:**
  - A correção (`historico()` passando `$historicoTramites`) está validada por teste, mas falta confirmar visualmente no servidor local em `http://127.0.0.1:8000/processos/215/historico`.

- [ ] **Ajustes finos na UI (a definir pelo usuário):**
  - O usuário indicou que fará "pequenos ajustes" no box "Devolução Resolvida" e em outros pontos do fluxo (não especificados ainda).

- [ ] **Falhas pré-existentes na suíte de testes (4):**
  - `AuthenticationTest::test_users_can_authenticate_using_the_login_screen`
  - `EmailVerificationTest::test_email_can_be_verified`
  - `RegistrationTest::test_new_users_can_register`
  - `ExampleTest::test_the_application_returns_a_successful_response`
  - Causa: esperam redirecionamento para `/dashboard`, mas o app redireciona para `/` (`processos.index`). Não relacionadas às mudanças desta sessão; corrigir os testes ou a rota de pós-login.

- [ ] **Avaliar registro de trâmite em `abrir()`:**
  - O método `abrir()` (transição de "Aguardando Análise" para "Indicação do Imóvel") não cria trâmite. Avaliar se a abertura do processo deve ser registrada como movimento no histórico.

- [ ] **AGENTS.md ausente:**
  - Não existe `agents.md`/`AGENTS.md` no repositório. O protocolo de encerramento de sessão (documentar em `docs/estado_desenvolvimento_YYYYMMDD.md` e `docs/pendencias_YYYYMMDD.md`) vem sendo seguido por convenção. Considerar criar `AGENTS.md` para codificar esse protocolo e os comandos de validação (`php artisan test`, `php -l`).
