<?php

class InventoryAdmin {
    
    public static function registerRoutes() {
        // Rota principal do inventário
        RoutesHandler::addRoute("GET", "/inventory", [self::class, 'dashboard'], ["auth" => true]);

        // Rotas de produtos
        RoutesHandler::addRoute("GET", "/inventory/products", [self::class, 'productsList'], ["auth" => true]);
        RoutesHandler::addRoute("GET", "/inventory/products/new", [self::class, 'productForm'], ["auth" => true]);
        RoutesHandler::addRoute("POST", "/inventory/products/new", [self::class, 'createProduct'], ["auth" => true]);
        RoutesHandler::addRoute("GET", "/inventory/products/edit", [self::class, 'productForm'], ["auth" => true]);
        RoutesHandler::addRoute("POST", "/inventory/products/edit", [self::class, 'updateProduct'], ["auth" => true]);

        // Rotas de categorias
        RoutesHandler::addRoute("GET", "/inventory/categories", [self::class, 'categoriesList'], ["auth" => true]);

        // Rotas de estoque
        RoutesHandler::addRoute("GET", "/inventory/stock", [self::class, 'stockList'], ["auth" => true]);
        RoutesHandler::addRoute("POST", "/inventory/stock/update", [self::class, 'updateStock'], ["auth" => true]);

        // Rotas de movimentações
        RoutesHandler::addRoute("GET", "/inventory/movements", [self::class, 'movementsList'], ["auth" => true]);

        // Rotas de relatórios
        RoutesHandler::addRoute("GET", "/inventory/reports", [self::class, 'reports'], ["auth" => true]);
    }

    // Dashboard principal
    public static function dashboard() {
        AuthHandler::requireAuth();
        
        $totalProducts = self::getProducts();
        $stockItems = self::getStockItems();
        
        $inStock = array_filter($stockItems, function($item) {
            return $item['quantity'] > 0;
        });
        
        $lowStock = array_filter($stockItems, function($item) {
            return $item['quantity'] <= $item['min_stock'] && $item['quantity'] > 0;
        });
        
        $outOfStock = array_filter($stockItems, function($item) {
            return $item['quantity'] == 0;
        });
        
        include __DIR__ . '/templates/dashboard.php';
    }

    // Lista de produtos
    public static function productsList() {
        AuthHandler::requireAuth();
        $products = self::getProducts();
        include __DIR__ . '/templates/products_list.php';
    }

    // Formulário de produto
    public static function productForm() {
        AuthHandler::requireAuth();
        $productId = $_GET['id'] ?? 0;
        $product = $productId ? self::getProduct($productId) : null;
        $categories = self::getCategories();
        include __DIR__ . '/templates/product_form.php';
    }

    // Criar produto
    public static function createProduct() {
        AuthHandler::requireAuth();
        $productId = self::createProductData($_POST);
        AuthHandler::redirect('/inventory/products');
    }

    // Atualizar produto
    public static function updateProduct() {
        AuthHandler::requireAuth();
        $productId = $_POST['id'] ?? 0;
        self::updateProductData($productId, $_POST);
        AuthHandler::redirect('/inventory/products');
    }

    // Lista de categorias
    public static function categoriesList() {
        AuthHandler::requireAuth();
        $categories = self::getCategories();
        include __DIR__ . '/templates/categories_list.php';
    }

    // Lista de estoque
    public static function stockList() {
        AuthHandler::requireAuth();
        $stockItems = self::getStockItems();
        include __DIR__ . '/templates/stock_list.php';
    }

    // Atualizar estoque
    public static function updateStock() {
        AuthHandler::requireAuth();
        $productId = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 0;
        $movementType = $_POST['movement_type'] ?? 'ADJUSTMENT';
        $reason = $_POST['reason'] ?? '';
        
        self::updateStockData($productId, $quantity, $movementType, $reason);
        AuthHandler::redirect('/inventory/stock');
    }

    // Lista de movimentações
    public static function movementsList() {
        AuthHandler::requireAuth();
        $movements = self::getMovements();
        include __DIR__ . '/templates/movements_list.php';
    }

    // Relatórios
    public static function reports() {
        AuthHandler::requireAuth();
        include __DIR__ . '/templates/reports.php';
    }

    // Métodos de manipulação de dados
    public static function getProducts() {
        $query = "SELECT p.*, c.name as category_name 
                  FROM inventory_products p 
                  LEFT JOIN inventory_categories c ON p.category_id = c.id 
                  WHERE p.active = 1 
                  ORDER BY p.name";
        return DatabaseHandler::query($query)->fetchAll();
    }

    public static function getProduct($id) {
        $result = (new QueryBuilder("inventory_products"))
            ->select()
            ->where("id", "=", $id)
            ->get();
        return $result[0] ?? null;
    }

    public static function createProductData($data) {
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
        
        // Criar entrada inicial no estoque
        self::initializeStock($productId);
        
        return $productId;
    }

    public static function updateProductData($id, $data) {
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

    public static function getCategories() {
        return (new QueryBuilder("inventory_categories"))
            ->select()
            ->get();
    }

    public static function getStockItems() {
        $query = "SELECT s.*, p.name as product_name, p.sku, p.min_stock, p.max_stock 
                  FROM inventory_stock s 
                  JOIN inventory_products p ON s.product_id = p.id 
                  ORDER BY p.name";
        return DatabaseHandler::query($query)->fetchAll();
    }

    public static function getMovements($limit = 50) {
        $query = "SELECT m.*, p.name as product_name, p.sku 
                  FROM inventory_movements m 
                  JOIN inventory_products p ON m.product_id = p.id 
                  ORDER BY m.created_at DESC 
                  LIMIT ?";
        return DatabaseHandler::query($query, [$limit])->fetchAll();
    }

    public static function initializeStock($productId) {
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

    public static function updateStockData($productId, $quantity, $movementType = 'ADJUSTMENT', $reason = '') {
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
    }
}

// Registrar as rotas
InventoryAdmin::registerRoutes(); 