# Estado do Desenvolvimento (2026-07-28)

## Concluído na Última Sessão

### Página Intermediária de Escolha de Modelo de Histórico
- Criada rota `processos.historico.escolha` (`GET /processos/{processo}/historico/escolha`) com view seletora.
- Ícone "olho" no painel de requerimentos agora aponta para a página de escolha em vez de ir direto ao histórico.

### Modelo A — Histórico Cronológico (inalterado)
- Visualização original com accordions inline e detalhes completos por aba.

### Modelo B — Timeline Vertical Compacta
- Criada rota `processos.historico.modelo-b` e view `historico_modelo_b.blade.php`.
- Linha do tempo vertical com bolinhas coloridas por tipo de evento.
- Cards resumidos contendo apenas timestamp, título e autor.
- Botão "Ver detalhes" abre modal com o conteúdo completo (accordions, pareceres, manifestações).
- Fechamento por clique no fundo, botão X ou tecla ESC.

### Modelo C — KanBan por Responsável
- Criada rota `processos.historico.modelo-c` e view `historico_modelo_c.blade.php`.
- Grid horizontal com colunas para cada perfil (9 colunas: Equipe Destinação a CDE).
- Cada tramite é alocado à coluna do perfil responsável via helper `getColunaTramite()`.
- Cards compactos com timestamp + resumo, clicáveis para abrir modal de detalhes.
- Ideal para identificar gargalos e responsabilização.

### Modelo D — Grafo de Fluxo de Estados
- Criada rota `processos.historico.modelo-d` e view `historico_modelo_d.blade.php`.
- Visualização vertical com nós conectados por setas coloridas (azul = salva, teal = manifestação, vermelho = devolução, verde = recebido).
- Legenda explicativa no topo.
- Cada nó abre modal com detalhamento completo.
- Ideal para auditoria: desvios e loops de devolução ficam visualmente evidentes.

### Helpers no Controller
- `getColunaTramite()`: Mapeia cada tramite para o perfil responsável, usando etapa, snapshot de assinatura ou role do usuário.
- `montarFluxoEstados()`: Constrói a estrutura de nós e arestas para o grafo do Modelo D.

### Knowledge Base
- KB registrada: `historico/modelo_c_kanban_historico_20260728.md`
- KB registrada: `historico/modelo_d_grafo_estados_20260728.md`

## Status Atual
Os quatro modelos de visualização de histórico (A, B, C, D) estão operacionais e disponíveis na página de escolha. Todos compartilham o mesmo modal de detalhamento, garantindo consistência visual e baixa curva de aprendizado. Nenhuma pendência técnica ativa.
