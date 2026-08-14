# Injeção de Dados Mockados em 'Ações Judiciais' na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Inclusão de um fallback com dados mockados na seção 'Ações judiciais ou órgãos de controle' em `aba3.blade.php`. Se a função `fetchAcoes` não retornar dados válidos, os campos serão preenchidos automaticamente com informações de exemplo.

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Modificação do script JS responsável por carregar os dados das ações.

## Plano de Rollback / Desfazer
1. Reverter a alteração no arquivo `aba3.blade.php` removendo o objeto de dados mockados e voltando à lógica original que apenas preenche os dados se `fetchAcoes` retornar dados válidos.
