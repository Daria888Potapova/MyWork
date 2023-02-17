<?php

class Events
{
    public $type;
    public $events = [];


    //Конструктор класса
    public function __construct($type, $event, $date){

        $this->type = $type;
        $this->addEvent($event, $date);
    }


    public function addEvent($event, $date){
        $this->events[] = new SimpleObject($date, $event);
    }
}