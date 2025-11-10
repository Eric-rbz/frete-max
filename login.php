<?php
require 'conexao.php';
session_start();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = $usuario['email'];
        header('Location: dashboard.php');
        exit;
    } else {
        $msg = "E-mail ou senha inválidos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        h2 { color: #0205a5; }
        body {
            background-color: #000;
            font-family: Arial;
            display: flex; justify-content: center; align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 400px; text-align: center;
        }
        input, button {
            width: 100%; padding: 10px; margin: 8px 0;
            border-radius: 5px; border: 1px solid #ccc;
        }
        button {
            background-color: #0205a5; color: white; border: none; cursor: pointer;
        }
        button:hover { background-color: #2a2dff; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if ($msg): ?><p class="error"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Entrar</button>
    </form>
    <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
</div>
</body>
</html>
