<?php
require_once 'functions.php';
require_once 'ConnectDB.php';

//Проверка на РЕГИСТРАЦИЮ
if (isset($_POST['register']) and (!empty($_POST['login']) or !empty($_POST['password']))) {
    if (checkUser(getParam('login'),getParam('password'), $pdo) and checkLogin(getParam('login'),$pdo)) {
        if (register(getParam('login'), getParam('password'), $pdo)) {
            //redirect('Index');
            echo '<h2>Вы зарегестрированны, можете зайти под своими логином и паролем.</h2>';
        } else {
            echo '<h2>ошибка добавления</h2>';
        }
    } else {
        echo '<h2>Такой пользователь уже есть в базе. </h2>';
    }
} elseif (isset($_POST['register']) and empty($_POST['login']) and empty($_POST['password'])) {
    echo '<h2>Ошибка регистрации. Введите все необхдоимые данные.</h2>';
}

//Проверка на ВХОД
if (isset($_POST['sign_in']) and !empty($_POST['login']) and !empty($_POST['password'])) {
    if (login(getParam('login'), getParam('password'), $pdo)) {
        redirect('Index');
    } else {
        echo '<h2>Такого пользователя нет в базе! Либо неверно введены данные.</h2>';
    }
} else if (isset($_POST['sign_in']) and empty($_POST['login']) and empty($_POST['password'])) {
    echo '<h2>Ошибка входа. Введите все необхдоимые данные.</h2>';
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>login</title>
</head>
<body>
<div>
    <p>
    <h2>Зарегестрируйтесь или войдите под своими логин/пароль</h2></p>
    <form method="POST">
        <input type="text" name="login" placeholder="Логин">
        <input type="password" name="password" placeholder="Пароль">
        <input type="submit" name="sign_in" value="Вход">
        <input type="submit" name="register" value="Регистрация">
    </form>
</div>
</body>
</html>
