<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? 'Editar' : 'Novo'; ?> Produto - CoreCRM</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Renderiza o header do tema
    ThemeHandler::render_header(['title' => ($product ? 'Editar' : 'Novo') . ' Produto - CoreCRM']);
    ?>
    
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-indigo-800"><?php echo $product ? 'Editar' : 'Novo'; ?> Produto</h1>
                <a href="/inventory/products" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <form method="POST" action="<?php echo $product ? '/inventory/products/edit' : '/inventory/products/new'; ?>">
                        <?php if ($product): ?>
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Produto *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       id="name" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                            </div>
                            
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                      id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                        id="category_id" name="category_id">
                                    <option value="">Selecione uma categoria</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo ($product['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">Preço Unitário</label>
                                <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       id="unit_price" name="unit_price" value="<?php echo $product['unit_price'] ?? '0.00'; ?>">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">Preço de Custo</label>
                                <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       id="cost_price" name="cost_price" value="<?php echo $product['cost_price'] ?? '0.00'; ?>">
                            </div>
                            
                            <div>
                                <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-2">Estoque Mínimo</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       id="min_stock" name="min_stock" value="<?php echo $product['min_stock'] ?? '0'; ?>">
                            </div>
                            
                            <div>
                                <label for="max_stock" class="block text-sm font-medium text-gray-700 mb-2">Estoque Máximo</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       id="max_stock" name="max_stock" value="<?php echo $product['max_stock'] ?? '0'; ?>">
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-4 mt-8">
                            <a href="/inventory/products" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                                <i class="fas fa-save me-2"></i>Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php ThemeHandler::render_footer(); ?>
</body>
</html> 