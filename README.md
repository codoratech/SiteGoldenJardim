# 🌿 Golden Jardim

> **Uma empresa focada em paisagismo, jardinagem e cuidado que transformam ambientes.**

Site institucional **+ painel administrativo** desenvolvidos como projeto de TCC do Colégio Politec para a **Golden Jardim**, empresa especializada em jardinagem, paisagismo e visagismo de ambientes, com foco em residências, condomínios e empresas, atendendo Americana-SP e região.

O projeto foi pensado para apresentar os serviços da empresa de forma moderna, visual e intuitiva, além de dar ao cliente (Golden Jardim) um painel próprio para gerenciar clientes, produtos, serviços, fornecedores, ordens de serviço, agendamentos, estoque e contas a receber.

---

## 📌 Sobre o Projeto

O **Golden Jardim** é composto por duas frentes:

- **Site público** (`index.html`) — página institucional voltada ao visitante/cliente final;
- **Painel administrativo** (`/admin`) — sistema interno em PHP + MySQL para a gestão do negócio.

O projeto foi desenvolvido pelos estudantes do Colégio Politec **Gustavo Sales** e **Rafael Leonardo**, sob a marca de desenvolvimento **Codora Tech**, com o rebranding visual da Golden Jardim assinado por **Beatriz Cacesi** e **Laura Dourado**.

### 🎯 Objetivos

* Apresentar a Golden Jardim e sua proposta de trabalho;
* Divulgar os principais serviços oferecidos;
* Facilitar o contato com novos clientes (WhatsApp e Instagram);
* Dar à empresa um painel administrativo para controlar sua operação (clientes, produtos, serviços, estoque, ordens de serviço, agendamentos e financeiro);
* Criar uma presença digital moderna e profissional, em Português e Inglês.

---

## ✨ Funcionalidades

### Site público
* 🏡 Página institucional da empresa
* 🌱 Apresentação dos serviços (jardinagem, poda, paisagismo, visagismo)
* 📸 Galeria de projetos (antes/depois)
* 📱 Botão flutuante e integração com WhatsApp
* 📲 Link para Instagram
* 🌎 Suporte para Português e Inglês (toggle de idioma)
* ☀️🌙 Modo claro / modo escuro
* 📱 Design responsivo
* ✨ Animações e interações visuais
* 📋 Seção de contato/orçamento

### Painel administrativo (`/admin`)
* 🔐 Login de acesso restrito
* 👥 Cadastro e gestão de clientes
* 🌿 Cadastro de serviços e produtos
* 🚚 Cadastro de fornecedores e compras
* 📦 Controle de estoque
* 🧾 Ordens de serviço
* 📅 Agendamentos
* 💰 Financeiro / contas a receber
* 🔑 Gestão de logins/usuários do sistema

> ⚠️ O painel administrativo está em desenvolvimento como parte do TCC — algumas telas (como o cadastro completo de produtos e serviços e a integração das edições com o site público) ainda estão sendo implementadas.

---

## 🛠️ Tecnologias Utilizadas

**Site público**
* **HTML5** — Estrutura das páginas
* **CSS3** — Estilização, tema claro/escuro e responsividade
* **JavaScript** — Interações, i18n (PT/EN) e funcionalidades

**Painel administrativo**
* **PHP** — Lógica do backend e páginas do painel
* **MySQL / MariaDB** — Banco de dados (`banco.sql`)
* Template administrativo baseado em **Bootstrap**

**Ferramentas**
* **Git** e **GitHub** — Controle de versão e hospedagem do código
* **OpenCode** — Apoio no desenvolvimento

---

## 📂 Estrutura do Projeto

```text
golden-jardim/
│
├── admin/                  # Painel administrativo (PHP)
│   ├── index.php           # Login
│   ├── dashboard.php       # Visão geral
│   ├── clientes.php
│   ├── produtos.php
│   ├── servicos.php
│   ├── fornecedores.php
│   ├── ordens.php
│   ├── agendamentos.php
│   ├── estoque.php
│   ├── financeiro.php
│   ├── logins.php
│   └── ...
│
├── assets/
│   ├── css/                # Estilos do painel admin
│   ├── js/                 # Scripts do painel admin
│   ├── vendor/             # Bibliotecas de terceiros (CSS/JS/fonts)
│   ├── img/                # Imagens e ícones do admin
│   └── *.jpg                # Imagens do site público (hero, galeria, serviços)
│
├── conexao/
│   └── banco.php           # Conexão com o banco de dados
│
├── banco.sql                # Script de criação do banco (tabelas do sistema)
├── index.html                # Site público
└── README.md
```

---

## 🚀 Executando o Projeto

O site público (`index.html`) pode ser aberto diretamente no navegador. Já o **painel administrativo** depende de PHP e MySQL, então é necessário um ambiente como XAMPP, WAMP ou similar.

### 1. Clone o repositório

```bash
git clone https://github.com/codoratech/golden-jardim-digital-bloom.git
```

### 2. Acesse a pasta

```bash
cd golden-jardim-digital-bloom
```

### 3. Configure o banco de dados

* Crie um banco no MySQL/MariaDB e importe o arquivo `banco.sql`;
* Ajuste as credenciais de conexão em `conexao/banco.php` para o seu ambiente local.

### 4. Rode o projeto

* Coloque a pasta do projeto no diretório servido pelo seu servidor local (ex: `htdocs` no XAMPP);
* Acesse o site público em `http://localhost/golden-jardim-digital-bloom/index.html`;
* Acesse o painel administrativo em `http://localhost/golden-jardim-digital-bloom/admin/`.

> Para o site público isoladamente, também é possível usar a extensão **Live Server** do VS Code.

---

## 👥 Desenvolvimento

Projeto desenvolvido pela **Codora Tech** como parte do TCC do Colégio Politec, para a presença digital e gestão da Golden Jardim.

* **Desenvolvimento:** Gustavo Sales e Rafael Leonardo
* **Rebranding / Identidade Visual:** Beatriz Cacesi e Laura Dourado

> **Codora Tech** — Soluções digitais pensadas para pessoas.

---

## 🌿 Golden Jardim

A Golden Jardim atua com serviços de:

* 🌱 Jardinagem
* 🌳 Poda e cuidados com árvores
* 🏡 Paisagismo
* 🌿 Visagismo de ambientes
* 🪴 Venda de plantas
* 📋 Projetos e manutenção de jardins

---

## 📄 Status do Projeto

🟡 **Em desenvolvimento** — site público concluído; painel administrativo em construção (integração com o site público e cadastros ainda sendo finalizados).

---

## 📸 Preview

> Adicione aqui imagens ou GIFs do site para apresentar o projeto no GitHub.

---

## 📞 Contato

Para conhecer o trabalho da Golden Jardim ou solicitar um orçamento, entre em contato através dos canais disponibilizados no site (WhatsApp e Instagram).

---

## 📜 Licença

Este projeto foi desenvolvido especificamente para a **Golden Jardim** como trabalho de conclusão de curso. O código e os elementos visuais deste projeto não devem ser reutilizados ou redistribuídos sem autorização dos responsáveis.

---

<p align="center">
  Desenvolvido com 🌿 pela <strong>Codora Tech</strong>
</p>
