<?php

require_once('../Classes/dbConnect.php');
require_once('../Settings/params.php');
use params\Params;
use DB\dbConnect;


session_start();
$error = "";
$password = "";
$id = $_GET['id'] ?? $_POST['id'];

if (isset($_POST['password']) && mb_strlen($_POST['password']) && isset($_POST['id']) && mb_strlen($_POST['id'])){

    $password = trim($_POST['password']);
    $password = htmlspecialchars($password, ENT_QUOTES);

    //Шифруем пароль
    $password = md5($password.Params::$keyEncrypt);

    $dbConnect = new dbConnect();
    $query = sprintf("UPDATE users SET password='%s' WHERE password='%s'", $password, $_POST['id']);
    $result_query_select = $dbConnect::$mysqli->query($query);
    if($result_query_select){
        header('Location: /Index.php');
    }else{
       $error = "Пароль не был обновлен";
    }
}else if (isset($_POST['password']) && !mb_strlen($_POST['password'])){
    $error = "Пароль не может быть пустым";
}else if (!mb_strlen($_POST['id']) &&  !mb_strlen($_GET['id']) ){
    $error = "Страница открыта не правильно";
}
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../Styles/Fonts.css">
    <link rel="stylesheet" href="../Styles/RefreshPassword.css">

    <title>Новый пароль</title>
</head>
<body class="refresh_background">
<div class="refresh_logoContainer">
    <img src="../Images/logo.png" alt="лого"/>
    <h1>АРМ куратора колледжа</h1>
</div>
<form action="RefreshPassword.php" method="post" name="form_refresh" class="refresh_refreshContainer">
    <h2>Новый пароль</h2>
    <label class="refresh_error"><?php echo $error ?></label>
    <label for="password">Пароль</label>
    <input type="hidden" name="id" value="<?php echo $id ?>">
    <input id="password" name="password" type="password" placeholder="Введите новый пароль" value="<?php echo $password ?>"/>
    <input type="submit" value="Сохранить">
</form>
</body>
</html>