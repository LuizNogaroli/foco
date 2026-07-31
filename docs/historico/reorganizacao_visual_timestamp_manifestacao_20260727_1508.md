# Reorganização Visual do Timestamp nas Manifestações da Timeline

**Arquivo Modificado:** `resources/views/processos/abas/partials/timeline.blade.php`

## Motivo da Alteração
O usuário solicitou que o carimbo de data/hora (*timestamp*) e o nome do responsável fossem removidos da barra de título do acordeão e movidos para o corpo do cartão da manifestação, visando despoluir a barra do cabeçalho.

**Ajuste Realizado:**
- O título da barra verde-escuro do cabeçalho agora exibe estritamente o rótulo limpo: `📝 Manifestação - [Nome do Perfil]`.
- O carimbo de registro completo contendo o ícone de usuário, o nome do responsável e o ícone de relógio com data/hora foi posicionado no topo do corpo do cartão (`.acordeao-corpo`):
  `Registro: 👤 [Nome do Usuário] — 🕒 [Data/Hora]`

## 1. Estado Anterior (Antes)
```html
<div class="acordeao-titulo" style="color: #fff;">
    📝 Manifestação - {{ $etapaOrigem }} ({{ $dataAcao }})
</div>
<span style="font-size: 0.85em; color:#f0fdfa; margin-right:15px;">👤 {{ $dadosSnapshot['assinatura_'.$prefix.'_nome'] }}</span>
```

## 2. Estado Novo (Depois)
```html
<div class="acordeao-titulo" style="color: #fff;">
    📝 Manifestação - {{ $etapaOrigem }}
</div>
<span class="acordeao-seta" style="color: #fff;">▶</span>
</div>
<div class="acordeao-corpo" style="padding: 15px; background: #f0fdfa;">
<div style="margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #99f6e4; color: #0f766e; font-size: 0.9em;">
    <strong>Registro:</strong> 👤 {{ $dadosSnapshot['assinatura_'.$prefix.'_nome'] }} &mdash; 🕒 {{ $dadosSnapshot['assinatura_'.$prefix.'_data'] ?? $dataAcao }}
</div>
```

## 3. Plano de Rollback / Desfazer
1. Abra `resources/views/processos/abas/partials/timeline.blade.php`.
2. Mova a linha `<strong>Registro:</strong> ...` do corpo do acordeão de volta para a tag `<div class="acordeao-titulo">`.
