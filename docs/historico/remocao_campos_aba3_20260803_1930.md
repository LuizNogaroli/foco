# Remoção de campos na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Exclusão dos campos 'Há vinculação com Políticas Públicas?' (`campo57`) e 'Há expectativa de impacto ambiental?' (`campo510`) na Aba 3 (proposta de destinação), incluindo seus subcampos, opções de preenchimento e lógica JS associada.

## Estado Anterior (Antes) - Trechos principais
- `resources\views\processos\abas\aba3.blade.php`: Seções `group-campo57` e `group-campo510`.
- `app\Http\Controllers\ProcessoController.php`: Chaves `campo57_radio`, `campo57`, `campo57_obs`, `campo510_radio`, `impacto_ambiental`, `impacto_ambiental_obs`.
- `public\js\foco-06.js`: Lógica `handleRadioToggle` e `addSelectToggle` para esses campos.
- `resources\views\processos\abas\resumos\aba3_proposta.blade.php`: Exibição desses campos.

## Estado Novo (Depois)
- Campos removidos das views, controlador e JS.

## Plano de Rollback / Desfazer
1. Reverter as alterações nos arquivos editados (`aba3.blade.php`, `aba3_proposta.blade.php`, `ProcessoController.php`, `foco-06.js`).
2. Restaurar as seções, chaves e lógica JS removidas.
