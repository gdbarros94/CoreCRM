# Desenvolvimento de Plugins para o CoreCRM

O CoreCRM foi projetado com a extensibilidade em mente, e o sistema de plugins é o principal mecanismo para adicionar novas funcionalidades ou modificar o comportamento existente sem alterar o código-fonte principal. Este guia detalha como criar e desenvolver plugins para o CoreCRM.

## Estrutura de um Plugin

Cada plugin no CoreCRM deve residir em seu próprio subdiretório dentro da pasta `plugins/` e conter, no mínimo, dois arquivos essenciais:

1.  **`plugin.json`**: Um arquivo de metadados que descreve o plugin.
2.  **`main.php`**: O arquivo principal do plugin, onde a lógica e as funcionalidades são implementadas.

### Exemplo de Estrutura de Diretório de Plugin

```
plugins/
├── MeuPlugin/
│   ├── plugin.json
│   └── main.php
│   └── assets/ (opcional)
│   └── views/ (opcional)
└── OutroPlugin/
    ├── plugin.json
    └── main.php
```

### `plugin.json`

O arquivo `plugin.json` é um manifesto JSON que fornece informações cruciais sobre o seu plugin. Ele é lido pelo CoreCRM para identificar e carregar o plugin corretamente. Os campos comuns incluem:

*   **`name`**: (Obrigatório) O nome legível do seu plugin.
*   **`slug`**: (Obrigatório) Um identificador único e amigável para o seu plugin (geralmente em minúsculas e sem espaços).
*   **`version`**: (Obrigatório) A versão atual do seu plugin.
*   **`description`**: (Opcional) Uma breve descrição do que o plugin faz.
*   **`author`**: (Opcional) O nome do autor ou da equipe de desenvolvimento.
*   **`routes`**: (Opcional) Um array de rotas que este plugin irá gerenciar. Isso permite que o `RoutesHandler` saiba quais URLs devem ser direcionadas para o seu plugin.

**Exemplo de `plugin.json`:**

```json
{
  "name": "Gerenciador de Clientes",
  "slug": "clientes",
  "version": "1.0.0",
  "description": "Gerencia informações de clientes e seus contatos.",
  "author": "Seu Nome ou Empresa",
  "routes": [
    "/clientes",
    "/clientes/novo",
    "/clientes/editar/{id}"
  ]
}
```

### `main.php`

O arquivo `main.php` é o ponto de entrada principal do seu plugin. É aqui que você registrará seus hooks, definirá suas funções, classes e qualquer lógica específica do plugin. Este arquivo será incluído pelo CoreCRM quando o plugin for carregado.

**Exemplo Básico de `main.php`:**

```php
<?php
// Seu código PHP para o plugin

// Exemplo: Registrar um hook para adicionar um item ao menu de administração
HookHandler::register_hook(
    "admin_menu_items",
    function($menu_items) {
        $menu_items["clientes"] = [
            "title" => "Clientes",
            "url" => "/admin/clientes",
            "icon" => "users"
        ];
        return $menu_items;
    },
    "filter" // Exemplo de um hook de filtro (ainda não implementado no CoreCRM, mas comum em sistemas de hooks)
);

// Exemplo: Criar uma rota para o plugin
RoutesHandler::addRoute(
    "/clientes",
    function() {
        // Lógica para exibir a lista de clientes
        echo "<h1>Lista de Clientes</h1>";
    }
);

// Exemplo: Adicionar um endpoint de API para o plugin
HookHandler::register_hook(
    "api_clientes_listar",
    function() {
        // Lógica para listar clientes via API
        $clientes = [
            ["id" => 1, "nome" => "Cliente A"],
            ["id" => 2, "nome" => "Cliente B"]
        ];
        APIHandler::sendJsonResponse($clientes);
    },
    "before" // Executa antes do erro 404 do APIHandler
);

// Outras classes e funções do seu plugin podem ser definidas aqui ou em arquivos separados e incluídas.
```

## Utilizando o Sistema de Hooks e Actions (`HookHandler`)

O sistema de hooks e actions é a maneira mais poderosa de estender o CoreCRM. Ele permite que seu plugin execute código em momentos específicos do ciclo de vida da aplicação, sem a necessidade de modificar o código-fonte principal. A classe `HookHandler` gerencia este sistema.

### `HookHandler::register_hook(string $actionName, callable $callback, string $when = 'after', int $priority = 10)`

Este método é usado para registrar uma função de callback (seu código) para ser executada quando uma ação específica (`$actionName`) for disparada.

*   **`$actionName`**: O nome da ação à qual você deseja se 


engatar. Por exemplo, `"user_login"`, `"api_clientes_listar"`.
*   **`$callback`**: A função PHP (pode ser uma função anônima, nome de função ou método de classe) que será executada quando o hook for acionado.
*   **`$when`**: (Opcional) Define quando o hook deve ser executado em relação à ação principal. Pode ser `"before"` (antes da ação principal) ou `"after"` (depois da ação principal). O padrão é `"after"`.
*   **`$priority`**: (Opcional) Um número inteiro que define a ordem de execução dos hooks para a mesma ação. Hooks com menor número de prioridade são executados primeiro. O padrão é `10`.

**Exemplo de Uso:**

```php
// Registrar um hook para ser executado antes de uma ação de salvamento de dados
HookHandler::register_hook(
    "data_save",
    function($data_to_save) {
        // Lógica para validar ou modificar os dados antes de salvar
        System::log("Dados prestes a serem salvos: " . json_encode($data_to_save));
        return $data_to_save; // Se for um filtro, retorne os dados modificados
    },
    "before",
    5 // Prioridade alta, executa antes de outros hooks
);

// Registrar um hook para ser executado após a criação de um usuário
HookHandler::register_hook(
    "user_created",
    function($user_id, $user_data) {
        // Lógica para enviar um e-mail de boas-vindas ou registrar em outro sistema
        System::log("Novo usuário criado com ID: " . $user_id);
    },
    "after",
    10
);
```

### `HookHandler::do_action(string $actionName, array $args = [], callable $actionCallback = null)`

Este método é usado para disparar uma ação, o que, por sua vez, executa todos os hooks registrados para essa ação.

*   **`$actionName`**: O nome da ação a ser disparada (deve corresponder ao `$actionName` usado em `register_hook`).
*   **`$args`**: (Opcional) Um array de argumentos que serão passados para todas as funções de callback registradas para este hook. Certifique-se de que a assinatura da sua função de callback corresponda aos argumentos esperados.
*   **`$actionCallback`**: (Opcional) Uma função de callback que representa a ação principal em si. Se fornecida, ela será executada entre os hooks `"before"` e `"after"`.

**Exemplo de Uso:**

```php
// Em algum lugar do seu código principal ou de um handler

// Disparar uma ação antes de processar um pedido
HookHandler::do_action("order_process_start", ["order_id" => 123, "status" => "pending"]);

// Lógica principal de processamento do pedido
// ...

// Disparar uma ação após o processamento do pedido
HookHandler::do_action("order_processed", ["order_id" => 123, "status" => "completed"]);

// Exemplo com actionCallback
$result = HookHandler::do_action(
    "calculate_total",
    ["items" => $cart_items],
    function($items) {
        $total = 0;
        foreach ($items as $item) {
            $total += $item["price"] * $item["quantity"];
        }
        return $total;
    }
);
System::log("Total calculado: " . $result);
```

## Interagindo com Outros Componentes do CoreCRM

Seu plugin pode e deve interagir com as classes principais do CoreCRM para acessar funcionalidades como banco de dados, autenticação e logging.

### `System`

Para registrar mensagens de log e depurar seu plugin:

*   **`System::log(string $message, string $level = 'info')`**
    *   **Descrição:** Registra uma mensagem no arquivo de log do sistema. Essencial para depuração e monitoramento do comportamento do seu plugin.
    *   **Exemplo:**
        ```php
        System::log("Plugin 'MeuPlugin' ativado com sucesso.", "info");
        System::log("Erro ao processar dados no plugin: " . $e->getMessage(), "error");
        ```

### `AuthHandler`

Para verificar o status de autenticação ou permissões de usuários dentro do seu plugin:

*   **`AuthHandler::isLoggedIn(): bool`**
    *   **Descrição:** Verifica se um usuário está logado.
*   **`AuthHandler::checkPermission(string $permission): bool`**
    *   **Descrição:** Verifica se o usuário logado possui uma permissão específica.
    *   **Exemplo:**
        ```php
        if (AuthHandler::isLoggedIn() && AuthHandler::checkPermission("manage_plugin_settings")) {
            // Mostrar opções de configuração do plugin
        } else {
            // Acesso negado
        }
        ```

### `DatabaseHandler` e `QueryBuilder`

Para interagir com o banco de dados e armazenar/recuperar dados específicos do seu plugin:

*   **`DatabaseHandler::getConnection(): PDO`**
    *   **Descrição:** Retorna a instância da conexão PDO. Use com cautela, preferindo `QueryBuilder` sempre que possível.
*   **`DatabaseHandler::query(string $sql, array $params = []): PDOStatement`**
    *   **Descrição:** Executa uma consulta SQL preparada.
*   **`DatabaseHandler::fetch(string $sql, array $params = []): array|false`**
    *   **Descrição:** Retorna uma única linha de resultado.
*   **`DatabaseHandler::fetchAll(string $sql, array $params = []): array`**
    *   **Descrição:** Retorna todas as linhas de resultado.
*   **`QueryBuilder`**: A maneira recomendada de interagir com o banco de dados. Permite construir consultas de forma fluente e segura.
    *   **Exemplo de Uso:**
        ```php
        // Inserir dados específicos do plugin
        (new QueryBuilder("plugin_settings"))->insert([
            "plugin_slug" => "meu_plugin",
            "setting_key" => "api_key",
            "setting_value" => "sua_api_key_aqui"
        ]);

        // Recuperar dados
        $settings = (new QueryBuilder("plugin_settings"))
            ->select()
            ->where("plugin_slug", "meu_plugin")
            ->get();
        ```

## Considerações Finais para o Desenvolvimento de Plugins

*   **Nomes Únicos**: Certifique-se de que os nomes de suas classes, funções e slugs de plugins sejam únicos para evitar conflitos com outros plugins ou com o CoreCRM.
*   **Segurança**: Sempre valide e sanitize os dados de entrada. Use consultas preparadas com `DatabaseHandler` ou `QueryBuilder` para evitar injeção SQL. Hash senhas com `AuthHandler::hashPassword()`.
*   **Performance**: Otimize suas consultas de banco de dados e evite operações que consumam muitos recursos em loops.
*   **Documentação Interna**: Comente seu código e forneça documentação clara para outros desenvolvedores que possam usar ou contribuir para o seu plugin.
*   **Testes**: Teste seu plugin exaustivamente para garantir que ele funcione conforme o esperado e não introduza bugs no sistema principal.

Seguindo estas diretrizes, você poderá desenvolver plugins robustos e bem integrados para o CoreCRM, estendendo suas funcionalidades de forma eficiente e segura.

