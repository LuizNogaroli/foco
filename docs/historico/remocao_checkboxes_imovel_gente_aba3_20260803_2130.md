# Remoção de checkboxes no campo 'Imóvel da Gente' na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Remoção dos checkboxes de multiseleção no campo 'Há vinculação com o programa Imóvel da Gente?' na Aba 3, mantendo apenas as opções de rádio (Sim / Não / Não há informação). Limpeza da lógica JavaScript e campos de observação associados.

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Remoção dos checkboxes, textarea de observações, função JS `toggleObs56` e configuração JS relacionada.
- `app\Http\Controllers\ProcessoController.php`: Remoção dos campos `campo56` e `campo56_obs` do array de salvamento.
- `resources\views\processos\abas\resumos\aba3_proposta.blade.php`: Remoção da exibição dos checkboxes e observações no resumo.

## Plano de Rollback / Desfazer
1. Reverter as alterações nos arquivos editados.
