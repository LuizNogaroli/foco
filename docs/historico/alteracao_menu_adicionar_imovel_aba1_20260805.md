# Alteração do Menu Adicionar Imóvel na Aba 1 - 05/08/2026

## Descrição da Mudança
O menu dropdown do botão "Adicionar Imóvel/Área" na Aba 1 foi unificado, passando a exibir todas as 7 opções de conceituação. Todas as opções foram ajustadas para utilizar o parâmetro `com_rip` na função `selecionarConceituacaoBotao`, direcionando para o modal "Inserir RIP". O botão "Adicionar Imóvel/Área sem RIP" foi removido.

## Estado Anterior (Antes)
```html
                        <div class="dropdown-hover">
                            <button type="button" class="btn-action btn-inst btn-inst-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Adicionar Imóvel/Área com RIP
                            </button>
                            <div class="dropdown-hover-content">
                                <div class="dropdown-hover-label">Selecione a conceituação do imóvel:</div>
                                <button type="button" onclick="selecionarConceituacaoBotao('Terreno/acrescido de marinha', 'com_rip')">Terreno/acrescido de marinha</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Terreno/acrescido marginal', 'com_rip')">Terreno/acrescido marginal</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Nacional interior', 'com_rip')">Nacional interior</button>
                            </div>
                        </div>

                        <div class="dropdown-hover">
                            <button type="button" class="btn-action btn-inst btn-inst-outline">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Adicionar Imóvel/Área sem RIP
                            </button>
                            <div class="dropdown-hover-content">
                                <div class="dropdown-hover-label">Selecione a conceituação do imóvel:</div>
                                <button type="button" onclick="selecionarConceituacaoBotao('Espelho d\'água', 'sem_rip')">Espelho d'água</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Cavidades naturais subterrâneas', 'sem_rip')">Cavidades naturais subterrâneas</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Manguezal', 'sem_rip')">Manguezal</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Praias', 'sem_rip')">Praias</button>
                            </div>
                        </div>
                    </div>
```

## Estado Novo (Depois)
```html
                        <div class="dropdown-hover">
                            <button type="button" class="btn-action btn-inst btn-inst-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Adicionar Imóvel/Área
                            </button>
                            <div class="dropdown-hover-content">
                                <div class="dropdown-hover-label">Selecione a conceituação do imóvel:</div>
                                <button type="button" onclick="selecionarConceituacaoBotao('Terreno/acrescido de marinha', 'com_rip')">Terreno/acrescido de marinha</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Terreno/acrescido marginal', 'com_rip')">Terreno/acrescido marginal</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Nacional interior', 'com_rip')">Nacional interior</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Espelho d\'água', 'com_rip')">Espelho d'água</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Cavidades naturais subterrâneas', 'com_rip')">Cavidades naturais subterrâneas</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Manguezal', 'com_rip')">Manguezal</button>
                                <button type="button" onclick="selecionarConceituacaoBotao('Praias', 'com_rip')">Praias</button>
                            </div>
                        </div>
                    </div>
```

## Plano de Rollback / Desfazer
1. Abrir o arquivo `resources/views/processos/abas/aba1.blade.php`.
2. Substituir o bloco atual do menu dropdown pelo bloco salvo na seção "Estado Anterior".
3. Salvar o arquivo.
