<?php
    session_start();


    //Если переменная email в сессии не заполнена
    if (!isset($_SESSION['email'])){
        //Возвращаем пользователя на страницу авторизации
        header('Location: /Pages/Auth.php');
    }
    else{
        //Бросаем в основной интерфейс
        header('Location: /Pages/Main.php');
    }

