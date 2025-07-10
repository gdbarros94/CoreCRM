# 📦 Plugin: Inventory Core

**Slug:** `InventoryCore`  
**Versão:** `1.0.0`  
**Autor:** CoreCRM Team

## 🧩 Descrição

O plugin **Inventory Core** é responsável pelo gerenciamento completo do inventário do sistema CRM. Ele oferece funcionalidades robustas para controle de produtos, categorias, estoque e movimentações, além de integração com rotas, hooks e endpoints da API.

## 🧪 Funcionalidades

- 📦 Cadastro, edição e exclusão de produtos com informações completas.
- 🏷️ Organização de produtos em categorias (com suporte a hierarquia).
- 📊 Relatórios sobre valor total do estoque e produtos com estoque baixo.
- 📈 Controle de movimentações de entrada, saída e ajustes de estoque.
- 🔄 Hooks que permitem ações automáticas após eventos importantes (ex: criação de produto ou atualização de estoque).
- 🔐 Sistema de permissões por perfil.
- 🛠️ Criação automática da categoria padrão "Geral".
- 🌐 Endpoints para integração com APIs REST.

## 🔗 Rotas Atendidas

- `/inventory` – Painel principal do inventário.
- `/inventory/products` – Lista de produtos cadastrados.
- `/inventory/products/new` – Cadastro de novo produto.
- `/inventory/products/edit` – Edição de produto existente.
- `/inventory/categories` – Lista e gestão de categorias.
- `/inventory/stock` – Visão geral do estoque atual.
- `/inventory/movements` – Histórico de movimentações.
- `/inventory/reports` – Relatórios e análises.

## 🧩 Hooks Disponíveis

- `after_product_created` – Após criação de produto.
- `after_stock_updated` – Após atualização do estoque.
- `before_product_deleted` – Antes da exclusão de produto.
- `after_movement_registered` – Após nova movimentação registrada.

## 🔐 Permissões

- `inventory_view` – Visualização de dados do inventário.
- `inventory_manage` – Gerenciamento de produtos e categorias.
- `inventory_reports` – Acesso a relatórios do inventário.
- `inventory_admin` – Permissões administrativas completas.

## 🗃️ Tabelas de Banco de Dados

- `inventory_products` – Produtos cadastrados.
- `inventory_categories` – Categorias de produtos.
- `inventory_stock` – Controle de estoque por produto e local.
- `inventory_movements` – Histórico de movimentações (entrada/saída).

## 🌐 API Endpoints

- `GET/POST /api/inventory/products` – Consulta e criação de produtos via API.
- `GET /api/inventory/stock` – Consulta de estoque via API.
- `GET /api/inventory/movements` – Consulta de movimentações via API.

## ⚙️ Dependências

- PHP >= 8.0
- CoreCRM >= 1.0

## 📁 Estrutura do Plugin

plugins/
└── inventory_core/
├── index.php
├── main.php
└── plugins.json