<?php
// Script para testar conexão com banco de dados
require_once 'bootstrap.php';

echo "<h2>Teste de Conexão com Banco de Dados</h2>";

try {
    // Tenta conectar ao banco
    $pdo = DatabaseHandler::getConnection();
    echo "<p style='color: green;'>✅ Conexão com banco de dados estabelecida com sucesso!</p>";
    
    // Verifica se a tabela users existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Tabela 'users' encontrada!</p>";
        
        // Conta usuários existentes
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        echo "<p>📊 Usuários cadastrados: {$count}</p>";
        
        if ($count == 0) {
            echo "<p style='color: orange;'>⚠️ Nenhum usuário encontrado. Você pode inserir o usuário admin.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tabela 'users' não encontrada!</p>";
        echo "<p>Você precisa executar as migrations primeiro.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na conexão: " . $e->getMessage() . "</p>";
    echo "<p><strong>Verifique:</strong></p>";
    echo "<ul>";
    echo "<li>Se o MySQL está rodando</li>";
    echo "<li>Se as credenciais em config/database.config.php estão corretas</li>";
    echo "<li>Se o banco de dados 'crm_alunostds_dev_br' existe</li>";
    echo "<li>Se o usuário 'crm_alunostds_dev_br' tem permissões</li>";
    echo "</ul>";
}
?> 