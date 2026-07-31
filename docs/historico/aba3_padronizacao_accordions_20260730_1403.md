# Padronização de Accordions na Aba 3

## 1. Estado Anterior (Antes)
Os painéis expansíveis na Aba 3 (`aba3.blade.php`) estavam implementados com blocos contendo Javascript inline acoplado (ex: `onclick="const body = this.nextElementSibling...`), configurados para alternar a visualização manualmente e rotacionar as setas de forma independente. As setas possuíam ícones variantes (`▼` ou `▶`) com estilização inconsistente.

## 2. Estado Novo (Depois)
Todos os containers expansíveis da Aba 3 (incluindo "Aba 1a", "Aba 1b", "ABA 2" e todos os sub-blocos dinâmicos injetados por script como Imóveis, Cadastros Mínimos, Pessoas e Benfeitorias) foram padronizados de acordo com as novas regras:
- Substituição do script manual inline pelo gatilho padrão do sistema: `onclick="toggleAccordion(this)"`.
- Uso estrito das classes `accordion-header` e `accordion-body collapsed`.
- Adoção das setas padrão `<span class="accordion-icon">▶</span>`, usufruindo da padronização global (tamanho `0.9rem` e cor branca obrigatória), apontando para baixo via `.active`.

## 3. Plano de Rollback / Desfazer
Para reverter todas essas atualizações na aba 3:
1. Retorne o arquivo `resources/views/processos/abas/aba3.blade.php` ao estado anterior através do controle de versão (`git restore`).
2. Como alternativa manual, reconstrua os atributos `onclick` antigos para alternarem o display (`if(b.style.display==='none'){b.style.display='block';i.style.transform='rotate(180deg)';}`) e reverta as setas para `▼` fora do controle de `.accordion-icon`.
