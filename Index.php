<?php
require_once 'ConnectDB.php';
require_once 'functions.php';

$currentUser = getCurrentUser();
if (!$currentUser) {
    header("HTTP/1.0 403 Forbidden");
    die("Доступ закрыт !!! Авторизуйтесь!!");
    //redirect('login');
}

if (isset($_POST['assign']) and !empty($_POST['assigned_user_id'])) {
    $params = explode('_', $_POST['assigned_user_id']);
    $sqlAssigned = "UPDATE `task` SET `assigned_user_id` = ? WHERE id = ?";
    $statement = $pdo->prepare($sqlAssigned);
    $statement->execute([$params[0], $params[1]]);
}

if (isset($_POST['description']) and !empty($_POST['description'])) {
    $desc = $_POST['description'];
    $sqlInsert = "INSERT INTO `task` (`user_id`, `assigned_user_id`, `description`, `is_done`, `date_added`) VALUES (?, ?, ?, '0', now())";
    $statement = $pdo->prepare($sqlInsert);
    $statement->execute([$_SESSION['user']['id'], $_SESSION['user']['id'], $desc]);
    redirect('Index');
}

if (isset ($_GET['action']) and !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sqlD = "";
    if ($_GET['action'] == 'done') {
        $sqlD = "UPDATE `task` SET `is_done` = 1 WHERE id = ?";
    } else if ($_GET['action'] == 'delete') {
        $sqlD = "DELETE FROM `task` WHERE id = ?";
    }
    $statement = $pdo->prepare($sqlD);
    $statement->execute([$id]);
    if ($_GET['action'] == 'edit') {
        $sqlDesc = "SELECT * FROM task WHERE id = ? ";
        $statement = $pdo->prepare($sqlDesc);
        $statement->execute([$id]);
        $row1 = $statement->FETCH(PDO::FETCH_ASSOC);
        ?>

        <?php
        if (!isset($_POST['save'])) { ?>
            <div>
                <form method="POST">
                    <input name="Newdescription" placeholder="Описание задачи" value="<?= $row1['description'] ?>"
                           type="text">
                    <input name="save" value="Сохранить" type="submit">
                </form>
            </div>
        <?php } ?>

        <?php
        if (isset($_POST['Newdescription'])) {
            $newDesc = $_POST['Newdescription'];
            $sqlNewDesc = "UPDATE task SET `description` = '$newDesc' WHERE id = ?  ";
            $statement = $pdo->prepare($sqlNewDesc);
            $statement->execute([$id]);
        }
    }
}
?>
<html>
<head>
    <style>
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        table td, table th {
            border: 1px solid #ccc;
            padding: 5px;
        }

        table th {
            background: #eee;
        }
    </style>
</head>
<body>
<h1>Ваш список дел на сегодня,<?= $_SESSION['user']['login'] ?> <a href="logout.php" style="color: darkblue">Выйти</a>
</h1>

<div style="float: left">
    <form method="POST">
        <input name="description" placeholder="Описание задачи" value="" type="text">
        <input name="save" value="Добавить" type="submit">
    </form>
</div>
<div style="float: left; margin-left: 20px;">
    <!--    <form method="POST">-->
    <!--        <label for="sort">Сортировать по:</label>-->
    <!--        <select name="sort_by">-->
    <!--            <option value="date_created">Дате добавления</option>-->
    <!--            <option value="is_done">Статусу</option>-->
    <!--            <option value="description">Описанию</option>-->
    <!--        </select>-->
    <!--        <input name="sort" value="Отсортировать" type="submit">-->
    <!--    </form>-->
</div>
<div style="clear: both"></div>

<table>
    <tbody>
    <tr>
        <th>Описание задачи</th>
        <th>Статус</th>
        <th>Дата добавления</th>
        <th>Действия</th>
        <th>Ответственный</th>
        <th>Автор</th>
        <th>Закрепить задачу за пользователем</th>
    </tr>
    <?php showTasks($pdo); ?>
    </tbody>
</table>
    <p>Также, посмотрите, что от Вас требуют другие люди:</p>
    <table>
        <tbody>
        <tr>
            <th>Описание задачи</th>
            <th>Статус</th>
            <th>Дата добавления</th>
            <th>Действия</th>
            <th>Ответственный</th>
            <th>Автор</th>
        </tr>

        <?php showAssognedTasks($pdo); ?>

</body>
</html>

