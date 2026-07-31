# Base de Conhecimento: Stack Tecnológica e Opções de Hospedagem

Este documento consolida as discussões técnicas e dúvidas a respeito da arquitetura, stack de desenvolvimento e opções de infraestrutura para o projeto Foco.

## 1. Stack do Projeto
O projeto utiliza uma stack robusta focada em produtividade e manutenibilidade para sistemas de gestão (ERPs, processos, formulários e auditoria):
- **Laravel (PHP):** O framework "chassi" da aplicação. Toda a estrutura de rotas, segurança e orquestração do servidor roda aqui.
- **Blade:** O Motor de Templates do Laravel. Permite escrever HTML dinâmico, misturando lógica PHP (`@if`, `@foreach`) diretamente na interface.
- **Eloquent ORM:** A camada que traduz o código PHP (`Processo::find(1)`) para consultas seguras no banco de dados, sem precisar escrever SQL puro.
- **SQLite (Atual - Desenvolvimento):** O banco de dados local do sistema. 
- **Supabase (API Externa):** Utilizado via requisições JavaScript para consumo de dados externos, como validação de informações de imóveis e RIPs, não sendo o banco central da aplicação.

## 2. Termos do Ecossistema Laravel
- **Artisan:** A interface de linha de comando (CLI) usada para gerar arquivos, rodar migrações de banco (`php artisan migrate`), etc.
- **Livewire:** Uma ferramenta que permite criar interfaces altamente dinâmicas no navegador escrevendo apenas código PHP, dispensando a necessidade de escrever JavaScript manualmente para coisas como atualização em tempo real de trechos da página.
- **Tailwind CSS e Alpine.js:** Bibliotecas frequentemente usadas no ecossistema (Stack TALL) para agilizar o design CSS e adicionar pequenas animações (menus, modais) sem o peso de frameworks complexos.
- **Inertia.js:** Uma "ponte" para desenvolvedores que preferem usar React ou Vue.js no lugar do Blade, conectando o frontend moderno ao backend Laravel de forma transparente.

## 3. Hospedagem e Infraestrutura (Deploy)

Por se tratar de um sistema dinâmico em PHP (e não apenas HTML estático), o projeto **não pode ser hospedado no GitHub Pages**. O código-fonte fica no GitHub, mas a execução exige um servidor.

### Modelos de Infraestrutura

1. **VPS (Servidor Cloud):** Ex: DigitalOcean (Droplets), AWS (EC2), Hetzner, Linode.
   - **O que é:** Um servidor "vazio". Você é responsável por instalar o Linux, Nginx, PHP e gerenciar a segurança e o banco de dados.
   - **Vantagem:** Muito mais barato e flexível. Máquinas potentes custam pouco (a partir de $5/mês).
   - **Desvantagem:** Exige conhecimentos sólidos de administração de servidores (DevOps). Se houver um problema no sistema operacional, a responsabilidade é sua.

2. **PaaS (Plataforma como Serviço):** Ex: Railway, Render, Fly.io, Heroku.
   - **O que é:** Um ambiente pronto. Você entrega seu código do GitHub para a plataforma e eles cuidam da infraestrutura de segurança, HTTPS, Load Balancer, etc.
   - **Vantagem:** Foco 100% no código. É a forma mais fácil e rápida de colocar um sistema no ar. Escalabilidade em 1-clique.
   - **Desvantagem:** É mais caro que um VPS puro (em torno de $10 a $25 dólares/mês para rodar Web + Banco, dependendo da plataforma). Oferece menos liberdade para configurações profundas a nível de sistema operacional (senha *root*).

### A Questão do Banco de Dados em Produção

Atualmente, as tabelas centrais do sistema (usuários, requerimentos, históricos) operam em **SQLite** (um arquivo local).

Se o projeto for hospedado num **PaaS (como Railway ou Render)**, manter o SQLite **não é viável**, pois plataformas PaaS utilizam um *File System Efêmero*. Ou seja, toda vez que o servidor for reiniciado ou que uma nova versão do código for lançada, a máquina é apagada e recriada, resultando na **perda total dos dados locais do SQLite**. Além disso, o SQLite enfrenta gargalos pesados (bloqueio de banco) quando há muitos acessos simultâneos (alta concorrência).

**Solução:** Antes da publicação em ambiente de produção definitivo, é obrigatório **migrar o banco de dados para PostgreSQL ou MySQL**. Como o projeto usa o *Eloquent* do Laravel, essa migração exige apenas a troca de variáveis no arquivo `.env` (credenciais do novo banco) e a execução das migrações (`php artisan migrate`), sem necessidade de reescrever a lógica do sistema.

## 4. Protótipos: Blade vs HTML Estático e SPA (React/Vue)

- **Protótipos (Direto no Laravel):** A abordagem mais madura para desenvolvimento de sistemas é começar os protótipos já na stack definitiva (Blade + Banco de Testes). Isso elimina o "retrabalho" de ter que limpar e traduzir protótipos estáticos (HTML/JS puros) e permite validar componentes, banco e lógica logo no dia um.
- **Quando usar React/Vue.js:** O uso de frameworks pesados de frontend justifica-se apenas para Single Page Applications (SPAs) que exigem **interatividade extrema** (como Trello, Spotify, Dashboards hipercruzados). O uso indiscriminado para sistemas de gestão baseados em formulários introduz complexidade excessiva sem ganho proporcional. Para 90% dos fluxos de trabalho (ERP, aprovações, relatórios), a combinação tradicional do Laravel (Blade + Livewire/Alpine) oferece a melhor proporção de agilidade, segurança e manutenibilidade.
