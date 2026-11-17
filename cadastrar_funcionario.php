
<?php
header('Content-Type: application/json; charset=utf-8');

require 'db.php';
require 'auth.php';

exigirGerente();

$dados = json_decode(file_get_contents('php://input'), true) ?? [];

$nome = trim($dados['nome'] ?? '');
$email = trim($dados['email'] ?? '');
$senha = $dados['senha'] ?? '';
$cargo = trim($dados['cargo'] ?? '');

if ($nome === '' || $email === '' || $senha === '' || $cargo === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Preencha todos os campos.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Informe um e-mail válido.']);
    exit;
}

if (strlen($senha) < 8) {
    http_response_code(400);
    echo json_encode(['erro' => 'A senha deve ter pelo menos 8 caracteres.']);
    exit;
}

$cargosPermitidos = ['gerente', 'funcionario'];

if (!in_array($cargo, $cargosPermitidos, true)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Cargo inválido.']);
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id_funcionario FROM funcionario
     WHERE email_funcionario = ? LIMIT 1'
);
$stmt->execute([$email]);

if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['erro' => 'Este e-mail já está cadastrado.']);
    exit;
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    'INSERT INTO funcionario
    (nome_funcionario, email_funcionario, senha_funcionario, cargo)
    VALUES (?, ?, ?, ?)'
);

$stmt->execute([$nome, $email, $senhaHash, $cargo]);

echo json_encode([
    'sucesso' => true,
    'mensagem' => 'Funcionário cadastrado com sucesso!'
]);
