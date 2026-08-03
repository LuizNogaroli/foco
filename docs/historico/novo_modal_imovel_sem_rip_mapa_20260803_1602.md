# Histórico de Alterações - Novo Modal Imóvel/Área sem RIP - 03/08/2026

Este documento registra a implementação estrutural e funcional do novo fluxo de "Imóvel/Área sem RIP" (antigo Cadastro Mínimo), incluindo a adição de mapa (Leaflet) e campos dinâmicos de destinação.

## 1. Problemas e Requisitos Atendidos
- **Nomenclatura:** Título do modal foi alterado para "Imóvel/Área sem RIP".
- **Obrigatoriedade:** CEP deixou de ser um campo de preenchimento obrigatório.
- **Georreferenciamento:** Adicionada funcionalidade de inserir ponto geográfico via mapa interativo, coordenadas diretas ou via aproximação de CEP.
- **Destinação:** Adicionados campos condicionais para determinar se a área do terreno e a área do imóvel serão destinadas integral ou parcialmente (e qual a metragem caso parcial).

## 2. Alterações Realizadas

### Em `resources/views/processos/show.blade.php`:
- Importados o CSS e JS do [Leaflet 1.9.4](https://leafletjs.com/) via CDN do Unpkg no cabeçalho do documento, permitindo renderizar o mapa.

### Em `resources/views/processos/abas/aba1.blade.php`:
- `<h2>` do `#modalCadastroMinimo` ajustado.
- Adicionada estrutura de botões rádio `(Sim/Não)` para inserção de Geo.
- Adicionada `div#containerMapaGeo` contendo a renderização do Leaflet e os inputs de Latitude/Longitude.
- Adicionados os containers lógicos para "Área do terreno" e "Área do imóvel" (Integral/Parcial).

### Em `public/js/foco-01.js`:
- `initMap()` configurado com `L.map` apontando para o Brasil (OpenStreetMap).
- Evento de `click` no mapa para criar `L.marker` e preencher lat/lng.
- Evento de `blur` no `#modalCep` fazendo fetch via [Nominatim (OpenStreetMap)](https://nominatim.openstreetmap.org) para recentralizar o mapa caso o usuário não queira clicar.
- Formulário ajustado para não exigir o CEP.
- Adicionada coleta dos novos campos ao clicar em `btnSalvarCadastro` e sua respectiva formatação para a lista na tela.

---

## 3. Plano de Rollback / Desfazer

Caso haja problemas com a integração de mapas ou regras de negócio:
1. Em `show.blade.php`, remover as tags `<link>` e `<script>` do Leaflet.
2. Em `aba1.blade.php`, deletar os containers de inserção de pontos Geo (onde os inputs `inserir_geo` estão) e os containers de destinação (`destinacao_terreno`, `destinacao_imovel`). Restaurar o asterisco do CEP.
3. Em `foco-01.js`, remover `initMap`, seus eventListeners, os listeners de Nominatim e retornar `btnSalvarCadastro` para validar se `cep && area` existem.
