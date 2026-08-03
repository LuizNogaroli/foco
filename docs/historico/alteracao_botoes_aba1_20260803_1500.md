# Histórico de Alterações - Divisão do Botão Adicionar Imóvel/Área (Aba 1) - 03/08/2026

Este documento registra a alteração realizada para dividir o botão único da Aba 1 em dois botões dedicados e as instruções para reversão, se necessário.

## 1. Estado Anterior (Antes)

### Em [aba1.blade.php](file:///C:/dev/Foco-19/resources/views/processos/abas/aba1.blade.php):
```html
                    <!-- Botão Adicionar Imóvel/Área -->
                    <div style="display: flex; justify-content: center; margin: 15px 0;" class="editavel">
                        <button type="button" id="btnAdicionarImovelArea" class="btn-action btn-inst btn-inst-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Adicionar Imóvel/Área
                        </button>
                    </div>
```

### Em [foco-01.js](file:///C:/dev/Foco-19/public/js/foco-01.js):
```javascript
    // Toggle para o botão Adicionar Imóvel/Área
    const btnAdicionarImovel = document.getElementById('btnAdicionarImovelArea');
    if (btnAdicionarImovel) {
        btnAdicionarImovel.addEventListener('click', () => {
            const containerDropdown = document.getElementById('container_conceituacao_dropdown');
            if (containerDropdown) {
                const vaiAbrir = containerDropdown.style.display === 'none';
                containerDropdown.style.display = vaiAbrir ? 'block' : 'none';
                atualizarLayoutConceituacao();
            }
        });
    }
```

---

## 2. Estado Novo (Depois)

### Em [aba1.blade.php](file:///C:/dev/Foco-19/resources/views/processos/abas/aba1.blade.php):
```html
                    <!-- Botão Adicionar Imóvel/Área -->
                    <div style="display: flex; justify-content: center; gap: 15px; margin: 15px 0;" class="editavel">
                        <button type="button" id="btnAdicionarImovelAreaComRip" class="btn-action btn-inst btn-inst-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Adicionar Imóvel/Área com RIP
                        </button>
                        <button type="button" id="btnAdicionarImovelAreaSemRip" class="btn-action btn-inst btn-inst-outline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Adicionar Imóvel/Área sem RIP
                        </button>
                    </div>
```

### Em [foco-01.js](file:///C:/dev/Foco-19/public/js/foco-01.js):
```javascript
    // Toggle para os botões Adicionar Imóvel/Área com/sem RIP e filtragem de opções
    const btnAdicionarImovelComRip = document.getElementById('btnAdicionarImovelAreaComRip');
    const btnAdicionarImovelSemRip = document.getElementById('btnAdicionarImovelAreaSemRip');
    let tipoConceituacaoAtivo = null;

    function filtrarConceituacao(tipo) {
        if (!selectConceituacao) return;
        selectConceituacao.value = "";
        const exigeRip = ["Terreno/acrescido de marinha", "Terreno/acrescido marginal", "Nacional interior"];
        const exigeCadastro = ["Espelho d'água", "Cavidades naturais subterrâneas", "Manguezal", "Praias"];
        const options = selectConceituacao.querySelectorAll('option');
        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = "";
                return;
            }
            if (tipo === 'com_rip') {
                opt.style.display = exigeRip.includes(opt.value) ? "" : "none";
            } else if (tipo === 'sem_rip') {
                opt.style.display = exigeCadastro.includes(opt.value) ? "" : "none";
            } else {
                opt.style.display = "";
            }
        });
    }

    if (btnAdicionarImovelComRip) {
        btnAdicionarImovelComRip.addEventListener('click', () => {
            const containerDropdown = document.getElementById('container_conceituacao_dropdown');
            if (containerDropdown) {
                if (containerDropdown.style.display === 'block' && tipoConceituacaoAtivo === 'com_rip') {
                    containerDropdown.style.display = 'none';
                    tipoConceituacaoAtivo = null;
                } else {
                    filtrarConceituacao('com_rip');
                    containerDropdown.style.display = 'block';
                    tipoConceituacaoAtivo = 'com_rip';
                }
                atualizarLayoutConceituacao();
            }
        });
    }

    if (btnAdicionarImovelSemRip) {
        btnAdicionarImovelSemRip.addEventListener('click', () => {
            const containerDropdown = document.getElementById('container_conceituacao_dropdown');
            if (containerDropdown) {
                if (containerDropdown.style.display === 'block' && tipoConceituacaoAtivo === 'sem_rip') {
                    containerDropdown.style.display = 'none';
                    tipoConceituacaoAtivo = null;
                } else {
                    filtrarConceituacao('sem_rip');
                    containerDropdown.style.display = 'block';
                    tipoConceituacaoAtivo = 'sem_rip';
                }
                atualizarLayoutConceituacao();
            }
        });
    }
```

---

## 3. Plano de Rollback / Desfazer

Caso queira reverter a mudança e restaurar o estado original:

1. Acesse o arquivo `resources/views/processos/abas/aba1.blade.php`.
2. Localize o trecho contendo os botões `btnAdicionarImovelAreaComRip` e `btnAdicionarImovelAreaSemRip` e substitua pelo bloco original com o botão único `btnAdicionarImovelArea`.
3. Acesse o arquivo `public/js/foco-01.js`.
4. Localize o bloco de código contendo `btnAdicionarImovelComRip`, `btnAdicionarImovelSemRip` e a função `filtrarConceituacao`.
5. Substitua pelo listener original de clique do `btnAdicionarImovel`.
