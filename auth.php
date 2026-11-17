<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
function exigirLogin(): void {
    if (empty($_SESSION['id_funcionario'])) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['erro' => 'Faça login para continuar.']);
        exit;
    }
}

function exigirGerente(): void {
    exigirLogin();

    if (($_SESSION['cargo'] ?? '') !== 'gerente') {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'erro' => 'Apenas gerentes podem cadastrar funcionários.'
        ]);

        exit;
    }
}