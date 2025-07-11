<?php

class InventoryMigration {
    
    public static function migrate($pdo) {
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
                $pdo->exec($query);
                System::log("Tabela do inventário criada/verificada com sucesso.");
            } catch (Exception $e) {
                System::log("Erro ao criar tabela do inventário: " . $e->getMessage(), "error");
            }
        }

        // Inserir categorias padrão
        self::insertDefaultCategories($pdo);
    }

    private static function insertDefaultCategories($pdo) {
        $defaultCategories = [
            ['name' => 'Eletrônicos', 'description' => 'Produtos eletrônicos'],
            ['name' => 'Informática', 'description' => 'Produtos de informática'],
            ['name' => 'Escritório', 'description' => 'Material de escritório'],
            ['name' => 'Limpeza', 'description' => 'Produtos de limpeza'],
            ['name' => 'Outros', 'description' => 'Outros produtos']
        ];

        foreach ($defaultCategories as $category) {
            try {
                $stmt = $pdo->prepare("INSERT IGNORE INTO inventory_categories (name, description) VALUES (?, ?)");
                $stmt->execute([$category['name'], $category['description']]);
            } catch (Exception $e) {
                System::log("Erro ao inserir categoria padrão: " . $e->getMessage(), "error");
            }
        }
    }
} 