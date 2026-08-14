# Atualização de Mock Data para 'Ações Judiciais' na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Substituição dos dados mockados genéricos da seção 'Ações judiciais ou órgãos de controle' por dados reais exemplificativos fornecidos pelo usuário:
1. ACP da Associação Quilombola Kulumbu do Patuazinho.
2. Ação de Adjudicação Compulsória do Horto Florestal de Sumaré/SP.

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Atualização do script JS com os novos dados mockados.

## Plano de Rollback / Desfazer
1. Reverter a alteração no arquivo `aba3.blade.php` para o estado anterior (dados mockados genéricos).
