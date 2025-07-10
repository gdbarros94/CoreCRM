<?php
// users.php - CRUD de usuários para API do system_manager

// class SystemManagerUsersApi {
//     public static function registerRoutes() {
//         // GET /api/users
//         RoutesHandler::addRoute("GET", "/api/users", [self::class, 'listUsers']);
//         // POST /api/users
//         RoutesHandler::addRoute("POST", "/api/users", [self::class, 'createUser']);
//         // GET /api/users/{id}
//         RoutesHandler::addRoute("GET", "/api/users/{id}", [self::class, 'getUser']);
//         // PUT /api/users/{id}
//         RoutesHandler::addRoute("PUT", "/api/users/{id}", [self::class, 'updateUser']);
//         // DELETE /api/users/{id}
//         RoutesHandler::addRoute("DELETE", "/api/users/{id}", [self::class, 'deleteUser']);
//     }

//     public static function listUsers() {
//         if (!APIHandler::authenticate()) {
//             APIHandler::sendJsonResponse(["error" => "Unauthorized99"], 401);
//         }
//         $users = (new QueryBuilder("users"))->select()->get();
//         APIHandler::sendJsonResponse($users);
//     }

//     public static function createUser() {
//         if (!APIHandler::authenticate()) {
//             APIHandler::sendJsonResponse(["error" => "Unauthorized88"], 401);
//         }
//         $input = json_decode(file_get_contents("php://input"), true);
//         if (empty($input["username"]) || empty($input["password"])) {
//             APIHandler::sendJsonResponse(["error" => "Username and password required"], 400);
//         }
//         $data = [
//             "username" => $input["username"],
//             "password" => password_hash($input["password"], PASSWORD_DEFAULT),
//             "role" => $input["role"] ?? "user"
//         ];
//         (new QueryBuilder("users"))->insert($data)->execute();
//         APIHandler::sendJsonResponse(["message" => "User created"], 201);
//     }

//     public static function getUser($params) {
//         if (!APIHandler::authenticate()) {
//             APIHandler::sendJsonResponse(["error" => "Unauthorized77"], 401);
//         }
//         $user = (new QueryBuilder("users"))->select()->where(["id" => $params["id"]])->first();
//         if (!$user) {
//             APIHandler::sendJsonResponse(["error" => "User not found"], 404);
//         }
//         APIHandler::sendJsonResponse($user);
//     }

//     public static function updateUser($params) {
//         if (!APIHandler::authenticate()) {
//             APIHandler::sendJsonResponse(["error" => "Unauthorized66"], 401);
//         }
//         $input = json_decode(file_get_contents("php://input"), true);
//         $data = [];
//         if (!empty($input["username"])) $data["username"] = $input["username"];
//         if (!empty($input["password"])) $data["password"] = password_hash($input["password"], PASSWORD_DEFAULT);
//         if (!empty($input["role"])) $data["role"] = $input["role"];
//         if (empty($data)) {
//             APIHandler::sendJsonResponse(["error" => "No data to update"], 400);
//         }
//         (new QueryBuilder("users"))->update($data)->where(["id" => $params["id"]])->execute();
//         APIHandler::sendJsonResponse(["message" => "User updated"]);
//     }

//     public static function deleteUser($params) {
//         if (!APIHandler::authenticate()) {
//             APIHandler::sendJsonResponse(["error" => "Unauthorized55"], 401);
//         }
//         (new QueryBuilder("users"))->delete()->where(["id" => $params["id"]])->execute();
//         APIHandler::sendJsonResponse(["message" => "User deleted"]);
//     }

// }

// Garante que as rotas da API de usuários sejam registradas ao carregar o módulo
// SystemManagerUsersApi::registerRoutes();