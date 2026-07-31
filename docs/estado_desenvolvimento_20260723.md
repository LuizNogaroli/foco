# Relatório de Estado do Desenvolvimento - Foco 15 (23/07/2026)

## 1. Estado Atual do Desenvolvimento
Continuamos o foco no roteamento gerencial do sistema e estabilização do fluxo de trabalho. As falhas silenciosas de sincronização entre o Frontend e o Backend foram mitigadas. Estamos nos preparando para a fase de Migração da Stack (Remoção do Supabase) a pedido do usuário.

---

## 2. Implementações e Problemas Resolvidos Recentemente

### 2.1. Alerta Visual de Devoluções (UX)
*   **Problema:** Os usuários não eram ativamente alertados sobre o motivo de um processo ter sido devolvido para eles, a menos que pesquisassem ativamente nos históricos.
*   **Solução:** Criação de um componente visual (`<x-alerta-devolucao>`) renderizado dinamicamente no topo absoluto da interface (`show.blade.php`), garantindo visibilidade na Aba 1, Aba 2, Aba 3 e Aba 7.
*   **Comportamento:** O accordion exibe quem efetuou a devolução, o timestamp e a justificativa exata preenchida no momento do handoff reverso.

### 2.2. Correção de Rota do Comitê de Destinação (CDE)
*   **Problema:** O fluxo programado para o CDE na Aba 7 não possuía a opção lógica de devolver o processo para a Coordenação-Geral, permitindo apenas a aprovação ou indeferimento final.
*   **Solução:** Lógica do `ProcessoController.php` estendida para que o `cde_deliberacao` reconheça a ação de `devolver`, alterando o status_atual para `Validação - Coordenação-Geral` e marcando a `tramitacao` como "Devolvido".

### 2.3. Fidelidade de Dados no Histórico (Tramite)
*   **Problema:** A tabela de `tramites` apenas recebia um snapshot genérico e não anotava devoluções explicitamente em suas colunas próprias.
*   **Solução:** Sempre que a tramitação assume "Devolvido", o sistema passa a preencher as colunas `acao` ('Devolvido'), `usuario_id`, `etapa` e `justificativa` no Tramite respectivo, garantindo rastreabilidade do motivo pelas abas.

### 2.4. Correção de Sobrescrita de Status na Aba 2
*   **Problema:** Ao salvar e enviar a Aba 2, o status do processo não alterava para "Análise de Viabilidade", retroagindo para "Diagnóstico Preliminar". Isso se devia a um envio anômalo do parâmetro `aba_atual=1` pelo browser.
*   **Solução:** Implementado um "safety fallback" (`ProcessoController::tramitar`) que detecta a presença de campos exclusivos da Aba 2 (ex: `situacao_ocupacional`) e força logicamente a identificação da requisição como sendo da Aba 2, garantindo o roteamento correto.

### 2.5. Unificação do Histórico e Timeline Cronológica (Aba 7 e Olho)
*   **Problema:** A visualização global de histórico (acessada pelo ícone de Olho no painel) e a nova visão histórica da Aba 7 estavam dessincronizadas em código e layout, violando a regra de negócio de que o Olho deve ser um espelho estático da Aba 7.
*   **Solução:** 
    * Restauração da fidelidade visual dos containers (acordeões azuis escuros individuais) em ordem cronológica de eventos.
    * Extração de toda a lógica da timeline para um componente reutilizável (`partials/timeline.blade.php`).
    * Refatoração completa de `aba7.blade.php` e `historico.blade.php` para consumirem o mesmo componente base, consolidando a fonte da verdade e garantindo que devoluções, tramitações e assinaturas apareçam simultaneamente nas duas telas.
