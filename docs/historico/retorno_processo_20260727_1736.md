# Correção do Fluxo de Retorno de Processo (2026-07-27)

## O Problema
O sistema não possuía uma distinção visual e lógica clara entre a aba "alvo" de uma devolução (que deve fornecer a justificativa) e as abas subsequentes por onde o processo flui de volta ao remetente original. Além disso, a `aba3` exibia o botão de "Devolver Processo" mesmo quando o processo já estava em estado de devolução (em trânsito para frente), e a timeline rotulava manifestações erradas como "Manifestação - Chefia" devido a um *fallback* agressivo no código.

## Estado Novo (Depois)
1. **`ProcessoController.php` (linha ~315)**: O método `show()` agora varre os trâmites subsequentes à devolução para verificar se já existe uma `resposta_devolucao`. O array `$respostaDevolucao` é passado para as views contendo o texto, usuário e data.
2. **`aba1.blade.php`, `aba2.blade.php`, `aba3.blade.php`**: O bloco original de Resposta de Devolutiva foi substituído por uma lógica condicional. Se `$respostaDevolucao` existir, exibe um container read-only verde "Retornar Processo" no topo. Caso não, exibe o `<textarea>` para a aba alvo justificar. Na `aba3`, a div completa de `Devolver Processo` (para trás) foi envolvida em `@if($processo->tramitacao !== 'Devolvido')` para ficar oculta enquanto o processo retorna.
3. **`timeline.blade.php` (linha ~283)**: A lógica de nomeação do label de manifestação foi corrigida de:
```php
if (!$prefix || !isset($dadosSnapshot['assinatura_' . $prefix . '_nome'])) { ... }
```
para:
```php
if (!$prefix) { ... }
```
Assim, se a etapa original for válida no map, ela é respeitada e o label é fixado na etapa de origem correta da ação (mesmo sendo um rascunho sem assinatura), acabando com o erro de rotular tudo como Chefia.

## Estado Anterior (Antes)
- Controller apenas capturava a `$ultimaDevolucao` mas não checava se a resposta já fora preenchida no fluxo.
- `aba1`, `aba2` e `aba3` tinham instâncias soltas (e algumas abas nem tinham) do form de resposta.
- `timeline.blade.php` realizava um `array_reverse` sobre os perfis quando não encontrava data casando, parando no primeiro perfil que tivesse *qualquer* assinatura registrada na história do processo, sobrescrevendo rótulos de perfis hierarquicamente inferiores.

## Plano de Rollback / Desfazer
Caso as modificações quebrem o carregamento das abas ou gerem efeitos indesejados nas transições:
1. No `ProcessoController.php`, reverta as mudanças dentro do bloco `if ($processo->tramitacao === 'Devolvido')`, apagando o laço `foreach ($tramitesAposDevolucao)`.
2. Em `aba1.blade.php`, `aba2.blade.php` e `aba3.blade.php`, localize `@if(isset($respostaDevolucao))` e substitua pelo antigo código simples contendo o `<textarea id="resposta_devolucao">`.
3. Em `aba3.blade.php`, remova a condicional `@if($processo->tramitacao !== 'Devolvido')` ao redor de `<!-- ========== ACCORDION DEVOLUÇÃO ========== -->`.
4. Em `timeline.blade.php`, retorne o `if` da linha 283 para avaliar `!isset($dadosSnapshot['assinatura_' . $prefix . '_nome'])` em conjunto com `!$prefix`.
