# Correção do Botão de Devolução na Aba 3

**Arquivo Modificado:** `resources/views/processos/abas/aba3.blade.php`

## Motivo da Alteração
O usuário reportou que ao tentar devolver um processo para a aba "Indicação do Imóvel" utilizando o container "Devolver Processo" na Aba 3, o botão não respondia e nada acontecia.
Isso ocorria por dois motivos:
1. O `<textarea>` da justificativa não possuía o atributo `name="motivo_devolucao"`, impedindo que o HTMX enviasse o valor no POST, sendo necessário para o backend.
2. A inclusão do CSRF token estava sendo feita via seletor global (`input[name='_token']`), o que em certas estruturas DOM do HTMX fora do `<form>` principal pode falhar e resultar em erro de autorização 419 Page Expired, impedindo o redirecionamento.

A correção adicionou o atributo `name="motivo_devolucao"` no textarea e substituiu a forma de envio do CSRF para envio explícito via `hx-headers`.

## 1. Estado Anterior (Antes)
```html
              <label for="motivo_devolucao_rapida" style="color: #9f1239; font-weight: bold; font-size: 0.9em; display: block; margin-bottom: 5px;">Motivo (Obrigatório):</label>
              <textarea id="motivo_devolucao_rapida" placeholder="Justifique a devolução..." style="width: 100%; min-height: 80px; padding: 8px; border: 1px solid #fecdd3; border-radius: 4px; margin-bottom: 15px; font-family: inherit; box-sizing: border-box;"></textarea>

              <p style="margin-top: 0; margin-bottom: 10px; color: #9f1239; font-weight: bold;">Para qual fase o processo deve retornar?</p>
              <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <button type="button" 
                        hx-post="{{ route('processos.devolver', $processo->id) }}" 
                        hx-vals='{"aba": 1}' 
                        hx-include="#motivo_devolucao_rapida, input[name='_token']" 
                        hx-target="#aba3-container"
                        hx-indicator="#form-indicator-aba3"
                        class="btnEnviarDevolucaoRapida" 
                        style="flex: 1; min-width: 200px; background-color: #be123c; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s; box-shadow: 0 2px 4px rgba(190, 18, 60, 0.2);">
                    🔙 Indicação do Imóvel
                </button>
                <!-- O botão Diagnóstico Preliminar também seguia o mesmo padrão anterior -->
```

## 2. Estado Novo (Depois)
```html
              <label for="motivo_devolucao_rapida" style="color: #9f1239; font-weight: bold; font-size: 0.9em; display: block; margin-bottom: 5px;">Motivo (Obrigatório):</label>
              <textarea id="motivo_devolucao_rapida" name="motivo_devolucao" placeholder="Justifique a devolução..." style="width: 100%; min-height: 80px; padding: 8px; border: 1px solid #fecdd3; border-radius: 4px; margin-bottom: 15px; font-family: inherit; box-sizing: border-box;" required></textarea>

              <p style="margin-top: 0; margin-bottom: 10px; color: #9f1239; font-weight: bold;">Para qual fase o processo deve retornar?</p>
              <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <button type="button" 
                        hx-post="{{ route('processos.devolver', $processo->id) }}" 
                        hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'
                        hx-vals='{"aba": 1}' 
                        hx-include="#motivo_devolucao_rapida" 
                        hx-target="#aba3-container"
                        hx-indicator="#form-indicator-aba3"
                        class="btnEnviarDevolucaoRapida" 
                        style="flex: 1; min-width: 200px; background-color: #be123c; color: white; border: none; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s; box-shadow: 0 2px 4px rgba(190, 18, 60, 0.2);">
                    🔙 Indicação do Imóvel
                </button>
                <!-- O botão Diagnóstico Preliminar também foi corrigido com hx-headers -->
```

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/aba3.blade.php`.
2. Localize a tag `<textarea id="motivo_devolucao_rapida"`.
3. Remova os atributos `name="motivo_devolucao"` e `required`.
4. Nos botões de devolução, remova o atributo `hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'`.
5. Nos atributos `hx-include` dos botões, adicione novamente `, input[name='_token']` ao lado de `#motivo_devolucao_rapida`.
