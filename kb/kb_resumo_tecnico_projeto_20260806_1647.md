# KB: Resumo Técnico do Projeto (SPU Foco)

Documento de referência rápida da arquitetura, tabelas, arquivos e fluxos do sistema de Admissibilidade SPU (Foco). Mantenha atualizado ao realizar mudanças estruturais.

**Última atualização:** 2026-08-06

---

## 1. Visão Geral

Aplicação **Laravel 11** que gerencia processos de destinação de imóveis da União (fluxo: Indicação → Diagnóstico → Viabilidade → Cadeia de Assinaturas). Frontend em **Blade + JS Vanilla** (arquivos em `public/js`), banco **PostgreSQL** via **Eloquent**, com integrações legadas ao **Supabase** (webhooks/Cloud Functions e consultas de RIP).

Arquitetura híbrida: telas em Blade, regras centralizadas em `ProcessoController`, e scripts JS legados ainda conversando com Supabase para algumas rotinas.

---

## 2. Tabelas do Banco de Dados

### 2.1 Núcleo / Cadastro

| Tabela | O que armazena | PK | Observações |
|---|---|---|---|
| `users` | Usuários/servidores (com `cpf`, `cargo`, `telefone`) | id | Autenticação Laravel |
| `roles`, `permissions`, `model_has_roles`, `role_has_permissions` | Spatie Permission (roles: `Equipe Destinação`, `Chefia`, `CDE`, etc.) | id | Migration `2026_07_18_142616` |
| `equipe_servidores` | Vínculo usuário ↔ perfil ↔ UF (`NAC` = nacional) | id | Unique `(user_id, perfil, uf)` |
| `processos` | Processos de admissibilidade (`numero_requerimento` único, `status_atual`, `tipo_requerimento`, `uf`, `municipio`, `tramitacao`) | id | Ex: `PR2026001` |
| `requerimentos` | Dados do requerimento (nup_sei, requerente, representante, prioridade_legal, documentos_anexados JSON) | numero_requerimento | PK não numérica |

### 2.2 Fluxo / Formulários (Estrutura Foco)

| Tabela | O que armazena | Relacionamento |
|---|---|---|
| `foco` | Vínculo 1:1 com `processos` + `aba_salva` | `foco.processo_id → processos.id` |
| `foco_aba1` | Dados da Aba 1 (conceituacao_imovel, resposta_devolucao, solicitacao_criacao_rip) | `foco_aba1.foco_id → foco.id` (PK = foco_id) |
| `foco_rips` | RIPs vinculados (numero_rip + destinação: `destinacao_terreno`, `area_terreno_parcial`, `destinacao_imovel`, `area_imovel_parcial`) | 1:N `foco_id` |
| `foco_cadastros_minimos` | Áreas sem RIP (cep, logradouro, municipio, uf, area, geo `latitude`/`longitude`/`modo_localizacao`, destinação) | 1:N `foco_id` |
| `foco_aba2` | Aba 2 - Diagnóstico (situacao_ocupacional, incidencias/riscos/restricoes JSON, geolocalização, observacoes) | `foco_aba2.foco_id → foco.id` (PK = foco_id) |
| `foco_aba3` | Aba 3 - Viabilidade: `dados_analise` (JSON) e `proposta_destinacao` (JSON) | `foco_aba3.foco_id → foco.id` (PK = foco_id) |
| `foco_drafts` | Rascunhos por (processo, usuário, aba) | Unique `(processo_id, user_id, aba)` |

### 2.3 Auditoria / Trilha

| Tabela | O que armazena |
|---|---|
| `tramites` | Audit trail imutável: `etapa`, `acao`, `usuario_id`, `justificativa`, `dados_snapshot` (JSON com todos os campos do formulário no momento) |
| `cache`, `jobs`, `sessions` | Tabelas de infraestrutura do Laravel |

---

## 3. Models (app/Models)

| Model | Tabela | Notas |
|---|---|---|
| `Processo` | `processos` | hasMany `tramites`, hasOne `foco`, hasOne `requerimento` |
| `Foco` | `foco` | hasOne `aba1/aba2/aba3`, hasMany `rips`/`cadastrosMinimos` |
| `FocoAba1`, `FocoAba2` | `foco_aba1`, `foco_aba2` | PK = foco_id (não incremental); `FocoAba2` com casts JSON (`ha_incidencia`, `riscos`, etc.) |
| `FocoAba3` | `foco_aba3` | casts JSON → array |
| `FocoRip`, `FocoCadastroMinimo` | `foco_rips`, `foco_cadastros_minimos` | N:1 foco |
| `FocoDraft` | `foco_drafts` | Rascunhos |
| `Tramite` | `tramites` | `dados_snapshot` cast array |
| `Requerimento` | `requerimentos` | PK = numero_requerimento |
| `EquipeServidor` | `equipe_servidores` | Vínculo perfil/UF |
| `User` | `users` | Spatie `HasRoles` |

---

## 4. Controllers

| Controller | Responsabilidade |
|---|---|
| `ProcessoController` | **Cérebro do sistema** — resolve aba por status/perfil, renderiza show, tramita, devolve, historico (modelos B-G). ~1470 linhas. |
| `DraftController` | Rotas de autosave (save/load/clear/cleanOld) |
| `ConfiguracoesController` | Configurações e status de processo |
| `EquipeController` | CRUD de equipe + importação |
| `ServidorController` | CRUD de servidores |
| `DashboardController` | Painel gerencial |
| `ProfileController` + Auth | Perfil e autenticação |

### Métodos-chave de `ProcessoController`
- `getAbaEStatus()` (l.22) — mapa status → aba + próximo status
- `perfilPodeOperar()` (l.66) — quais status cada perfil opera
- `getAbasDoPerfil()` (l.113) — quais abas cada perfil vê
- `index()` (l.133) — kanban/painel com colunas por status
- `show()` (l.284) — renderiza o processo na aba correta
- `tramitar()` (l.431) — salva formulário, cria trâmite, move status e cadeia de assinaturas
- `abrir()` (l.256), `devolver()` (l.839), `receberDevolucao()` (l.873)
- `historico()` e `historicoModeloB/G` — visualizações do histórico
- `getPerfilAtual()` (l.928) — resolve perfil (com cookie `perfil_simulado` da Direção)

---

## 5. Rotas (routes/web.php)

| Rota | Método |
|---|---|
| `/` | index (kanban) — `processos.index` |
| `/processos/{processo}` | show — `processos.show` |
| `/processos/{processo}/tramitar` | tramitar — `processos.tramitar` |
| `/processos/{processo}/abrir` · `/devolver` · `/receber-devolucao` | ações de fluxo |
| `/processos/{processo}/historico` + `/modelo-b` … `/modelo-g` | histórico |
| `/api/vocacoes` | vocações |
| `/dashboard`, `/configuracoes`, `/equipe`, `/servidores` | demais painéis |
| `/draft/save|load|clear|clean-old` | autosave |
| `/profile` | perfil |

Não há middleware de roles nas rotas; o controle é imperativo no `ProcessoController`.

---

## 6. Views (resources/views/processos)

| Arquivo | Conteúdo |
|---|---|
| `index.blade.php` | Kanban com colunas por status + simulador de perfil (Direção) |
| `show.blade.php` | Container que inclui a aba correta; carrega JS globais (`db.js`, `fetch_spu.js`, `custom-select.js`, `formulario.js`, `hints.js`) |
| `abas/aba1.blade.php` | Aba 1 - Indicação do Imóvel (modal Inserir RIP com destinação) |
| `abas/aba2.blade.php` | Aba 2 - Diagnóstico (accordions RIP, mapa Leaflet, destinação) |
| `abas/aba3.blade.php` | Aba 3 - Viabilidade (formulário extenso, JS embutido, mock de ações judiciais) |
| `abas/aba7.blade.php` | **Boxes de manifestação** (ver `kb_boxes_manifestacao_aba7_20260806_1647.md`) |
| `abas/resumos/*` | Blocos de resumo/leitura das abas |
| `historico*.blade.php` | Modelos de histórico (A escolha, B-G) |
| `abas/partials/timeline.blade.php` | Timeline |

---

## 7. JavaScript (public/js)

### Ativos (carregados pelas views)
| Arquivo | Onde é carregado | Função |
|---|---|---|
| `db.js` | `show.blade.php`, `configuracoes.blade.php` | Cliente Supabase + orquestração de dados |
| `workflow.js` | `configuracoes.blade.php` | Objeto `WORKFLOW_STAGES` (fonte de verdade JS dos status) |
| `fetch_spu.js` | `show.blade.php`, `aba3` | Busca dados da `tabela_spu` por RIP |
| `fetch_acoes.js` | `aba3.blade.php` | Busca ações judiciais |
| `formulario.js` | `show.blade.php`, `aba3` | Upload/manipulação de arquivos |
| `hints.js` | `show.blade.php`, `aba3` | Hints/tooltips |
| `custom-select.js` | `show.blade.php`, `aba3` | Selects customizados |
| `sync.js` | `aba3.blade.php` | Motor de sync/salvamento de campos |
| `foco-01.js` | `show.blade.php` (aba 1) | Lógica da Aba 1 (modal RIP, destinação) |
| `foco-02-v2.js` | `show.blade.php` (aba 2) | Lógica da Aba 2 |
| `painel-gerencial.js` | `dashboard.blade.php` | Dashboard |
| `configuracoes.js` | `configuracoes.blade.php` | Configurações |

### 7.2 Arquivos que NÃO são carregados por views (semi-ativos/legado)

> `foco-06.js` e `manifestacao-scripts.js` existem em `public/js` mas **nenhuma view os carrega** (confirmado por busca em `resources/views`). O `foco-06.js` foi editado junto da Aba 3 em 03/08, porém permanece órfão — sua lógica de `handleRadioToggle`/`addSelectToggle` (campos 51 a 511) é executada pelo JS embutido no próprio `aba3.blade.php`.

### Legado / utilitários pontuais
`seed_*.js`, `patch_*.js`, `test_*.js`, `fix_*.js`, `rebuild_*.js`, `update_*.js`, etc. — scripts de uma vez/seed que **não** são carregados nas views. Não editar sem necessidade.

---

## 8. Status e Fluxo

- **Status iniciais:** `Aguardando Análise` → `Indicação do Imóvel` (Aba 1) → `Diagnóstico do Imóvel` (Aba 2) → `Análise de Viabilidade` (Aba 3).
- **Cadeia de assinaturas (Aba 7):** Chefia → Coordenação → Superintendência → Conformidade Prévia (Equipe C.G.) → Coordenação-Geral → Direção → CDE → Deliberado.
- Ver detalhes completos em `docs/permissoes.md` e `kb_boxes_manifestacao_aba7_20260806_1647.md`.

> **Nota (renomeação incompleta):** `workflow.js` (e o fallback `_WORKFLOW_STAGES` em `db.js`) ainda usam nomes antigos — ex: "Validação análise de viabilidade - Chefia" (em vez de "Validação - Chefia") e "Conferência análise de viabilidade" para a Equipe C.G. (em vez de "Conformidade Prévia"). O controller usa os nomes novos. O `workflow.js` só é carregado em `configuracoes.blade.php`, então o impacto atual é baixo, mas a renomeação fica pendente (ver `kb_renomeacao_status_destinacao_rip_20260803_1858.md`).

---

## 9. Infraestrutura

- **Banco:** PostgreSQL (produção/homologação); **SQLite** no dev local (`.env` atual: `DB_CONNECTION=sqlite`). Migrations em `database/migrations`.
- **Deploy:** Render (`render.yaml`) + Supabase (ver `kb_deploy_render_postgresql_20260723_1635.md`).
- **Arquivo morto / protótipos:** `archive/prototipos/` — protótipos antigos das abas 4-10 (`prototipo_html/`, ignorado pelo git) e scripts pontuais órfãos que antes ficavam na raiz (`.py`, `.cjs`, `old_controller.php`, etc.). Nada aqui é carregado pelo app.
- **Frontend build:** Vite (`resources/js/app.js`, `resources/css/app.css`).

---

## 10. Pendências / Notas Ativas

- Branch `equipe_cg` em `tramitar()` ainda valida valores antigos (`suficiente`/`insuficiente`); formulário já envia novos valores (`favoravel`/`favoravel_condicionantes`/`nao_favoravel`).
- 4 testes pré-existentes quebrados (redirecionam para `/dashboard` em vez de `/`).
- Backlog: backfill de `destinacao_terreno`/`destinacao_imovel` para RIPs antigos; trâmite na abertura (`abrir()`).
