<?php

$host = "127.0.0.1";
$porta = "3306";
$usuario = "root";
$senha = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $bancos = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);

    echo "<h2>Bancos encontrados pelo PHP:</h2>";
    echo "<pre>";
    print_r($bancos);
    echo "</pre>";

} catch (PDOException $erro) {
    echo "Erro na conexão: " . $erro->getMessage();
}