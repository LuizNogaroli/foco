# Correção Global da Rotação de Setas dos Accordions

## 1. Estado Anterior (Antes)
Em diversos arquivos do projeto (incluindo o layout principal `show.blade.php`, a `aba3.blade.php` e diversos arquivos de `historico_modelo_*.blade.php`), a seta expansível (▶) estava configurada para rotacionar `-90deg` ao expandir o container. Isso fazia com que a seta apontasse para cima (▲) ao invés de para baixo (▼):
```css
    transform: rotate(-90deg);
```
ou em Javascript:
```javascript
    icon.style.transform = isCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)';
```

## 2. Estado Novo (Depois)
Foi executada uma substituição global em todas as views (arquivos `.blade.php`) onde a propriedade de rotação da expansão estava definida como `-90deg`, alterando-a para `90deg`. Isso garante que, ao expandir qualquer accordion no sistema (incluindo os resumos da Aba 1 dentro da Aba 3), a seta girará 90 graus no sentido horário, apontando para baixo (▼).
```css
    transform: rotate(90deg);
```
ou em Javascript:
```javascript
    icon.style.transform = isCollapsed ? 'rotate(90deg)' : 'rotate(0deg)';
```

## 3. Plano de Rollback / Desfazer
Caso seja necessário reverter esta mudança em lote:
1. Crie e execute o seguinte script Python na raiz do projeto (`c:\dev\Foco-17`) para substituir `rotate(90deg)` de volta para `rotate(-90deg)` em todos os arquivos de view:
```python
import glob
def replace_in_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    if 'rotate(90deg)' in content:
        new_content = content.replace('rotate(90deg)', 'rotate(-90deg)')
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)

files = glob.glob('resources/views/**/*.blade.php', recursive=True)
for f in files: replace_in_file(f)
```
