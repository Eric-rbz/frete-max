<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




header("Content-Type: application/json; charset=utf-8");

// Inicia buffer para capturar qualquer saída que quebre o JSON
ob_start();

// Conexão PDO
require_once "conexao.php";

try {
    // Inicia a transação
    $pdo->beginTransaction();

    // Recebe dados enviados pelo formulário
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;
    $nome = $_POST['nome'] ?? null;
    $telefone = $_POST['telefone'] ?? null;
    $cnh = $_POST['cnh'] ?? null;
    $categoria = $_POST['categoria'] ?? null;

    if (!$email || !$senha) {
        $debug = ob_get_clean();
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Email e senha são obrigatórios.",
            "debug" => $debug
        ]);
        exit;
    }

    // Verifica se o usuário já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Usuário já existe → usa o ID existente
        $usuario_id = $usuario["id"];
    } else {
        // Cria usuário novo
        $stmt = $pdo->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
        $stmt->execute([$email, $senha]);
        $usuario_id = $pdo->lastInsertId();
    }

    // Cadastra motorista vinculado ao usuario_id
    $stmt = $pdo->prepare("
        INSERT INTO motoristas (usuario_id, nome, telefone, cnh, categoria)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$usuario_id, $nome, $telefone, $cnh, $categoria]);

    // Finaliza a transação
    $pdo->commit();

    // Pega qualquer output inesperado
    $debug = ob_get_clean();

    echo json_encode([
        "status" => "sucesso",
        "mensagem" => "Motorista cadastrado com sucesso!",
        "debug" => $debug
    ]);
    exit;

} catch (Exception $e) {

    // Cancela transação em caso de erro
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $debug = ob_get_clean();

    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro ao salvar motorista: " . $e->getMessage(),
        "debug" => $debug
    ]);
    exit;
}
?>
