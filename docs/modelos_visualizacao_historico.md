# Odisséia dos Modelos de Visualização do Histórico

## Origem

O sistema **Foco-17** gerencia processos de destinação de imóveis da União. Cada
processo passa por um fluxo de abas (Indicação do Imóvel, Diagnóstico
Preliminar, Análise de Viabilidade) e perfis de aprovação (Chefia,
Coordenação, Superintendência, Equipe C.G., Coordenação-Geral, Direção, CDE),
gerando um histórico de trâmites.

O que começou como uma **timeline cronológica simples** (Modelo A) evoluiu para
um **laboratório de experimentação visual** — 7 modelos que contam a mesma
história sob ângulos completamente diferentes.

---

## Os 7 Modelos

### Modelo A — Timeline Cronológica (original)

| Característica | Descrição |
|---|---|
| **Rota** | `processos.historico` |
| **View** | `abas/partials/timeline.blade.php` |
| **Lógica** | Controller existente `historico()` |
| **Visual** | Timeline vertical com accordions inline. Cada evento expande para mostrar detalhes completos da aba (dados, RIPs, pareceres) |
| **Origem** | Pré-existente no sistema |
| **Diferencial** | Fidelidade ao "processo físico" — mostra tudo na página, sem modais |
| **Limitação** | Poluição visual com muitos eventos |

---

### Modelo B — Timeline Compacta

| Característica | Descrição |
|---|---|
| **Rota** | `processos.historico.modelo-b` |
| **View** | `historico_modelo_b.blade.php` |
| **Controller** | `historicoModeloB()` |
| **Visual** | Timeline vertical com cards resumidos (dot colorido, título, autor). Clique abre **modal** com detalhes completos |
| **Diferencial** | Consumo rápido — dot vermelho (devolução), verde (recebido), azul (salvo), teal (manifestação) |
| **Padrão adotado para modais** | `modal-overlay` com `aberto` class, fecha com clique no fundo, X ou ESC |

---

### Modelo C — KanBan por Perfil

| Característica | Descrição |
|---|---|
| **Rota** | `processos.historico.modelo-c` |
| **View** | `historico_modelo_c.blade.php` |
| **Controller** | `historicoModeloC()` |
| **Helper** | `getColunaTramite()` — mapeia cada trâmite a um perfil responsável |
| **Visual** | Grid horizontal com 9 colunas (Chefia, Coordenação, Superintendência, Equipe C.G., Coordenação-Geral, Direção, CDE + Sistema + Outros). Cards empilhados por perfil |
| **Diferencial** | Visão gerencial — "quem fez o quê?". Ideal para identificar gargalos por perfil |
| **Desafio** | Mapear corretamente cada trâmite ao perfil (via snapshot de assinatura ou etapa de origem) |

---

### Modelo D — Grafo de Fluxo de Estados

| Característica | Descrição |
|---|---|
| **Rota** | `processos.historico.modelo-d` |
| **View** | `historico_modelo_d.blade.php` |
| **Controller** | `historicoModeloD()` |
| **Helper** | `montarFluxoEstados()` — percorre trâmites e monta nós + arestas |
| **Visual** | Grafo vertical com nós (cards) e arestas (setas coloridas). Cada nó tem tipo: `salva`, `manifestacao`, `devolucao`, `recebido`, `resolucao` |
| **Diferencial** | Visão estrutural — mostra o "caminho" percorrido pelo processo. Cores e estilos diferentes para cada tipo de evento |
| **Detalhe** | O nó `Devolução Resolvida` (tipo `resolucao`) tem borda tripla verde |

---

### Modelo E — BPMN com Gateway e Swimlane

| Característica | Descrição |
|---|---|
| **Rota** | `processos.historico.modelo-e` |
| **View** | `historico_modelo_e.blade.php` |
| **Controller** | `historicoModeloE()` — agrupa trâmites em **blocos** (main / cycle) |
| **Visual** | Inspirado em BPMN. Timeline principal vertical + **gateway exclusivo** (◆ vermelho) que bifurca para uma **swimlane** (pool laranja) à direita. Ao final do ciclo, **convergência** (◇ verde) retorna ao fluxo principal |
| **Conceito** | `Devolvido` → ◆ gateway → swimlane com eventos internos → Devolução Resolvida → ◇ converge |
| **Diferencial** | O mais fiel à notação BPMN. Mostra visualmente o "sub-processo" de devolução como um desvio do fluxo normal |
| **Legenda** | Explica no topo: gateway exclusivo, pool, convergência |

---

### Modelo F — Colunas por Passagem

| Característica | Descrição |
|---|---|
| **Rota** | `processos.historico.modelo-f` |
| **View** | `historico_modelo_f.blade.php` |
| **Controller** | `historicoModeloF()` — divide trâmites em **colunas** (passagens). Cada `Devolvido` cria nova coluna |
| **Visual** | Múltiplas colunas lado a lado com scroll horizontal. Cada coluna é uma timeline vertical completa com header numerado ("1ª Passagem", "2ª Passagem"). Conector `→` entre colunas |
| **Diferencial** | "Replay visual" — mostra quantas vezes o processo passou por cada etapa. Devoluções viram novas colunas |

---

### Modelo G — Matriz Abas × Perfis × Passagens

| Característica | Descrição |
|---|---|
| **Rota** | `processos.historico.modelo-g` |
| **View** | `historico_modelo_g.blade.php` |
| **Controller** | `historicoModeloG()` — constrói matriz 2D (linhas = etapas/perfis, colunas = passagens) |
| **Visual** | Tabela/matriz com primeira coluna fixa (sticky) e scroll horizontal. Células coloridas por tipo de evento (salvo, manifestação, devolução, resolução) |
| **Diferencial** | Visão tabular — responde "o que aconteceu em cada etapa durante cada passagem?". Ideal para impressão e relatórios |

---

## Padrões Técnicos Compartilhados

### Modal de Detalhes

Todos os modelos (B a G) compartilham o mesmo padrão de modal:

```html
<!-- overlay + content + close button -->
<div id="modal-{{ $tramite->id }}" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close" onclick="fecharModal('...')">&times;</button>
        <!-- header: ação + usuário + data -->

        <!-- conditional content based on type:
             - abas salvas: accordions com includes (aba1a, aba1b, aba2, aba3)
             - devolvido: justificativa em vermelho
             - recebido: mensagem centralizada
             - devolução resolvida: resolução em verde
             - manifestação: parecer com assinatura, deliberação, observações
        -->
    </div>
</div>
```

JavaScript compartilhado:
- `abrirModal(id)` — add class `aberto`, trava scroll do body
- `fecharModal(id)` — remove class `aberto`, destrava scroll
- Listener de clique no fundo escuro (`e.target === modal`)
- Listener de tecla `Escape`

### Accordions (Modelos A, B, C, D, E, F, G)

```css
.acordeao-wrapper.aberto .acordeao-corpo { display: block; }
```

Toggle via `this.parentElement.classList.toggle('aberto')` no `onclick` do header.

### Ordenação dos Trâmites

Sempre:
```php
->orderBy('created_at', 'asc')->orderBy('id', 'asc')
```

Garantia de ordem consistente quando timestamps coincidem (ex: criação de
`Devolução Resolvida` e `Aba X Salva` na mesma requisição).

### Devolução Resolvida (criação automática)

No método `tramitar()` do `ProcessoController`, antes de criar o trâmite
"Aba X Salva":

```php
if (!empty($validated['resposta_devolucao'])) {
    // cria trâmite 'Devolução Resolvida'
    Tramite::create([
        'processo_id' => $processo->id,
        'acao' => 'Devolução Resolvida',
        'usuario_id' => Auth::id(),
        'dados_snapshot' => $dadosSnapshot,
        'justificativa' => $validated['resposta_devolucao'],
    ]);
}
// depois cria o trâmite 'Aba X Salva'
```

Isso garante a ordenação correta: `Devolução Resolvida` ANTES de
`Aba X Salva`.

### Aprendizados sobre Blade

| Problema | Solução |
|---|---|
| `@if(...)` inline na mesma linha causa erro de parse no Blade 13 | Usar `{!! $altTag !!}` com ternário ou bloco `@php ... @endphp` |
| `@php($var = $val)` gera `<?php($var = $val)` sem fechar bloco PHP | Usar `@php $var = $val; @endphp` (com ponto e vírgula) |
| Strings longas de CSS inline repetidas | Extrair classes CSS e alternar via `@php` + variável |

---

## Estrutura do Código

```
routes/web.php                       # Rotas (modelo-a a modelo-g)
app/Http/Controllers/
  ProcessoController.php
    historico()                       # Modelo A
    historicoModeloB()                # Modelo B
    historicoModeloC()                # Modelo C
    historicoModeloD()                # Modelo D
    montarFluxoEstados()              # Helper do Modelo D
    getColunaTramite()                # Helper do Modelo C
    historicoModeloE()                # Modelo E
    historicoModeloF()                # Modelo F
    historicoModeloG()                # Modelo G
    tramitar()                        # Criação automática Devolução Resolvida
resources/views/processos/
  historico_escolha.blade.php         # Página central de escolha
  abas/partials/timeline.blade.php    # Modelo A
  historico_modelo_b.blade.php        # Modelo B
  historico_modelo_c.blade.php        # Modelo C
  historico_modelo_d.blade.php        # Modelo D
  historico_modelo_e.blade.php        # Modelo E
  historico_modelo_f.blade.php        # Modelo F
  historico_modelo_g.blade.php        # Modelo G
```

---

## Possíveis Evoluções (Modelos H, I, J...)

- **Modelo H** — Grafo interativo com D3.js (nós arrastáveis, zoom, clusters)
- **Modelo I** — Timeline estilo Gantt (duração entre eventos, tempo ocioso)
- **Modelo J** — Visão "diff" (comparar lado a lado duas passagens)
- **Modelo K** — Heatmap por perfil + tempo (intensidade de atividade)
- **Modelo L** — Mapa mental / tree map hierárquico

---

> _"O que começou como uma timeline virou 7 visualizações do mesmo processo.
> Cada uma conta a história de um jeito. Nenhuma está errada."_
