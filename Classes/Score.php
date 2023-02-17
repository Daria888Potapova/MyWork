<?php

class Score
{
    public $name;
    public $id;
    public $discipline;
    public $NoVisited;
    public $GoodReason;

    //Конструктор класса
    public function __construct($id, $name, $discipline, $NoReason, $GoodReason)
    {
        $this->name = $name;
        $this->id = $id;
        $this->discipline = $discipline;
        $this->NoVisited = $NoReason;
        $this->GoodReason = $GoodReason;

    }

}