# Remoção de Listener JS Obsoleto de Salvamento na Aba 3

**Arquivo Modificado:** `resources/views/processos/abas/aba3.blade.php`

## Motivo da Alteração
O usuário reportou que ao clicar no botão "Salvar e Enviar", o texto do botão mudava para "⏳ Salvando..." e o sistema travava indefinidamente, não realizando o envio.
Ao analisar o arquivo, localizamos um listener de evento `submit` residual de uma versão antiga da página. Esse listener interceptava a submissão, alterava o texto do botão e tentava aguardar a execução de uma função inexistente (`executarSalvamentoAba3()`), a qual havia sido removida em iterações anteriores de migração para o HTMX.
Ao tentar invocar a função não declarada, o JavaScript disparava uma exceção que parava a execução do script instantaneamente, deixando a interface travada e impedindo que o fluxo normal de envio via HTMX ocorresse.

A solução foi remover completamente o bloco desse listener obsoleto, passando a responsabilidade total de submissão do formulário para o HTMX.

## 1. Estado Anterior (Antes)
```javascript
      // =========================================================================
      // LÓGICA DE SALVAMENTO E MANIFESTAÇÃO (ABA 3)
      // =========================================================================

      const formReq3 =
        document.getElementById("form03") || document.querySelector("form");
      if (formReq3) {
        formReq3.addEventListener("submit", async (e) => {
          e.preventDefault();
          const btn = formReq3.querySelector('button[type="submit"]');
          const originalText = btn ? btn.innerHTML : '';
          if (btn) {
            btn.disabled = true;
            btn.innerHTML = "⏳ Salvando...";
          }
          const sucesso = await executarSalvamentoAba3();
          if (sucesso) {
            formReq3.submit();
          } else {
            if (btn) {
              btn.disabled = false;
              btn.innerHTML = originalText;
            }
          }
        });
      }
```

## 2. Estado Novo (Depois)
```javascript
      // Listener obsoleto de salvamento removido para permitir submissão HTMX nativa
```

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/aba3.blade.php`.
2. Navegue até o final do arquivo, logo antes da tag de fechamento `</script></body></html>`.
3. Restaure o bloco Javascript citado no "Estado Anterior".
