<?php

//очищаем сессию
session_start();
$_SESSION = array();
session_destroy();
header('Location: /Index.php');