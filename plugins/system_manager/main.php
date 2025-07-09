<?php
// Carrega os módulos do plugin system_manager
require_once __DIR__ . '/admin/admin.php';
require_once __DIR__ . '/api/token.php';
require_once __DIR__ . '/api/users.php';

// As classes SystemManagerAdmin, SystemManagerTokenApi e SystemManagerUsersApi
// já registram suas rotas ao serem carregadas, não é necessário chamar registerRoutes() aqui novamente.

// Exemplo de hook: log após gerar relatório
HookHandler::register_hook("after_gerar_relatorio", function() {
    System::log("Hook do plugin system-manager executado após gerar relatório.", "info");
});

// Log de carregamento do plugin
System::log("Plugin system_manager carregado com sucesso.");