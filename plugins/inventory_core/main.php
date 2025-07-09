<?php

// Classe principal do plugin InventoryCore
class InventoryCore
{
    public static function init()
    {
        self::createTables();
        self::registerRoutes();
        self::registerHooks();
        self::registerApiEndpoints();
        System::log("InventoryCore plugin initialized successfully.");
    }

    private static function createTables()
    {
        $queries = [
            // Tabela de categorias
            "CREATE TABLE IF NOT EXISTS inventory_categories (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                parent_id INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (parent_id) REFERENCES inventory_categories(id) ON DELETE SET NULL
            )",

            // Tabela de produtos
            "CREATE TABLE IF NOT EXISTS inventory_products (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                sku VARCHAR(50) UNIQUE NOT NULL,
                category_id INT,
                unit_price DECIMAL(10,2) DEFAULT 0.00,
                cost_price DECIMAL(10,2) DEFAULT 0.00,
                min_stock INT DEFAULT 0,
                max_stock INT DEFAULT 0,
                active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES inventory_categories(id) ON DELETE SET NULL
            )",

            // Tabela de estoque
            "CREATE TABLE IF NOT EXISTS inventory_stock (
                id INT PRIMARY KEY AUTO_INCREMENT,
                product_id INT NOT NULL,
                quantity INT NOT NULL DEFAULT 0,
                reserved_quantity INT NOT NULL DEFAULT 0,
                location VARCHAR(100) DEFAULT 'default',
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES inventory_products(id) ON DELETE CASCADE,
                UNIQUE KEY unique_product_location (product_id, location)
            )",

            // Tabela de movimentações
            "CREATE TABLE IF NOT EXISTS inventory_movements (
                id INT PRIMARY KEY AUTO_INCREMENT,
                product_id INT NOT NULL,
                movement_type ENUM('IN', 'OUT', 'ADJUSTMENT') NOT NULL,
                quantity INT NOT NULL,
                reason VARCHAR(255),
                reference_id INT DEFAULT NULL,
                reference_type VARCHAR(50) DEFAULT NULL,
                user_id VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES inventory_products(id) ON DELETE CASCADE
            )"
        ];

        foreach ($queries as $query) {
            try {
                DatabaseHandler::query($query);
                System::log("Database table created/verified successfully.");
            } catch (Exception $e) {
                System::log("Error creating database table: " . $e->getMessage(), "error");
            }
        }
    }

    private static function registerRoutes()
    {
        // Rota principal do inventário
        RoutesHandler::addRoute("GET", "/inventory", function() {
            AuthHandler::requireAuth();
            include __DIR__ . '/templates/dashboard.php';
        }, ["auth" => true]);

        // Listar produtos
        RoutesHandler::addRoute("GET", "/inventory/products", function() {
            AuthHandler::requireAuth();
            $products = self::getProducts();
            include __DIR__ . '/templates/products_list.php';
        }, ["auth" => true]);

        // Novo produto (GET)
        RoutesHandler::addRoute("GET", "/inventory/products/new", function() {
            AuthHandler::requireAuth();
            $categories = self::getCategories();
            include __DIR__ . '/templates/product_form.php';
        }, ["auth" => true]);

        // Novo produto (POST)
        RoutesHandler::addRoute("POST", "/inventory/products/new", function() {
            AuthHandler::requireAuth();
            self::createProduct($_POST);
            AuthHandler::redirect('/inventory/products');
        }, ["auth" => true]);

        // Editar produto
        RoutesHandler::addRoute("GET", "/inventory/products/edit", function() {
            AuthHandler::requireAuth();
            $productId = $_GET['id'] ?? 0;
            $product = self::getProduct($productId);
            $categories = self::getCategories();
            include __DIR__ . '/templates/product_form.php';
        }, ["auth" => true]);

        // Atualizar produto (POST)
        RoutesHandler::addRoute("POST", "/inventory/products/edit", function() {
            AuthHandler::requireAuth();
            $productId = $_POST['id'] ?? 0;
            self::updateProduct($productId, $_POST);
            AuthHandler::redirect('/inventory/products');
        }, ["auth" => true]);

        // Categorias
        RoutesHandler::addRoute("GET", "/inventory/categories", function() {
            AuthHandler::requireAuth();
            $categories = self::getCategories();
            include __DIR__ . '/templates/categories_list.php';
        }, ["auth" => true]);

        // Controle de estoque
        RoutesHandler::addRoute("GET", "/inventory/stock", function() {
            AuthHandler::requireAuth();
            $stockItems = self::getStockItems();
            include __DIR__ . '/templates/stock_list.php';
        }, ["auth" => true]);

        // Movimentações
        RoutesHandler::addRoute("GET", "/inventory/movements", function() {
            AuthHandler::requireAuth();
            $movements = self::getMovements();
            include __DIR__ . '/templates/movements_list.php';
        }, ["auth" => true]);

        // Relatórios
        RoutesHandler::addRoute("GET", "/inventory/reports", function() {
            AuthHandler::requireAuth();
            include __DIR__ . '/templates/reports.php';
        }, ["auth" => true]);
    }

    private static function registerHooks()
    {
        // Hook após criar produto
        HookHandler::register_hook("after_product_created", function($productId) {
            System::log("Product created with ID: {$productId}", "info");
            // Criar entrada inicial no estoque
            self::initializeStock($productId);
        });

        // Hook após atualizar estoque
        HookHandler::register_hook("after_stock_updated", function($productId, $newQuantity) {
            System::log("Stock updated for product {$productId}: {$newQuantity}", "info");
            // Verificar se está abaixo do estoque mínimo
            self::checkMinStock($productId);
        });
    }

    private static function registerApiEndpoints()
    {
        // Adicionar endpoints de API específicos no APIHandler
        HookHandler::register_hook("api_inventory_products", function() {
            $method = $_SERVER['REQUEST_METHOD'];
            switch ($method) {
                case 'GET':
                    $products = self::getProducts();
                    APIHandler::sendJsonResponse($products);
                    break;
                case 'POST':
                    $input = json_decode(file_get_contents("php://input"), true);
                    $productId = self::createProduct($input);
                    APIHandler::sendJsonResponse(['id' => $productId, 'message' => 'Product created'], 201);
                    break;
                default:
                    APIHandler::sendJsonResponse(['error' => 'Method not allowed'], 405);
            }
        });

        HookHandler::register_hook("api_inventory_stock", function() {
            $stockItems = self::getStockItems();
            APIHandler::sendJsonResponse($stockItems);
        });
    }

    // Métodos de manipulação de dados
    public static function getProducts()
    {
        $query = "SELECT p.*, c.name as category_name 
                  FROM inventory_products p 
                  LEFT JOIN inventory_categories c ON p.category_id = c.id 
                  WHERE p.active = 1 
                  ORDER BY p.name";
        return DatabaseHandler::query($query)->fetchAll();
    }

    public static function getProduct($id)
    {
        $result = (new QueryBuilder("inventory_products"))
            ->select()
            ->where("id", "=", $id)
            ->get();
        return $result[0] ?? null;
    }

    public static function createProduct($data)
    {
        $productData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'sku' => $data['sku'],
            'category_id' => $data['category_id'] ?? null,
            'unit_price' => $data['unit_price'] ?? 0,
            'cost_price' => $data['cost_price'] ?? 0,
            'min_stock' => $data['min_stock'] ?? 0,
            'max_stock' => $data['max_stock'] ?? 0
        ];

        $result = (new QueryBuilder("inventory_products"))
            ->insert($productData)
            ->execute();

        $productId = DatabaseHandler::getConnection()->lastInsertId();
        
        // Executar hook após criação
        HookHandler::do_action("after_product_created", [$productId]);
        
        return $productId;
    }

    public static function updateProduct($id, $data)
    {
        $productData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category_id' => $data['category_id'] ?? null,
            'unit_price' => $data['unit_price'] ?? 0,
            'cost_price' => $data['cost_price'] ?? 0,
            'min_stock' => $data['min_stock'] ?? 0,
            'max_stock' => $data['max_stock'] ?? 0
        ];

        return (new QueryBuilder("inventory_products"))
            ->update($productData)
            ->where("id", "=", $id)
            ->execute();
    }

    public static function getCategories()
    {
        return (new QueryBuilder("inventory_categories"))
            ->select()
            ->get();
    }

    public static function getStockItems()
    {
        $query = "SELECT s.*, p.name as product_name, p.sku, p.min_stock, p.max_stock 
                  FROM inventory_stock s 
                  JOIN inventory_products p ON s.product_id = p.id 
                  ORDER BY p.name";
        return DatabaseHandler::query($query)->fetchAll();
    }

    public static function getMovements($limit = 50)
    {
        $query = "SELECT m.*, p.name as product_name, p.sku 
                  FROM inventory_movements m 
                  JOIN inventory_products p ON m.product_id = p.id 
                  ORDER BY m.created_at DESC 
                  LIMIT ?";
        return DatabaseHandler::query($query, [$limit])->fetchAll();
    }

    public static function initializeStock($productId)
    {
        $stockData = [
            'product_id' => $productId,
            'quantity' => 0,
            'reserved_quantity' => 0,
            'location' => 'default'
        ];

        return (new QueryBuilder("inventory_stock"))
            ->insert($stockData)
            ->execute();
    }

    public static function updateStock($productId, $quantity, $movementType = 'ADJUSTMENT', $reason = '')
    {
        // Buscar estoque atual
        $currentStockResult = (new QueryBuilder("inventory_stock"))
            ->select()
            ->where("product_id", "=", $productId)
            ->get();

        $currentStock = $currentStockResult[0] ?? null;

        if ($currentStock) {
            $newQuantity = $currentStock['quantity'] + $quantity;
            (new QueryBuilder("inventory_stock"))
                ->update(['quantity' => $newQuantity])
                ->where("product_id", "=", $productId)
                ->execute();
        }

        // Registrar movimentação
        $movementData = [
            'product_id' => $productId,
            'movement_type' => $movementType,
            'quantity' => abs($quantity),
            'reason' => $reason,
            'user_id' => $_SESSION['user_id'] ?? 'system'
        ];

        (new QueryBuilder("inventory_movements"))
            ->insert($movementData)
            ->execute();

        // Executar hook após atualização
        HookHandler::do_action("after_stock_updated", [$productId, $newQuantity]);
    }

    public static function checkMinStock($productId)
    {
        $query = "SELECT p.name, p.min_stock, s.quantity 
                  FROM inventory_products p 
                  JOIN inventory_stock s ON p.id = s.product_id 
                  WHERE p.id = ? AND s.quantity <= p.min_stock AND p.min_stock > 0";
        
        $result = DatabaseHandler::query($query, [$productId])->fetch();
        
        if ($result) {
            System::log("Low stock alert for product: {$result['name']} (Current: {$result['quantity']}, Min: {$result['min_stock']})", "warning");
        }
    }
}

// Inicializar o plugin
InventoryCore::init();

System::log("InventoryCore plugin loaded successfully.");
