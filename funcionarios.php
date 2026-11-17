
<?php
header('Content-Type: application/json; charset=utf-8');

require 'db.php';
require 'auth.php';

exigirGerente();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

try {
    $stmt = $pdo->query(
        'SELECT id_funcionario, nome_funcionario, email_funcionario, cargo
         FROM funcionario
         ORDER BY nome_funcionario ASC'
    );

    $funcionarios = $stmt->fetchAll();

    echo json_encode($funcionarios);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao consultar funcionários.'
    ]);
}
