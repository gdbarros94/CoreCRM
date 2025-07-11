<?php
// Script para criar usuário administrador
require_once 'bootstrap.php';

echo "<h2>Criação de Usuário Administrador</h2>";

try {
    $pdo = DatabaseHandler::getConnection();
    
    // Verifica se a tabela users existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ Tabela 'users' não existe!</p>";
        echo "<p>Execute as migrations primeiro.</p>";
        exit;
    }
    
    // Verifica se o usuário admin já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        echo "<p style='color: orange;'>⚠️ Usuário 'admin' já existe!</p>";
    } else {
        // Insere o usuário administrador
        $sql = "INSERT INTO users (nome, email, senha, tipo, username, ativo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $result = $stmt->execute([
            'Administrador',
            'admin@corecrm.com',
            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'admin',
            'admin',
            1
        ]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Usuário administrador criado com sucesso!</p>";
            echo "<p><strong>Credenciais de acesso:</strong></p>";
            echo "<ul>";
            echo "<li><strong>Usuário:</strong> admin</li>";
            echo "<li><strong>Senha:</strong> password</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao criar usuário administrador!</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?> 