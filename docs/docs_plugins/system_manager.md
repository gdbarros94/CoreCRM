# 📦 Plugin: System Manager

**Versão:** 1.0  
**Slug:** `system_manager`  
**Descrição:** Plugin para gerenciamento do sistema, autenticação, painel administrativo e controle de usuários via API.  
**Autor:** Luiz Gustavo D. Pulz e demais colaboradores  

---

## 📌 Funcionalidades Principais

- Login de usuários com autenticação via senha hash.
- Painel administrativo com verificação de permissões.
- API para gerenciamento de usuários (`CRUD`) com autenticação via token.
- Emissão e verificação de tokens para uso na API.
- Gatilhos (hooks) para integração com outras partes do sistema.

---

## 🛣️ Rotas Registradas

| Método | Rota                    | Protegida | Descrição                                      |
|--------|-------------------------|-----------|------------------------------------------------|
| GET    | `/login`                | ❌        | Exibe o formulário de login                    |
| POST   | `/login`                | ❌        | Processa o login do usuário                    |
| GET    | `/logout`               | ❌        | Encerra a sessão atual e redireciona ao login  |
| GET    | `/admin`                | ✅        | Painel administrativo, exige permissão `admin` |
| POST   | `/api/token`            | ❌        | Gera token para autenticação na API            |
| GET    | `/api/users`            | ✅ token  | Lista todos os usuários                        |
| POST   | `/api/users`            | ✅ token  | Cria um novo usuário                           |
| GET    | `/api/users/{id}`       | ✅ token  | Consulta um usuário específico                 |
| PUT    | `/api/users/{id}`       | ✅ token  | Atualiza dados de um usuário                   |
| DELETE | `/api/users/{id}`       | ✅ token  | Remove um usuário                              |

> ✅ token = requer token de autenticação via cabeçalho Authorization

---

## 🪝 Hooks Registrados

- `after_gerar_relatorio`: executado após a geração de relatórios.

---

## 🗃️ Tabelas do Banco de Dados

### `api_tokens`

| Campo        | Tipo         | Descrição                              |
|--------------|--------------|----------------------------------------|
| `id`         | INT          | ID primário (auto incremento)          |
| `user_id`    | INT          | ID do usuário (chave estrangeira)      |
| `token`      | VARCHAR(128) | Token de autenticação gerado           |
| `created_at` | DATETIME     | Data de criação do token               |

> Chave estrangeira `user_id` referencia a tabela `users(id)`

---

## 👤 Usuários Pré-Definidos para Teste

| Usuário | Senha    | Permissão |
|---------|----------|-----------|
| admin   | admin123 | admin     |
| user    | user123  | user      |

---

## 📁 Estrutura de Arquivos

system_manager/
├── admin/
│   ├── templates/
│   │   ├── admin_panel.php
│   │   ├── login.php
│   ├── admin.php
├── api/
│   ├── api_tokens.sql
│   ├── token.php
│   ├── users.php
├── main.php
└── plugin.json
