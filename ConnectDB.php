<?php
$host = 'localhost';    //127.0.0.1
$db = 'SelectOfSeveralTables(4.3)'; //Lesson13(4.2)
$user = 'root'; //root
$password = null; //null
$charset = 'utf8';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $password);
if ($pdo) {

} else {
    echo "Ошибка!".E_ERROR;
    exit();
}