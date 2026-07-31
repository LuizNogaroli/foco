# Refatoração HTMX: Desafios e Soluções com Interceptadores JS Legados

**Data:** 27 de Julho de 2026
**Assunto:** Conflitos entre HTMX, interceptadores de formulário nativos e Fallbacks de Submissão

## 1. O Problema
Durante a refatoração das Abas 2 e 3 para utilizarem submissões assíncronas nativas via HTMX (`hx-post`), enfrentamos um comportamento inconsistente onde a submissão de formulários (Aba 2) falhava e redirecionava o usuário para uma aba incorreta (Aba 1) através de uma requisição `GET`. 

### Sintoma
- O usuário clicava em "Salvar e Enviar" e o formulário parecia recarregar a tela, apresentando sempre a Aba 1.
- No backend, o controlador `ProcessoController::tramitar` (que processaria o POST) sequer era acionado, e nenhum log de erro de servidor era gerado.

## 2. A Causa Raiz
O erro resultou da sobreposição de três fatores:

1. **Interceptadores Legados e Cache:** O arquivo javascript original (`foco-02-v2.js`) continha um listener de `submit` atrelado ao formulário, o qual interrompia o fluxo (`e.preventDefault()`) e, posteriormente, executava `form.submit()`. Mesmo após a remoção no código fonte, os navegadores continuaram executando a versão através de cache não-invalidado.
2. **Formulários Modificados para HTMX sem Fallback:** Para adequar o sistema ao HTMX, a declaração do formulário havia tido suas propriedades originais `action="..."` e `method="POST"` removidas e substituídas por `hx-post`. 
3. **Comportamento Padrão de Formulários HTML:** Devido à chamada imperativa de `form.submit()` oriunda do script legado engatilhado pelo cache, o HTMX foi sumariamente ignorado. Como a tag `<form>` estava desprovida de `method` e `action`, a submissão padrão do navegador ocorreu via método `GET` para a rota corrente (`/processos/{id}?aba_atual=2`). A rota de exibição (show) falhava em receber a query `aba=2` e caía no modo de segurança exibindo a Aba default daquele status, que era a 1.

## 3. A Solução Adotada (Arquitetura e Boas Práticas)

Para garantir que a integração do HTMX seja perfeitamente estável mesmo diante de falhas de rede, scripts não carregados, caches desatualizados ou browsers restritivos, definimos a seguinte arquitetura de **Fallbacks**:

1. **Manutenção de Atributos HTML Nativos:** 
   O formulário sempre deve possuir seus atributos normais, trabalhando em conjunto com os do HTMX:
   ```html
   <form action="{{ route('rota.tramitar') }}" method="POST" hx-post="{{ route('rota.tramitar') }}">
   ```
2. **Respostas Flexíveis no Backend:** 
   No backend, é fundamental determinar se a requisição é HTMX ou nativa. Uma `htmxRedirect` segura foi criada:
   ```php
   if ($request->header('HX-Request')) {
       return response('')->header('HX-Redirect', route('painel.index'));
   }
   return redirect()->route('painel.index');
   ```
3. **Cache Busting Obrigatório (Invalidation):** 
   Na inclusão de recursos estáticos cruciais do legado submetidos à refatoração, utilizar a function do servidor para romper cache instantaneamente nos clients testadores:
   ```html
   <script src="{{ asset('js/foco-02-v2.js') }}?v={{ time() }}"></script>
   ```

Essa abordagem combinada garante **tolerância a falhas**. Se o HTMX opera, a requisição flui dinamicamente com respostas 200 OK e `HX-Redirect`; se falha, o comportamento nativo via HTTP POST resguarda o salvamento e previne que o usuário caia em falsas impressões de erros de sistema ou perca os dados de sua atividade.
