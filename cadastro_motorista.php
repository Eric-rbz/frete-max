<?php
include 'conexao.php'; 

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $cnh = $_POST['cnh'];
    $categoria = $_POST['categoria'];

    
    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $mensagem = "Este e-mail já está cadastrado!";
    } else {

        
        $sql1 = $conn->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
        $sql1->bind_param("ss", $email, $senha);

        if ($sql1->execute()) {
            $usuario_id = $sql1->insert_id;

           
            $sql2 = $conn->prepare("INSERT INTO motoristas (usuario_id, nome, telefone, cnh, categoria)
                                    VALUES (?, ?, ?, ?, ?)");
            $sql2->bind_param("issss", $usuario_id, $nome, $telefone, $cnh, $categoria);

            if ($sql2->execute()) {
                $mensagem = "Motorista cadastrado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar dados do motorista.";
            }
        } else {
            $mensagem = "Erro ao criar usuário.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Motorista</title>
</head>
<body>

<h2>Cadastro de Motorista</h2>

<?php if ($mensagem != "") { echo "<p><strong>$mensagem</strong></p>"; } ?>

<form method="POST" action="">
    <label>Nome:</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Telefone:</label><br>
    <input type="text" name="telefone" required><br><br>

    <label>CNH:</label><br>
    <input type="text" name="cnh" required><br><br>

    <label>Categoria da CNH:</label><br>
    <input type="text" name="categoria" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Senha:</label><br>
    <input type="password" name="senha" required><br><br>

    <button type="submit">Cadastrar Motorista</button>
</form>

</body>
</html>
