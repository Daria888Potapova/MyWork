<?php

require_once('../Classes/dbConnect.php');
require_once('../Settings/params.php');
use DB\dbConnect;
use params\Params;

    session_start();

    $error_email = '';
    $error_password = '';
    $error_auth = '';
    $email = '';

    //Если передеанная переменная email существует и она не пустая
    if(isset($_POST['email']) && !empty($_POST['email'])){
        $email = trim($_POST['email']);
        $email = htmlspecialchars($email, ENT_QUOTES);

        //Проверяем формат полученного почтового адреса с помощью регулярного выражения
        $reg_email = "/^[a-z\d][a-z\d._-]*[a-z\d]*@([a-z\d]+([a-z\d-]*[a-z\d]+)*\.)+[a-z]+/i";

        //Если формат полученного почтового адреса не соответствует регулярному выражению
        if( !preg_match($reg_email, $email)){
           $error_email = 'Неправильный E-Mail';
        }
    }

    //Если переданная переменная password существует и она пустая
    if (isset($_POST['password']) && empty($_POST['password'])){
        $error_password = 'Введите пароль';
    }

    //Если переданныйе переменные email и password существуют и не пустые, выполняем запрос к БД
    if(isset($_POST['password']) && empty($error_password) && isset($_POST['email']) && empty($error_email)){
        $password = trim($_POST['password']);
        $password = htmlspecialchars($password, ENT_QUOTES);

        //Шифруем пароль
        $password = md5($password.Params::$keyEncrypt);


        $dbConnect = new dbConnect();

        //Выполняем запрос к БД
        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM users LEFT JOIN role ON users.id_role = role.id_role WHERE email = '%s' AND password = '%s'   ", $email, $password));

        //Если пользователь не найден
        if(!$result_query_select->num_rows){
            $error_email = 'Неправильный логин или пароль';
        }else{

            //Записываем в сессию данные о пользователе
            while ( $rows = $result_query_select->fetch_assoc() ) {
                $_SESSION['email'] = $rows['email'];
                $_SESSION['id'] = $rows['id_user'];
                $_SESSION['fio'] = $rows['fio'];
                $_SESSION['id_role'] = $rows['id_role'];
                $_SESSION['name_role'] = $rows['name_role'];
                $_SESSION['avatar'] = $rows['avatar'];
            }
            //Переходим на страницу старта приложения
            header('Location: /Index.php');
        }

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
    <link rel="stylesheet" href="../Styles/Auth.css">

    <title>Авторизация</title>
</head>
<body class="auth_background">
<script>
    function openForgotPassword(){
        window.location.href = document.location.origin + '/Pages/ForgotPassword.php';
    }
</script>
    <div class="auth_logoContainer">
        <img src="../Images/logo.png" alt="лого"/>
        <h1>АРМ куратора колледжа</h1>
    </div>
    <form action="Auth.php" method="post" name="form_auth" class="auth_AuthContainer">
        <h2>Авторизация</h2>
        <label class="auth_error"><?php echo $error_auth ?></label>
        <label for="email">E-Mail </label>
        <label class="auth_error"><?php echo $error_email ?></label>
        <input name="email" type="email" placeholder="E-Mail" value="<?php echo $email ?>"/>
        <label for="password">Пароль</label>
        <label class="auth_error"><?php echo $error_password?></label>
        <input name="password" type="password" placeholder="Пароль"/>
        <div class="auth_buttons">
            <input type="submit" value="Войти">
            <a href="#" onclick="openForgotPassword()">Забыли пароль?</a>
        </div>

    </form>
</body>
</html>