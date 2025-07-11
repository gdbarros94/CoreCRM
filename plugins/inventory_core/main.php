<?php
// Carrega os módulos do plugin inventory_core
require_once __DIR__ . '/admin/inventory_admin.php';
require_once __DIR__ . '/api/inventory_api.php';

// Log de carregamento do plugin
System::log("Plugin inventory_core carregado com sucesso.");

// Executa a migration do inventário ao iniciar o plugin
try {
    $pdo = DatabaseHandler::getConnection();
    require_once __DIR__ . '/admin/migrations/inventory_migration.php';
    InventoryMigration::migrate($pdo);
} catch (Exception $e) {
    System::log('Erro ao executar migration do inventário: ' . $e->getMessage(), 'error');
}

// Registra o menu no painel admin
System::addAdminSidebarMenuItem([
    'name' => 'Inventário',
    'icon' => 'inventory',
    'url' => '/inventory'
]);
