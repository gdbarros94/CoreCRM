<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - CoreCRM</title>
</head>
<body>
    <h2>Login de Teste</h2>
    <form method="post" action="/login">
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect ?? '/admin'); ?>">
        <label for="user">Usuário:</label>
        <input type="text" id="user" name="user" required><br><br>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Entrar</button>
    </form>
    <?php if (!empty($error)) { echo '<p style="color:red">' . htmlspecialchars($error) . '</p>'; } ?>
    <!-- TODO: Adicionar proteção CSRF e limitação de tentativas para produção -->
</body>
</html>
