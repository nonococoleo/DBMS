<?php
try {
    $pdo = new PDO("mysql:host=118.89.191.235;charset=utf8;dbname=library", "leo", "leo212121");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException$e) {
    die("Error!:" . $e->getMessage() . "<br/>");
}
