# Adição de Campos de Identificação do Imóvel na Tabela de RIPs - 11/08/2026

## Descrição da Mudança
Verificado o conjunto de campos exibidos na identificação do imóvel da tabela de RIPs (Aba 2) e adicionados os campos ausentes, conforme especificação do usuário.

Campos do conjunto (ordem de exibição):
1. Conceituação do Imóvel
2. Tipo de Imóvel — opções: Lote/Terreno, Gleba, Ilha, Outros
3. Natureza do Imóvel — opções: Urbano, Rural
4. Classificação do Imóvel — opções: Dominial, Especial, Uso Comum
5. Condicional por natureza:
   - Urbano → exibe "Inscrição Municipal"
   - Rural → exibe "CCIR"

## Arquivos alterados
- `resources/views/processos/abas/aba2.blade.php` — bloco de identificação nos dois caminhos de render (fallback JS/Supabase via `buildField` e MySQL/PHP via `f`): adicionados `Classificação do Imóvel` (`dadosSPU.classificacao` / `d.classificacao`) e os campos condicionais `Inscrição Municipal` (Urbano) / `CCIR` (Rural); reordenados Tipo de Imóvel antes de Natureza do Imóvel.
- `resources/views/processos/abas/resumos/aba1b.blade.php` — mesma aplicação no resumo da aba 2 (ambos os caminhos de render).
- `resources/views/processos/abas/aba3.blade.php` — mesma aplicação nos accordions "Imóvel (RIP)" (ambos os caminhos de render), mantendo consistência entre as abas.
- `public/js/foco-02-v2.js` — `CAMPOS_COM_OPCOES.tipo_imovel` atualizado para Lote/Terreno, Gleba, Ilha, Outros; adicionado `classificacao` com Dominial, Especial, Uso Comum; `MAPA_CAMPOS` passou a mapear `classificacao`.
- `public/js/seed_tabela_spu.js` — seed da `tabela_spu` agora grava `natureza` (Urbano/Rural), `tipo_imovel`, `classificacao`, `inscricao_municipal` e `ccir` coerentes com a especificação.
- `public/js/seed-supabase.js` — mock corrigido: `tipo_imovel: 'Dominial'` (valor de classificação) → `tipo_imovel: 'Lote/Terreno'` + `classificacao: 'Dominial'`; `natureza: 'Terreno'` → `'Urbano'` + `inscricao_municipal` preenchida.

## Decisão do usuário
Exibição somente leitura (não foram adicionados inputs editáveis). As opções acima foram definidas pelo usuário como a especificação canônica dos campos.
