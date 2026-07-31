# Guia de Deploy Laravel no Render

Guia passo a passo para publicar o sistema Laravel no [Render](https://render.com/),
alternativa ao Railway com plano gratuito generoso.

## 1. Pré-requisitos

- Conta no GitHub com o repositório do projeto
- Conta no [render.com](https://render.com/) (login via GitHub)
- Projeto Laravel funcional localmente
- Banco de dados PostgreSQL externo (ex: Supabase, Neon, ou o próprio PostgreSQL
  do Render)

## 2. Preparação no GitHub

```bash
git add .
git commit -m "Preparando para deploy no Render"
git push origin main
```

## 3. Criando o Web Service no Render

1. Acesse [dashboard.render.com](https://dashboard.render.com/)
2. Clique em **"New +"** → **"Web Service"**
3. Conecte seu repositório GitHub e selecione o projeto
4. O Render detecta automaticamente PHP/Laravel. Preencha:

   - **Name**: `foco-17` (ou nome desejado)
   - **Region**: `São Paulo (South America)` — menor latência
   - **Branch**: `main`
   - **Runtime**: `PHP`
   - **Build Command**:
     ```bash
     composer install --no-dev --optimize-autoloader && npm install && npm run build
     ```
   - **Start Command**:
     ```bash
     php artisan serve --host=0.0.0.0 --port=$PORT
     ```
   - **Plan**: `Free` (ou `Starter` para mais performance)

5. Clique em **"Create Web Service"** — o build inicial começa e **vai falhar**
   (falta configurar as variáveis de ambiente). Isso é normal.

> **Atenção sobre o Free Plan**: após 15 minutos sem atividade, o Render
> "hiberna" o serviço. A primeira requisição após hibernação leva ~30s para
> acordar. Em plano `Starter` (USD 7/mês) não há hibernação.

## 4. Configurando as Variáveis de Ambiente

1. No painel do seu Web Service, vá em **"Environment"** → **"Environment Variables"**
2. Adicione:

```env
APP_NAME=SPUnet
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:TY2uHWSrFpDdQ57zBUJTyYRqnkK26ZCuiLdMXTP5uP8=
LOG_CHANNEL=errorlog

# URL do app (substitua após gerar o domínio)
APP_URL=https://foco-17.onrender.com
ASSET_URL=https://foco-17.onrender.com

# Banco de Dados (ex: Supabase PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=aws-0-sa-east-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.seu_usuario
DB_PASSWORD=sua_senha

# Sessão (obrigatório para produção)
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true

# Trusted Proxies (para HTTPS funcionar)
TRUSTED_PROXIES=*

# Configurações de e-mail (se aplicável)
MAIL_MAILER=log
```

> **APP_KEY**: gere uma nova para produção rodando localmente:
> ```bash
> php artisan key:generate --show
> ```
> Copie o valor (começa com `base64:...`) e cole no `APP_KEY` do Render.
> **NUNCA** use a mesma APP_KEY do ambiente local.

## 5. Configurando Domínio Personalizado (opcional)

1. Vá em **"Settings"** → **"Custom Domain"**
2. Clique em **"Add Custom Domain"** e digite seu domínio (ex: `app.seusistema.gov.br`)
3. Configure o DNS apontando o CNAME para `foco-17.onrender.com`
4. O Render cuida do certificado SSL/HTTPS automaticamente

## 6. Primeiro Deploy com Sucesso

Após configurar as variáveis, o Render reinicia o deploy automaticamente.
Se não fizer, clique em **"Manual Deploy"** → **"Deploy latest commit"**.

Acompanhe os logs em **"Events"** ou **"Logs"**. Deploy bem-sucedido mostra:

```
=> ✓ Service is live on port 10000
```

## 7. Migrações do Banco de Dados

Após o primeiro deploy bem-sucedido, você precisa rodar as migrations:

1. No painel do Render, vá em **"Shell"** (terminal interativo)
2. Execute:
   ```bash
   php artisan migrate --force
   ```
3. Se houver seeders:
   ```bash
   php artisan db:seed --force
   ```
4. Execute também:
   ```bash
   php artisan storage:link
   php artisan view:cache
   php artisan config:cache
   php artisan route:cache
   ```

> **Alternativa**: crie um **Cron Job** no Render na seção **"Cron Jobs"** para
> rodar `php artisan schedule:run` a cada minuto, se seu sistema usa
> agendamentos.

## 8. Fluxo de Trabalho (Push → Deploy)

A partir de agora, o fluxo é idêntico ao Railway:

```bash
git push
```

O Render detecta o push, faz o build automaticamente e publica em ~2-3 minutos.

## 9. Diferenças entre Render e Railway

| Característica | Render | Railway |
|---|---|---|
| Hibernação free | 15 min inatividade | Sem hibernação |
| Plano free | 750h/mês | 500h/mês |
| PostgreSQL nativo | Sim (USD 7/mês) | Sim (incluído) |
| Domínio .onrender.com | Grátis | Grátis |
| Shell interativo | Sim (via painel) | Sim (via CLI) |
| Build minutos/mês | 500 min free | 500 min free |
| Performance free | 512 MB RAM | 1 GB RAM |

## 10. Troubleshooting Comum

| Problema | Causa | Solução |
|---|---|---|
| Tela branca | APP_KEY inválida ou faltando | Verificar variável no painel |
| Erro de conexão DB | DB_HOST errado | Usar host do pooler do Supabase |
| Asset quebrado | ASSET_URL errada | Atualizar para URL real do Render |
| 503 Service Unavailable | Hibernação free | Aguardar ~30s e recarregar |
| Erro 500 no login | SESSION_DRIVER=file | Trocar para `database` |
| CSS não carrega | Vite manifest ausente | Rodar `npm run build` no build command |
