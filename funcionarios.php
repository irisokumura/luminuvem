
<?php
header('Content-Type: application/json; charset=utf-8');

require 'db.php';
require 'auth.php';

exigirGerente();

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    try {
        $stmt = $pdo->query(
            'SELECT id_funcionario, nome_funcionario,
                    email_funcionario, cargo
             FROM funcionario
             ORDER BY nome_funcionario ASC'
        );

        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao consultar funcionários.']);
    }
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true) ?? [];

if ($metodo === 'PUT') {
    $id = (int)($dados['id_funcionario'] ?? 0);
    $nome = trim($dados['nome'] ?? '');
    $email = trim($dados['email'] ?? '');
    $cargo = trim($dados['cargo'] ?? '');
    $senha = $dados['senha'] ?? '';

    if ($id < 1 || $nome === '' || $email === '' || $cargo === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'Preencha todos os campos obrigatórios.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Informe um e-mail válido.']);
        exit;
    }

    if (!in_array($cargo, ['gerente', 'funcionario'], true)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Cargo inválido.']);
        exit;
    }

    if ($senha !== '' && strlen($senha) < 8) {
        http_response_code(400);
        echo json_encode(['erro' => 'A senha deve ter pelo menos 8 caracteres.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare(
            'SELECT id_funcionario FROM funcionario
             WHERE email_funcionario = ? AND id_funcionario <> ?
             LIMIT 1'
        );
        $stmt->execute([$email, $id]);

        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['erro' => 'Este e-mail já está cadastrado.']);
            exit;
        }

        if ($senha !== '') {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'UPDATE funcionario
                 SET nome_funcionario = ?, email_funcionario = ?,
                     cargo = ?, senha_funcionario = ?
                 WHERE id_funcionario = ?'
            );

            $stmt->execute([$nome, $email, $cargo, $senhaHash, $id]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE funcionario
                 SET nome_funcionario = ?, email_funcionario = ?, cargo = ?
                 WHERE id_funcionario = ?'
            );

            $stmt->execute([$nome, $email, $cargo, $id]);
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Funcionário atualizado com sucesso!'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível atualizar o funcionário.']);
    }
    exit;
}

if ($metodo === 'DELETE') {
    $id = (int)($dados['id_funcionario'] ?? 0);

    if ($id < 1) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do funcionário inválido.']);
        exit;
    }

    if ($id === (int)$_SESSION['id_funcionario']) {
        http_response_code(403);
        echo json_encode(['erro' => 'Você não pode excluir sua própria conta.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare(
            'DELETE FROM funcionario WHERE id_funcionario = ?'
        );
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['erro' => 'Funcionário não encontrado.']);
            exit;
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Funcionário excluído com sucesso!'
        ]);
    } catch (PDOException $e) {
        http_response_code(409);
        echo json_encode([
            'erro' => 'Não foi possível excluir este funcionário. Verifique se existem registros vinculados a ele.'
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode([
    'erro' => 'Método não permitido.',
    'metodo_recebido' => $_SERVER['REQUEST_METHOD']
]);