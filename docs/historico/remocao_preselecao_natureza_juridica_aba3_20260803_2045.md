# Remoção de pré-seleção em 'Natureza Jurídica' na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Remoção do atributo `selected` na opção '206-2 - Sociedade Empresária Limitada' do campo 'Natureza Jurídica do Destinatário' na Aba 3 (`aba3.blade.php`).

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Atualização do `<select>` para remover a pré-seleção forçada.

## Plano de Rollback / Desfazer
1. Reverter a alteração no arquivo `aba3.blade.php` recolocando o `selected` na opção '206-2'.
