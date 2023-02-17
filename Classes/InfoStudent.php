<?php

require_once ('../Classes/Discipline.php');

//Данный класс хранит информацию о студенте и его успеваемости
class InfoStudent{
    public $name;
    public $discipline = [];

    //Конструктор класса
    public function __construct($nameStudent, $nameDiscipline,$time, $score, $NoVisited, $GoodReason)
    {
        $this->name = $nameStudent;
        $this->discipline[] = new Discipline($nameDiscipline,$time, $score, $NoVisited, $GoodReason);

    }

    //Добавление новый данных по предмету
    public function addData($nameDiscipline,$time, $score, $NoVisited, $GoodReason){
        $DisciplineExist = false;

        //Ищем предмет, с таким же именем как у переданного предмета
        foreach ($this->discipline as $item) {
            if ($nameDiscipline == $item->name){
                $item -> addData($score, $NoVisited, $GoodReason);
                $DisciplineExist = true;
            }
        }

        //Если предмет не был найден, то создаем новый с таким названием
        if (!$DisciplineExist){
            $this->discipline[] = new Discipline($nameDiscipline,$time, $score, $NoVisited, $GoodReason);
        }
    }

    //Получение предмета
    public function getDiscipline($nameDiscipline){
        foreach ($this->discipline as $item){
            if ($item->name == $nameDiscipline){
                return $item;
            }
        }
        return null;
    }

}
