# Histórico de Alterações - Menu Adicionar Imóvel/Área (Aba 1) - 03/08/2026

Este documento registra a alteração realizada para transformar os dois botões de inserção de imóvel/área em um menu interativo e as instruções para reversão, se necessário.

## 1. Estado Anterior (Antes)

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

---

## 2. Estado Novo (Depois)

### Em [aba1.blade.php](file:///C:/dev/Foco-19/resources/views/processos/abas/aba1.blade.php):
```html
                    <!-- Botão Menu Adicionar Imóvel/Área -->
                    <div style="display: flex; flex-direction: column; align-items: center; margin: 15px 0;" class="editavel">
                        <button type="button" id="btnMenuAdicionarImovel" class="btn-action btn-inst btn-inst-primary" style="margin-bottom: 10px; padding: 12px 24px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Adicionar Imóvel/Área
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 5px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        
                        <!-- Opções do Menu (Ocultas por padrão) -->
                        <div id="menuOpcoesAdicionarImovel" style="display: none; flex-direction: column; gap: 10px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); width: 100%; max-width: 380px;">
                            <button type="button" id="btnAdicionarImovelAreaComRip" class="btn-action btn-inst btn-inst-primary" style="width: 100%; justify-content: center;">
                                Adicionar Imóvel/Área com RIP
                            </button>
                            <button type="button" id="btnAdicionarImovelAreaSemRip" class="btn-action btn-inst btn-inst-outline" style="width: 100%; justify-content: center;">
                                Adicionar Imóvel/Área sem RIP
                            </button>
                        </div>
                    </div>
```

---

## 3. Plano de Rollback / Desfazer

Caso queira reverter a mudança e restaurar o estado original:
1. Acesse o arquivo `resources/views/processos/abas/aba1.blade.php`.
2. Substitua o bloco que contém o `btnMenuAdicionarImovel` pelo bloco de botões em linha (estado anterior documentado neste arquivo).
3. Acesse o arquivo `public/js/foco-01.js` e desfaça os listeners correspondentes a `btnMenuAdicionarImovel`.
