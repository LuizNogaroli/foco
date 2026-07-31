# Correção do Script de Seta do Accordion (Aba 1)

## 1. Estado Anterior (Antes)
No arquivo `public/js/foco-02-v2.js`, o script alterava o estilo inline da seta (sobrescrevendo o CSS) para rodar a seta para cima (-90deg):
```javascript
            icon.style.transform = isCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)';
```

## 2. Estado Novo (Depois)
Agora o script rotaciona a seta para baixo (90deg) quando o container é expandido:
```javascript
            icon.style.transform = isCollapsed ? 'rotate(90deg)' : 'rotate(0deg)';
```

## 3. Plano de Rollback / Desfazer
Para reverter esta mudança e restaurar a rotação original:
1. Abra o arquivo `public/js/foco-02-v2.js`.
2. Na linha 1318, altere `icon.style.transform = isCollapsed ? 'rotate(90deg)' : 'rotate(0deg)';` para `icon.style.transform = isCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)';`.
3. Salve o arquivo.
