# 📦 Plugin: Clientes

**Slug:** `Plugin-de-cliente`  
**Versão:** `1.0`  
**Autor:** Juliano Scherer

## 🧩 Descrição

O **Plugin de Clientes** tem como objetivo principal o gerenciamento de clientes dentro do sistema CRM. Ele fornece uma interface moderna e interativa que permite visualizar, filtrar, cadastrar, editar e excluir registros de clientes de forma intuitiva.

## 🚀 Funcionalidades

- 📋 Listagem de clientes com nome, e-mail, telefone, status e data de cadastro.
- 🔍 Campo de pesquisa para facilitar a localização de clientes.
- 🎯 Filtros por status, data de cadastro e ordenação por diferentes critérios.
- ➕ Cadastro de novos clientes.
- ✏️ Edição de registros existentes.
- 🗑️ Exclusão de clientes com confirmação.
- 📤 Exportação de dados.
- 📄 Paginação com navegação interativa.

## 🖥️ Interface

A interface é construída com **TailwindCSS**, com foco em responsividade e estética moderna. Há uso de efeitos visuais como gradientes, sombras e animações suaves para melhorar a experiência do usuário.

## 🔗 Rotas Atendidas

O plugin está disponível nas seguintes rotas:

- `/clientes` – Página principal com listagem de clientes.
- `/clientes/novo` – Tela de cadastro de um novo cliente.

## 🧠 Observações Técnicas

- Todos os dados são representados de forma estática no exemplo, mas o layout já está preparado para integração dinâmica com backend.
- Os eventos de clique (`onclick`) simulam ações reais com `console.log`, sendo facilmente adaptáveis para chamadas AJAX ou consumo de APIs.
- O painel de filtros é responsivo e pode ser exibido ou ocultado dinamicamente.

## 📁 Estrutura Base

```text
plugins/
└── clientes/
    ├── index.html
    └── plugins.json
