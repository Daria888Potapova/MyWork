<?php

//Данный класс служит для формирвоания select списков
class SimpleObject{
    public $name;
    public $id;
    public $firstParam;

    //Конструктор класса
    public function __construct($id, $name)
    {
        $this->name = $name;
        $this->id = $id;

    }

    public function addFirstParam($firstParam)
    {
        $this->firstParam = $firstParam;
    }
}
