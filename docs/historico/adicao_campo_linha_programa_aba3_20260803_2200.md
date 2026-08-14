# Adição de Campo Condicional 'Linha do Programa' na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Inclusão de um campo de texto condicional 'Linha do programa' no campo 'Há vinculação com o programa Imóvel da Gente?' na Aba 3. O campo aparece apenas quando a opção 'Sim' é selecionada.

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Adição do input de texto e atualização da lógica de exibição JS (toggleBloco).
- `app\Http\Controllers\ProcessoController.php`: Inclusão de `linha_programa` no array de salvamento.
- `resources\views\processos\abas\resumos\aba3_proposta.blade.php`: Adição da exibição condicional do campo no resumo.

## Plano de Rollback / Desfazer
1. Reverter as alterações nos arquivos editados.
