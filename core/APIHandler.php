<?php
/**
 * Class APIHandler
 *
 * Esta classe é responsável por gerenciar as requisições da API.
 * Ela autentica as requisições, roteia os endpoints apropriados e
 * envia respostas em formato JSON. Os métodos principais incluem
 * autenticação, listagem de clientes e criação de novos usuários.
 *
 * Exemplos de endpoints suportados:
 * - clientes/listar: Retorna uma lista de clientes.
 * - usuarios/novo: Cria um novo usuário.
 *
 * @package CoreCRM\Core
 */
class APIHandler
{
    /**
     * Envia resposta JSON padronizada
     */
    public static function sendJsonResponse($data, $statusCode = 200)
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    /**
     * Autentica a requisição via token salvo no banco (tabela user_tokens)
     * Retorna true se autenticado, false caso contrário
     */
    public static function authenticate()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authToken = isset($headers["Authorization"]) ? str_replace("Bearer ", "", $headers["Authorization"]) : null;
        if (!$authToken) return false;
        $token = (new QueryBuilder("api_tokens"))->select()->where(["token" => $authToken])->first();
        return $token ? true : false;
    }

    /**
     * Gera um token seguro e salva no banco para o usuário autenticado
     */
    public static function generateToken()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (empty($headers['Authorization']) || stripos($headers['Authorization'], 'Basic ') !== 0) {
            self::sendJsonResponse(["error" => "Authorization header with Basic Auth required"], 400);
        }
        $auth = base64_decode(substr($headers['Authorization'], 6));
        if (!$auth || strpos($auth, ':') === false) {
            self::sendJsonResponse(["error" => "Invalid Basic Auth format"], 400);
        }
        list($username, $password) = explode(':', $auth, 2);
        if (empty($username) || empty($password)) {
            self::sendJsonResponse(["error" => "Username and password required"], 400);
        }
        // Utiliza o array de usuários fixos para autenticação
        $users = [
            'admin' => [
                // senha: admin123
                'password' => '$argon2id$v=19$m=65536,t=4,p=1$VlBaWVhocXRpcHBLSXdNZA$junmjqeOW2EN90RPy0Z5MLxu30YgUVg4/yrvY0pzqs4',
                'role' => 'admin',
                'id' => 1
            ],
            'user' => [
                // senha: user123
                'password' => '$argon2id$v=19$m=65536,t=4,p=1$cXNHUzU3aVBUbEUudEZLVQ$qLfEhKVj0ssf7re1zwiOsHWL4bA7Y+y1CqEJY9x5p0c',
                'role' => 'user',
                'id' => 2
            ]
        ];
        $user = isset($users[$username]) ? $users[$username] : null;
        if (!$user || !AuthHandler::verifyPassword($password, $user["password"])) {
            self::sendJsonResponse(["error" => "Invalid credentials"], 401);
        }
        $token = self::generateSecureToken();
        (new QueryBuilder("api_tokens"))->insert([
            "user_id" => $user["id"],
            "token" => $token,
            "created_at" => date("Y-m-d H:i:s")
        ])->execute();
        self::sendJsonResponse(["token" => $token]);
    }

    /**
     * Gera um token seguro
     */
    public static function generateSecureToken($length = 64)
    {
        return bin2hex(random_bytes($length / 2));
    }
}

