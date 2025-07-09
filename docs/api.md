# API REST do CoreCRM

A API REST do CoreCRM permite a comunicação programática com o sistema, facilitando a integração com outras aplicações e serviços. Ela é gerenciada pela classe `APIHandler.php` e utiliza um sistema de autenticação baseado em token.

## Autenticação

Todas as requisições à API exigem autenticação via token. O token deve ser enviado no cabeçalho `Authorization` no formato `Bearer <seu_token>`. Atualmente, o sistema utiliza um token de placeholder simples (`your_secret_api_token`). Em um ambiente de produção, é altamente recomendável implementar um mecanismo de autenticação mais robusto, como JWT (JSON Web Tokens) ou chaves de API geradas dinamicamente.

**Exemplo de Cabeçalho de Autenticação:**

```
Authorization: Bearer your_secret_api_token
```

## Endpoints da API

Os endpoints da API são roteados pelo `APIHandler` e podem ser estendidos por plugins através do sistema de hooks. Abaixo, detalhamos os endpoints de exemplo e como interagir com eles.

### `GET /api/clientes/listar`

Este endpoint retorna uma lista de todos os clientes registrados no sistema.

*   **Método:** `GET`
*   **URL:** `/api/clientes/listar`
*   **Autenticação:** Necessária
*   **Resposta (Sucesso - 200 OK):**

    ```json
    [
        {
            "id": 1,
            "nome": "Cliente Exemplo 1",
            "email": "cliente1@example.com"
        },
        {
            "id": 2,
            "nome": "Cliente Exemplo 2",
            "email": "cliente2@example.com"
        }
    ]
    ```

*   **Resposta (Erro - 401 Unauthorized):**

    ```json
    {
        "error": "Unauthorized"
    }
    ```

### `POST /api/usuarios/novo`

Este endpoint permite a criação de um novo usuário no sistema.

*   **Método:** `POST`
*   **URL:** `/api/usuarios/novo`
*   **Autenticação:** Necessária
*   **Corpo da Requisição (JSON):**

    ```json
    {
        "username": "novo_usuario",
        "password": "senha_segura"
    }
    ```

*   **Resposta (Sucesso - 200 OK):**

    ```json
    {
        "message": "User created successfully.",
        "user_id": 3
    }
    ```

*   **Resposta (Erro - 400 Bad Request - Campos Faltando):**

    ```json
    {
        "error": "Username and password are required."
    }
    ```

*   **Resposta (Erro - 401 Unauthorized):**

    ```json
    {
        "error": "Unauthorized"
    }
    ```

## Estendendo a API com Hooks

O `APIHandler` utiliza o `HookHandler` para permitir que plugins adicionem novos endpoints ou modifiquem o comportamento dos endpoints existentes. Quando um endpoint não é encontrado nos casos padrão do `switch` dentro de `handleRequest`, o `APIHandler` dispara um hook dinâmico:

```php
HookHandler::do_action("api_" . str_replace("/", "_", $endpoint), [], function() use ($endpoint) {
    self::sendJsonResponse(["error" => "API endpoint not found: {$endpoint}"], 404);
});
```

Isso significa que um plugin pode registrar um hook para `api_<nome_do_endpoint_com_underline>` e interceptar a requisição antes que o erro 404 seja enviado. Por exemplo, para criar um endpoint `/api/produtos/listar`:

```php
// No arquivo main.php do seu plugin

HookHandler::register_hook(
    "api_produtos_listar",
    function() {
        // Lógica para listar produtos
        $produtos = [
            ["id" => 1, "nome" => "Produto A", "preco" => 100.00],
            ["id" => 2, "nome" => "Produto B", "preco" => 200.00]
        ];
        APIHandler::sendJsonResponse($produtos);
    },
    "before" // Executa antes do erro 404
);
```

Ao registrar o hook com `"before"`, você garante que sua função será executada antes que o `APIHandler` retorne o erro de 


endpoint não encontrado. Isso permite que você crie endpoints personalizados para seus plugins.

## Métodos e Classes Essenciais para Interação com a API

Ao desenvolver com a API do CoreCRM, você interagirá com várias classes e métodos principais. Abaixo, detalhamos os mais relevantes:

### `System`

A classe `System` fornece funcionalidades básicas de sistema, incluindo logging, que é essencial para depuração e monitoramento das interações da API.

*   **`System::log(string $message, string $level = 'info')`**
    *   **Descrição:** Registra uma mensagem no arquivo de log do sistema. Útil para depurar requisições da API e entender o fluxo de execução.
    *   **Parâmetros:**
        *   `$message` (string): A mensagem a ser registrada.
        *   `$level` (string, opcional): O nível de severidade do log (`'info'`, `'warning'`, `'error'`). Padrão é `'info'`.
    *   **Exemplo:**
        ```php
        System::log("Requisição API recebida para: " . $endpoint, "info");
        System::log("Erro de autenticação na API.", "error");
        ```

### `AuthHandler`

A classe `AuthHandler` é crucial para gerenciar a autenticação e autorização de usuários, o que é fundamental para proteger seus endpoints de API e recursos.

*   **`AuthHandler::isLoggedIn(): bool`**
    *   **Descrição:** Verifica se um usuário está atualmente logado no sistema. Embora a API tenha sua própria autenticação, este método pode ser útil para endpoints que também dependem do estado de login de um usuário de sessão.
    *   **Retorno:** `true` se o usuário estiver logado, `false` caso contrário.

*   **`AuthHandler::checkPermission(string $permission): bool`**
    *   **Descrição:** Verifica se o usuário logado possui uma permissão específica. Pode ser usado dentro de seus endpoints de API personalizados para controlar o acesso a recursos específicos.
    *   **Parâmetros:**
        *   `$permission` (string): O nome da permissão a ser verificada (e.g., `'manage_users'`, `'view_reports'`).
    *   **Retorno:** `true` se o usuário tiver a permissão, `false` caso contrário.
    *   **Exemplo:**
        ```php
        if (!AuthHandler::checkPermission("api_write_access")) {
            APIHandler::sendJsonResponse(["error" => "Forbidden"], 403);
            return;
        }
        ```

### `DatabaseHandler` e `QueryBuilder`

Para interagir com o banco de dados a partir de seus endpoints de API, você usará o `DatabaseHandler` e a classe `QueryBuilder` (que o `APIHandler` já utiliza internamente).

*   **`DatabaseHandler::getConnection(): PDO`**
    *   **Descrição:** Retorna a instância da conexão PDO ativa com o banco de dados. Permite executar consultas SQL diretas se o `QueryBuilder` não for suficiente para um caso de uso específico.
    *   **Retorno:** Uma instância de `PDO`.

*   **`DatabaseHandler::query(string $sql, array $params = []): PDOStatement`**
    *   **Descrição:** Executa uma consulta SQL preparada com parâmetros. Essencial para consultas seguras e eficientes.
    *   **Parâmetros:**
        *   `$sql` (string): A consulta SQL a ser executada.
        *   `$params` (array, opcional): Um array associativo de parâmetros para a consulta preparada.
    *   **Retorno:** Um objeto `PDOStatement`.
    *   **Exemplo:**
        ```php
        $stmt = DatabaseHandler::query("SELECT * FROM produtos WHERE categoria = :cat", [":cat" => "eletronicos"]);
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        APIHandler::sendJsonResponse($produtos);
        ```

*   **`DatabaseHandler::fetch(string $sql, array $params = []): array|false`**
    *   **Descrição:** Executa uma consulta SQL e retorna uma única linha de resultado.
    *   **Retorno:** Um array associativo representando a linha, ou `false` se nenhum resultado for encontrado.

*   **`DatabaseHandler::fetchAll(string $sql, array $params = []): array`**
    *   **Descrição:** Executa uma consulta SQL e retorna todas as linhas de resultado.
    *   **Retorno:** Um array de arrays associativos, ou um array vazio se nenhum resultado for encontrado.

*   **`QueryBuilder` (Exemplo de Uso)**
    *   A classe `QueryBuilder` simplifica a construção de consultas SQL. Embora não seja um handler no diretório `core/`, ela é usada extensivamente para operações de banco de dados.
    *   **Exemplo de SELECT:**
        ```php
        $clientes = (new QueryBuilder("clients"))->select()->where("status", "active")->get();
        APIHandler::sendJsonResponse($clientes);
        ```
    *   **Exemplo de INSERT:**
        ```php
        (new QueryBuilder("users"))->insert(["username" => "testuser", "password" => "hashed_password"]);
        ```
    *   **Exemplo de UPDATE:**
        ```php
        (new QueryBuilder("clients"))->where("id", 1)->update(["email" => "novo@email.com"]);
        ```
    *   **Exemplo de DELETE:**
        ```php
        (new QueryBuilder("products"))->where("id", 5)->delete();
        ```

Ao utilizar esses métodos e classes, você pode construir endpoints de API robustos e seguros, integrando-se profundamente com as funcionalidades do CoreCRM.

