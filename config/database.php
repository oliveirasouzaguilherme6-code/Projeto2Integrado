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

} catch (PDOException $erro) {
    die("Erro ao conectar com o banco de dados: " . $erro->getMessage());
}