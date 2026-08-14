# Permissão de '0' no campo 'Número de beneficiários' na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Atualização da validação JavaScript na Aba 3 para permitir '0' como um valor válido no campo 'Número estimado de beneficiários em potencial' (`num_beneficiarios`).

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Alteração da lógica de validação JS.

## Plano de Rollback / Desfazer
1. Reverter a alteração na lógica de validação no arquivo `aba3.blade.php`.
