# Pendências — 11/08/2026

## Feito nesta sessão

- **Aba 2 — Campos de identificação do imóvel na tabela de RIPs:** verificado o conjunto de campos e adicionados os ausentes (somente leitura, conforme usuário):
  - Campos: Conceituação do Imóvel, Tipo de Imóvel (Lote/Terreno, Gleba, Ilha, Outros), Natureza do Imóvel (Urbano, Rural), Classificação do Imóvel (Dominial, Especial, Uso Comum) e condicionais por natureza: **Urbano → Inscrição Municipal**, **Rural → CCIR**.
  - Aplicado em `aba2.blade.php`, `resumos/aba1b.blade.php` e `aba3.blade.php` (ambos os caminhos de render: JS/Supabase e MySQL/PHP).
  - `foco-02-v2.js`: `CAMPOS_COM_OPCOES` (tipo_imovel/classificacao) e `MAPA_CAMPOS` atualizados.
  - Seeds/mocks alinhados: `seed_tabela_spu.js` e `seed-supabase.js` (corrigido mock com `tipo_imovel: 'Dominial'` → `classificacao: 'Dominial'`).
  - Documentação: `docs/historico/adicao_campos_identificacao_imovel_aba2_20260811.md`.

## Pendências abertas

- **Aba 1:** campo "Tipo de Requerimento" adicionado no cabeçalho, abaixo do h2 (padrão `form-group.inline` readonly), refletindo o valor do painel de requerimentos (`aba1.blade.php:67-70`).
- **`tramitar()` ainda com valores antigos no branch `equipe_cg`:** `ProcessoController.php:726` valida `insuficiente`/`obs_equipe_cg`, mas o formulário envia `favoravel`/`favoravel_condicionantes`/`nao_favoravel` e `obs_equipe_cg_condicionantes`. Demais branches (chefia, coordenacao, coordenacao_geral, direcao) já foram atualizados.
- **Renomeação de status incompleta em JS:** `workflow.js` e fallback `_WORKFLOW_STAGES` em `db.js` ainda usam nomes antigos — ex: "Validação análise de viabilidade - Chefia" (nova: "Validação - Chefia") e "Conferência análise de viabilidade" (nova: "Conformidade Prévia"). Ver `kb/kb_renomeacao_status_destinacao_rip_20260803_1858.md`. Impacto atual baixo (só carregado em `configuracoes.blade.php`).
- 4 testes pré-existentes quebrados (redirecionam para `/dashboard` em vez de `/`).
- Backlog: backfill de `destinacao_terreno`/`destinacao_imovel` para RIPs antigos; trâmite na abertura (`abrir()`).
