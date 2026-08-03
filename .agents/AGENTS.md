## Regra de Base de Conhecimento (Knowledge Base)
Sempre que o agente enfrentar um desafio complexo e encontrar uma solução (arquitetural, lógica ou técnica), ele deve documentar esse aprendizado gerando um artefato (arquivo .md). Para evitar sobrescrita e acumular o histórico, o arquivo deve ser nomeado no formato `kb_<assunto>_YYYYMMDD_HHMM.md` (ex: `kb_datalake_fields_20260626_1755.md`). O objetivo é construir uma base de conhecimento duradoura para o projeto que depois será depurada em um documento único.

O agente deve criar os artefatos de Knowledge Base de forma autônoma e silenciosa, sem precisar relatar ao usuário a criação de cada um deles, pois o usuário fará a revisão posterior.

Certifique-se de salvar todos esses arquivos .md diretamente no diretório 'kb/' na raiz do projeto (crie-o se não existir), para manter o repositório organizado e não poluir a raiz do código.

## Regra de Reversibilidade e Histórico de Alterações (Rollback/Desfazer)
Sempre que salvar o log de uma correção importante em `docs/historico/` (utilizando o padrão `<nome>_<timestamp>.md`), você deve incluir obrigatoriamente:
1. **Estado Anterior (Antes):** O bloco de código original exato que foi alterado/removido.
2. **Estado Novo (Depois):** O bloco de código novo após a alteração.
3. **Plano de Rollback / Desfazer:** Instruções detalhadas passo a passo de como reverter a mudança e restaurar o estado original se necessário.
Isso garante a reversibilidade total do código e permite reverter erros de forma ágil e segura em turnos futuros.

## Regra: Cookies setados por JS no Laravel 11
O middleware `EncryptCookies` do Laravel 11 está ativo por padrão no grupo `web`. Se um cookie é setado por JavaScript (texto plano), a descriptografação falha silenciosamente e `request()->cookie()` retorna null. Para cookies setados por JS (como `perfil_simulado`), sempre adicionar exceção em `bootstrap/app.php`:
```php
$middleware->encryptCookies(except: ['nome_do_cookie']);
```
Referência: `kb/kb_controle_acesso_cookie_paginacao_20260719_2330.md`

## Regra: Paginação customizada no Laravel 11
A view padrão do Laravel 11 usa classes Tailwind inline. Se o projeto não usa Tailwind, criar view customizada em `resources/views/vendor/pagination/custom.blade.php` e registrar com `Paginator::defaultView('vendor.pagination.custom')` no `AppServiceProvider::boot()`.
Referência: `kb/kb_controle_acesso_cookie_paginacao_20260719_2330.md`

## Regra de Atualização de Status (Handoff) e Pendências
Sempre, ao final de uma sessão de trabalho contínua ou ao encerrarmos um ciclo de tarefas, o agente deve, obrigatoriamente e de forma proativa, atualizar os arquivos de controle do projeto:
1. `docs/estado_desenvolvimento_YYYYMMDD.md`: Criar ou atualizar o relatório com as implementações concluídas na sessão.
2. `docs/pendencias_YYYYMMDD.md`: Atualizar a lista de tarefas restantes (TODO list).
Essa regra garante que nenhum conhecimento seja perdido entre o fim de uma sessão e o início da próxima.

## Regra: Monitoramento do Limite de Contexto e Handoff Preventivo
Qualquer IA rodando neste projeto deve identificar, sempre que for possível, o nível de uso da janela de contexto/tokens da sessão atual:
- No TUI do opencode, o indicador fica no canto inferior direito (ex.: `125.8K (63%)`), informando tokens usados e a porcentagem da janela de contexto (ex.: Big Pickle = 200K).
- Em outras ferramentas, usar qualquer indicador de uso de contexto disponível (metadados de uso, stats de tokens, etc.).

Sempre que o uso de contexto atingir ou ultrapassar **90%**, o agente DEVE, de forma automática e proativa, produzir o handoff preventivo antes que a conversa seja truncada:
1. Atualizar `docs/estado_desenvolvimento_YYYYMMDD.md` com todas as implementações concluídas até o momento (seguindo o padrão já existente no diretório `docs/`).
2. Atualizar `docs/pendencias_YYYYMMDD.md` com as tarefas restantes (TODO list), incluindo o estado atual e o próximo passo.
3. Se necessário, criar um artefato em `kb/` (`kb_<assunto>_YYYYMMDD_HHMM.md`) registrando descobertas importantes da sessão.
4. Avisar o usuário de que a sessão está próxima do limite e recomendar iniciar uma nova sessão do opencode, resumindo o estado salvo.

Esse handoff preventivo é obrigatório também quando o limite for atingido no meio de uma tarefa: finalizar primeiro o handoff, depois avisar o usuário. Nenhum conhecimento da sessão deve ser perdido por truncamento de contexto.
