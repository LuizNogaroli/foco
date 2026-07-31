# Lista de Pendências — 30/07/2026

Abaixo estão listadas as tarefas restantes (TODO list) identificadas no encerramento desta sessão de desenvolvimento:

- [ ] **Homologar Rascunhos nas Abas 4, 5 e 6:**
  - Verificar se as abas subsequentes (Aba 4 em diante) necessitam de salvamento parcial de rascunhos. Se sim, migrar a lógica Javascript de cada uma delas para o endpoint `/draft/save` do Laravel e assegurar a carga através do `ProcessoController`.
- [ ] **Limpar chamadas e variáveis obsoletas de Supabase:**
  - Fazer uma varredura geral nos arquivos Javascript (`foco-01.js`, `foco-02-v2.js`, etc.) e remover chamadas/comentários ao `window.parent.SUPABASE_URL`, `SUPABASE_ANON_KEY` ou `window.parent.forceSaveDraft`, mantendo a arquitetura limpa de legados já desativados.
- [ ] **Validar remoção de rascunho após Tramitar:**
  - Confirmar se ao clicar em "Salvar e Enviar" (método `tramitar` do controller), o registro correspondente da tabela `foco_drafts` é corretamente limpo para evitar conflitos de dados desatualizados na próxima abertura do processo.
- [x] **Solução de Cache e Vinculação de Rascunho da Aba 1:**
  - Corrigido o botão de "Salvar Rascunho" da Aba 1 embutindo a lógica do script diretamente no Blade `aba1.blade.php`, eliminando a dependência do cache de arquivos estáticos, e forçando a invalidação de cache via query string de tempo.
