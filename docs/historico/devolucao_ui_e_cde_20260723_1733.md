# Histórico de Alteração: UI de Devolução e Ajuste CDE
**Data:** 23/07/2026 17:33

## Alteração 1: ProcessoController.php (Lógica CDE e Extração de Devolução)
**Estado Anterior:**
```php
// CDE
                if ($deliberacao === 'aprovar') {
                    if ($competencia === 'superintendencia') {
                        $processo->status_atual = 'Deliberado - SPU/UF';
                    } elseif ($competencia === 'cde') {
                        $processo->status_atual = 'Deliberado - CDE';
                    }
                } elseif ($deliberacao === 'indeferir') {
                    if ($competencia === 'superintendencia') {
                        $processo->status_atual = 'Indeferido - SPU/UF';
                    } elseif ($competencia === 'cde') {
                        $processo->status_atual = 'Indeferido - CDE';
                    }
                }
// SHOW
        $abasPreenchidas = [
            1 => $foco && $foco->aba1,
// TRAMITAR/DEVOLVER (Criação cega de tramite)
        $processo->save();
        $this->syncProcessoStatusToSupabase($processo);
```

**Estado Novo:**
Adicionada lógica para extrair `$ultimaDevolucao` em `show()`, tratar o estado `'devolver'` para CDE, e adicionar a justificativa, acao e usuario_id ao `Tramite` no momento da criação da devolução.

**Plano de Rollback:**
1. Em `app/Http/Controllers/ProcessoController.php`, procure o método `show` e remova o bloco `$ultimaDevolucao = null; if(...)`. Remova também a variável do `compact()`.
2. Em `salvarAba7`, remova o `elseif ($deliberacao === 'devolver')` no bloco do CDE.
3. No final do método `tramitar`, apague o bloco `// Atualizar o tramite com a justificativa de devolução`.

---

## Alteração 2: show.blade.php (Injeção de Componente)
**Estado Anterior:**
```html
    <div class="main-content">

        @if ($aba == 1)
```

**Estado Novo:**
```html
    <div class="main-content" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
        @if(isset($ultimaDevolucao))
            <x-alerta-devolucao :devolucao="$ultimaDevolucao" />
        @endif

        @if ($aba == 1)
```

**Plano de Rollback:**
1. Abra `resources/views/processos/show.blade.php` e remova a tag `<x-alerta-devolucao :devolucao="$ultimaDevolucao" />`.

---

## Alteração 3: Componente Visual (Novo Arquivo)
Criado o arquivo `resources/views/components/alerta-devolucao.blade.php`.
Para reverter, basta deletar o arquivo.
