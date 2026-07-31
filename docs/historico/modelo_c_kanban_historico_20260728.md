# Modelo C — KanBan por Responsável (Histórico)

## Propósito
Oferecer uma visualização do histórico de movimentações do processo organizada em colunas por **perfil responsável**, permitindo identificar rapidamente quem fez o quê e onde estão os gargalos.

## Funcionamento

### Alocação de Tramites às Colunas
Cada tramite é atribuído a uma das 9 colunas de perfil através do helper `ProcessoController::getColunaTramite()` (`ProcessoController.php`). A lógica de alocação segue esta precedência:

1. **Etapa direta** — Se o campo `etapa` do trâmite já é um nome de perfil conhecido (ex: `"Chefia"`, `"Coordenação"`), usa-o diretamente.
2. **Snapshot de assinatura** — Para manifestações, varre o `dados_snapshot` procurando campos `assinatura_<prefixo>_nome` para identificar qual perfil assinou.
3. **Devolução/Recebimento** — Usa o campo `etapa` como fallback.
4. **Role do usuário** — Para saves de aba (Aba 1/2/3), usa a role Spatie do usuário que executou a ação.

### Colunas (9 perfis)
Equipe Destinação, Equipe Caracterização, Chefia, Coordenação, Superintendência, Equipe C.G., Coordenação-Geral, Direção, CDE.

### Cards
- Timestamp
- Ícone + label resumida (ex: "📋 Dados Requerimento", "⚠️ Devolução", "📝 Manifestação")
- Autor
- Botão "Detalhes" que abre modal com o conteúdo completo (mesmo conteúdo do Modelo A)

### Estilo
- Cores de cabeçalho variadas por coluna para diferenciação visual
- Rolagem horizontal se houver muitas colunas com conteúdo
- Badge com contagem de cards por coluna

## Arquivos de Referência

| Arquivo | Responsabilidade |
|---------|-----------------|
| `resources/views/processos/historico_modelo_c.blade.php` | View do KanBan e modais |
| `app/Http/Controllers/ProcessoController.php` | Método `historicoModeloC()` e helper `getColunaTramite()` |
| `routes/web.php` | Rota `processos.historico.modelo-c` |

## Observações
- Colunas sem itens são omitidas automaticamente.
- Tramites que não puderam ser mapeados para nenhum perfil vão para a coluna "Outros".
- O modal reusa os mesmos accordions e includes de resumo do Modelo A (`aba1a`, `aba1b`, `aba2`, `aba3_analise`, `aba3_proposta`).
