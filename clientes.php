<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth.php';
exigirLogin();
require 'db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $stmt = $pdo->query('SELECT id_cliente, nome_cliente, cpf_cliente, telefone_cliente, email_cliente, endereco_cliente, id_funcionario FROM cliente ORDER BY id_cliente DESC');
    echo json_encode($stmt->fetchAll());
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true) ?? [];

if ($metodo === 'POST' || $metodo === 'PUT') {
    $id = (int)($dados['id_cliente'] ?? 0);
    $nome = trim($dados['nome_cliente'] ?? '');
    $cpf = preg_replace('/\D/', '', $dados['cpf_cliente'] ?? '');
    $telefone = preg_replace('/\D/', '', $dados['telefone_cliente'] ?? '');
    $email = trim($dados['email_cliente'] ?? '');
    $endereco = trim($dados['endereco_cliente'] ?? '');

    if ($nome === '' || strlen($cpf) !== 11 || strlen($telefone) < 10 || strlen($telefone) > 11 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) || $endereco === '' || strlen($endereco) > 150) {
        http_response_code(422);
        echo json_encode(['erro' => 'Confira os campos. CPF deve ter 11 números; telefone, 10 ou 11; endereço, até 150 caracteres.']);
        exit;
    }

    try {
        if ($metodo === 'POST') {
            $stmt = $pdo->prepare('INSERT INTO cliente (nome_cliente, cpf_cliente, telefone_cliente, email_cliente, endereco_cliente, id_funcionario) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nome, $cpf, $telefone, $email, $endereco, $_SESSION['id_funcionario']]);
            echo json_encode(['sucesso' => true, 'id_cliente' => (int)$pdo->lastInsertId()]);
        } else {
            if ($id < 1) {
                http_response_code(400);
                echo json_encode(['erro' => 'ID do cliente inválido.']);
                exit;
            }
            $stmt = $pdo->prepare('UPDATE cliente SET nome_cliente=?, cpf_cliente=?, telefone_cliente=?, email_cliente=?, endereco_cliente=? WHERE id_cliente=?');
            $stmt->execute([$nome, $cpf, $telefone, $email, $endereco, $id]);
            echo json_encode(['sucesso' => true]);
        }
    } catch (PDOException $e) {
        http_response_code(409);
        echo json_encode(['erro' => 'Não foi possível salvar. Verifique se o CPF já está cadastrado.']);
    }
    exit;
}

if ($metodo === 'DELETE') {
    $id = (int)($dados['id_cliente'] ?? 0);
    if ($id < 1) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do cliente inválido.']);
        exit;
    }
    $stmt = $pdo->prepare('DELETE FROM cliente WHERE id_cliente = ?');
    $stmt->execute([$id]);
    echo json_encode(['sucesso' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);
