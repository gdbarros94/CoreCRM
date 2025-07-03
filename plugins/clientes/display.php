<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Clientes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .controls {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 12px 50px 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            background: white;
        }

        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        .filter-btn, .export-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .filter-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .export-btn {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .export-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #f0f0f0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .table th {
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .table th:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .table th::after {
            content: '↕';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
            font-size: 12px;
        }

        .table td {
            padding: 20px 15px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
            color: #333;
            transition: all 0.3s ease;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .status-inactive {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .status-pending {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-right: 10px;
        }

        .client-info {
            display: flex;
            align-items: center;
        }

        .client-details {
            display: flex;
            flex-direction: column;
        }

        .client-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }

        .client-email {
            color: #666;
            font-size: 12px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
            font-weight: 500;
        }

        .edit-btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .delete-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            padding: 20px;
        }

        .page-btn {
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            background: white;
            color: #667eea;
            border: 2px solid #e0e0e0;
        }

        .page-btn:hover, .page-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-2px);
        }

        .loading {
            display: none;
            text-align: center;
            padding: 40px;
            color: #667eea;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .filter-panel {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            display: none;
            animation: slideDown 0.3s ease;
        }

        .filter-panel.active {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .filter-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .filter-select, .filter-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .filter-select:focus, .filter-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                min-width: auto;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .table {
                min-width: 600px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Clientes</h1>
            <div class="stats">
                <span style="color: #667eea; font-weight: 600;">Total: <span id="totalClients">127</span></span>
            </div>
        </div>

        <div class="controls">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Pesquisar clientes..." id="searchInput">
                <span class="search-icon">🔍</span>
            </div>
            <button class="filter-btn" onclick="toggleFilters()">Filtros</button>
            <button class="export-btn" onclick="exportData()">Exportar</button>
        </div>

        <div class="filter-panel" id="filterPanel">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select class="filter-select" id="statusFilter">
                        <option value="">Todos</option>
                        <option value="active">Ativo</option>
                        <option value="inactive">Inativo</option>
                        <option value="pending">Pendente</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Data de Cadastro</label>
                    <input type="date" class="filter-input" id="dateFilter">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Ordenar por</label>
                    <select class="filter-select" id="sortFilter">
                        <option value="nome">Nome</option>
                        <option value="email">Email</option>
                        <option value="criado_em">Data de Cadastro</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Carregando clientes...</p>
            </div>
            
            <table class="table" id="clientsTable">
                <thead>
                    <tr>
                        <th onclick="sortTable('nome')">Cliente</th>
                        <th onclick="sortTable('telefone')">Telefone</th>
                        <th onclick="sortTable('status')">Status</th>
                        <th onclick="sortTable('criado_em')">Cadastrado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="clientsTableBody">
                    <!-- Dados de exemplo -->
                    <tr onclick="viewClient('uuid-1')">
                        <td>
                            <div class="client-info">
                                <div class="avatar">JD</div>
                                <div class="client-details">
                                    <div class="client-name">João da Silva</div>
                                    <div class="client-email">joao@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td>(11) 99999-9999</td>
                        <td><span class="status-badge status-active">Ativo</span></td>
                        <td>15/06/2024</td>
                        <td>
                            <div class="actions">
                                <button class="action-btn edit-btn" onclick="editClient('uuid-1', event)">Editar</button>
                                <button class="action-btn delete-btn" onclick="deleteClient('uuid-1', event)">Excluir</button>
                            </div>
                        </td>
                    </tr>
                    <tr onclick="viewClient('uuid-2')">
                        <td>
                            <div class="client-info">
                                <div class="avatar">MS</div>
                                <div class="client-details">
                                    <div class="client-name">Maria Santos</div>
                                    <div class="client-email">maria@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td>(11) 88888-8888</td>
                        <td><span class="status-badge status-pending">Pendente</span></td>
                        <td>14/06/2024</td>
                        <td>
                            <div class="actions">
                                <button class="action-btn edit-btn" onclick="editClient('uuid-2', event)">Editar</button>
                                <button class="action-btn delete-btn" onclick="deleteClient('uuid-2', event)">Excluir</button>
                            </div>
                        </td>
                    </tr>
                    <tr onclick="viewClient('uuid-3')">
                        <td>
                            <div class="client-info">
                                <div class="avatar">PO</div>
                                <div class="client-details">
                                    <div class="client-name">Pedro Oliveira</div>
                                    <div class="client-email">pedro@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td>(11) 77777-7777</td>
                        <td><span class="status-badge status-inactive">Inativo</span></td>
                        <td>13/06/2024</td>
                        <td>
                            <div class="actions">
                                <button class="action-btn edit-btn" onclick="editClient('uuid-3', event)">Editar</button>
                                <button class="action-btn delete-btn" onclick="deleteClient('uuid-3', event)">Excluir</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button class="page-btn" onclick="changePage(1)">« Primeira</button>
            <button class="page-btn" onclick="changePage('prev')">‹ Anterior</button>
            <button class="page-btn active" onclick="changePage(1)">1</button>
            <button class="page-btn" onclick="changePage(2)">2</button>
            <button class="page-btn" onclick="changePage(3)">3</button>
            <button class="page-btn" onclick="changePage('next')">Próxima ›</button>
            <button class="page-btn" onclick="changePage('last')">Última »</button>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let sortColumn = 'nome';
        let sortDirection = 'asc';

        function toggleFilters() {
            const panel = document.getElementById('filterPanel');
            panel.classList.toggle('active');
        }

        function sortTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }
            
            // Aqui você faria a requisição AJAX para o PHP
            console.log(`Ordenando por ${column} (${sortDirection})`);
            showLoading();
            
            // Simula carregamento
            setTimeout(() => {
                hideLoading();
            }, 1000);
        }

        function showLoading() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('clientsTableBody').style.display = 'none';
        }

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('clientsTableBody').style.display = 'table-row-group';
        }

        function changePage(page) {
            // Aqui você faria a requisição AJAX para o PHP
            console.log(`Mudando para página ${page}`);
            showLoading();
            
            // Remove active class from all buttons
            document.querySelectorAll('.page-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            setTimeout(() => {
                hideLoading();
            }, 1000);
        }

        function viewClient(clientId) {
            console.log(`Visualizando cliente ${clientId}`);
            // Aqui você redirecionaria para a página de detalhes do cliente
        }

        function editClient(clientId, event) {
            event.stopPropagation();
            console.log(`Editando cliente ${clientId}`);
            // Aqui você abriria um modal ou redirecionaria para edição
        }

        function deleteClient(clientId, event) {
            event.stopPropagation();
            if (confirm('Tem certeza que deseja excluir este cliente?')) {
                console.log(`Excluindo cliente ${clientId}`);
                // Aqui você faria a requisição AJAX para excluir
            }
        }

        function exportData() {
            console.log('Exportando dados...');
            // Aqui você faria a requisição para exportar os dados
        }

        // Busca em tempo real
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value;
            console.log(`Buscando por: ${searchTerm}`);
            
            if (searchTerm.length > 2 || searchTerm.length === 0) {
                // Aqui você faria a requisição AJAX para buscar
                showLoading();
                setTimeout(() => {
                    hideLoading();
                }, 500);
            }
        });

        // Filtros
        document.getElementById('statusFilter').addEventListener('change', function(e) {
            console.log(`Filtrando por status: ${e.target.value}`);
            // Aqui você aplicaria o filtro via AJAX
        });

        document.getElementById('dateFilter').addEventListener('change', function(e) {
            console.log(`Filtrando por data: ${e.target.value}`);
            // Aqui você aplicaria o filtro via AJAX
        });

        document.getElementById('sortFilter').addEventListener('change', function(e) {
            console.log(`Ordenando por: ${e.target.value}`);
            // Aqui você aplicaria a ordenação via AJAX
        });
    </script>
</body>
</html>