<?php
$host = 'localhost';    //127.0.0.1
$db = 'yegoshin'; //Lesson13(4.2)
$user = 'yegoshin'; //root
$password = 'neto1339'; //null
$charset = 'utf8';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $password);
if ($pdo) {

} else {
    echo "Ошибка!".E_ERROR;
    exit();
}