# Lições de Arquitetura: Transição de Supabase para PostgreSQL
**Data:** 23/07/2026

## O Paradoxo do Protótipo
O projeto Foco-16 iniciou como um protótipo rápido com stack descentralizada (Front-end manipulando estado e lendo dados via Javascript + Supabase). Com a evolução e complexidade das regras de negócio (especialmente aquelas que envolvem controle de devolução de processos, validação pesada em back-end e retrocesso de status entre formulários e múltiplas abas), foi introduzido o backend em Laravel para centralizar, controlar e proteger essas operações num banco relacional isolado (SQLite / PostgreSQL).

## A Armadilha da Dupla Escrita (Double Write)
Ao manter essa arquitetura híbrida de forma provisória, inevitavelmente esbarra-se no gargalo sistêmico de "Double Write" (Dupla Escrita):
- O Laravel passa a receber o POST nativo, validar os dados de forma segura, e salvá-los no seu banco de dados primário.
- Simultaneamente, o Front-end em JS (ou o próprio PHP do Laravel) precisa disparar conexões Restful extras e isoladas para a API do Supabase apenas para garantir que a `tabela_status_fluxo` (que alimenta o painel Kanban visual) se atualize.

Isso gera **dessincronização e opacidade do estado real da aplicação**. Presenciamos situações de falha silenciosa onde:
1. A validação no Laravel impedia o salvamento (pois os campos obrigatórios não exibiam mensagem de erro de volta à view).
2. Como o front-end acreditava ter submetido, ou o Supabase ficava inalterado, ou pior: a UI JS emitia uma promessa positiva de salvamento sem de fato confirmar o commit transacional no servidor.

## Conclusão Estratégica e Recomendação
Mesmo para o escopo de Minimum Viable Products (MVPs) ou prototipação ágil, quando a lógica se afasta de fluxos CRUD simples e entra no território de "máquinas de estado" complexas, o esforço inicial de se estruturar a *stack definitiva* é vital. 

No caso de uso do Foco, migrar diretamente para o **Laravel acoplado de forma nativa a um PostgreSQL no Render/Railway** demanda muito menos trabalho total (TCO - Total Cost of Ownership) do que a manutenção cíclica de sincronizações paliativas e limpeza de "débitos técnicos" ou códigos legados. 

A recomendação técnica primordial para os próximos passos de evolução é **unificar o banco de dados**, desativando por completo a dependência do Supabase no Javascript (`db.js`) e transferindo as leituras e requisições do Kanban e Dashboards para rotas controladas e unificadas pelo framework Laravel.
