# Base de Conhecimento: Deploy do Laravel 11 no Render com PostgreSQL

**Data:** 23/07/2026
**Assunto:** Saga de Deploy no Render, migração para PostgreSQL e resolução de problemas estruturais de produção.

## 1. O Problema Inicial: SQLite em Sistema de Arquivos Efêmero
Inicialmente, a aplicação utilizava o banco de dados SQLite. O deploy no Render (plano gratuito) funcionava, mas apresentava um defeito crítico: sempre que a aplicação entrava em hibernação ou um novo deploy era realizado, todos os dados desapareciam. 
- **Causa:** O Render (assim como Heroku e afins) utiliza sistemas de arquivos efêmeros. Quaisquer arquivos gravados no disco em tempo de execução (como o `database.sqlite`) são apagados quando o container é recriado.
- **Solução:** Migração obrigatória para um banco de dados externo e relacional (PostgreSQL foi a escolha pelo suporte nativo no Render).

## 2. A Configuração do PostgreSQL no Render
- Criamos um serviço de banco de dados PostgreSQL separado no Render.
- O endereço interno (`External/Internal Database URL`) fornecido pelo Render foi configurado nas variáveis de ambiente (Environment Variables) do serviço Web.
- A configuração no `.env` (via Render dashboard) ficou:
  ```env
  DB_CONNECTION=pgsql
  DB_HOST=<endereco_do_host>
  DB_PORT=5432
  DB_DATABASE=<nome_do_banco>
  DB_USERNAME=<usuario>
  DB_PASSWORD=<senha>
  ```

## 3. Script de Inicialização (Start Script)
Diferente de um servidor dedicado, no Render precisávamos que o banco fosse preparado assim que a aplicação ligasse. Para isso, criamos o arquivo `start.sh` na raiz do projeto:
```bash
#!/usr/bin/env bash
echo "Rodando as migrations do banco de dados..."
php artisan migrate --force
php artisan db:seed --force
echo "Iniciando a aplicacao web..."
php artisan serve --host=0.0.0.0 --port=$PORT
```
> [!IMPORTANT]
> O uso do `--force` é obrigatório, pois em ambiente de produção (quando `APP_ENV=production`) o Laravel exige confirmação humana para rodar migrations, o que travaria o deploy automático.
> O `db:seed` foi inserido para garantir a criação do usuário administrador (`admin@spu.gov.br` / `password`).

## 4. O Problema de Timeout / Porta (Port Binding)
Durante o deploy, o Render acusava falha de saúde (Health Check Failed) ou Timeout.
- **Causa:** O Render exige que a aplicação "escute" na porta definida na variável de ambiente nativa `$PORT` (que geralmente é a porta 10000 no Render). O Laravel por padrão roda na porta 8000.
- **Solução:** O comando final do `start.sh` foi ajustado para `php artisan serve --host=0.0.0.0 --port=$PORT`.

## 5. A Compilação dos Assets (Vite)
O Vite não buildava automaticamente no Render, fazendo a aplicação renderizar sem nenhum CSS ou JavaScript.
- **Solução:** O "Build Command" do Render foi modificado para executar a compilação do Node juntamente com a do PHP:
  `npm install && npm run build && composer install --no-dev --optimize-autoloader`
- Foi necessário certificar que o Node.js estava em uma versão atualizada (versão 20), caso contrário o Vite falhava ao compilar as dependências de sintaxe moderna.

## 6. O Problema de Conteúdo Misto (Mixed Content / Insecure Requests)
Quando o sistema foi pro ar, a página de login abria normal (pois o CSS era nativo), mas o Dashboard vinha "quebrado". Ao olhar o console, o navegador bloqueava os arquivos CSS gerados pelo Vite acusando "Mixed Content".
- **Causa:** O Render entrega o site sob HTTPS seguro com o certificado SSL. Porém, o servidor de balanceamento interno do Render redireciona o tráfego para a nossa aplicação via HTTP simples. O Laravel "acha" que está rodando em HTTP e gera todos os links do Vite e de paginação como `http://...`. O navegador moderno bloqueia requisições HTTP dentro de um site HTTPS.
- **Solução:** No arquivo `app/Providers/AppServiceProvider.php`, forçamos o "scheme" HTTPS para todo o roteamento do Laravel:
  ```php
  use Illuminate\Support\Facades\URL;
  
  public function boot(): void
  {
      if (env('APP_ENV') === 'production') {
          URL::forceScheme('https');
      }
  }
  ```

## 7. Ajustes de Banco e Interface na Etapa 7 (Assinaturas)
Após ter a infraestrutura funcionando, descobrimos que partes do código front-end na Etapa 7 não carregavam corretamente as informações do Supabase e SQLite simultaneamente devido a corrida de processos JavaScript.
- **Solução (Timing JS):** Envolvermos o bloco de carregamento em `document.addEventListener('DOMContentLoaded')` para garantir que o framework JS completo do Foco (`fetchSPU`) existisse antes de ser invocado.
- **Solução (Backend):** Implementamos validações no `ProcessoController.php` para impedir que os botões de "Salvar e Enviar" gerassem falsos-positivos de submissão quando opções obrigatórias de rádio não estavam preenchidas.
