# Estado do Desenvolvimento — 31/07/2026

## Implementações e Correções Realizadas nesta Sessão

1. **Correção do Registro de Trâmites (Audit Trail):**
   - **Causa raiz:** `tramitar()` e `devolver()` criavam trâmites apenas com `dados_snapshot`, sem preencher `acao`, `etapa`, `usuario_id` e `justificativa`. Por isso os movimentos mais recentes apareciam com campos nulos e o histórico não classificava os eventos.
   - **`tramitar()`** agora grava:
     - Abas 1–3: `acao` = "Aba {N} Salva" (primeira vez) ou "Aba {N} Alterada" (quando a aba já existia no foco); `etapa` = "Preenchimento - Aba {N}".
     - `resposta_devolucao` presente → trâmite "Devolução Resolvida" (com a resposta em `justificativa`) criado **antes** do trâmite de salvamento.
     - Aba 7 (assinatura): `acao` = "Manifestação", `etapa` = perfil (Chefia, Coordenação, Superintendência, Equipe C.G., Coordenação-Geral, Direção, CDE).
     - Rascunho: `acao` = "Atualização".
     - Quando a ação da Aba 7 faz a tramitação assumir "Devolvido": trâmite extra `acao` = "Devolvido" com `justificativa` extraída do campo `obs_*` do perfil.
   - **`devolver()`** agora grava `acao` = "Devolvido", `etapa` = status de origem (antes da alteração), `usuario_id` e `justificativa` = `motivo_devolucao`.
   - **Novo método `receberDevolucao()`:** a rota `processos.receber-devolucao` existia mas o método não (gerava erro 500 no botão "Estou Ciente / Receber"). Implementado: reativa `tramitacao` para "Normal", sincroniza status no Supabase e grava trâmite `acao` = "Recebido" herdando a justificativa da última devolução.

2. **Renderização do Alerta de Devolução:**
   - `show()` agora computa `$ultimaDevolucao` (último trâmite "Devolvido") e `$jaRecebido` (existência de "Recebido" após a devolução). Antes essas variáveis não existiam no controller e o alerta "Processo Devolvido" nunca era exibido.

3. **Box "Devolução Resolvida" nas Abas Seguintes:**
   - Novo componente `resources/views/components/alerta-devolucao-resolvida.blade.php`.
   - Exibido em `show.blade.php` logo abaixo do alerta "Processo Devolvido", quando existe uma "Devolução Resolvida" posterior à última devolução.
   - Mostra quem resolveu, data da resolução e o texto dos ajustes realizados, permitindo que o usuário das abas seguintes saiba de imediato que o processo foi devolvido e que o motivo já foi resolvido pela equipe anterior.
   - `show()` passou a computar `$ultimaResolucao`.

4. **Bugfix — Histórico Modelo A:**
   - O método `historico()` não passava `$historicoTramites` para a view `historico.blade.php`, que inclui a timeline (`processos/abas/partials/timeline`). Resultado: "Nenhum evento registrado no histórico ainda." mesmo com trâmites no banco.
   - Corrigido adicionando `$historicoTramites = $this->getHistoricoTramites($processo)` e passando a variável via `compact`.

## Validação Realizada

- **Teste de ponta a ponta manual** no processo **SP2026003 (ID 215)** via servidor local (`http://127.0.0.1:8000`):
  - Sequência completa registrada corretamente: Aba 1 Salva → Aba 2 Salva → **Devolvido** (justif.) → **Recebido** → **Devolução Resolvida** (justif.) → Aba 1 Alterada → Aba 2 Alterada.
  - Todos os trâmites com `acao`, `etapa`, `usuario_id` e `justificativa` preenchidos.
- **Testes automatizados novos:**
  - `tests/Feature/TramiteRecordingTest.php` (8 testes) — abas salvas/alteradas, devolução resolvida, manifestação na aba 7, devolução na aba 7, `devolver()`, `receberDevolucao()` e box "Devolução Resolvida" na aba 2.
  - `tests/Feature/HistoricoViewsTest.php` (3 testes) — replicação da sequência real do SP2026003, todas as 7 telas de histórico renderizam, modelo B e modelo A exibem devolução/resolução.
- **Observação de design confirmada:** a tag do trâmite é imutável — uma vez "Devolvido", permanece "Devolvido" (event sourcing / audit trail). Sem alteração (a pedido do usuário).

5. **Configuração de Deploy no Render com Supabase:**
   - Ajuste em `start.sh` para condicionar a execução de seeders a `$SEED_DATABASE = true`, protegendo bases populadas contra truncamentos acidentais.
   - Ajuste em `render.yaml` definindo a topologia do web service `foco-18-app` e eliminando dependência do banco local do Render.
   - Deploy concluído com sucesso e aplicação **Live** em produção conectada de forma estável através do Connection Pooler IPv4 do Supabase (`port 6543`).
