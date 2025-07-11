<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - CoreCRM</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Renderiza o header do tema
    ThemeHandler::render_header(['title' => 'Produtos - CoreCRM']);
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
                <a href="/inventory/products" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-semibold">
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
                <a href="/inventory/reports" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors">
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
                <h1 class="text-2xl font-bold text-indigo-800">Produtos</h1>
                <a href="/inventory/products/new" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-plus me-2"></i>Novo Produto
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <?php if (!empty($products)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Unitário</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($products as $product): ?>
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
                                            <?php echo htmlspecialchars($product['category_name'] ?? 'Sem categoria'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            R$ <?php echo number_format($product['unit_price'], 2, ',', '.'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php 
                                            // Buscar estoque do produto
                                            $stockQuery = "SELECT quantity FROM inventory_stock WHERE product_id = ?";
                                            $stockResult = DatabaseHandler::query($stockQuery, [$product['id']])->fetch();
                                            $stockQuantity = $stockResult['quantity'] ?? 0;
                                            $stockClass = $stockQuantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $stockClass; ?>">
                                                <?php echo $stockQuantity; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php if ($product['active']): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Ativo
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Inativo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="/inventory/products/edit?id=<?php echo $product['id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/inventory/stock?product_id=<?php echo $product['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-warehouse"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto cadastrado</h4>
                            <p class="text-gray-500 mb-4">Comece cadastrando seu primeiro produto.</p>
                            <a href="/inventory/products/new" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                                <i class="fas fa-plus me-2"></i>Cadastrar Produto
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php ThemeHandler::render_footer(); ?>
</body>
</html> 