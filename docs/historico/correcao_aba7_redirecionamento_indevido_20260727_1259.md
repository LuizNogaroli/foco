# Correção do Redirecionamento Indevido e Validação na Aba 7

**Arquivo Modificado:** `resources/views/processos/abas/aba7.blade.php`

## Motivo da Alteração
O usuário reportou que ao clicar em "Salvar e Enviar" na Aba 7 (Validação - Chefia), o sistema não mudava o status e direcionava automaticamente para a Aba 1.
Esse comportamento era causado por duas falhas simultâneas:
1. **Perda de Payload no HTMX:** Em formulários HTMX com múltiplos botões de submit, o valor do botão clicado (no caso, `name="acao_aba7" value="chefia"`) frequentemente não é serializado corretamente para o servidor caso não seja o formulário nativo. Isso fazia o backend receber a ação como nula, pulando as lógicas de alteração de status.
2. **Redirecionamento Silencioso (Validation Error):** Quando o usuário tentava enviar sem selecionar a opção "Suficiente / Insuficiente" (que não possuía validação obrigatória no HTML frontend), o backend detectava o erro e retornava a função nativa do Laravel `back()->withErrors()`. No contexto de páginas chamadas via HTMX, o `back()` redireciona para a URL do navegador atual (que por padrão abre a Aba 1). O resultado é que o usuário era jogado na Aba 1 sem ver o motivo da recusa.

**Solução:**
- Foram introduzidos campos ocultos (`<input type="hidden">`) populados via evento `onclick` dos botões. Isso garante de forma absoluta que a intenção da ação (`acao_aba7` ou `acao_aba7_rascunho`) chegue ao servidor.
- Foi implementada uma rotina Javascript nativa atrelada ao evento `submit` que verifica de forma dinâmica todos os grupos de *radio buttons* presentes na seção ativa. Caso falte alguma opção obrigatória, ele emite um `alert()` amigável na tela e cancela o evento, impedindo que a requisição chegue ao servidor e dispare o redirecionamento indevido.

## 1. Estado Anterior (Antes)
```html
                        @if(!isset($dados[$s['assinatura'] . '_nome']))
                        <div class="decl-btn-assinar" style="justify-content: flex-end;">
                            @if($perfil === 'ALL' || $perfil === $s['perfil'])
                                <button type="submit" name="acao_aba7_rascunho" value="{{ $chave }}" class="btn-inst btn-inst-outline" style="padding: 8px 22px; font-weight: 600;">💾 Salvar Rascunho</button>
                                <button type="submit" name="acao_aba7" value="{{ $chave }}" class="btn-assinar" style="padding: 8px 22px; font-weight: 600;">📤 Salvar e Enviar</button>
                            @endif
                        </div>
                        @endif
```
O Javascript no fim do arquivo apenas validava `form.checkValidity()`.

## 2. Estado Novo (Depois)
```html
                        @if(!isset($dados[$s['assinatura'] . '_nome']))
                        <div class="decl-btn-assinar" style="justify-content: flex-end;">
                            @if($perfil === 'ALL' || $perfil === $s['perfil'])
                                <button type="submit" class="btn-inst btn-inst-outline" style="padding: 8px 22px; font-weight: 600;" onclick="document.getElementById('hidden_acao_aba7_rascunho').value='{{ $chave }}'; document.getElementById('hidden_acao_aba7').value='';">💾 Salvar Rascunho</button>
                                <button type="submit" class="btn-assinar" style="padding: 8px 22px; font-weight: 600;" onclick="document.getElementById('hidden_acao_aba7').value='{{ $chave }}'; document.getElementById('hidden_acao_aba7_rascunho').value='';">📤 Salvar e Enviar</button>
                            @endif
                        </div>
                        @endif
```
Com adição dos campos hidden na base do form e um script JS de validação de `radioGroups`.

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/aba7.blade.php`.
2. Delete os `<input type="hidden">` adicionados no final do `#form07`.
3. Substitua o `onclick` dos botões pelos antigos atributos `name="acao_aba7" value="{{ $chave }}"`.
4. Restaure o script no fim do arquivo para a sua estrutura simples anterior (removendo a verificação customizada).
