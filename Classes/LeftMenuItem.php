<?php

//Данный класс служит для формирования пункта меню
class LeftMenuItem{
    public $name;
    public $role;
    public $path;
    public $namePage;

    //Конструктор класса
    public function __construct($name, $role, $path, $namePage){

        $this->name = $name;
        $this->role = array_map("strtolower", $role);;
        $this->path = $path;
        $this->namePage = $namePage;
    }


    public function haveRole($role): bool{
        return in_array(strtolower($role), $this->role);
    }
}
