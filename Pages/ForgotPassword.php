<?php

require_once('../Classes/dbConnect.php');
use DB\dbConnect;


session_start();
$error = "";
$email = "";
$password = "";
$success = "";
if (isset($_POST['email']) && mb_strlen($_POST['email'])){
    $dbConnect = new dbConnect();
    $query = sprintf("SELECT * FROM users WHERE email='%s'", $_POST['email']);
    $result_query_select = $dbConnect::$mysqli->query($query);
    if(!$result_query_select->num_rows){
        $error = 'Акаунт не найден';
    }else{
        $user = $result_query_select->fetch_assoc();
        mail($_POST['email'], "Восстановление пароля", "Для изменения пароля, перейдите по ссылке: http://".$_SERVER['SERVER_NAME']."/Pages/RefreshPassword.php?id=".$user['password']);
        $success = "Перейдите на почту и следуйте инструкциям";
    }
}else if (isset($_POST['email'])){
    $error = "Почта пустая";
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
    <link rel="stylesheet" href="../Styles/ForgotPassword.css">

    <title>Забыли пароль</title>
</head>
<body class="forgot_background">
<div class="forgot_logoContainer">
    <img src="../Images/logo.png" alt="лого"/>
    <h1>АРМ куратора колледжа</h1>
</div>
<form action="ForgotPassword.php" method="post" name="form_forgot" class="forgot_ForgotContainer">
    <h2>Забыли пароль</h2>
    <label class="<?php echo mb_strlen($error) ? "forgot_error" : "forgot_success" ?>"><?php echo mb_strlen($error) ? $error : $success ?></label>
    <?php
    if (!mb_strlen($success)){
        echo '<label for="email">E-Mail</label>';
        echo '<input id="email" name="email" type="email" placeholder="E-Mail" value="'.$email.'"/>';
        echo '<input type="submit" value="Восстановить">';
    }
    ?>

</form>
</body>
</html>