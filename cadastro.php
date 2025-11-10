<?php
require 'conexao.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $msg = "E-mail já cadastrado.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
        if ($stmt->execute([$email, $senha])) {
            $msg = "Usuário cadastrado com sucesso!";
        } else {
            $msg = "Erro ao cadastrar.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
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
            padding: 30px; border-radius: 10px;
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
        .msg { margin-bottom: 10px; color: green; }
    </style>
</head>
<body>
<div class="container">
    <h2>Cadastro</h2>
    <?php if ($msg): ?><p class="msg"><?= $msg ?></p><?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Cadastrar</button>
    </form>
     </form>
    <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
</div>
</body>
</html>
