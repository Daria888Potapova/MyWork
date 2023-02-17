<?php
require_once ('../Settings/params.php');

//Данный класс хранит информацию о предмете, оценках и пропусках
class Discipline{
    public $name;
    public $time;
    public $score = [];
    public $NoVisited = [];
    public $GoodReason = [];

    //Конструтор класса
    public function __construct($name,$time, $score, $NoVisited, $GoodReason)
    {
        $this->name = $name;
        $this->time = $time;
        $this->addData($score, $NoVisited, $GoodReason);
    }

    //Добавление данных
    public function addData($score, $NoVisited, $GoodReason){
        $this->score[] = (int)$score;
        if ((int)$GoodReason == 1){
            $this->GoodReason[] = (int)$GoodReason;
        }elseif ((int)$NoVisited == 1){
            $this->NoVisited[] = (int)$NoVisited;
        }

    }

    //Подсчет средней оценки
    public function getAverScore(){
        $sum = 0;
        foreach ($this->score as $value){
            $sum += $value;
        }
        return $sum / count($this->score);
    }

    //Возврат количества пропусков
    public function getCountNoVisited(): int
    {
        return count($this->NoVisited);
    }

    //Возврат количества уважительных пропусков
    public function getCountGoodReason(): int
    {
        return count($this->GoodReason);
    }
}
