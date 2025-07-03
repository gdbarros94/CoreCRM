<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRM - Clientes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen p-5 font-sans">
  <div class="max-w-7xl mx-auto bg-white bg-opacity-95 backdrop-blur-lg rounded-2xl p-8 shadow-2xl">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center border-b pb-5 mb-6">
      <h1 class="text-4xl font-extrabold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent">Clientes</h1>
      <span class="text-indigo-600 font-semibold mt-4 md:mt-0">Total: <span id="totalClients">127</span></span>
    </div>

    <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
      <div class="relative w-full md:w-1/2">
        <input id="searchInput" type="text" placeholder="Pesquisar clientes..." class="w-full px-5 py-3 pr-12 rounded-full border-2 border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-400" />
        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
      </div>
      <button onclick="toggleFilters()" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg">Filtros</button>
      <button onclick="exportData()" class="border-2 border-indigo-500 text-indigo-500 px-6 py-3 rounded-xl font-semibold hover:bg-indigo-500 hover:text-white transition">Exportar</button>
    </div>

    <div id="filterPanel" class="hidden bg-white rounded-xl p-6 mb-6 shadow-lg animate-fade-in">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <label class="block font-semibold mb-2">Status</label>
          <select id="statusFilter" class="w-full p-3 border-2 border-gray-200 rounded-xl">
            <option value="">Todos</option>
            <option value="active">Ativo</option>
            <option value="inactive">Inativo</option>
            <option value="pending">Pendente</option>
          </select>
        </div>
        <div class="flex-1">
          <label class="block font-semibold mb-2">Data de Cadastro</label>
          <input type="date" id="dateFilter" class="w-full p-3 border-2 border-gray-200 rounded-xl" />
        </div>
        <div class="flex-1">
          <label class="block font-semibold mb-2">Ordenar por</label>
          <select id="sortFilter" class="w-full p-3 border-2 border-gray-200 rounded-xl">
            <option value="nome">Nome</option>
            <option value="email">Email</option>
            <option value="criado_em">Data de Cadastro</option>
          </select>
        </div>
      </div>
    </div>

    <div class="relative overflow-x-auto rounded-xl shadow-lg">
      <div id="loading" class="hidden text-center p-10 text-indigo-500">
        <div class="w-10 h-10 border-4 border-gray-200 border-t-indigo-500 rounded-full animate-spin mx-auto mb-3"></div>
        Carregando clientes...
      </div>

      <table id="clientsTable" class="min-w-full bg-white">
        <thead class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
          <tr>
            <th class="py-4 px-6 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer">Cliente</th>
            <th class="py-4 px-6 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer">Telefone</th>
            <th class="py-4 px-6 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer">Status</th>
            <th class="py-4 px-6 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer">Cadastrado em</th>
            <th class="py-4 px-6 text-left text-xs font-semibold uppercase tracking-wider">Ações</th>
          </tr>
        </thead>
        <tbody id="clientsTableBody" class="text-gray-700 divide-y">
          <!-- Exemplo de linha -->
          <tr onclick="viewClient('uuid-1')" class="hover:bg-indigo-50">
            <td class="py-4 px-6">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-semibold">JD</div>
                <div>
                  <div class="font-semibold">João da Silva</div>
                  <div class="text-sm text-gray-500">joao@email.com</div>
                </div>
              </div>
            </td>
            <td class="py-4 px-6">(11) 99999-9999</td>
            <td class="py-4 px-6">
              <span class="px-3 py-1 rounded-full text-white text-xs font-semibold bg-green-500">Ativo</span>
            </td>
            <td class="py-4 px-6">15/06/2024</td>
            <td class="py-4 px-6">
              <div class="flex gap-2">
                <button onclick="editClient('uuid-1', event)" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium">Editar</button>
                <button onclick="deleteClient('uuid-1', event)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">Excluir</button>
              </div>
            </td>
          </tr>
          <!-- Outras linhas seriam preenchidas dinamicamente -->
        </tbody>
      </table>
    </div>

    <div class="flex justify-center items-center gap-2 mt-8">
      <button onclick="changePage(1)" class="px-4 py-2 rounded-md border border-gray-300 text-indigo-500 hover:bg-indigo-500 hover:text-white transition">« Primeira</button>
      <button onclick="changePage('prev')" class="px-4 py-2 rounded-md border border-gray-300 text-indigo-500 hover:bg-indigo-500 hover:text-white transition">‹ Anterior</button>
      <button onclick="changePage(1)" class="px-4 py-2 rounded-md border border-indigo-500 bg-indigo-500 text-white font-semibold">1</button>
      <button onclick="changePage(2)" class="px-4 py-2 rounded-md border border-gray-300 text-indigo-500 hover:bg-indigo-500 hover:text-white transition">2</button>
      <button onclick="changePage('next')" class="px-4 py-2 rounded-md border border-gray-300 text-indigo-500 hover:bg-indigo-500 hover:text-white transition">Próxima ›</button>
      <button onclick="changePage('last')" class="px-4 py-2 rounded-md border border-gray-300 text-indigo-500 hover:bg-indigo-500 hover:text-white transition">Última »</button>
    </div>
  </div>

  <script>
    function toggleFilters() {
      document.getElementById('filterPanel').classList.toggle('hidden');
    }
    function viewClient(id) {
      console.log('Visualizando cliente: ' + id);
    }
    function editClient(id, event) {
      event.stopPropagation();
      console.log('Editando cliente: ' + id);
    }
    function deleteClient(id, event) {
      event.stopPropagation();
      if (confirm('Tem certeza que deseja excluir este cliente?')) {
        console.log('Excluindo cliente: ' + id);
      }
    }
    function exportData() {
      console.log('Exportando dados...');
    }
    function changePage(page) {
      console.log('Mudando para a página: ' + page);
    }
  </script>
</body>
</html>
