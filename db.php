<?php
// Conexão com o MySQL do XAMPP.
$host = 'localhost';
$dbname = 'luminuvem';
$user = 'root';
$password = ''; // Padrão do XAMPP. Altere se você definiu uma senha para o MySQL.

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Erro ao conectar ao banco. Confira se o MySQL está ligado e os dados em db.php.');
}
