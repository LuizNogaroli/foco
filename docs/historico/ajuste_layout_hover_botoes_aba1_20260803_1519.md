# Histórico de Alterações - Ajuste Hover Menus (Aba 1) - 03/08/2026

Este documento registra os ajustes finais realizados nos botões de indicação do imóvel com menu em hover para correção visual e de usabilidade (Ponto 1.1).

## 1. Problema Identificado
- Os menus em hover ("Com RIP" e "Sem RIP") estavam sendo cortados (aparecendo por baixo do rodapé ou cortados pela limitação do container).
- Faltava uma indicação (label) no topo de cada menu explicando o que as opções representavam.

## 2. Alterações Realizadas

### Em [aba1.blade.php](file:///C:/dev/Foco-19/resources/views/processos/abas/aba1.blade.php):
- Aumentado o `z-index` do `.dropdown-hover-content` para `9999` para evitar conflito com outros elementos de tela.
- Aumentada a largura mínima dos menus para `280px` para acomodar bem o texto.
- Adicionado um `padding-bottom: 120px` ao container dos botões para forçar o scroll/tamanho da caixa principal e evitar que o menu vaze os limites do accordion.
- Adicionada a classe `.dropdown-hover-label` no topo de cada menu dropdown contendo o texto: *"Selecione a conceituação do imóvel:"*.

Exemplo do novo código:
```html
                            <div class="dropdown-hover-content">
                                <div class="dropdown-hover-label">Selecione a conceituação do imóvel:</div>
                                <button type="button" onclick="selecionarConceituacaoBotao('Terreno/acrescido de marinha')">Terreno/acrescido de marinha</button>
                                ...
                            </div>
```

---

## 3. Plano de Rollback / Desfazer

Para reverter esses ajustes cosméticos:
1. Em `resources/views/processos/abas/aba1.blade.php`, localize a div flexível que engloba os botões e remova `padding-bottom: 120px;`.
2. Localize e remova as `div.dropdown-hover-label` de dentro dos menus dropdown.
3. No bloco de estilos CSS, volte o `z-index` para 100 e remova o bloco `.dropdown-hover-label`.


## 4. Atualiza��o de Contraste (Acessibilidade)
Foi alterado o \ackground-color\ das op��es em hover de \#f8fafc\ para \#e2e8f0\, a cor do texto para \#0f172a\ e a espessura da fonte para \500\, a fim de destacar mais a sele��o para usu�rios com idade avan�ada.
