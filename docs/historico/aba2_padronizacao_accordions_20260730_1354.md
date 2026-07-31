# Padronização de Accordions na Aba 2

## 1. Estado Anterior (Antes)
Diversos blocos expansíveis na Aba 2 (`aba2.blade.php`) estavam estruturados com Javascript injetado de forma inline. Eles usavam rotação de `180deg` para girar a seta nativa `▼` e manipulavam a visualização (`display:none`/`block`) manualmente:
```html
<div class="accordion-header" style="..." onclick="const body = this.nextElementSibling; const icon = this.querySelector('span:last-child'); if(body.style.display === 'none'){ body.style.display = 'block'; icon.style.transform = 'rotate(180deg)'; } else { body.style.display = 'none'; icon.style.transform = 'rotate(0deg)'; }">
    <span>📍 RIP(s) ou Cadastro(s) Mínimo(s)</span>
    <span style="transition: transform 0.3s; font-size: 1.2em;">▼</span>
</div>
```
Além disso, o CSS local da aba sobrescrevia a rotação.

## 2. Estado Novo (Depois)
Todos os painéis da Aba 2 (HTML fixo e blocos dinâmicos gerados em Javascript no final do arquivo) foram convertidos com sucesso para a estrutura padrão do sistema:
- Adição da tag contêiner obrigatória `<div class="accordion-item" style="border: none;">`.
- Substituição da manipulação inline de CSS pelo gatilho centralizado padrão: `onclick="toggleAccordion(this)"`.
- Padronização do ícone fixo inicial `▶` protegido pela tag `<span class="accordion-icon">`, removendo conflitos inline.
- Correção pontual no CSS interno de `aba2.blade.php` para garantir a regra `transform: rotate(90deg)` da classe `.active`, mantendo o alinhamento com as demais abas.

## 3. Plano de Rollback / Desfazer
Se houver qualquer regressão no layout ou funcionamento dos accordions da Aba 2:
1. Volte ao commit original de `aba2.blade.php` caso esteja versionado no git.
2. Caso contrário, os passos de rollback envolverão o retorno manual do script JS inline removido: `onclick="const b=this.nextElementSibling... b.style.display='block'; ..."` e a re-inserção da seta `▼` nos campos substituídos por `▶`, acompanhado da regressão da regra CSS de `.active .accordion-icon` para `180deg`.
