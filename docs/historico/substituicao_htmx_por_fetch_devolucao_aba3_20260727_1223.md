# Substituição de HTMX por Fetch JS na Devolução da Aba 3

**Arquivo Modificado:** `resources/views/processos/abas/aba3.blade.php`

## Motivo da Alteração
Após adicionar o atributo `formnovalidate`, o botão de devolução da Aba 3 permaneceu sem funcionar em certos contextos do HTMX 1.9, que pode interceptar e bloquear requisições caso a estrutura do formulário onde o botão está contido (`#form03`) possua conflitos de validação interna ou se a biblioteca HTMX não conseguir fazer o bypass em botões do tipo `button`.
Como essa requisição não deve estar atrelada ao salvamento do formulário principal da aba, a solução mais robusta e à prova de falhas foi substituir os atributos do HTMX (`hx-post`, `hx-include`, etc.) por uma função nativa JavaScript (`fetch`).
Isso garante:
1. Bypass total das validações HTML5 do HTMX do `#form03`.
2. Feedback visual via `alert()` caso a justificativa esteja vazia.
3. Feedback visual no botão (`⏳ Devolvendo...`) para que o usuário saiba que o clique funcionou.
4. Redirecionamento automático e confiável para o Painel de Requerimentos.

## 1. Estado Anterior (Antes)
```html
                <button type="button" formnovalidate
                        hx-post="{{ route('processos.devolver', $processo->id) }}" 
                        hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'
                        hx-vals='{"aba": 1}' 
                        hx-include="#motivo_devolucao_rapida" 
                        hx-target="#aba3-container"
                        hx-indicator="#form-indicator-aba3"
                        class="btnEnviarDevolucaoRapida" 
                        style="...">
                    🔙 Indicação do Imóvel
                </button>
```

## 2. Estado Novo (Depois)
```html
                <button type="button" 
                        onclick="enviarDevolucao(1)"
                        class="btnEnviarDevolucaoRapida" 
                        style="...">
                    🔙 Indicação do Imóvel
                </button>
                
                ... (script `enviarDevolucao` adicionado abaixo)
```

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/aba3.blade.php`.
2. Localize os botões de devolução (`btnEnviarDevolucaoRapida`).
3. Remova os `onclick="enviarDevolucao(1)"` e `onclick="enviarDevolucao(2)"`.
4. Restaure os atributos `hx-post`, `hx-headers`, `hx-vals`, `hx-include`, `hx-target`, e `hx-indicator` originais.
5. Remova a tag `<script>` contendo a função `enviarDevolucao(aba)` inserida no final do container.
