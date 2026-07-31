# Guia de Migração: Modelos de Histórico (Kanban e Grafo)

Para transferir os modelos **C (KanBan)** e **D (Grafo de Estados)** para outro projeto Laravel, você precisará portar três camadas principais: Banco de Dados/Modelos, Lógica de Controle (Controller) e Visualização (Blade/CSS). 

Siga este roteiro para garantir que nada fique para trás:

## 1. Requisitos de Banco de Dados e Models
O novo projeto precisa ter uma estrutura de rastreamento de histórico similar à nossa tabela `tramites`. 
Certifique-se de que o projeto destino possua:
- Uma relação onde um `Processo` (ou entidade principal) `hasMany` `Tramites` (histórico).
- O model de histórico (ex: `Tramite`) deve possuir os campos mínimos:
  - `acao` (string)
  - `etapa` (string - para saber qual perfil/departamento agiu)
  - `dados_snapshot` (json - para capturar o payload da ação, assinaturas e justificativas)
  - `usuario_id` (foreign key do autor)
  - `created_at` (timestamp)

## 2. Lógica de Backend (Controllers)
No projeto atual, toda a "inteligência" de parseamento dos dados está no `ProcessoController.php`. Você precisará copiar os seguintes métodos para o Controller equivalente no novo projeto:

**Para o Modelo C (KanBan):**
- Copie o método principal que retorna a view: `historicoModeloC()`
- Copie o *helper* de categorização: `getColunaTramite()`. É ele quem varre o `dados_snapshot` e a `etapa` para descobrir em qual das 9 colunas o card deve ser renderizado. Se o novo projeto tiver perfis/departamentos diferentes, você precisará ajustar as chaves do *switch/array* neste método.

**Para o Modelo D (Grafo):**
- Copie o método principal: `historicoModeloD()`
- Copie o *helper* construtor: `montarFluxoEstados()`. Este método é crucial, pois ele gera os arrays de `nos[]` e `arestas[]` lidos pela view.

## 3. Rotas (`routes/web.php`)
Não esqueça de registrar as rotas que apontam para os métodos copiados no passo anterior:
```php
Route::get('/processos/{id}/historico/modelo-c', [ProcessoController::class, 'historicoModeloC'])->name('processos.historico.modelo-c');
Route::get('/processos/{id}/historico/modelo-d', [ProcessoController::class, 'historicoModeloD'])->name('processos.historico.modelo-d');
```

## 4. Camada de Apresentação (Views e CSS)
Copie os arquivos base de visualização localizados em `resources/views/processos/`:
- `historico_modelo_c.blade.php`
- `historico_modelo_d.blade.php`

**⚠️ Atenção aos Includes de Resumo (Modais):**
Dentro dessas views, os modais que abrem ao clicar em um card ou nó fazem uso de de vários `@include` para exibir os detalhes dos dados, como:
- `@include('processos.abas.resumos.aba1a')`
- `@include('processos.abas.resumos.aba2')`
- etc.

**O que fazer:** 
1. Se o novo projeto compartilhar os mesmos formulários, você deverá copiar a pasta `resources/views/processos/abas/resumos/` inteira.
2. Se o novo projeto for de um escopo totalmente diferente, você precisará apagar esses `@include` dentro dos modais de `historico_modelo_c.blade.php` e `historico_modelo_d.blade.php` e substituí-los pela lógica de exibição de dados própria do novo projeto (lendo diretamente da variável `$dadosSnapshot`).

## 5. Ajustes Finais de UI
O Modelo D (Grafo) foi construído de forma nativa (apenas HTML e CSS puro, sem dependências como D3.js). Todo o CSS necessário para renderizar as arestas e nós (as setas e as caixas coloridas) está contido diretamente dentro de uma tag `<style>` no topo do arquivo `historico_modelo_d.blade.php`.
Garanta que seu novo projeto use uma estrutura HTML ou layout base (ex: `@extends('layouts.app')`) compatível para que não haja conflitos de CSS com a *Master Page* do novo sistema.
