<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - CoreCRM</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Renderiza o header do tema
    ThemeHandler::render_header(['title' => 'Relatórios - CoreCRM']);
    ?>
    
    <div class="min-h-screen flex bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white/90 shadow-lg p-6 flex flex-col min-h-screen">
            <div class="mb-8">
                <h2 class="text-xl font-bold text-indigo-700 mb-2">Inventário</h2>
                <p class="text-gray-500 text-sm">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_id'] ?? 'Usuário'); ?>!</p>
                <p class="text-gray-400 text-xs mb-2">Sistema de Controle de Estoque</p>
            </div>
            
            <nav class="space-y-2">
                <a href="/inventory" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="/inventory/products" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors">
                    <i class="fas fa-boxes"></i>
                    Produtos
                </a>
                <a href="/inventory/categories" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors">
                    <i class="fas fa-tags"></i>
                    Categorias
                </a>
                <a href="/inventory/stock" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors">
                    <i class="fas fa-warehouse"></i>
                    Estoque
                </a>
                <a href="/inventory/movements" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors">
                    <i class="fas fa-exchange-alt"></i>
                    Movimentações
                </a>
                <a href="/inventory/reports" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-semibold">
                    <i class="fas fa-chart-bar"></i>
                    Relatórios
                </a>
            </nav>
            
            <div class="mt-auto pt-8">
                <a href="/admin" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700 hover:text-gray-900 transition-colors">
                    <i class="fas fa-cog"></i>
                    Administração
                </a>
                <a href="/logout" class="block text-center text-red-600 hover:underline font-semibold mt-4">Sair</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10 flex flex-col gap-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-indigo-800">Relatórios do Inventário</h1>
                <a href="/inventory" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Inventário
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Resumo Geral -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-indigo-500"></i>
                            Resumo Geral
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php 
                        $totalProducts = DatabaseHandler::query("SELECT COUNT(*) as count FROM inventory_products WHERE active = 1")->fetch()['count'];
                        $totalStock = DatabaseHandler::query("SELECT SUM(quantity) as total FROM inventory_stock")->fetch()['total'] ?? 0;
                        $lowStockCount = DatabaseHandler::query("
                            SELECT COUNT(*) as count 
                            FROM inventory_stock s 
                            JOIN inventory_products p ON s.product_id = p.id 
                            WHERE s.quantity <= p.min_stock AND s.quantity > 0
                        ")->fetch()['count'];
                        $outOfStockCount = DatabaseHandler::query("
                            SELECT COUNT(*) as count 
                            FROM inventory_stock s 
                            JOIN inventory_products p ON s.product_id = p.id 
                            WHERE s.quantity = 0
                        ")->fetch()['count'];
                        ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600"><?php echo $totalProducts; ?></div>
                                <div class="text-sm text-gray-600">Total de Produtos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600"><?php echo $totalStock; ?></div>
                                <div class="text-sm text-gray-600">Total em Estoque</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600"><?php echo $lowStockCount; ?></div>
                                <div class="text-sm text-gray-600">Estoque Baixo</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600"><?php echo $outOfStockCount; ?></div>
                                <div class="text-sm text-gray-600">Sem Estoque</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produtos por Categoria -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-green-500"></i>
                            Produtos por Categoria
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php 
                        $categoriesReport = DatabaseHandler::query("
                            SELECT c.name, COUNT(p.id) as product_count 
                            FROM inventory_categories c 
                            LEFT JOIN inventory_products p ON c.id = p.category_id AND p.active = 1 
                            GROUP BY c.id, c.name 
                            ORDER BY product_count DESC
                        ")->fetchAll();
                        ?>
                        <?php if (!empty($categoriesReport)): ?>
                            <div class="space-y-3">
                                <?php foreach ($categoriesReport as $cat): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo $cat['product_count']; ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center">Nenhuma categoria encontrada</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Produtos com Estoque Baixo -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        Produtos com Estoque Baixo
                    </h3>
                </div>
                <div class="p-6">
                    <?php 
                    $lowStockProducts = DatabaseHandler::query("
                        SELECT p.name, p.sku, s.quantity, p.min_stock 
                        FROM inventory_stock s 
                        JOIN inventory_products p ON s.product_id = p.id 
                        WHERE s.quantity <= p.min_stock AND s.quantity > 0 AND p.active = 1 
                        ORDER BY s.quantity ASC
                    ")->fetchAll();
                    ?>
                    <?php if (!empty($lowStockProducts)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque Atual</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque Mínimo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diferença</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($lowStockProducts as $product): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <?php echo htmlspecialchars($product['sku']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <?php echo $product['quantity']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $product['min_stock']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php $diff = $product['min_stock'] - $product['quantity']; ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                -<?php echo $diff; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto com estoque baixo!</h4>
                            <p class="text-gray-500">Todos os produtos estão com estoque adequado.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php ThemeHandler::render_footer(); ?>
</body>
</html> 