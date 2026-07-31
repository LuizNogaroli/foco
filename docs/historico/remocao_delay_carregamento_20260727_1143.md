# Remoção de Delay de Carregamento Simulado

**Arquivo Modificado:** `public/js/sync.js`

## Motivo da Alteração
O sistema estava apresentando um comportamento onde a página era carregada, um modal escrito "Carregando dados..." aparecia (simulando um carregamento por 1.2 segundos), e depois voltava para a página com os dados preenchidos. Esse temporizador simulado não era mais necessário, portanto foi removido para que os dados sejam preenchidos instantaneamente sem forçar a espera do usuário.

## 1. Estado Anterior (Antes)
```javascript
        // Remove o loader após simular 1.2 segundos de busca de dados
        console.log("🚩 [sync.js] Iniciando temporizador de 1.2 segundos...");
        setTimeout(() => {
            console.log("🚩 [sync.js] Temporizador esgotado. Populando formulário e ocultando overlay.");
            isSimulatedLoadFinished = true;
            
            // Fallback robusto: se o db.js recriou o objeto e perdemos o postMessage por milissegundos
            if (window.parent && window.parent.formDataState && Object.keys(window.parent.formDataState).length > 0) {
                dbState = window.parent.formDataState;
            }
            
            populateForm(dbState);

            loader.style.opacity = '0';
            setTimeout(() => {
                if (loader.parentNode) {
                    loader.remove();
                    console.log("🚩 [sync.js] Overlay removido do DOM.");
                }
            }, 400);
        }, 1200);
```

## 2. Estado Novo (Depois)
```javascript
        // Executa imediatamente o preenchimento de dados e remove o overlay
        console.log("🚩 [sync.js] Populando formulário e ocultando overlay imediatamente.");
        isSimulatedLoadFinished = true;
        
        // Fallback robusto: se o db.js recriou o objeto e perdemos o postMessage por milissegundos
        if (window.parent && window.parent.formDataState && Object.keys(window.parent.formDataState).length > 0) {
            dbState = window.parent.formDataState;
        }
        
        populateForm(dbState);

        loader.style.opacity = '0';
        setTimeout(() => {
            if (loader.parentNode) {
                loader.remove();
                console.log("🚩 [sync.js] Overlay removido do DOM.");
            }
        }, 400);
```

## 3. Plano de Rollback / Desfazer
Para reverter a mudança e voltar ao comportamento anterior (com o delay):
1. Abra o arquivo `public/js/sync.js`.
2. Localize a linha que inicia com `// Executa imediatamente o preenchimento de dados e remove o overlay` (próximo à linha 443).
3. Substitua todo o bloco de código que popula o formulário imediatamente pelo bloco descrito em **Estado Anterior (Antes)**, que envolve a lógica dentro de um `setTimeout(..., 1200)`.
4. Salve o arquivo e atualize a página no navegador.
