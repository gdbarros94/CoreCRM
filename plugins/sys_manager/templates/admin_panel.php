<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - CoreCRM</title>
</head>
<body>
    <h2>Painel Admin (Teste)</h2>
    <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_id'] ?? 'Usuário'); ?>!</p>
    <p>Seu papel: <?php echo htmlspecialchars($_SESSION['user_role'] ?? 'N/A'); ?></p>
    <a href="/logout">Sair</a>
</body>
</html>
