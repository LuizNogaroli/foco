# AGENTS.md

Instruções para agentes de IA que atuam neste repositório.

## Regras gerais

1. **Antes de qualquer tarefa**, leia o resumo técnico do projeto para se contextualizar:
   - `kb/kb_resumo_tecnico_projeto_20260806_1647.md` — arquitetura, tabelas do banco, models, controllers, views, JS ativos.
2. **Ao se referir aos boxes de manifestação da Aba 7**, use a nomenclatura canônica documentada em:
   - `kb/kb_boxes_manifestacao_aba7_20260806_1647.md` — nomes "Box - X", chaves de código (`box-{chave}`) e ID HTML.
   - Quando o usuário citar "Box - Equipe C.G.", "box equipe_cg" ou "box-equipe_cg", trata-se da mesma referência.
3. **Regras de negócio e preenchimento** (campos, hints, validações):
   - `kb/regras_negocio.md` e `kb/regras_preenchimento.md`
4. **Permissões, perfis e controle de acesso:**
   - `docs/permissoes.md`
5. **Mudanças de status:** qualquer renomeação de status exige atualizar TODOS os pontos listados em `kb/kb_renomeacao_status_destinacao_rip_20260803_1858.md` (controller, `workflow.js`, `db.js`, abas 2/3).
6. **Pendências da última sessão:** ver o arquivo `docs/pendencias_*` mais recente.
7. **Sempre que alterar arquivos de tela/lógica**, confira os padrões em `kb/estrutura_do_projeto.md` (Separation of Concerns).

## Workflow sugerido ao iniciar uma sessão

1. Ler `kb/kb_resumo_tecnico_projeto_20260806_1647.md`.
2. Ler o `docs/pendencias_*` mais recente e o `kb/kb_handoff_estado_atual_*` mais recente.
3. Ler `git status` / `git diff` para ver o trabalho não commitado.
4. Executar a tarefa pedida.
5. Atualizar `docs/pendencias_AAAAMMDD.md` ao final da sessão.

## Convenções de código

- PHP/Laravel: seguir Eloquent e Blade; sem middleware de roles nas rotas (controle imperativo no controller).
- JS: Vanilla, arquivos em `public/js`; não misturar lógica de tela entre arquivos.
- Comandos de verificação: `php artisan migrate`, `php artisan test`, `npm run build` (Vite).
