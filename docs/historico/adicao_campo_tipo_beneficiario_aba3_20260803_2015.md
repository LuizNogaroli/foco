# Adição de Campo 'Tipo de Beneficiário' na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Inclusão do campo 'Os beneficiário são famílias ou indivíduos?' (radio: Famílias/Indivíduos) na Aba 3, posicionado logo acima do campo 'Número estimado de beneficiários em potencial'.

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Adição do campo radio e labels.
- `app\Http\Controllers\ProcessoController.php`: Inclusão de `tipo_beneficiario` na array `proposta_destinacao` para salvamento.
- `resources\views\processos\abas\resumos\aba3_proposta.blade.php`: Adição do campo no resumo para exibição.

## Plano de Rollback / Desfazer
1. Reverter as alterações nos três arquivos.
2. Remover a chave `tipo_beneficiario` do array de salvamento no controller.
