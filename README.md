# CoreCRM

**CoreCRM** é um sistema de CRM (Customer Relationship Management) modular, construído em PHP puro, com arquitetura baseada em plugins, sistema de rotas dinâmicas, autenticação, API REST e um robusto sistema de *hooks* e *actions* para extensão de funcionalidades.

> Este projeto foi desenvolvido em sala de aula por Gabriel Barros e a turma V1 do curso técnico em Desenvolvimento de Sistemas do Senac Novo Hamburgo.

---

## ✨ Funcionalidades principais

- Carregamento de páginas e módulos via **plugins**
- Sistema de **temas** com interface desacoplada
- Roteamento automático via `RoutesHandler`
- **Autenticação** e controle de acesso (ACL)
- **API REST** para comunicação externa com plugins
- Sistema de **hooks e actions** inspirado no WordPress
- Interface de **admin** para gerenciar o sistema
- Suporte a **instalação de plugins via upload**
- Query Builder próprio no `DatabaseHandler`
- `config.php` global com modo debug, timezone, nome do app, etc.

---

## 📁 Estrutura do Projeto

```plaintext
/
├── index.php
├── bootstrap.php
├── config/
├── core/
├── plugins/
├── themes/
├── assets/
├── logs/
└── admin/
````

---

## 🛠 Requisitos

* PHP 8.x+
* MySQL ou SQLite
* Servidor com suporte a URL rewriting (Apache/Nginx)

---

## 🚀 Instalação

1. Clone o repositório:

  ```bash
  git clone https://github.com/gdbarros/corecrm.git
  cd corecrm
  ```

2. Execute o Composer para instalar as dependências:

  ```bash
  php composer.phar install
  ```

3. Configure seu banco de dados em `config/database.config.php`

4. Altere `config/config.php` com as configurações globais do sistema

5. Acesse `index.php` no navegador

---

## 🧩 Desenvolvimento de Plugins

Cada plugin deve conter um `plugin.json` e um `main.php`.

Exemplo de `plugin.json`:

```json
{
  "name": "Clientes",
  "slug": "clientes",
  "version": "1.0",
  "routes": ["/clientes", "/clientes/novo"]
}
```

---

## 🔐 Licença

Este projeto é open-source e pode ser utilizado para fins educacionais ou comerciais com os devidos créditos.

---

## 🙌 Créditos

Desenvolvido por [Gabriel Barros](https://github.com/gdbarros) e a turma TDSV1 — Senac Novo Hamburgo.

```