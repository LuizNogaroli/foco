# Lista de Pendências e Próximos Módulos (Todo-List)

**Data de Atualização:** 23/07/2026

Este documento serve como roteiro norteador para as próximas fases de desenvolvimento do sistema SPUnet.

---

## ⏳ Módulos e Subsistemas a Implantar / Aprimorar

### 1. Migração de Stack (Remoção do Supabase) [PRÓXIMO]
*   **Objetivo:** Eliminar a dependência da API REST do Supabase e migrar as operações de banco de dados diretamente para o PostgreSQL via Eloquent ORM.
*   **Requisitos:**
    *   Remover scripts `db.js`, `fetch_spu.js` e `autosave.js` que fazem chamadas REST.
    *   Reescrever a lógica de persistência e autosave utilizando rotas AJAX para o Laravel.
    *   Sincronizar as migrations e models (tabela_spu, tabela_foco, etc) dentro do ecossistema do Laravel.

### 2. Subsistema de Atribuição de Atividades
*   **Objetivo:** Permitir que coordenadores ou chefias distribuam requerimentos específicos (ou blocos de processos) para analistas/técnicos da sua equipe.
*   **Progresso:** A lógica estrutural do fluxo reverso (Devoluções e Handoff) foi resolvida com sucesso. Falta o mecanismo proativo de delegar tarefas para a frente (encaminhamentos diretos para a fila de um usuário).

### 3. Montagem de Equipes
*   **Objetivo:** Agrupar usuários em células operacionais lógicas (ex: Equipe de Caracterização, Equipe de Destinação, Coordenação-Geral, Chefias).
*   **Requisitos:**
    *   Painel CRUD (Criar, Ler, Atualizar, Deletar) de grupos/departamentos.
    *   Associação de N:N entre servidores (usuários) e as equipes.
    *   Restrição de visualização de painel com base na equipe.

### 4. Configuração Geral do Sistema
*   **Objetivo:** Tela administrativa de alto nível para gerir parâmetros globais que ditam o comportamento do software sem precisar alterar o código-fonte.
*   **Requisitos:**
    *   Tabela de configurações com chave-valor.
    *   Personalização de templates de texto e SLAs.

### 5. Painel de Informações Estratégicas (Dashboard BI)
*   **Objetivo:** Visão analítica de altíssimo nível (CDE / Direção) do gargalo operacional e vazão de processos no país.
