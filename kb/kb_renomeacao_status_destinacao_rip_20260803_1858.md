# KB: Renomeação de Status e Campos de Destinação de RIP — 03/08/2026

## Contexto

Sessão de 03/08/2026. Duas entregas principais: (1) adição dos campos de destinação no modal de RIP da Aba 1; (2) renomeação do status "Diagnóstico Preliminar" para "Diagnóstico do Imóvel" em todo o sistema.

---

## 1. Renomeação de Status: Pontos de Atenção

O status é altamente redundante no projeto. Para renomear qualquer status, é **obrigatório** atualizar os seguintes arquivos:

| Arquivo | Tipo de referência |
|---|---|
| `app/Http/Controllers/ProcessoController.php` | Arrays PHP: `statusToAba()`, `perfilPodeOperar()`, lista `$statuses` do kanban, `getStatusesDoPerfil()` (bloco ALL e bloco por perfil), label do histórico (`getDescricaoTramite()`) |
| `public/js/workflow.js` | Objeto `WORKFLOW_STAGES` (fonte de verdade JS) |
| `public/js/db.js` | Objeto `_WORKFLOW_STAGES` (fallback JS, cópia do workflow.js) |
| `resources/views/processos/abas/aba2.blade.php` | Título `<h2>` |
| `resources/views/processos/abas/aba3.blade.php` | Botão de devolução `data-workflow="13"` |

> **Atenção:** O controller mantém múltiplos aliases do mesmo status (ex: `'Diagnóstico Preliminar'` e `'Diagnóstico preliminar do imóvel'`) para compatibilidade com registros históricos no banco. Ao renomear, apenas o alias principal deve ser atualizado; os aliases legados devem ser **mantidos** para não quebrar processos existentes.

---

## 2. Campos de Destinação do RIP

### Estrutura do banco (após migration 2026_08_03_214501)
```sql
ALTER TABLE foco_rips ADD COLUMN destinacao_terreno VARCHAR(20) NULL;
ALTER TABLE foco_rips ADD COLUMN area_terreno_parcial DECIMAL(10,2) NULL;
ALTER TABLE foco_rips ADD COLUMN destinacao_imovel VARCHAR(20) NULL;
ALTER TABLE foco_rips ADD COLUMN area_imovel_parcial DECIMAL(10,2) NULL;
```

### Fluxo de dados
1. **Frontend (aba1.blade.php):** radios + inputs no modal de RIP → serializados como JSON num `input[name="rips[]"]` hidden.
2. **JS (foco-01.js):** evento `change` nos radios para toggle do container de metragem; `coletarDadosRip()` inclui os novos campos no objeto; `carregarRipsIniciais()` os lê no modo edição.
3. **Backend (ProcessoController.php):** `json_decode($ripRaw, true)` extrai o objeto e popula os campos `destinacao_terreno`, etc. antes do `save()`.

### Cuidado: `removerRipItem()`
A função `removerRipItem` em `foco-01.js` foi corrigida para aceitar tanto **objetos** (novos) quanto **strings** (legados) no array `ripsInseridos`. Usar `.filter()` com checagem `typeof`:
```js
ripsInseridos = ripsInseridos.filter(r => {
    const ripNup = typeof r === 'object' ? r.nup : r;
    return ripNup !== nupParaRemover;
});
```
