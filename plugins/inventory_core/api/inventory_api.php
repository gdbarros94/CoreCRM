<?php

class InventoryApi {
    
    public static function registerApiEndpoints() {
        // Endpoint para produtos
        HookHandler::register_hook("api_inventory_products", [self::class, 'handleProductsApi']);
        
        // Endpoint para estoque
        HookHandler::register_hook("api_inventory_stock", [self::class, 'handleStockApi']);
        
        // Endpoint para categorias
        HookHandler::register_hook("api_inventory_categories", [self::class, 'handleCategoriesApi']);
        
        // Endpoint para movimentações
        HookHandler::register_hook("api_inventory_movements", [self::class, 'handleMovementsApi']);
    }

    public static function handleProductsApi() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                $products = InventoryAdmin::getProducts();
                APIHandler::sendJsonResponse($products);
                break;
            case 'POST':
                $input = json_decode(file_get_contents("php://input"), true);
                $productId = InventoryAdmin::createProductData($input);
                APIHandler::sendJsonResponse(['id' => $productId, 'message' => 'Produto criado com sucesso'], 201);
                break;
            case 'PUT':
                $input = json_decode(file_get_contents("php://input"), true);
                $productId = $input['id'] ?? 0;
                if ($productId) {
                    InventoryAdmin::updateProductData($productId, $input);
                    APIHandler::sendJsonResponse(['message' => 'Produto atualizado com sucesso']);
                } else {
                    APIHandler::sendJsonResponse(['error' => 'ID do produto é obrigatório'], 400);
                }
                break;
            default:
                APIHandler::sendJsonResponse(['error' => 'Método não permitido'], 405);
        }
    }

    public static function handleStockApi() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                $stockItems = InventoryAdmin::getStockItems();
                APIHandler::sendJsonResponse($stockItems);
                break;
            case 'POST':
                $input = json_decode(file_get_contents("php://input"), true);
                $productId = $input['product_id'] ?? 0;
                $quantity = $input['quantity'] ?? 0;
                $movementType = $input['movement_type'] ?? 'ADJUSTMENT';
                $reason = $input['reason'] ?? '';
                
                if ($productId) {
                    InventoryAdmin::updateStockData($productId, $quantity, $movementType, $reason);
                    APIHandler::sendJsonResponse(['message' => 'Estoque atualizado com sucesso']);
                } else {
                    APIHandler::sendJsonResponse(['error' => 'ID do produto é obrigatório'], 400);
                }
                break;
            default:
                APIHandler::sendJsonResponse(['error' => 'Método não permitido'], 405);
        }
    }

    public static function handleCategoriesApi() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                $categories = InventoryAdmin::getCategories();
                APIHandler::sendJsonResponse($categories);
                break;
            default:
                APIHandler::sendJsonResponse(['error' => 'Método não permitido'], 405);
        }
    }

    public static function handleMovementsApi() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                $limit = $_GET['limit'] ?? 50;
                $movements = InventoryAdmin::getMovements($limit);
                APIHandler::sendJsonResponse($movements);
                break;
            default:
                APIHandler::sendJsonResponse(['error' => 'Método não permitido'], 405);
        }
    }
}

// Registrar os endpoints da API
InventoryApi::registerApiEndpoints(); 