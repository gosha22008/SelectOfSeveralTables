<?php
session_start();
require_once 'ConnectDB.php';

function login($login, $password, $pdo)
{
    if (!checkUser($login, $password, $pdo)) {
        $user = getUser($login, $password, $pdo);
        $_SESSION['user'] = $user;
        return true;
    } else {
        return false;
    }
}

function getCurrentUser()
{
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function getParam($name)
{
    return isset ($_REQUEST[$name]) ? $_REQUEST[$name] : null;
}

function checkLogin($login, $pdo)
{
    $i = 0;
    $sqlCheckUser = "SELECT * FROM `user` WHERE `login`= ?";
    $statement = $pdo->prepare($sqlCheckUser);
    $statement->execute([$login]);
    while ($row = $statement->FETCH(PDO::FETCH_ASSOC)) {
        $i++;
    }
    if ($i == 0) {
        return true;
    } else {
        return false;
    }
}

function checkUser($login,$password, $pdo)
{
    $i = 0;
    $sqlCheckUser = "SELECT * FROM `user` WHERE `login`= ? and `password`= ?";
    $statement = $pdo->prepare($sqlCheckUser);
    $statement->execute([$login,$password]);
    while ($row = $statement->FETCH(PDO::FETCH_ASSOC)) {
        $i++;
    }
    if ($i == 0) {
        return true;
    } else {
        return false;
    }
}

function register($login, $password, $pdo)
{
    $sqlAddNewUser = "INSERT INTO  `user` (`id`,`login`,`password`) VALUES ('', ?, ?)";
    $statement = $pdo->prepare($sqlAddNewUser);
    return $statement->execute([$login, $password]);
}

function getNamesUsers($pdo)
{
    $sqlGetNamesUsers = "SELECT `login` FROM `user`  ";
    $statement = $pdo->prepare($sqlGetNamesUsers);
    $statement->execute();
    while ($row = $statement->FETCH(PDO::FETCH_ASSOC)) {

        if ($row['login'] == $_SESSION['user']['login']) {
            continue;
        }
        $names[] = $row;
    }
    return $names;
}

function getUser($login,$password,$pdo)
{
    $sqlGetUser = "SELECT * FROM `user` WHERE `login`= ? and `password`= ?";
    $statement = $pdo->prepare($sqlGetUser);
    $statement->execute([$login,$password]);
    $row = $statement->FETCH(PDO::FETCH_ASSOC);
    return $row;
}

function getUserId($login,$pdo)
{
    $sqlGetUserId = "SELECT * FROM `user` WHERE `login`= ? ";
    $statement = $pdo->prepare($sqlGetUserId);
    $statement->execute([$login]);
    $row = $statement->FETCH(PDO::FETCH_ASSOC);
    return $row['id'];
}

function logout()
{
    session_destroy();
    redirect('login');
}

function redirect($action)
{
    header('Location: ' . $action . '.php');
    die;
}

function getAssignedId($id,$pdo)
{
    $sqlAssId = "SELECT assigned_user_id FROM task WHERE `id`= ?";
    $statement = $pdo->prepare($sqlAssId);
    $statement->execute([$id]);
    $row = $statement->FETCH(PDO::FETCH_ASSOC);
    return $row['assigned_user_id'];
}

function getAssignedLogin($id, $pdo)
{

    $sqlAssId = "SELECT * FROM task WHERE `id`= ?";
    $statement = $pdo->prepare($sqlAssId);
    $statement->execute([$id]);
    $row = $statement->FETCH(PDO::FETCH_ASSOC);
    $assigned = $row['assigned_user_id'];
    //return $row['assigned_user_id'];
    $sqlAssLogin = "SELECT * FROM `user` WHERE `id`= ?";
    $statement = $pdo->prepare($sqlAssLogin);
    $statement->execute([$assigned]);
    $roww = $statement->FETCH(PDO::FETCH_ASSOC);
    return $roww['login'];
}

function getAuthorId()
{

}

function getAuthorLogin($id, $pdo)
{
    $sqlAssId = "SELECT * FROM task WHERE `id`= ?";
    $statement = $pdo->prepare($sqlAssId);
    $statement->execute([$id]);
    $row = $statement->FETCH(PDO::FETCH_ASSOC);
    //return $row['assigned_user_id'];
    $sqlAssLogin = "SELECT * FROM `user` WHERE `id`= ?";
    $statement = $pdo->prepare($sqlAssLogin);
    $statement->execute([$row['user_id']]);
    $roww = $statement->FETCH(PDO::FETCH_ASSOC);
    return $roww['login'];
}

function showTasks($pdo)
{
    $sql = "SELECT * FROM `task` WHERE `user_id` = ? ";
    $statement = $pdo->prepare($sql);
    $statement->execute([$_SESSION['user']['id']]);
    while ($row = $statement->FETCH(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?= $row['description'] ?> </td>
            <?php if ($row['is_done'] == 0) {
                $done = 'В процессе';
                $color = 'orange';
            } else if ($row['is_done'] == 1) {
                $done = 'Выполнено';
                $color = 'green';
            } ?>
            <td><span style="color: <?= $color ?>;"><?= $done ?></span></td>
            <td><?= $row['date_added'] ?> </td>
            <td>
                <a href="?id=<?= $row['id'] ?>&amp;action=edit">Изменить</a>
                <?php if (getAssignedId($row['id'],$pdo) == $_SESSION['user']['id']) { ?>
                <a href="?id=<?= $row['id'] ?>&amp;action=done">Выполнить</a>
                <?php } ?>
                <a href="?id=<?= $row['id'] ?>&amp;action=delete">Удалить</a>
            </td>
            <td><?php echo getAssignedLogin($row['id'],$pdo)?></td>
            <td><?php echo getAuthorLogin($row['id'],$pdo)?></td>
            <td>
                <form method="post">
                    <select name="assigned_user_id">
                        <?php foreach (getNamesUsers($pdo) as $name) { ?>
                        <option value="<?php echo getUserId($name['login'],$pdo)."_".$row['id']?>"><?= $name['login'] ?> </option>
                        <?php } ?>
                        <input type="submit" name="assign" value="Переложить ответственность">
                    </select>
                </form>
            </td>
        </tr>

    <?php }
}

function showAssognedTasks($pdo)
{
    $sql = "SELECT * FROM `task` WHERE `assigned_user_id` = ? and `user_id` != ? ";
    $statement = $pdo->prepare($sql);
    $statement->execute([$_SESSION['user']['id'],$_SESSION['user']['id']]);
    while ($row = $statement->FETCH(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?= $row['description'] ?> </td>
            <?php if ($row['is_done'] == 0) {
                $done = 'В процессе';
                $color = 'orange';
            } else if ($row['is_done'] == 1) {
                $done = 'Выполнено';
                $color = 'green';
            } ?>
            <td><span style="color: <?= $color ?>;"><?= $done ?></span></td>
            <td><?= $row['date_added'] ?> </td>
            <td>
                <a href="?id=<?= $row['id'] ?>&amp;action=edit">Изменить</a>
                <a href="?id=<?= $row['id'] ?>&amp;action=done">Выполнить</a>
                <a href="?id=<?= $row['id'] ?>&amp;action=delete">Удалить</a>
            </td>
            <td><?php echo getAssignedLogin($row['id'],$pdo)?></td>
            <td><?php echo getAuthorLogin($row['id'],$pdo)?></td>
        </tr>

    <?php }
}