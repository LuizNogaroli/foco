# Alteração do Texto da Tela de Login

## 1. Estado Anterior (Antes)
```html
    <title>Plataforma Integrada de Gestão do Patrimônio da União - Login</title>
...
                <span class="spu">Plataforma</span><span class="net"> Integrada de Gestão do Patrimônio da União</span>
```

## 2. Estado Novo (Depois)
```html
    <title>Protótipo - Login</title>
...
                <span class="spu">Protótipo</span>
```

## 3. Plano de Rollback / Desfazer
Para reverter esta mudança e restaurar o texto original:
1. Abra o arquivo `resources/views/auth/login.blade.php`.
2. Na linha 7, altere `<title>Protótipo - Login</title>` para `<title>Plataforma Integrada de Gestão do Patrimônio da União - Login</title>`.
3. Na linha 243, altere `<span class="spu">Protótipo</span>` para `<span class="spu">Plataforma</span><span class="net"> Integrada de Gestão do Patrimônio da União</span>`.
4. Salve o arquivo.
