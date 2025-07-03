<?php

// Rota de login (GET)
RoutesHandler::addRoute("GET", "/login", function() {
    $error = '';
    $redirect = $_GET['redirect'] ?? '/admin';
    include __DIR__ . '/templates/login.php';
});

// Rota de login (POST)
RoutesHandler::addRoute("POST", "/login", function() {
    $error = '';
    $user = $_POST['user'] ?? '';
    $pass = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? '/admin';
    // Hashes fixos para teste (gerados previamente)
    $users = [
        'admin' => [
            // senha: admin123
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$VlBaWVhocXRpcHBLSXdNZA$junmjqeOW2EN90RPy0Z5MLxu30YgUVg4/yrvY0pzqs4',
            'role' => 'admin'
        ],
        'user' => [
            // senha: user123
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$cXNHUzU3aVBUbEUudEZLVQ$qLfEhKVj0ssf7re1zwiOsHWL4bA7Y+y1CqEJY9x5p0c',
            'role' => 'user'
        ]
    ];
    if (isset($users[$user]) && AuthHandler::verifyPassword($pass, $users[$user]['password'])) {
        AuthHandler::login($user, $users[$user]['role']);
        // Redireciona para a rota original, se for segura
        if (strpos($redirect, '/') === 0) {
            AuthHandler::redirect($redirect);
        } else {
            AuthHandler::redirect('/admin');
        }
    } else {
        $error = 'Usuário ou senha inválidos!';
        echo "<script>alert('$error');</script>";
        include __DIR__ . '/templates/login.php';
    }
});

// Rota principal do painel admin (protegida)
RoutesHandler::addRoute("GET", "/admin", function() {
    AuthHandler::requireAuth();
    if (!AuthHandler::checkPermission('admin')) {
        echo "Acesso negado.";
        return;
    }
    include __DIR__ . '/templates/admin_panel.php';
}, [
    "auth" => true,
    "permission" => "admin"
]);

// Rota de logout
RoutesHandler::addRoute("GET", "/logout", function() {
    AuthHandler::logout();
    // O método logout já faz redirect para /login
});

// Exemplo de hook: log após gerar relatório
HookHandler::register_hook("after_gerar_relatorio", function() {
    System::log("Hook do plugin admin-panel executado após gerar relatório.", "info");
});

// Log de carregamento do plugin
System::log("Plugin admin-panel carregado com sucesso.");