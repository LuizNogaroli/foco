# Modelo D — Grafo de Fluxo de Estados (Histórico)

## Propósito
Oferecer uma visualização do histórico como um **grafo direcionado** (nós e arestas) que mostra o caminho real percorrido pelo processo, destacando visualmente desvios, loops de devolução e a sequência de aprovações.

## Funcionamento

### Construção do Grafo
O helper `ProcessoController::montarFluxoEstados()` (`ProcessoController.php`) percorre os tramites em ordem cronológica e constrói duas estruturas:

- **Nós**: Cada tramite vira um nó, com as propriedades: `id`, `label`, `tipo`, `data`, `usuario`.
- **Arestas**: Conectam nós consecutivos, carregando o `tipo` da transição.

### Tipos de Nó (Cores)
| Tipo | Cor | Significado |
|------|-----|-------------|
| `salva` | Azul (#2563eb) | Dados salvos (Aba 1/2/3) |
| `manifestacao` | Teal (#0d9488) | Manifestação/parecer de perfil |
| `devolucao` | Vermelho (#ef4444) | Devolução do processo |
| `recebido` | Verde (#16a34a) | Recebimento do processo |

### Legendas
Uma legenda no topo explica o significado de cada cor.

### Interação
- Cada nó é clicável e abre o mesmo modal de detalhamento usado nos outros modelos (accordions, pareceres, justificativas).
- Setas entre os nós seguem a cor do tipo da transição.
- Atalho ESC ou clique fora fecha o modal.

### Casos de Uso
- **Auditoria**: Um processo que deveria ter ido direto de "Diagnóstico" para "Análise de Viabilidade" mas teve 3 devoluções no meio fica visualmente óbvio pelas setas vermelhas.
- **Gargalos**: Se muitos nós aparecem na mesma coluna de perfil, o processo pode estar represado.
- **Ciclos**: Devoluções em sequência criam um padrão visual de "zigue-zague" fácil de detectar.

## Arquivos de Referência

| Arquivo | Responsabilidade |
|---------|-----------------|
| `resources/views/processos/historico_modelo_d.blade.php` | View do grafo e modais |
| `app/Http/Controllers/ProcessoController.php` | Método `historicoModeloD()` e helper `montarFluxoEstados()` |
| `routes/web.php` | Rota `processos.historico.modelo-d` |

## Observações
- O grafo é puramente CSS/HTML — não depende de bibliotecas externas como D3.js ou vis.js.
- A estrutura de dados do grafo é simples (`nos[]` e `arestas[]`), facilitando futuras extensões.
- O modal de detalhes reusa os mesmos includes de resumo dos outros modelos.
