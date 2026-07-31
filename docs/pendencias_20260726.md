# Pendências (26/07/2026)

- Validar todo o fluxo de submissão HTMX para garantir que os demais menus interativos da UI funcionam corretamente de ponta a ponta na integração com o Laravel.
- Revisar possíveis lixos de JS legados que invocavam APIs diretas com o Supabase nas demais abas (Aba 1, Aba 4, Aba 5, Aba 6) caso ainda não estejam mapeadas no refatoramento geral.
- Validar se a Aba 3 exige refatoração visual após remoção dos modais de Aprovação, já que a submissão agora é direta via botão nativo de Salvar e Enviar.
- (Opcional) Reintegrar/adaptar os Relatórios PDF, se houver um requisito de gerar um "documento PDF assinado" agora que os botões de aprovação não enviam os snapshots isolados do frontend, mas sim do backend.
