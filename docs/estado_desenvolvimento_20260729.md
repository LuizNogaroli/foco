# Handoff — 29/07/2026

## O que foi feito hoje

### Modelos de Visualização de Histórico (E, F, G)
- **Modelo E (BPMN)**: Rota `processos.historico.modelo-e`, controller `historicoModeloE()`, view `historico_modelo_e.blade.php` (gateway ◆ → swimlane laranja → converge ◇; ciclo de devolução à direita)
- **Modelo F (Colunas)**: Rota, controller `historicoModeloF()`, view `historico_modelo_f.blade.php` (colunas lado a lado, scroll horizontal, conector →)
- **Modelo G (Matriz)**: Rota, controller `historicoModeloG()` (11 linhas × N passagens), view `historico_modelo_g.blade.php` (tabela sticky, cores por tipo de evento)
- Cards de escolha adicionados em `historico_escolha.blade.php`
- Controller `ProcessoController.php`: métodos E, F, G + `tramitar()` já trata `solicitacao_criacao_rip`
- Rotas em `routes/web.php`

### APP_NAME
- Alterado para `"Plataforma Integrada de Gestão do Patrimônio da União"` em:
  - `.env`, `.env.production`
  - `config/app.php`
  - Título da tela de login (`auth/login.blade.php` — azul + cinza, sem subtítulo)
  - `<title>` em `show.blade.php`

### Prioridade Legal
- Migration `2026_07_28_183503_set_prioridade_legal_default.php`: `default('Não')` na coluna
- DB update: 141 registros atualizados para `'Não'`

### Solicitação de Criação de RIP (bugs corrigidos)
1. **`renderSolicitacaoCriacaoRip()`** (`foco-01.js`): reescrita de `innerHTML` para DOM API (`createElement`, `textContent`, `setAttribute`) — agora lida com caracteres especiais sem quebrar HTML
2. **`window._saveDraft`** estava **undefined** em todas as abas:
   - `foco-01.js`: `window._saveDraft = executarSalvamento`
   - `foco-02-v2.js`: `window._saveDraft = executarSalvamentoAba2`
   - `aba3.blade.php`: inline `window._saveDraft = function(){ document.getElementById('form03').requestSubmit(); }`
3. **Fallback MySQL**: `aba1.blade.php` → `window.INLINE_SOLICITACAO_RIP` lido do servidor; `foco-01.js` carrega dele quando Supabase não tem o dado
4. **Resumo (`aba1b.blade.php`)**: já exibe o card 🔔 quando `solicitacao_criacao_rip` existe

### Mapa / Geolocalização (Aba 2) — estava quebrado
- `patch-modal.js` (build script) continha as funções `inicializarMapa`, `fecharGeoModal`, `buscarNoModal`, `salvarGeoModal` mas **nunca eram injetadas no runtime**
- Leaflet e Leaflet.draw **não eram carregados** (nenhum CDN)
- **Correção**:
  - `aba2.blade.php`: `link` + `script` CDN do Leaflet 1.9.4 e Leaflet.draw 1.0.4
  - `foco-02-v2.js`: todas as funções do modal (mapa, busca ViaCEP + Nominatim JSONP, salvar coordenadas) adicionadas globalmente a partir da linha 1549

### Deploy Railway
- `railway.json`: build (composer install, npm ci, php artisan storage:link, view:cache, config:cache)
- `nixpacks.toml`: extensões PHP (gd, zip, pdo_pgsql, pgsql)
- `.env.production`: template com variáveis Railway (APP_KEY, DB_CONNECTION pgsql, etc.)
- `public/storage`: symlink criado
- Guia: `docs/historico/guia_deploy_railway_20260722_1841.md`

### Deploy Render
- Guia: `docs/historico/guia_deploy_render_20260728.md`

### Documentação Geral
- `docs/modelos_visualizacao_historico.md` — especificação completa dos 7 modelos (A a G)

## Pendências / Próximos Passos

### 1. `window._saveDraft` na Aba 3
- `aba3.blade.php` agora define `window._saveDraft` via `requestSubmit()` (que dispara o listener do `sync.js`)
- **Verificar** se o `sync.js` salva corretamente todos os campos da Aba 3 ao ser acionado por "Salvar Rascunho"

### 2. Sincronização entre Supabase e MySQL
- `executarSalvamento()` salva em `tabela_indicacao` (Supabase)
- "Salvar e Enviar" salva em `FocoAba1` (MySQL) via controller
- O carregamento prefere Supabase com fallback para MySQL (`INLINE_*`)
- **Verificar** se após "Salvar e Enviar" + reload os dados aparecem (especialmente RIPs, cadastros mínimos, solicitação)

### 3. Mapa Leaflet
- Leaflet.draw + Nominatim + ViaCEP foram adicionados, mas **testar manualmente**:
  - Abrir modal → mapa deve renderizar
  - Buscar CEP → deve centralizar
  - Desenhar polígono/marcador → "Salvar Coordenadas" deve preencher lat/lng
  - Fechar/reabrir modal → mapa não deve duplicar

### 4. Histórico (Modelos A-G)
- Testar cada modelo com processos reais (que tenham múltiplas tramitações)
- Modelo B (Cronológico): verificar ordenação
- Modelo C (Kanban): verificar cores dos cards
- Modelo D (Grafo): verificar conexões entre estados

### 5. APP_NAME
- Verificar se há outros lugares hardcoded com "Sistema de Gestão SPU" ou similar

### 6. Prioridade Legal
- Migration já criada. Rodar `php artisan migrate` no próximo deploy

### 7. Limpeza de arquivos temporários
- `fix.php`, `patch_*.py`, `*.cjs`, `old_controller.php`, `temp_*.txt`, `test_*.php` — podem ser removidos

## Arquivos Modificados Hoje

### Novos
- `docs/modelos_visualizacao_historico.md`
- `docs/historico/guia_deploy_railway_20260722_1841.md`
- `docs/historico/guia_deploy_render_20260728.md`
- `database/migrations/2026_07_28_183503_set_prioridade_legal_default.php`
- `railway.json`
- `nixpacks.toml`
- `.env.production`
- `resources/views/processos/historico_modelo_e.blade.php`
- `resources/views/processos/historico_modelo_f.blade.php`
- `resources/views/processos/historico_modelo_g.blade.php`
- `resources/views/processos/historico_escolha.blade.php`

### Modificados
- `app/Http/Controllers/ProcessoController.php` — métodos E, F, G; `tramitar()` com `solicitacao_criacao_rip`
- `routes/web.php` — rotas dos novos modelos
- `public/js/foco-01.js` — `renderSolicitacaoCriacaoRip` (DOM API), `window._saveDraft`, fallback `INLINE_SOLICITACAO_RIP`
- `public/js/foco-02-v2.js` — `window._saveDraft`, funções do mapa Leaflet
- `resources/views/processos/abas/aba1.blade.php` — `window.INLINE_SOLICITACAO_RIP`
- `resources/views/processos/abas/aba2.blade.php` — Leaflet CDN
- `resources/views/processos/abas/aba3.blade.php` — `window._saveDraft`
- `resources/views/processos/abas/resumos/aba1b.blade.php` — exibição solicitação RIP
- `resources/views/auth/login.blade.php` — título unificado
- `resources/views/processos/show.blade.php` — `<title>`
- `.env`, `config/app.php` — APP_NAME
