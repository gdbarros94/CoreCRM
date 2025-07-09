<?php

/**
 * Class AuthHandler
 *
 * This class handles user authentication and session management.
 * It provides methods to initialize the authentication system,
 * check if a user is logged in, require authentication, check user
 * permissions, log in and log out users, and manage password hashing.
 *
 * It is designed to be used in a web application where user sessions
 * are necessary for maintaining user state and access control.
 *
 * Usage:
 * - Call `AuthHandler::init()` at the start of your application to
 *   initialize the session.
 * - Use `AuthHandler::isLoggedIn()` to check if a user is logged in.
 * - Call `AuthHandler::requireAuth()` to enforce authentication on
 *   protected routes.
 * - Use `AuthHandler::checkPermission($permission)` to verify user
 *   permissions.
 * - Call `AuthHandler::login($userId, $userRole)` to log in a user.
 * - Call `AuthHandler::logout()` to log out the current user.
 * - Use `AuthHandler::hashPassword($password)` to securely hash
 *   passwords.
 * - Use `AuthHandler::verifyPassword($password, $hash)` to verify
 *   a password against its hash.
 */

class AuthHandler
{
/**
 * Initializes the AuthHandler.
 *
 * This method starts a session if none exists and logs the initialization
 * of the AuthHandler. It should be called once during the system startup
 * to ensure session management is properly configured.
 *
 * @return void
 */

    public static function init()
    {
        // Iniciar sessão
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        System::log("AuthHandler initialized. Session started.");
    }

/**
 * Checks if the user is currently logged in.
 *
 * This method verifies the presence of a user session, indicating
 * an active logged-in user.
 *
 * @return bool True if the user is logged in, false otherwise.
 */

    public static function isLoggedIn()
    {
        return isset($_SESSION["user_id"]);
    }

    /**
     * Ensures that the user is authenticated.
     *
     * This method checks if the user is logged in by verifying the session.
     * If the user is not logged in, it redirects them to the login page.
     *
     * @return void
     */

    public static function requireAuth()
    {
        if (!self::isLoggedIn()) {
            self::redirect("/login");
        }
    }

    /**
     * Checks if the user has the given permission.
     *
     * This method verifies if the user has the specified permission.
     * If the user is not logged in, it returns false.
     * If the user is logged in, it checks if the user has the permission
     * by verifying the user's role and the permissions associated with it.
     *
     * @param string $permission The permission to be checked.
     *
     * @return bool True if the user has the permission, false otherwise.
     */
    public static function checkPermission($permission)
    {
        // Implementar lógica de permissões aqui
        // Por enquanto, apenas um placeholder
        System::log("Checking permission: {$permission}", "debug");
        if (self::isLoggedIn() && isset($_SESSION["user_role"])) {
            // Exemplo simples: admin tem todas as permissões
            if ($_SESSION["user_role"] === "admin") {
                return true;
            }
            // Lógica mais complexa de ACL viria aqui, talvez lendo de um banco de dados
            // Por exemplo, verificar se a permissão está associada ao papel do usuário
            return false; // Por padrão, nega
        }
        return false;
    }

    /**
     * Logs in a user by setting session variables.
     *
     * This method sets the user ID and role in the session to indicate
     * that the user is logged in. It also logs the login event with
     * the user ID and role.
     *
     * @param string $userId The ID of the user to log in.
     * @param string $userRole The role of the user to log in.
     *
     * @return void
     */

    public static function login($userId, $userRole)
    {
        $_SESSION["user_id"] = $userId;
        $_SESSION["user_role"] = $userRole;
        System::log("User {$userId} logged in with role {$userRole}.");
    }

    /**
     * Logs out the current user.
     *
     * This method clears the user's session data and destroys the session,
     * effectively logging out the user. It then logs the logout event and
     * redirects the user to the login page.
     *
     * @return void
     */

    public static function logout()
    {
        session_unset();
        session_destroy();
        System::log("User logged out.");
        self::redirect("/login");
    }

    /**
     * Redireciona para a URL especificada.
     *
     * Esta função muda a localização da página para a URL especificada.
     * Ela utiliza o cabeçalho HTTP "Location" para fazer a redireção.
     * A função exit() é chamada após a redireção para encerrar a execução
     * do script atual.
     *
     * @param string $url A URL para redirecionar.
     *
     * @return void
     */
    public static function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }

    // Gera o hash da senha usando Argon2id
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    // Verifica se a senha corresponde ao hash
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}


