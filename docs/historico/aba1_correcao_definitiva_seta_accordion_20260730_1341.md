# Correção Definitiva da Rotação da Seta do Accordion (Aba 1)

## 1. Estado Anterior (Antes)
No arquivo `public/js/foco-02-v2.js`, o script alterava o estilo inline da seta via javascript, e a verificação de estado do container era baseada apenas em estilos inline, causando falha no primeiro clique:
```javascript
    if (content) {
        const isCollapsed = content.style.display === 'none';
        
        // ... (animações)
        
        if (icon) {
            icon.style.display = "inline-block";
            icon.style.transition = "transform 0.3s ease";
            icon.style.transform = isCollapsed ? 'rotate(90deg)' : 'rotate(0deg)';
        }
        header.classList.toggle('active', isCollapsed);
    }
```
No arquivo `aba1.blade.php`, faltava a propriedade `display: inline-block;` para garantir o funcionamento do transform.

## 2. Estado Novo (Depois)
O script `public/js/foco-02-v2.js` foi refatorado para não injetar rotações inline, delegando o comportamento para o CSS via classe `.active`, e a verificação do estado recolhido foi melhorada usando `getComputedStyle`:
```javascript
    if (content) {
        const isCollapsed = window.getComputedStyle(content).display === 'none';
        
        // ... (animações)
        
        header.classList.toggle('active', isCollapsed);
    }
```
No arquivo `aba1.blade.php`, foi adicionado `display: inline-block;` na classe `.accordion-icon`.

## 3. Plano de Rollback / Desfazer
Para reverter:
1. Em `public/js/foco-02-v2.js`, retorne `window.getComputedStyle(content).display === 'none';` para `content.style.display === 'none';`.
2. Adicione de volta o bloco de estilização inline do `icon` antes de `header.classList.toggle`.
3. Em `aba1.blade.php`, remova `display: inline-block;` da classe `.accordion-icon`.
