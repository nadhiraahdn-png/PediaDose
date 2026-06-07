<?php

$host = "sql103.infinityfree.com";
$db   = "if0_41955491_pediadose";
$user = "if0_41955491";
$pass = "PediaDoseXX";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die($e->getMessage());
}