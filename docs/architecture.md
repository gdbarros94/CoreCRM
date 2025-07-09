# Arquitetura do Sistema CoreCRM

A arquitetura do CoreCRM é projetada para ser modular, extensível e de fácil manutenção. O sistema é construído em PHP puro e segue um modelo de design que separa as preocupações, utilizando 


handlers para gerenciar diferentes aspectos da aplicação. A seguir, detalhamos os principais componentes e sua interação.

## Visão Geral da Estrutura de Pastas

A organização do projeto é fundamental para a clareza e a manutenibilidade. O CoreCRM adota a seguinte estrutura de diretórios:

```
/
├── index.php             # Ponto de entrada principal da aplicação
├── bootstrap.php         # Arquivo de inicialização do sistema
├── config/               # Contém arquivos de configuração (e.g., database.config.php, config.php)
├── core/                 # Contém as classes principais do sistema (handlers)
├── plugins/              # Diretório para os plugins. Cada plugin tem seu próprio subdiretório
├── themes/               # Diretório para os temas da interface do usuário
├── assets/               # Recursos estáticos (CSS, JavaScript, imagens)
├── logs/                 # Arquivos de log gerados pelo sistema
└── admin/                # Interface administrativa do sistema
```

## Componentes Principais (`core/`)

O diretório `core/` abriga as classes fundamentais que compõem o coração do CoreCRM. Cada classe é um 


handler responsável por uma funcionalidade específica, garantindo a modularidade e a separação de responsabilidades.

### `System.php`

Esta classe é a base do sistema, responsável pela inicialização, verificação de saúde e gerenciamento de logs. Ela garante que o ambiente esteja pronto para a execução da aplicação e fornece um mecanismo centralizado para o registro de eventos e erros.

**Métodos Chave:**

*   **`init()`**: O método de inicialização do sistema. Ele é responsável por chamar `healthCheck()` e, futuramente, `checkDatabaseIntegrity()` e `setupLogging()`. Deve ser chamado apenas uma vez no início da aplicação.
*   **`healthCheck()`**: Realiza verificações essenciais para o funcionamento do sistema, como permissões de diretórios e a presença de extensões PHP necessárias. É um ponto crucial para garantir a estabilidade do ambiente.
*   **`logMessage($logFile, $message)`**: Uma função utilitária para escrever mensagens em arquivos de log específicos. Ela gerencia a criação de diretórios e arquivos de log, se necessário, e garante que as mensagens sejam anexadas corretamente.
*   **`log($message, $level)`**: A interface principal para o registro de logs no sistema. Permite categorizar as mensagens por nível de severidade (`info`, `warning`, `error`), facilitando a depuração e o monitoramento do sistema.
*   **`getLogs()`**: Projetado para retornar os logs mais recentes, provavelmente para exibição na interface administrativa, auxiliando na depuração e monitoramento em tempo real.

**Interação:**

O `System.php` é utilizado por quase todos os outros componentes do CoreCRM para registrar eventos e erros, garantindo que o fluxo de execução seja monitorado e que quaisquer problemas sejam devidamente registrados. Ele é o ponto de partida para a estabilidade e observabilidade do sistema.

### `APIHandler.php`

O `APIHandler` é o coração da comunicação externa do CoreCRM, expondo funcionalidades do sistema através de uma API RESTful. Ele lida com a autenticação de requisições, roteamento de endpoints e formatação de respostas em JSON, permitindo que outras aplicações interajam programaticamente com o CoreCRM.

**Métodos Chave:**

*   **`handleRequest($endpoint)`**: O método principal que recebe o endpoint da API e coordena o processo. Ele primeiro chama `authenticate()` e, se bem-sucedido, roteia a requisição para a função apropriada com base no endpoint. Em caso de endpoints não encontrados, ele utiliza o `HookHandler` para permitir que plugins adicionem ou modifiquem o comportamento padrão.
*   **`authenticate()`**: Responsável por verificar a validade do token de autenticação presente no cabeçalho `Authorization` da requisição. Atualmente, utiliza um token simples de placeholder, mas é projetado para ser estendido para métodos de autenticação mais robustos (e.g., JWT).
*   **`listClients()`**: Um exemplo de endpoint que demonstra a recuperação de dados do banco de dados (utilizando `DatabaseHandler` e `QueryBuilder`) e a formatação da resposta em JSON.
*   **`createNewUser()`**: Outro exemplo de endpoint que mostra como processar dados de entrada (JSON) e interagir com o sistema para criar novos registros.
*   **`sendJsonResponse($data, $statusCode)`**: Uma função utilitária para padronizar as respostas da API, enviando dados em formato JSON com o código de status HTTP apropriado.

**Interação:**

O `APIHandler` interage diretamente com o `AuthHandler` para autenticação, com o `DatabaseHandler` para operações de dados e, crucialmente, com o `HookHandler` para permitir a extensibilidade da API via plugins. Isso significa que desenvolvedores de plugins podem adicionar novos endpoints ou modificar o comportamento dos existentes sem tocar no código principal do `APIHandler`.

### `HookHandler.php`

O `HookHandler` é a espinha dorsal da modularidade e extensibilidade do CoreCRM, implementando um sistema de 


ganchos (hooks) e ações (actions) inspirado em sistemas como o WordPress. Este mecanismo permite que funções de callback sejam registradas e executadas em pontos específicos do ciclo de vida da aplicação, sem a necessidade de modificar o código-fonte principal.

**Conceitos Chave:**

*   **Ações (Actions)**: São eventos disparados em momentos predefinidos do código. Por exemplo, `do_action('user_login')` pode ser disparado após um usuário fazer login.
*   **Hooks (Ganchos)**: São funções de callback que se 


engatam a essas ações. Eles podem ser executados `before` (antes) ou `after` (depois) da ação principal, e podem ter uma `priority` (prioridade) que determina a ordem de execução.

**Métodos Chave:**

*   **`register_hook($actionName, $callback, $when = 'after', $priority = 10)`**: Permite que desenvolvedores registrem suas funções (`$callback`) para serem executadas quando uma `$actionName` específica for disparada. O parâmetro `$when` define se o hook será executado antes ou depois da ação principal, e `$priority` define a ordem de execução (menor número executa primeiro).
*   **`do_action($actionName, $args = [], $actionCallback = null)`**: Este é o método que dispara uma ação. Ele executa todos os hooks registrados para `$actionName` com `$when = 'before'`, em seguida, executa a `$actionCallback` (se fornecida), e finalmente executa todos os hooks registrados com `$when = 'after'`. Os `$args` são passados para todas as funções de callback.

**Interação:**

O `HookHandler` é um componente central para a extensibilidade do CoreCRM. Ele permite que plugins e módulos se integrem profundamente ao sistema sem modificar o código-fonte principal. Isso é crucial para a manutenção e atualização do sistema, pois garante que as personalizações não sejam perdidas com as novas versões. Outros handlers, como `APIHandler`, utilizam `do_action` para criar pontos de extensão.

### `AuthHandler.php`

O `AuthHandler` é responsável por todo o ciclo de vida da autenticação e autorização de usuários no CoreCRM. Ele gerencia sessões, logins, logouts e verifica permissões, garantindo que apenas usuários autorizados acessem recursos protegidos.

**Métodos Chave:**

*   **`init()`**: Inicializa o sistema de autenticação, garantindo que uma sessão PHP esteja ativa. Este método é fundamental para o funcionamento de todas as outras funcionalidades de autenticação.
*   **`isLoggedIn()`**: Retorna `true` se um usuário estiver logado (ou seja, se `$_SESSION["user_id"]` estiver definido), e `false` caso contrário.
*   **`requireAuth()`**: Um método de conveniência que verifica se o usuário está logado. Se não estiver, ele redireciona o usuário para a página de login, protegendo rotas e recursos.
*   **`checkPermission($permission)`**: Verifica se o usuário logado possui uma permissão específica. Atualmente, a implementação é um placeholder simples que concede todas as permissões a usuários com a função 


`admin`. Em uma implementação completa, isso envolveria a consulta a um sistema de controle de acesso baseado em funções (RBAC) ou em atributos (ABAC).
*   **`login($userId, $userRole)`**: Realiza o login do usuário, armazenando o `$userId` e `$userRole` na sessão. Isso estabelece o estado de autenticação do usuário.
*   **`logout()`**: Encerra a sessão do usuário, removendo as informações de autenticação e redirecionando o usuário para a página inicial ou de login.
*   **`hashPassword($password)`**: Utiliza funções seguras de hashing (como `password_hash` do PHP) para criar um hash da senha fornecida. Essencial para armazenar senhas de forma segura no banco de dados.
*   **`verifyPassword($password, $hash)`**: Verifica se uma senha em texto plano corresponde a um hash armazenado, utilizando `password_verify` do PHP.
*   **`redirect($url)`**: Uma função auxiliar para redirecionar o navegador do usuário para uma URL específica.

**Interação:**

O `AuthHandler` é um componente crítico que interage com quase todas as partes do sistema que exigem controle de acesso. Ele é chamado pelo `APIHandler` para autenticar requisições e por controladores de página para proteger rotas. A integração com o `DatabaseHandler` seria necessária para buscar credenciais de usuário e informações de permissão.

### `DatabaseHandler.php`

O `DatabaseHandler` é a camada de abstração para a interação com o banco de dados. Ele gerencia a conexão, a execução de consultas SQL e a manipulação de erros, suportando diferentes drivers de banco de dados (MySQL e SQLite).

**Métodos Chave:**

*   **`init()`**: Inicializa o manipulador de banco de dados, carregando as configurações de `config/database.config.php` e chamando `connect()` para estabelecer a conexão.
*   **`connect()`**: Estabelece a conexão PDO com o banco de dados. Ele lê as configurações (driver, host, database, username, password, charset, collation) e configura o PDO para lidar com erros e modos de busca. Lança exceções em caso de falha na conexão ou driver não suportado.
*   **`getConnection()`**: Retorna a instância da conexão PDO ativa, permitindo que outros componentes executem operações de banco de dados diretamente, se necessário.
*   **`query($sql, $params = [])`**: Executa uma consulta SQL preparada. Este método é fundamental para prevenir ataques de injeção SQL, pois os parâmetros são vinculados separadamente da consulta. Retorna um objeto `PDOStatement`.
*   **`execute($sql, $params = [])`**: Um método de conveniência para executar consultas que não retornam resultados (INSERT, UPDATE, DELETE). Retorna `true` em caso de sucesso, `false` em caso de falha.
*   **`fetch($sql, $params = [])`**: Executa uma consulta e retorna uma única linha de resultado como um array associativo.
*   **`fetchAll($sql, $params = [])`**: Executa uma consulta e retorna todas as linhas de resultado como um array de arrays associativos.
*   **`lastInsertId()`**: Retorna o ID da última linha inserida em uma tabela com uma coluna de auto-incremento.

**Interação:**

O `DatabaseHandler` é utilizado por componentes como `APIHandler` e `AuthHandler` para persistir e recuperar dados. A classe `QueryBuilder` (mencionada no `APIHandler`) provavelmente utiliza o `DatabaseHandler` internamente para construir e executar consultas de forma mais abstrata e orientada a objetos.

### `PluginHandler.php`

Embora não totalmente detalhado na análise inicial, o `PluginHandler` é o componente responsável por descobrir, carregar e gerenciar os plugins instalados no sistema. Ele provavelmente lê os arquivos `plugin.json` para obter metadados e inclui os arquivos `main.php` dos plugins para ativar suas funcionalidades.

**Funções Esperadas:**

*   **`loadPlugins()`**: Percorre o diretório `plugins/`, identifica os subdiretórios de plugins, lê seus `plugin.json` e carrega seus `main.php`.
*   **`getPluginInfo($slug)`**: Retorna informações detalhadas sobre um plugin específico.
*   **`activatePlugin($slug)` / `deactivatePlugin($slug)`**: Gerencia o estado de ativação dos plugins.

**Interação:**

O `PluginHandler` interage com o sistema de arquivos para carregar os plugins e, uma vez carregados, os plugins interagem com o `HookHandler` para estender as funcionalidades do CoreCRM.

### `RoutesHandler.php`

O `RoutesHandler` é o componente encarregado de gerenciar o roteamento dinâmico das requisições HTTP para as funções ou controladores apropriados dentro do CoreCRM. Ele interpreta a URL da requisição e a mapeia para a lógica de aplicação correspondente.

**Funções Esperadas:**

*   **`handleRequest()`**: Analisa a URL da requisição e despacha para o módulo ou plugin correto.
*   **`addRoute($path, $callback)`**: Permite o registro programático de novas rotas.
*   **`getRoute($path)`**: Retorna informações sobre uma rota registrada.

**Interação:**

O `RoutesHandler` é um dos primeiros componentes a ser invocado no ciclo de vida de uma requisição, provavelmente a partir do `index.php` ou `bootstrap.php`. Ele interage com os plugins para descobrir rotas definidas por eles (conforme indicado no `plugin.json`).

### `ThemeHandler.php`

O `ThemeHandler` é responsável por gerenciar os temas visuais do CoreCRM. Ele permite que a interface do usuário seja personalizada e alterada sem afetar a lógica de negócio subjacente.

**Funções Esperadas:**

*   **`loadTheme($themeName)`**: Carrega os arquivos e recursos de um tema específico.
*   **`getActiveTheme()`**: Retorna o tema atualmente ativo.
*   **`render($template, $data)`**: Renderiza um template de tema, passando dados para ele.

**Interação:**

O `ThemeHandler` interage com o sistema de arquivos para carregar os arquivos de tema e com os controladores para renderizar as views. Ele é crucial para a apresentação do CoreCRM.

### `UuidHandler.php`

O `UuidHandler` é uma classe utilitária dedicada à geração de Identificadores Únicos Universais (UUIDs). UUIDs são frequentemente usados para gerar identificadores únicos para registros de banco de dados, sessões ou outros elementos que requerem unicidade global.

**Funções Esperadas:**

*   **`generate()`**: Gera e retorna um novo UUID.

**Interação:**

O `UuidHandler` é uma dependência para qualquer parte do sistema que precise de identificadores únicos, como a criação de novos usuários, clientes ou plugins.

## Fluxo de Execução (Alto Nível)

1.  **`index.php`**: Ponto de entrada da aplicação.
2.  **`bootstrap.php`**: Inicializa o sistema, provavelmente chamando `System::init()`, `AuthHandler::init()`, `DatabaseHandler::init()`, e carregando o `RoutesHandler`.
3.  **`RoutesHandler`**: Analisa a URL da requisição e determina qual controlador ou função deve ser executada.
4.  **Controladores/Plugins**: A lógica de negócio é executada, interagindo com `DatabaseHandler` para dados, `AuthHandler` para autenticação/autorização, e `HookHandler` para estender funcionalidades.
5.  **`ThemeHandler`**: Renderiza a interface do usuário com base no tema ativo.
6.  **`APIHandler`**: Se a requisição for para um endpoint da API, o `APIHandler` assume o controle, autentica e processa a requisição, retornando uma resposta JSON.

Esta arquitetura modular permite que o CoreCRM seja facilmente estendido e mantido, com cada componente tendo uma responsabilidade clara e bem definida.

