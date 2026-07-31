# Histórico de Alterações — Configuração de Deploy no Render com Supabase

## 1. Estado Anterior (Antes)

### render.yaml
```yaml
services:
  - type: web
    name: foco-15-app
    env: docker
    plan: free
    region: ohio
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: APP_KEY
        sync: false
      - key: APP_URL
        generateValue: true
      - key: DB_CONNECTION
        value: pgsql
      - key: DATABASE_URL
        fromDatabase:
          name: foco-15-database
          property: connectionString
      - key: SESSION_DRIVER
        value: database
      - key: CACHE_STORE
        value: database
      - key: QUEUE_CONNECTION
        value: database

databases:
  - name: foco-15-database
    databaseName: foco15
    user: foco15
    plan: free
    region: ohio
```

### start.sh
```bash
echo "Rodando as migrations do banco de dados..."
# O --force é necessário em produção para não pedir confirmação
php artisan migrate --force
php artisan db:seed --force
```

---

## 2. Estado Novo (Depois)

### render.yaml
```yaml
services:
  - type: web
    name: foco-18-app
    env: docker
    plan: free
    region: ohio
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: APP_KEY
        sync: false # Preencha no painel do Render
      - key: APP_URL
        generateValue: true # O Render preenche com a URL do app
      - key: DB_CONNECTION
        value: pgsql
      - key: DB_HOST
        sync: false # Preencha com o host do Supabase
      - key: DB_PORT
        value: "5432"
      - key: DB_DATABASE
        value: postgres
      - key: DB_USERNAME
        sync: false # Preencha com o usuário do Supabase
      - key: DB_PASSWORD
        sync: false # Preencha com a senha do Supabase
      - key: DB_SSLMODE
        value: require
      - key: SESSION_DRIVER
        value: database
      - key: CACHE_STORE
        value: database
      - key: QUEUE_CONNECTION
        value: database
      - key: SEED_DATABASE
        value: false
```

### start.sh
```bash
echo "Rodando as migrations do banco de dados..."
php artisan migrate --force

if [ "$SEED_DATABASE" = "true" ]; then
  echo "Rodando seeders do banco de dados..."
  php artisan db:seed --force
fi
```

---

## 3. Plano de Rollback / Desfazer

Caso queira reverter a configuração para voltar a provisionar e utilizar o banco de dados interno do Render local (versão Foco-15 anterior):

1. Execute no terminal:
   ```bash
   git revert fc64121b10c85addc1639a72b612ce2d7b4d66f6
   ```
   *(Substitua pelo hash do commit correspondente se necessário).*
2. Faça o push para o repositório remoto:
   ```bash
   git push origin main
   ```
3. Exclua as variáveis manuais (`DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_PORT`, `SEED_DATABASE`, `APP_KEY`) no painel do Render.
4. Delete o serviço `foco-18-app` no Render e re-importe o Blueprint.
