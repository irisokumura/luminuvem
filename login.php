<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require 'db.php';

$dados = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($dados['email'] ?? '');
$senha = $dados['senha'] ?? '';

$stmt = $pdo->prepare('SELECT id_funcionario, nome_funcionario, email_funcionario, senha_funcionario, cargo FROM funcionario WHERE email_funcionario = ? LIMIT 1');
$stmt->execute([$email]);
$funcionario = $stmt->fetch();

if (!$funcionario || !password_verify($senha, $funcionario['senha_funcionario'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'E-mail ou senha incorretos.']);
    exit;
}

session_regenerate_id(true);
$_SESSION['id_funcionario'] = (int)$funcionario['id_funcionario'];
$_SESSION['nome_funcionario'] = $funcionario['nome_funcionario'];
$_SESSION['cargo'] = $funcionario['cargo'];
echo json_encode([
    'sucesso' => true,
    'nome' => $funcionario['nome_funcionario'],
    'cargo' => $funcionario['cargo']
]);