# Remoção de campos de área e valor na Aba 3
Data: 2026-08-03
Responsável: Agente

## Descrição da Mudança
Exclusão dos campos 'Área do terreno a ser destinada (m²)', 'Área construída a ser destinada (m²)' e 'Valor de referência da área a ser destinada (R$)' na seção 'Dados de Área e Valor do Imóvel a ser Destinado' na Aba 3, pois estes dados já são tratados na Aba 1.

## Arquivos Alterados
- `resources\views\processos\abas\aba3.blade.php`: Remoção dos campos HTML.

## Plano de Rollback / Desfazer
1. Reverter a alteração no arquivo `aba3.blade.php` recolocando os campos removidos.
