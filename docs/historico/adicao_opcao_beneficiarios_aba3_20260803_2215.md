# Inclusão da opção 'Não há informação' em 'Tipo de Beneficiário' na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Adição da opção 'Não há informação' ao campo 'Os beneficiários são famílias ou indivíduos?' (radio button) na Aba 3.

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Atualização do grupo de radio buttons.

## Plano de Rollback / Desfazer
1. Reverter a alteração no arquivo `aba3.blade.php` removendo a opção 'Não há informação'.
