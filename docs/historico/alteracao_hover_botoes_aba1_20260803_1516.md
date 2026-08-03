# Histórico de Alterações - Botões de Indicação com Hover Menu (Aba 1) - 03/08/2026

Este documento registra a alteração realizada para corrigir a lógica dos botões de indicação do imóvel. O usuário solicitou que retornassem os 2 botões separados ("Com RIP" e "Sem RIP"), mas que cada um possuísse um menu dropdown acionado via *hover* contendo as respectivas opções de conceituação.

## 1. Estado Anterior (Antes)

O estado anterior tinha um botão principal que atuava como toggle ("Adicionar Imóvel/Área"), que abria as opções genéricas "Com RIP" e "Sem RIP". (Ver arquivo de histórico `alteracao_menu_adicionar_imovel_20260803_1511.md`). 

E no arquivo `public/js/foco-01.js` havia lógica complexa de toggle via Javascript.

---

## 2. Estado Novo (Depois)

### Em [aba1.blade.php](file:///C:/dev/Foco-19/resources/views/processos/abas/aba1.blade.php):
- Adicionada classe `.dropdown-hover` e `.dropdown-hover-content` com CSS via bloco `<style>`.
- Recriados os botões separados:
```html
                    <div style="display: flex; justify-content: center; gap: 15px; margin: 15px 0;" class="editavel">
                        <div class="dropdown-hover">
                            <button type="button" class="btn-action btn-inst btn-inst-primary">
                                ... Adicionar Imóvel/Área com RIP
                            </button>
                            <div class="dropdown-hover-content">
                                <button type="button" onclick="selecionarConceituacaoBotao('Terreno/acrescido de marinha')">Terreno/acrescido de marinha</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Terreno/acrescido marginal')">Terreno/acrescido marginal</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Nacional interior')">Nacional interior</button>
                            </div>
                        </div>

                        <div class="dropdown-hover">
                            <button type="button" class="btn-action btn-inst btn-inst-outline">
                                ... Adicionar Imóvel/Área sem RIP
                            </button>
                            <div class="dropdown-hover-content">
                                <button type="button" onclick="selecionarConceituacaoBotao('Espelho d\'água')">Espelho d'água</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Cavidades naturais subterrâneas')">Cavidades naturais subterrâneas</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Manguezal')">Manguezal</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Praias')">Praias</button>
                            </div>
                        </div>
                    </div>
```

### Em [foco-01.js](file:///C:/dev/Foco-19/public/js/foco-01.js):
- A lógica de toggle foi removida.
- Adicionada a função global para seleção:
```javascript
    window.selecionarConceituacaoBotao = function(valor) {
        if (!selectConceituacao) return;
        selectConceituacao.value = valor;
        
        const containerDropdown = document.getElementById('container_conceituacao_dropdown');
        if (containerDropdown) {
            containerDropdown.style.display = 'block';
        }
        
        atualizarLayoutConceituacao();
    };
```

---

## 3. Plano de Rollback / Desfazer

Caso queira reverter a mudança e restaurar o estado original:
1. Acesse o arquivo `resources/views/processos/abas/aba1.blade.php`.
2. Substitua o bloco atual (com `dropdown-hover`) pelos botões anteriores.
3. Acesse o arquivo `public/js/foco-01.js` e remova a função `selecionarConceituacaoBotao`. Reintroduza a lógica anterior caso necessário.
