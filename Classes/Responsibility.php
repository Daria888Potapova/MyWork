<?php

class Responsibility
{
    public $name;
    public $students = [];

    //Конструктор класса
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function addStudent($student)
    {
        $this->students[] = $student;
    }
}