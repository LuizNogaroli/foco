# Pendências — 18/08/2026

## Feito nesta sessão

- **Fix 500 no Postgres ao salvar Aba 1 (`tramitar`):** o erro era causado por strings vazias (`""`) enviadas pelo JS (`foco-01.js`) em campos de área quando a destinação é "Integral". No SQLite isso passava; no PostgreSQL, inserir `""` em coluna `decimal(12,2)` (`foco_rips.area_terreno_parcial`, `foco_rips.area_imovel_parcial`, `foco_cadastros_minimos.area`) gera `invalid input syntax for type numeric` → 500.
- **Correção em `ProcessoController.php`:** adicionado helper privado `decimalNull()` que converte `""` → `null` e `,` → `.`; aplicado em:
  - Rips da Aba 1 (`area_terreno_parcial`, `area_imovel_parcial`).
  - Cadastros mínimos da Aba 1 (`area`, `area_terreno_parcial`, `area_imovel_parcial`).
  - RIPs vinculados da Aba 2 (`area_terreno_parcial`, `area_imovel_parcial`).
  - Aba 2 `latitude`/`longitude` (decimal 10,7).
- **Teste `tests/Feature/ReproTramitarTest.php`:** asserta redirect 302 de sucesso e que áreas vazias são persistidas como `NULL` (payload realista com `rips` e `cadastros_minimos` em JSON).

## Pendências abertas

- **`tramitar()` ainda com valores antigos no branch `equipe_cg`:** `ProcessoController.php` valida `insuficiente`/`obs_equipe_cg`, mas o formulário envia `favoravel`/`favoravel_condicionantes`/`nao_favoravel` e `obs_equipe_cg_condicionantes`. Demais branches já atualizados.
- **Renomeação de status incompleta em JS:** `workflow.js` e fallback `_WORKFLOW_STAGES` em `db.js` ainda usam nomes antigos — ex: "Validação análise de viabilidade - Chefia" (nova: "Validação - Chefia") e "Conferência análise de viabilidade" (nova: "Conformidade Prévia"). Ver `kb/kb_renomeacao_status_destinacao_rip_20260803_1858.md`.
- 4 testes pré-existentes quebrados (redirecionam para `/dashboard` em vez de `/`).
- Backlog: backfill de `destinacao_terreno`/`destinacao_imovel` para RIPs antigos; trâmite na abertura (`abrir()`).
