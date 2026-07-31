# Histórico de Alterações - Timeline e Recebimento de Processos (Aba 7)
Data: 24/07/2026 10:20

## 1. O que foi feito
- Implementação de Event Sourcing na Aba 7 para diferenciar "Avanço" de "Alteração".
- Inclusão de botão "Estou Ciente / Receber" assíncrono no componente de devolução.
- Renderização visual do carimbo "Recebido" e "Alteração" na linha do tempo.

---

## 2. Estado Anterior (Antes) vs Estado Novo (Depois)

### A. Controller - Criação do Trâmite (`app/Http/Controllers/ProcessoController.php`)
**Antes:**
```php
        // Criar tramite (audit trail imutavel)
        $novoTramite = $processo->tramites()->create([
            'dados_snapshot' => $newData,
            'acao' => $effectiveAba ? 'Aba ' . $effectiveAba . ' Salva' : 'Atualização',
            'etapa' => 'Preenchimento - Aba ' . $effectiveAba,
            'usuario_id' => auth()->id()
        ]);
```

**Depois:**
```php
        $acaoDefault = 'Atualização';
        if ($effectiveAba) {
            $jaSalva = $processo->tramites()->where('acao', 'Aba ' . $effectiveAba . ' Salva')->exists();
            $acaoDefault = $jaSalva ? 'Aba ' . $effectiveAba . ' Alterada' : 'Aba ' . $effectiveAba . ' Salva';
        }

        // Criar tramite (audit trail imutavel)
        $novoTramite = $processo->tramites()->create([
            'dados_snapshot' => $newData,
            'acao' => $effectiveAba ? $acaoDefault : 'Atualização',
            'etapa' => 'Preenchimento - Aba ' . $effectiveAba,
            'usuario_id' => auth()->id()
        ]);
```

---

## 3. Plano de Rollback / Desfazer

Caso seja necessário reverter estas alterações e voltar ao estado original, execute os seguintes passos:

1. **Remover a rota de recebimento:** 
   Abra `routes/web.php` e remova a linha:
   `Route::post('/processos/{processo}/receber-devolucao', [ProcessoController::class, 'receberDevolucao'])->middleware(['auth', 'verified'])->name('processos.receber-devolucao');`

2. **Reverter ProcessoController.php:**
   - No método `tramitar()`, remova a lógica da variável `$acaoDefault` e volte o `$novoTramite = ...` para usar `'acao' => $effectiveAba ? 'Aba ' . $effectiveAba . ' Salva' : 'Atualização'`.
   - Remova o método `public function receberDevolucao(Processo $processo, Request $request)` inteiro do arquivo.
   - No método `show()`, remova a checagem da variável `$jaRecebido` e remova do `compact()` no retorno da view.

3. **Reverter a UI de Alerta de Devolução:**
   - Em `resources/views/processos/show.blade.php`, volte a chamada do componente para: `<x-alerta-devolucao :devolucao="$ultimaDevolucao" />`.
   - Em `resources/views/components/alerta-devolucao.blade.php`, remova o `@props`, remova o bloco `@if(!$jaRecebido && $processoId)` que renderiza o botão "Estou Ciente / Receber", e exclua o script `function receberDevolucao()` do final do arquivo.

4. **Reverter Timeline (`resources/views/processos/abas/partials/timeline.blade.php`):**
   - No `@if(in_array($acao...`, remova as opções `'Aba 1 Alterada', 'Aba 2 Alterada', 'Aba 3 Alterada'`.
   - Remova a variável `$isAlteracao` e os estilos CSS atrelados a ela que pintam o header de laranja.
   - Remova todo o bloco `@elseif($acao === 'Recebido')` que renderiza a pílula de recebimento no meio da timeline.
