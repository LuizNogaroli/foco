# Histórico de Alterações - Abertura Automática do Modal Inserir RIP - 03/08/2026

Este documento registra a implementação da abertura automática do modal de inserção de RIP/Cadastro Mínimo ao selecionar uma conceituação no menu dropdown (Ponto 1.2).

## 1. Problema Identificado
- Após clicar em uma das opções no menu suspenso ("Com RIP" ou "Sem RIP"), o usuário ainda precisava clicar em um segundo botão ("Inserir RIP" ou "Inserir Cadastro Mínimo") que aparecia na tela para abrir a janela modal de preenchimento, adicionando um clique desnecessário.
- O botão do cadastro mínimo ainda estava com a nomenclatura antiga ("Inserir Cadastro Mínimo").

## 2. Alterações Realizadas

### Em [foco-01.js](file:///C:/dev/Foco-19/public/js/foco-01.js):
- A função `selecionarConceituacaoBotao(valor)` foi estendida para receber um segundo parâmetro `tipoBotao` (`'com_rip'` ou `'sem_rip'`).
- Inserida lógica para disparar o clique nos botões respectivos automaticamente (auto-open).
```javascript
        if (tipoBotao === 'com_rip') {
            const btnRip = document.getElementById('btnInserirRip');
            if (btnRip) btnRip.click();
        } else if (tipoBotao === 'sem_rip') {
            const btnCadastro = document.getElementById('btnInserirCadastroMinimo');
            if (btnCadastro) btnCadastro.click();
        }
```

### Em [aba1.blade.php](file:///C:/dev/Foco-19/resources/views/processos/abas/aba1.blade.php):
- O parâmetro extra foi adicionado às chamadas `onclick` no menu suspenso. Exemplo: `onclick="selecionarConceituacaoBotao('Terreno/acrescido de marinha', 'com_rip')"`
- O texto do botão `#btnInserirCadastroMinimo` foi atualizado de "Inserir Cadastro Mínimo" para "Inserir dados do imóvel/área".

---

## 3. Plano de Rollback / Desfazer

Para reverter para a seleção manual (clique duplo):
1. Em `public/js/foco-01.js`, remova a lógica condicional do final da função `window.selecionarConceituacaoBotao` que aciona `.click()`.
2. Em `resources/views/processos/abas/aba1.blade.php`, não é estritamente necessário remover o segundo argumento das chamadas inline, mas a nomenclatura do botão "Inserir dados do imóvel/área" pode ser desfeita para "Inserir Cadastro Mínimo" se desejado.
