<?php

$hostname = 'db4free.net';
$dbname   = 'banco_teste123';
$username = 'bancoteste123';
$password = 'teste1234';

try {
    $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão: ' . $e->getMessage();
}

?>