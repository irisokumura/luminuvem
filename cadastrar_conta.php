
<?php

header('Content-Type: application/json; charset=utf-8');

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'erro' => 'Método não permitido.'
    ]);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode([
        'erro' => 'Dados inválidos.'
    ]);
    exit;
}

$nome = trim($dados['nome'] ?? '');
$email = trim($dados['email'] ?? '');
$senha = $dados['senha'] ?? '';
$confirmarSenha = $dados['confirmarSenha'] ?? '';

if ($nome === '' || $email === '' || $senha === '' || $confirmarSenha === '') {
    http_response_code(400);
    echo json_encode([
        'erro' => 'Preencha todos os campos.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'erro' => 'Digite um e-mail válido.'
    ]);
    exit;
}

if (strlen($senha) < 8) {
    http_response_code(400);
    echo json_encode([
        'erro' => 'A senha precisa ter pelo menos 8 caracteres.'
    ]);
    exit;
}

if ($senha !== $confirmarSenha) {
    http_response_code(400);
    echo json_encode([
        'erro' => 'As senhas não são iguais.'
    ]);
    exit;
}

try {

    // Verifica se o e-mail já está cadastrado.
    $verificar = $pdo->prepare(
        'SELECT id_funcionario
         FROM funcionario
         WHERE email_funcionario = ?
         LIMIT 1'
    );

    $verificar->execute([$email]);

    if ($verificar->fetch()) {
        http_response_code(409);
        echo json_encode([
            'erro' => 'Este e-mail já está cadastrado.'
        ]);
        exit;
    }

    // Criptografa a senha.
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Toda conta criada por esta tela será de funcionário.
    $inserir = $pdo->prepare(
        "INSERT INTO funcionario
         (nome_funcionario, email_funcionario, senha_funcionario, cargo)
         VALUES (?, ?, ?, 'funcionario')"
    );

    $inserir->execute([
        $nome,
        $email,
        $senhaHash
    ]);

    http_response_code(201);

    echo json_encode([
        'mensagem' => 'Conta criada com sucesso! Faça login para continuar.'
    ]);

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        'erro' => 'Não foi possível criar a conta. Tente novamente.'
    ]);
}
