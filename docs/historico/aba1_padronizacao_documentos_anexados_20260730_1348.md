# Padronização do Container de Documentos Anexados (Aba 1)

## 1. Estado Anterior (Antes)
O container "Documentos anexados ao requerimento" utilizava as tags nativas HTML `<details>` e `<summary>`, o que o impedia de seguir o comportamento animado, os ícones (▶) e o padrão do sistema orquestrado pela função `toggleAccordion`:
```html
                    <section class="documentos-linkados-section" aria-labelledby="titulo-documentos-linkados" style="margin-top: 25px;">
                        <details class="documentos-expansivel">
                            <summary class="documentos-expansivel-header" style="...">
                                <span id="titulo-documentos-linkados" style="...">
                                    <svg>...</svg>
                                    Documentos anexados ao requerimento
                                </span>
                            </summary>
                            <div class="documentos-linkados-card" style="...">
                                <!-- Tabela de documentos -->
                            </div>
                        </details>
                    </section>
```

## 2. Estado Novo (Depois)
O bloco foi reescrito para utilizar as classes `accordion-container`, `accordion-item`, `accordion-header` e `accordion-body collapsed`, juntamente com a função global `toggleAccordion(this)` e o ícone de seta customizado `▶`. Isso padroniza a interface e faz com que essa seção herde as correções de rotação aplicadas anteriormente.
```html
                    <section class="documentos-linkados-section accordion-container" aria-labelledby="titulo-documentos-linkados" style="margin-top: 25px; margin-bottom: 0;">
                        <div class="accordion-item" style="border: none;">
                            <div class="accordion-header" style="background-color: #1e3a5f; color: white; border-radius: 8px;" onclick="toggleAccordion(this)">
                                <span class="accordion-title" id="titulo-documentos-linkados" style="font-weight: 600; color: #ffffff;">
                                    <svg>...</svg>
                                    Documentos anexados ao requerimento
                                </span>
                                <span class="accordion-icon">▶</span>
                            </div>
                            <div class="accordion-body collapsed" style="display: none; padding: 15px; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; background: #ffffff;">
                                <!-- Tabela de documentos -->
                            </div>
                        </div>
                    </section>
```

## 3. Plano de Rollback / Desfazer
Para reverter:
1. Abra o arquivo `resources/views/processos/abas/aba1.blade.php`.
2. Localize a classe `documentos-linkados-section`.
3. Restaure a estrutura para `<details>` e `<summary>`, removendo o `onclick="toggleAccordion(this)"` e a `span` da seta (`▶`).
4. Salve o arquivo.
