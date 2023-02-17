<?php

namespace DB;

use mysqli;

class dbConnect {

    private static $openserver = "localhost"; // имя хоста (уточняется у провайдера), если работаем на локальном сервере, то указываем localhost
    private static $username = "root"; // Имя пользователя БД
    private static $password = ""; // Пароль пользователя. Если у пользователя нету пароля то, оставляем пустое значение ""
    private static $database = "mybazad (1)"; // Имя базы данных, которую создали
    public static $mysqli = null;


    //Конструтор класса
    public function __construct(){
        dbConnect::CheckConnection();
    }

    //Подключение к базе, если соединения нет
    public static function CheckConnection(){

        if (!self::$mysqli){
            // Подключение к базе данных через MySQLi
            self::$mysqli = new mysqli(self::$openserver, self::$username, self::$password, self::$database);
            // Проверяем, успешность соединения.
            if (self::$mysqli->connect_errno) {
                die("<p><strong>Ошибка подключения к БД</strong></p><p><strong>Код ошибки: </strong> ". self::$mysqli->connect_errno ." </p><p><strong>Описание ошибки:</strong> ".self::$mysqli->connect_error."</p>");
            }

            // Устанавливаем кодировку подключения
            self::$mysqli->set_charset('utf8');
        }

    }

}
 


 


?>