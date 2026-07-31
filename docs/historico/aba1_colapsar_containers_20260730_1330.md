# Colapsar Containers e Ajustar Ícone de Seta (Aba 1)

## 1. Estado Anterior (Antes)
```css
    .active .accordion-icon { transform: rotate(-90deg); }
```
```html
                        <details class="documentos-expansivel" open>
```

## 2. Estado Novo (Depois)
```css
    .active .accordion-icon { transform: rotate(90deg); }
```
```html
                        <details class="documentos-expansivel">
```

## 3. Plano de Rollback / Desfazer
Para reverter esta mudança e restaurar o comportamento original:
1. Abra o arquivo `resources/views/processos/abas/aba1.blade.php`.
2. Na linha 25 (bloco CSS), altere `transform: rotate(90deg);` para `transform: rotate(-90deg);`.
3. Na linha 151 (bloco HTML), altere `<details class="documentos-expansivel">` para `<details class="documentos-expansivel" open>`.
4. Salve o arquivo.
