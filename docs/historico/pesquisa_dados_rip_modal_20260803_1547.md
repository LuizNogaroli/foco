# Histórico de Alterações - Pesquisa de Dados do Imóvel no Modal RIP - 03/08/2026

Este documento registra a melhoria realizada no modal de "Inserir RIP", implementando a função de pesquisa de dados cadastrais.

## 1. Problema Identificado
- O usuário inseria o RIP e não conseguia conferir antecipadamente os dados de endereço do imóvel para se certificar de que era o RIP correto antes de adicionar à lista.

## 2. Alterações Realizadas

### Em [aba1.blade.php](file:///C:/dev/Foco-19/resources/views/processos/abas/aba1.blade.php):
- A largura máxima do modal `#modalInserirRip` foi aumentada de 400px para 600px para acomodar mais informações visuais.
- Um botão de "Pesquisar" foi adicionado ao lado do campo de digitação do RIP usando `display: flex`.
- Uma seção oculta (`#dadosRipPesquisado`) foi criada logo abaixo do campo, contendo a estrutura para exibir Endereço, Bairro, CEP, Município e UF.

### Em [foco-01.js](file:///C:/dev/Foco-19/public/js/foco-01.js):
- A referência `const btnPesquisarRip` foi criada.
- Foi inserido o bloco `btnPesquisarRip.addEventListener('click', ...)` que chama a função de API existente `window.fetchSPU(rip)`.
- Se o RIP for encontrado, a div oculta é exibida e preenchida com os dados de localização retornados, e as bordas ficam verdes. Caso contrário, alerta de erro.
- O fechamento do modal (`fecharModalRip()`) também foi ajustado para voltar a esconder a área de pesquisa para as próximas interações.

---

## 3. Plano de Rollback / Desfazer

Para desfazer a alteração:
1. Em `resources/views/processos/abas/aba1.blade.php`, localize a div `#modalInserirRip`. Retorne o `max-width` para 400px. Remova o `<button id="btnPesquisarRip">` e a div `#dadosRipPesquisado`.
2. Em `public/js/foco-01.js`, apague o listener do `btnPesquisarRip` recém-adicionado e reverta a função `fecharModalRip()` para seu estado anterior de uma linha só.
