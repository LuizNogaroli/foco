# Desbloqueio do Campo de Justificativa de Devolução na Aba 3

**Arquivo Modificado:** `resources/views/processos/abas/aba3.blade.php`

## Motivo da Alteração
O usuário relatou que a caixa de texto (textarea) utilizada para justificar a devolução na Aba 3 estava desabilitada, impedindo o preenchimento. O script global de sincronização e controle de campos (`public/js/sync.js`) bloqueia automaticamente (transforma em `readonly` ou `disabled`) todos os campos de formulário da página que **não** estejam inseridos dentro de um container que possua a classe CSS `.editavel`. Como a caixa de texto da justificativa estava no container `.accordion-body-dev` e faltava a classe `.editavel`, ela estava sendo bloqueada pelo sistema na inicialização.

A correção consistiu em apenas adicionar a classe `editavel` na `div` que envolve o campo.

## 1. Estado Anterior (Antes)
```html
            <div class="accordion-body-dev">
              <label for="motivo_devolucao_rapida" style="color: #9f1239; font-weight: bold; font-size: 0.9em; display: block; margin-bottom: 5px;">Motivo (Obrigatório):</label>
              <textarea id="motivo_devolucao_rapida" name="motivo_devolucao" placeholder="Justifique a devolução..." style="width: 100%; min-height: 80px; padding: 8px; border: 1px solid #fecdd3; border-radius: 4px; margin-bottom: 15px; font-family: inherit; box-sizing: border-box;" required></textarea>
```

## 2. Estado Novo (Depois)
```html
            <div class="accordion-body-dev editavel">
              <label for="motivo_devolucao_rapida" style="color: #9f1239; font-weight: bold; font-size: 0.9em; display: block; margin-bottom: 5px;">Motivo (Obrigatório):</label>
              <textarea id="motivo_devolucao_rapida" name="motivo_devolucao" placeholder="Justifique a devolução..." style="width: 100%; min-height: 80px; padding: 8px; border: 1px solid #fecdd3; border-radius: 4px; margin-bottom: 15px; font-family: inherit; box-sizing: border-box;" required></textarea>
```

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/aba3.blade.php`.
2. Localize a tag `<div class="accordion-body-dev editavel">`.
3. Remova a classe `editavel` deixando apenas `<div class="accordion-body-dev">`.
4. Salve e recarregue a página, e o script de bloqueio voltará a impedir edições naquele campo.
