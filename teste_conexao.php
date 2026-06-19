<?php

$host = "127.0.0.1";
$porta = "3306";
$banco = "mm_centro_automotivo";
$usuario = "root";
$senha = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Conexão realizada com sucesso!</h1>";
    echo "<p>Banco conectado: <strong>$banco</strong></p>";

} catch (PDOException $erro) {
    echo "<h1>Erro ao conectar</h1>";
    echo "<pre>";
    echo $erro->getMessage();
    echo "</pre>";
}