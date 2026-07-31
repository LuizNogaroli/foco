# Correção do Rótulo Dinâmico do Perfil de Manifestação na Timeline

**Arquivo Modificado:** `resources/views/processos/abas/partials/timeline.blade.php`

## Motivo da Alteração
O usuário reportou que no histórico de movimentações, todas as manifestações estavam aparecendo incorretamente com o rótulo fixa `"Manifestação - Chefia"`, independentemente de qual perfil tinha se manifestado (por exemplo, Coordenação, Superintendência, Direção, etc.).

**Causa Raiz:**
Ao iterar os trâmites antigos ou snapshots acumulados, o código anterior fazia um loop pela lista `$prefixMap` na ordem original (`Chefia`, `Coordenação`, `Superintendência`...). Como os dados do snapshot são acumulativos (`array_merge`), o snapshot de uma manifestação feita pela Coordenação continha tanto a `assinatura_chefia_nome` quanto a `assinatura_coordenacao_nome`. O loop encontrava a Chefia primeiro e dava `break`, forçando a alteração do rótulo para "Chefia".

**Solução:**
Refatorou-se a lógica de resolução do prefixo da manifestação na `timeline.blade.php`:
1. Verifica se `$etapaOrigem` já possui o nome exato da etapa (solução nativa para novos trâmites).
2. Se o trâmite for antigo/genérico, cruza a data do trâmite (`$tramite->created_at`) com a data da assinatura (`assinatura_PREFIX_data`).
3. Se não houver correspondência direta por data, percorre o `$prefixMap` na **ordem hierárquica reversa** (`CDE` -> `Direção` -> `Coordenação-Geral` -> `Equipe C.G.` -> `Superintendência` -> `Coordenação` -> `Chefia`), selecionando a assinatura mais recente adicionada àquela etapa.

## 1. Estado Anterior (Antes)
```php
                    if (!$prefix) {
                        foreach ($prefixMap as $label => $pref) {
                            if (isset($dadosSnapshot['assinatura_' . $pref . '_nome'])) {
                                $prefix = $pref;
                                $etapaOrigem = $label;
                                break;
                            }
                        }
                    }
```

## 2. Estado Novo (Depois)
```php
                    if (!$prefix || !isset($dadosSnapshot['assinatura_' . $prefix . '_nome'])) {
                        $tramiteDataStr = \Carbon\Carbon::parse($tramite->created_at)->format('d/m/Y');
                        
                        foreach ($prefixMap as $label => $pref) {
                            $sigData = $dadosSnapshot['assinatura_' . $pref . '_data'] ?? '';
                            if ($sigData && str_contains($sigData, $tramiteDataStr)) {
                                $prefix = $pref;
                                $etapaOrigem = $label;
                            }
                        }

                        if (!$prefix || !isset($dadosSnapshot['assinatura_' . $prefix . '_nome'])) {
                            $reversedMap = array_reverse($prefixMap, true);
                            foreach ($reversedMap as $label => $pref) {
                                if (isset($dadosSnapshot['assinatura_' . $pref . '_nome'])) {
                                    $prefix = $pref;
                                    $etapaOrigem = $label;
                                    break;
                                }
                            }
                        }
                    }
```

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/partials/timeline.blade.php`.
2. Localize as linhas 280 a 305.
3. Substitua o algoritmo de resolução pelo loop simples original.
