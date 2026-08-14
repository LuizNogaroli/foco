# KB: Referência dos Boxes de Manifestação da Aba 7

Este documento é a **fonte canônica** de nomenclatura para os boxes de manifestação renderizados na Aba 7 (`resources/views/processos/abas/aba7.blade.php`). Use estes nomes ao se referir aos boxes em qualquer conversa, tarefa ou instrução.

---

## 1. Convenção de Nomenclatura

- **Nome amigável (para o usuário):** `Box - <Perfil>` (ex: `Box - Chefia`, `Box - CDE`).
- **Chave de código:** a chave do array `$secoes` (`aba7.blade.php:206-214`), em snake_case, sem acentos.
- **ID HTML canônico (aprovado e implementado em 2026-08-06):** `id="box-<chave>"` (ex: `box-equipe_cg`) em `aba7.blade.php:228`. Este padrão substituiu o `id` antigo (primeira letra da chave, que gerava IDs duplicados).

> **Regra para a IA:** quando o usuário disser "Box - Equipe C.G.", "box equipe_cg" ou "box-equipe_cg", todos significam a MESMA coisa. Use sempre a chave de código como referência de implementação.

---

## 2. Lista de Referências dos Boxes

| Nome amigável (invocar assim) | Chave de código | ID HTML | Perfil | Status que ativa o box | Assinatura | Tipo de formulário |
|---|---:|---|---|---|---|---|
| **Box - Chefia** | `chefia` | `box-chefia` | Chefia | `Validação - Chefia` | `assinatura_chefia` | Próprio |
| **Box - Coordenação** | `coordenacao` | `box-coordenacao` | Coordenação | `Validação - Coordenação` | `assinatura_coordenacao` | Próprio |
| **Box - Superintendência** | `superintendencia` | `box-superintendencia` | Superintendência | `Deliberação - Superintendência` | `assinatura_superintendencia` | Próprio |
| **Box - Equipe C.G.** | `equipe_cg` | `box-equipe_cg` | Equipe C.G. | `Conformidade Prévia` | `assinatura_equipe_cg` | Checklist + Manifestação |
| **Box - Coordenação-Geral** | `coordenacao_geral` | `box-coordenacao_geral` | Coordenação-Geral | `Validação - Coordenação-Geral` | `assinatura_coordenacao_geral` | Próprio |
| **Box - Direção** | `direcao` | `box-direcao` | Direção | `Validação - Direção` | `assinatura_direcao` | Próprio |
| **Box - CDE** | `cde` | `box-cde` | CDE | `Deliberação - CDE` | `assinatura_cde` | Próprio |

---

## 3. Tipos de Formulário

Cada perfil tem **partial próprio** em `resources/views/processos/abas/manifestacoes/` (`{chave}.blade.php`), incluído dinamicamente em `aba7.blade.php` via `@include('processos.abas.manifestacoes.' . $chave)`. Não há mais partial genérico: cada box tem seus textos, opções e regras de negócio particulares.

### 3.1 Chefia (`chefia.blade.php`)
Manifestação com 2 opções: `decl_chefia_opcao` (`suficiente` / `insuficiente`). Campo de observações (`obs_chefia`) exibido e obrigatório quando a opção for `insuficiente`.

### 3.2 Coordenação (`coordenacao.blade.php`)
Manifestação com 2 opções: `decl_coordenacao_opcao` (`suficiente` / `insuficiente`). Campo de observações (`obs_coordenacao`) exibido e obrigatório quando a opção for `insuficiente`.

### 3.3 Superintendência (`superintendencia.blade.php`)
Inclui regime de destinação, decisão (`sup_deliberacao`), competência CDE (`sup_competencia`: sim/nao).

### 3.4 Equipe C.G. (`equipe_cg.blade.php`)
Checklist de Conformidade Prévia (`chk_{id}` e `obs_chk_{secao}`) + Manifestação final (`decl_equipe_cg_opcao`, `obs_equipe_cg_condicionantes`, `decl_equipe_cg_conclusao`).

### 3.5 Coordenação-Geral (`coordenacao_geral.blade.php`)
"Manifestação:" com 3 opções (favoravel / favoravel_condicionantes / nao_favoravel), condicionantes, conclusão apta/inapta.

### 3.6 Direção (`direcao.blade.php`)
"Manifestação:" com 3 opções (`apta_cde` / `restituir_spuf` / `diligencia`), fechando com "Encaminhe-se conforme deliberado".

### 3.7 CDE (`cde.blade.php`)
Regime de destinação, condicionantes (`obs_cde`), deliberação (`cde_deliberacao`).

---

## 4. Renderização Condicional

Cada box é renderizado apenas quando:
1. O `status_atual` do processo corresponde ao status do box (`aba7.blade.php:218`), **ou**
2. Já existe assinatura registrada (`$dados[$s['assinatura'] . '_nome']`, `aba7.blade.php:227`).

Ou seja, para alterar o conteúdo de um box, além de editar o bloco correto da view, é preciso abrir um processo cujo status esteja na etapa correspondente (ou simular perfil de Direção).

---

## 5. Referência no Controller

O branch de processamento do envio de cada box está em `app/Http/Controllers/ProcessoController.php` no método `tramitar()` (`ProcessoController.php:682-785`), usando o mesmo nome da chave na variável `$acao`:
- `$acao === 'chefia'`, `'coordenacao'`, `'superintendencia'`, `'equipe_cg'`, `'coordenacao_geral'`, `'direcao'`, `'cde'`.

> **Atenção (pendência conhecida):** os branches do `tramitar()` (`ProcessoController.php:682-785`) ainda validam valores antigos `suficiente`/`insuficiente` e campos `obs_{chave}` para chefia, coordenacao, coordenacao_geral e direcao, e `obs_equipe_cg`/`insuficiente` para equipe_cg. Os formulários atuais enviam `favoravel`/`favoravel_condicionantes`/`nao_favoravel`, `obs_{chave}_condicionantes`, `decl_{chave}_conclusao` (e para direção: `apta_cde`/`restituir_spuf`/`diligencia`). Os branches devem ser atualizados quando o fluxo for finalizado. **Exceção:** os Boxes de Chefia e Coordenação foram revertidos ao formato antigo em 2026-08-10 (`decl_{chave}_opcao` = `suficiente`/`insuficiente`, com `obs_{chave}` obrigatório na insuficiência) e já estão alinhados aos branches `$acao === 'chefia'` e `$acao === 'coordenacao'` do `tramitar()`.
