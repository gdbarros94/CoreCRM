<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Inventário - CoreCRM</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Renderiza o header do tema
    ThemeHandler::render_header(['title' => 'Dashboard do Inventário - CoreCRM']);
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
                <a href="/inventory" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-semibold">
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
                <h1 class="text-2xl font-bold text-indigo-800">Dashboard do Inventário</h1>
                <a href="/inventory/products/new" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-plus me-2"></i>Novo Produto
                </a>
            </div>

            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total de Produtos</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo count($totalProducts); ?></p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-full">
                            <i class="fas fa-boxes text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Em Estoque</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo count($inStock); ?></p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-warehouse text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Estoque Baixo</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo count($lowStock); ?></p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Sem Estoque</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo count($outOfStock); ?></p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
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
                    <?php if (!empty($lowStock)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque Atual</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque Mínimo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach (array_slice($lowStock, 0, 5) as $item): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <?php echo htmlspecialchars($item['sku']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <?php echo $item['quantity']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $item['min_stock']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="/inventory/stock?product_id=<?php echo $item['product_id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit me-1"></i>Ajustar
                                            </a>
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

            <!-- Ações Rápidas -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-bolt text-indigo-500"></i>
                        Ações Rápidas
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="/inventory/products/new" class="flex items-center justify-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                            <i class="fas fa-plus me-2"></i>Novo Produto
                        </a>
                        <a href="/inventory/stock" class="flex items-center justify-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors">
                            <i class="fas fa-warehouse me-2"></i>Gerenciar Estoque
                        </a>
                        <a href="/inventory/movements" class="flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                            <i class="fas fa-exchange-alt me-2"></i>Movimentações
                        </a>
                        <a href="/inventory/reports" class="flex items-center justify-center px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-colors">
                            <i class="fas fa-chart-bar me-2"></i>Relatórios
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php ThemeHandler::render_footer(); ?>
</body>
</html> 