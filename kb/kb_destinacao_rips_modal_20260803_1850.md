# Integração de Perguntas de Destinação no Modal de Inserir RIP

Sempre que o usuário adiciona um RIP através do modal "Inserir RIP" na Aba 1, ele agora responde a duas perguntas adicionais sobre destinação de área:
1. Se a área do terreno terá destinação (Integral ou Parcial com metragem m²).
2. Se a área do imóvel terá destinação (Integral ou Parcial com metragem m²).

## Solução Técnica

Para evitar tabelas auxiliares adicionais e manter a unificação de fluxo sem quebrar os dados preexistentes (onde RIPs eram simples strings), foi adotado o seguinte fluxo:

1. **Migração do Banco de Dados:**
   Criada a migração `2026_08_03_214501_add_destinacao_fields_to_foco_rips_table.php` adicionando as colunas no banco:
   - `destinacao_terreno` (string, nullable)
   - `area_terreno_parcial` (decimal 12,2, nullable)
   - `destinacao_imovel` (string, nullable)
   - `area_imovel_parcial` (decimal 12,2, nullable)

2. **Interface HTML (Blade) & JS:**
   - Adicionadas as perguntas e inputs de metragem no modal de RIP em `resources/views/processos/abas/aba1.blade.php`.
   - Modificado `foco-01.js` para escutar a mudança do rádio para `Parcial` e exibir a caixa de metragem.
   - Ao clicar em "Salvar" ou "Inserir + 1 RIP", os dados são encapsulados em um objeto JSON contendo:
     - `numero_rip`, `cep`, `logradouro`, `municipio`, `uf`, `destinacao_terreno`, `area_terreno_parcial`, `destinacao_imovel`, `area_imovel_parcial`.
   - Esse objeto é serializado para string no `value` do hidden input `rips[]` (exatamente igual ao formato do `cadastros_minimos[]`).
   - A lista de arrays `window.ripsPendentes` armazena esses objetos e também envia no rascunho.

3. **Salvamento no Backend (Laravel):**
   - O `ProcessoController.php` tenta decodificar as strings de `rips[]`. Se for um JSON válido, desestrutura os campos e insere no banco. Se for uma string pura (legado/retrocompatível), salva apenas o `numero_rip`.
