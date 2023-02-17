<?php
require_once ('../Settings/params.php');

//Данный класс хранит информацию о предмете, оценках и пропусках
class AcademDiscipline{
    public $id;
    public $id_discipline;
    public $name_discipline;
    public $terms = [];

    //Конструтор класса
    public function  __construct($id,$term, $id_discipline, $name_discipline, $id_typeScore, $name_score, $time_term)
    {
        $this->id = $id;
        $this->id_discipline = $id_discipline;
        $this->name_discipline = $name_discipline;
        $this->addTerm($term, $time_term, $id_typeScore, $name_score);
    }

    public function addTerm($term, $time_term, $type_score, $name_score){
            $this->terms[] = array('term' => $term, 'value' => $time_term, 'typeScore' => $type_score, 'name_score' => $name_score);

    }


}
