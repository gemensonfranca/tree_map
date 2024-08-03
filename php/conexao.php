<?php

$hostname = '127.0.0.1';
$dbname   = 'treemap';
$username = 'root';
$password = 'Mysql2023@root';

try {
    $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão: ' . $e->getMessage();
}

?>