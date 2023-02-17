<?php

require_once ('../vendor/autoload.php');
require_once ('../Classes/dbConnect.php');
require_once ('../Classes/InfoStudent.php');

use DB\dbConnect;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Language;


//Формирвоание общего отчета по успеваемости группы в дисциплинах


//Получаем данные для отображения
$dbConnect = new dbConnect();
$error = '';
$info_students = [];
$disciplines = [];
$query = sprintf("SELECT * FROM academperfomancescores 
    LEFT JOIN academperformance a on academperfomancescores.id_academperformance = a.id_academPerformance
    LEFT JOIN students s on academperfomancescores.id_student = s.id_student
    LEFT JOIN disciplines d on a.id_discipline = d.id_discipline WHERE a.id_group = %d AND s.id_group = %d AND date_academPerformance BETWEEN '%s' AND '%s'",
    $_POST['group'],$_POST['group'], $_POST['startPeriod'], $_POST['endPeriod'] );

$result_query_select = $dbConnect::$mysqli->query($query);


if(!$result_query_select->num_rows){
    $error = 'Не удалось получить студентов';
}else{
    //Преобразовываем результат запроса для более удобного вывода данных и подсчета результатов
    while ($row = $result_query_select->fetch_assoc()){

        //Ищем студента и если он уже существует, то добавляем ему новые данные к существующим
        $studentExist = false;
        for($index = 0; $index < count($info_students); $index++){
            if ($info_students[$index]->name == $row['FIO']){
                $info_students[$index]->addData($row['name_discipline'], $row['time_discipline'] ,$row['score'], $row['NoVisited'], $row['GoodReason']);
                $studentExist = true;
                break;
            }
        }

        //Если студент не был найден, то создаем нового
        if (!$studentExist){
            $info_students[] = new InfoStudent($row['FIO'],$row['name_discipline'],$row['time_discipline'], $row['score'], $row['NoVisited'], $row['GoodReason']);
        }

        //Проверяем наличие дисциплины в основном перечне дисциплин
        $disciplineFound = false;
        foreach ($disciplines as $discipline){
            if ($discipline == $row['name_discipline']){
                $disciplineFound = true;
                break;
            }
        }

        //Если дисциплина не была найдена, добавляем ее
        if (!$disciplineFound){
            $disciplines[] = $row['name_discipline'];
        }
    }
}

//Получаем данные о группе
$info = $dbConnect::$mysqli->query("SELECT * FROM `groups` WHERE id_group = 1");
$info = $info->fetch_assoc();


//Формирование документа

//Заполнение основной информации
$word = new PhpWord();
$word->getSettings()->setThemeFontLang(new Language(Language::RU_RU));
$word ->setDefaultFontName('Times New Roman');
$word -> setDefaultFontSize(14);

$properties = $word->getDocInfo();
$properties->setCreator('Система \'ИРКПО\'');
$properties->setCompany('ИРКПО');
$properties->setTitle('Сводный отчет по группе');

$properties->setLastModifiedBy('Система \'ИРКПО\'');

$date = explode('.',date('m.d.y'));
$properties->setCreated(mktime(0, 0, 0, (int)$date[0], (int)$date[1], (int)$date[2]));

//Формирование тела документа

//Формирование заголовка
$sectionStyle = array(
    'marginTop' => Converter::pixelToTwip(50),
    'marginLeft' => 600,
    'marginRight' => 600,
    'colsNum' => 1

);
$sectionHeader = $word->addSection($sectionStyle);

$textHeader = 'Сводный отчет по группе '.$info['name_group'];
$fontStyle = array('name'=>'Times New Roman', 'size'=>14, 'color'=>'000000', 'bold'=>TRUE, 'italic'=>FALSE);
$parStyle = array('align'=>'center');

$sectionHeader -> addText(htmlspecialchars($textHeader), $fontStyle, $parStyle);


//Формирование списков с успеваемостью
foreach ($disciplines as $discipline){

    //Заголовок таблицы
    $sectionHeader -> addText(htmlspecialchars($discipline), $fontStyle, $parStyle);

    //Таблица
    $tableStyle = array(
        'borderColor' => '000000',
        'borderSize'  => 1
    );
    $table = $sectionHeader -> addTable($tableStyle);

    //Заголовки в таблице
    $table -> addRow();
    $table ->addCell() -> addText(htmlspecialchars('ФИО студента'));
    $table ->addCell() -> addText(htmlspecialchars('Общее количество часов'));
    $table ->addCell() -> addText(htmlspecialchars('Средняя оценка'));
    $table ->addCell() -> addText(htmlspecialchars('Общее количество пропусков'));
    $table ->addCell() -> addText(htmlspecialchars('Из них по уважительной'));

    //Студенты и их успеваемость
    foreach ($info_students as $student){

        $table -> addRow();

        //Колонка с  учеником
        $table -> addCell() -> addText(htmlspecialchars($student->name));
        $dis = $student -> getDiscipline($discipline);
        $table -> addCell() -> addText(htmlspecialchars($dis->time));
        $table -> addCell() -> addText(htmlspecialchars($dis->getAverScore()));
        $table -> addCell() -> addText(htmlspecialchars($dis->getCountNoVisited() + $dis->getCountGoodReason()));
        $table -> addCell() -> addText(htmlspecialchars($dis->getCountGoodReason()));
    }

}

//Выдача документа для скачивания
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="InfoGroup.docx"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');

$objWriter = IOFactory::createWriter($word, 'Word2007');
$objWriter->save("php://output");



