<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimentações - CoreCRM</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Renderiza o header do tema
    ThemeHandler::render_header(['title' => 'Movimentações - CoreCRM']);
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
                <a href="/inventory/movements" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-indigo-100 text-indigo-700 font-semibold">
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
                <h1 class="text-2xl font-bold text-indigo-800">Movimentações de Estoque</h1>
                <a href="/inventory" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Inventário
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <?php if (!empty($movements)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($movements as $movement): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('d/m/Y H:i', strtotime($movement['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($movement['product_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <?php echo htmlspecialchars($movement['sku']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php 
                                            $typeClass = 'bg-gray-100 text-gray-800';
                                            $typeText = 'Ajuste';
                                            if ($movement['movement_type'] == 'IN') {
                                                $typeClass = 'bg-green-100 text-green-800';
                                                $typeText = 'Entrada';
                                            } elseif ($movement['movement_type'] == 'OUT') {
                                                $typeClass = 'bg-red-100 text-red-800';
                                                $typeText = 'Saída';
                                            }
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $typeClass; ?>">
                                                <?php echo $typeText; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo $movement['quantity']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?php echo htmlspecialchars($movement['reason'] ?? ''); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($movement['user_id'] ?? 'Sistema'); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-exchange-alt text-gray-400 text-4xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Nenhuma movimentação registrada</h4>
                            <p class="text-gray-500">As movimentações de estoque aparecerão aqui.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php ThemeHandler::render_footer(); ?>
</body>
</html> 