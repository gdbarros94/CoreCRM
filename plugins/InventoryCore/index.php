<?php

/**
 * InventoryCore Plugin - Index File
 * 
 * Este arquivo serve como ponto de entrada alternativo para o plugin
 * e pode ser usado para configurações específicas ou redirects.
 */

// Verificar se o sistema CoreCRM está carregado
if (!class_exists('System')) {
    die('Este plugin requer o sistema CoreCRM para funcionar.');
}

// Verificar autenticação se acessado diretamente
if (!isset($_SESSION)) {
    session_start();
}

// Redirect para a página principal do inventário se acessado diretamente
if ($_SERVER['REQUEST_URI'] === '/plugins/InventoryCore/index.php') {
    header('Location: /inventory');
    exit();
}

// Configurações específicas do plugin
define('INVENTORY_PLUGIN_PATH', __DIR__);
define('INVENTORY_VERSION', '1.0.0');
define('INVENTORY_MIN_PHP_VERSION', '8.0');

// Verificar versão do PHP
if (version_compare(PHP_VERSION, INVENTORY_MIN_PHP_VERSION, '<')) {
    System::log("InventoryCore requires PHP {$version} or higher. Current version: " . PHP_VERSION, "error");
    die("InventoryCore requires PHP {$version} or higher.");
}

// Função auxiliar para validar SKU
function validateSKU($sku) {
    return preg_match('/^[A-Z0-9-_]{3,20}$/', $sku);
}

// Função auxiliar para formatar preço
function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

// Função auxiliar para validar dados de produto
function validateProductData($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Nome do produto é obrigatório';
    }
    
    if (empty($data['sku'])) {
        $errors[] = 'SKU é obrigatório';
    } elseif (!validateSKU($data['sku'])) {
        $errors[] = 'SKU deve conter apenas letras maiúsculas, números, hífens e underscores (3-20 caracteres)';
    }
    
    if (isset($data['unit_price']) && !is_numeric($data['unit_price'])) {
        $errors[] = 'Preço unitário deve ser um número válido';
    }
    
    if (isset($data['cost_price']) && !is_numeric($data['cost_price'])) {
        $errors[] = 'Preço de custo deve ser um número válido';
    }
    
    if (isset($data['min_stock']) && (!is_numeric($data['min_stock']) || $data['min_stock'] < 0)) {
        $errors[] = 'Estoque mínimo deve ser um número não negativo';
    }
    
    if (isset($data['max_stock']) && (!is_numeric($data['max_stock']) || $data['max_stock'] < 0)) {
        $errors[] = 'Estoque máximo deve ser um número não negativo';
    }
    
    return $errors;
}

// Função auxiliar para obter estatísticas do inventário
function getInventoryStats() {
    $stats = [];
    
    // Total de produtos
    $totalProducts = (new QueryBuilder("inventory_products"))
        ->select(["COUNT(*) as total"])
        ->where("active", "=", 1)
        ->get()[0]['total'] ?? 0;
    
    // Total de categorias
    $totalCategories = (new QueryBuilder("inventory_categories"))
        ->select(["COUNT(*) as total"])
        ->get()[0]['total'] ?? 0;
    
    // Produtos com estoque baixo
    $lowStockQuery = "SELECT COUNT(*) as total FROM inventory_products p 
                      JOIN inventory_stock s ON p.id = s.product_id 
                      WHERE s.quantity <= p.min_stock AND p.min_stock > 0 AND p.active = 1";
    $lowStockProducts = DatabaseHandler::query($lowStockQuery)->fetch()['total'] ?? 0;
    
    // Valor total do estoque
    $stockValueQuery = "SELECT SUM(s.quantity * p.cost_price) as total_value 
                        FROM inventory_stock s 
                        JOIN inventory_products p ON s.product_id = p.id 
                        WHERE p.active = 1";
    $stockValue = DatabaseHandler::query($stockValueQuery)->fetch()['total_value'] ?? 0;
    
    return [
        'total_products' => $totalProducts,
        'total_categories' => $totalCategories,
        'low_stock_products' => $lowStockProducts,
        'stock_value' => $stockValue
    ];
}

// Função auxiliar para criar categoria padrão se não existir
function ensureDefaultCategory() {
    $defaultCategory = (new QueryBuilder("inventory_categories"))
        ->select()
        ->where("name", "=", "Geral")
        ->get();
    
    if (empty($defaultCategory)) {
        $categoryData = [
            'name' => 'Geral',
            'description' => 'Categoria padrão para produtos sem categoria específica'
        ];
        
        (new QueryBuilder("inventory_categories"))
            ->insert($categoryData)
            ->execute();
            
        System::log("Default category 'Geral' created for InventoryCore plugin.");
    }
}

// Executar configurações iniciais
ensureDefaultCategory();

// Log de acesso ao plugin
System::log("InventoryCore plugin index accessed.", "debug");

// Verificar se há tarefas de manutenção pendentes
$maintenanceFile = __DIR__ . '/maintenance.flag';
if (file_exists($maintenanceFile)) {
    System::log("InventoryCore maintenance mode detected.", "info");
    echo "Plugin em manutenção. Tente novamente em alguns minutos.";
    exit();
}

// Disponibilizar funções globalmente para o plugin
$GLOBALS['inventory_functions'] = [
    'validateSKU' => 'validateSKU',
    'formatPrice' => 'formatPrice',
    'validateProductData' => 'validateProductData',
    'getInventoryStats' => 'getInventoryStats'
];

System::log("InventoryCore plugin index loaded successfully.");
