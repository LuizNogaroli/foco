# Histórico de Alterações - Correção de Espaçamento Superior (show.blade.php) - 03/08/2026

Este documento registra a correção de um grande espaço vazio no topo do formulário que era um resquício do antigo menu superior ("menu de bolinhas").

## 1. Problema Identificado
- Havia um vão de aproximadamente 105px no topo da página.
- Esse vão era gerado pelo arquivo genérico `index.css`, que aplicava `position: absolute; top: var(--menu-height);` na tag `<main>` para acomodar o antigo menu superior.

## 2. Alterações Realizadas

### Em [show.blade.php](file:///C:/dev/Foco-19/resources/views/processos/show.blade.php):
- A tag `<main style="padding-top: 20px;">` foi alterada para anular o comportamento herdado do CSS antigo, sem quebrar o layout de outras telas que ainda possam usar o menu.
- **Novo código:** `<main style="padding-top: 20px; position: static; height: auto;">`

---

## 3. Plano de Rollback / Desfazer

Para reverter essa correção:
1. Acesse `resources/views/processos/show.blade.php`.
2. Altere a tag `<main>` de volta para `<main style="padding-top: 20px;">`.
