<?php

require_once ('../vendor/autoload.php');
require_once ('../Classes/dbConnect.php');
require_once ('../Classes/Responsibility.php');
require_once ('../Classes/SimpleObject.php');
require_once ('../Classes/Events.php');

use DB\dbConnect;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet()->setTitle('Титульный лист');

//Получаем список групп для заполнения select
$dbConnect = new dbConnect();

//Титульный лист
$query = "SELECT * FROM `groups` LEFT JOIN users ON `groups`.id_user = users.id_user WHERE id_group=".$_POST['group'];

$groups = $dbConnect::$mysqli->query($query);
$groups = $groups->fetch_assoc();


$sheet->getStyle("C5")->getFont()->setSize(24);

$sheet->mergeCells("C5:G5");
$sheet->setCellValue('C5', 'Мониторинг куратора группы '.$groups['name_group']);

$sheet->getStyle("C8")->getFont()->setSize(11);
$sheet->setCellValue('C8', 'Куратор: '.$groups['fio']);

$sheet->getStyle("F8")->getFont()->setSize(11);

$sheet->setCellValue('F8', mb_substr($_POST['startPeriod'], 0, 4).'-'.mb_substr($_POST['endPeriod'],  0, 4) );
$sheet->getColumnDimension("F")->setAutoSize(true);
$sheet->getColumnDimension("C")->setAutoSize(true);



$sheet->getStyle("B4:G5")
    ->getBorders()
    ->getOutline()
    ->setBorderStyle(Border::BORDER_MEDIUM)
    ->setColor(new Color('00000000'));

$sheet->getStyle("B7:G9")
    ->getBorders()
    ->getOutline()
    ->setBorderStyle(Border::BORDER_MEDIUM)
    ->setColor(new Color('00000000'));


//Общие данные
$studentsQuery = "SELECT * FROM students  WHERE id_group=".$_POST['group'];

$students = $dbConnect::$mysqli->query($studentsQuery);

$sheet2 = $spreadsheet->createSheet();
$sheet2->mergeCells("B2:F2");
$sheet2->setTitle('Общие данные');

$sheet2->getStyle("B2")->getFont()->setSize(20);
$sheet2->setCellValue('B2', 'Общие сведения студентов');

$headers = array('','Ф.И.О.', 'День рождения', 'Ф.И.О. мамы', 'Ф.И.О. папы');
$columns = array('B', 'C', 'D', 'E', 'F');

$startIndex = 3;
for($index = 0; $index < count($headers); $index++){
    $sheet2->getStyle($columns[$index].$startIndex)->getFont()->setSize(14);
    $sheet2->setCellValue($columns[$index].$startIndex, $headers[$index]);
}

$index = 4;
while ( $row = $students->fetch_assoc()){
    $sheet2->getStyle("B".$index)->getFont()->setSize(14);
    $sheet2->setCellValue("B".$index, $index- $startIndex);

    $sheet2->getStyle("C".$index)->getFont()->setSize(14);
    $sheet2->setCellValue("C".$index, $row['FIO']);

    $sheet2->getStyle("D".$index)->getFont()->setSize(14);
    $sheet2->setCellValue("D".$index, $row['birthday']);

    $sheet2->getStyle("E".$index)->getFont()->setSize(14);
    $sheet2->setCellValue("E".$index, (mb_strlen($row['mother']) ? $row['mother'].', '.$row['mother_workplace'] : ''));

    $sheet2->getStyle("F".$index)->getFont()->setSize(14);
    $sheet2->setCellValue("F".$index, (mb_strlen($row['father']) ?$row['father'].', '.$row['father_workplace'] : ''));
    $index++;
}

foreach ($columns as $column){
    $sheet2->getColumnDimension($column)->setAutoSize(true);
}

$sheet2->getStyle("B".$startIndex.":F".($index-1))
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN)
    ->setColor(new Color('00000000'));


////////////////////////////////
//Актив группы
////////////////////////////////


//Получение всех обязанностей
$resp = "SELECT DISTINCT name_responsibility FROM responsibilities LEFT JOIN students s on s.id_student = responsibilities.id_student  WHERE id_group=".$_POST['group'];
$responsibilityQuery = $dbConnect::$mysqli->query($resp);

$responsibilities = [];

while ( $row = $responsibilityQuery->fetch_assoc()){
    $responsibilities[] = $row['name_responsibility'];
}


//Получение всех активов
$query = "SELECT * FROM responsibilities LEFT JOIN students s on s.id_student = responsibilities.id_student  WHERE id_group=".$_POST['group'];

$active = $dbConnect::$mysqli->query($query);

$studActive = [];
while ( $row = $active->fetch_assoc()){
    $studActive[] = new SimpleObject($row['name_responsibility'], $row['FIO']);
}

//Формирование читабельного объекта
$activeRes = [];

foreach ($responsibilities as $responsibility){
    $resp = new Responsibility($responsibility);

    foreach( $studActive as $student){
        if (mb_strtolower($student->id) == mb_strtolower($responsibility)){
            $resp->addStudent($student->name);
        }
    }
    $activeRes[] = $resp;
}

//Формирование страницы с активами

$sheet3 = $spreadsheet->createSheet();

$sheet3->setTitle('актив группы');

$sheet3->mergeCells('C3:E3');
$sheet3->getStyle("C3")->getFont()->setSize(20);
$sheet3->setCellValue('C3', 'Актив группы');

$sheet3->getStyle('C3:E3')
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN)
    ->setColor(new Color('00000000'));


$index = 6;

foreach ($activeRes as $activeRe){
    $sheet3->getStyle("C".$index)->getFont()->setSize(11);
    $sheet3->setCellValue("C".$index, $activeRe->name.':');


    foreach ($activeRe->students as $student){
        $sheet3->getStyle("E".$index)->getFont()->setSize(11);
        $sheet3->setCellValue("E".$index, $student);

        $index++;
    }

    $index++;
}

$sheet3->getStyle('B5:F'.($index - 1))
    ->getBorders()
    ->getOutline()
    ->setBorderStyle(Border::BORDER_THIN)
    ->setColor(new Color('00000000'));

$sheet3->getColumnDimension('C')->setAutoSize(true);
$sheet3->getColumnDimension('E')->setAutoSize(true);



////////////////////////////////
//План работы
////////////////////////////////


//Получение всех обязанностей
$eventsQuery = sprintf("SELECT * FROM events LEFT JOIN activity ON events.id_activity = activity.id_activity WHERE date_event BETWEEN '%s' AND '%s'", $_POST['startPeriod'], $_POST['endPeriod']);
$eventsQuery = $dbConnect::$mysqli->query($eventsQuery);


$events = [];

while ( $row = $eventsQuery->fetch_assoc()){
    $found = false;
    foreach ($events as $event){
        if ($event->type == $row['name_activity']){
            $found = true;
            $event->addEvent($row['name_event'], $row['date_event']);
            break;
        }
    }
    if (!$found){
        $events[] = new Events($row['name_activity'], $row['name_event'], $row['date_event']);
    }

}

$month = array("Январь" , "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");

$sheet4 = $spreadsheet->createSheet();
$sheet4->setTitle('План работы');



$startIndex = 6;

$startDate = explode("-",$_POST['startPeriod'])[0];
$endDate = explode("-",$_POST['endPeriod'])[0];

$sheet4->getStyle("C3")->getFont()->setSize(20);
$sheet4->mergeCells('C3:L3');
$sheet4->setCellValue('C3', sprintf("План воспитательной работы на %s-%s", $startDate, $endDate));

foreach (range($startDate, ($endDate > $startDate ? ($endDate - 1) : $endDate)) as $year){
    $startRange = $startIndex;
    $sheet4->getStyle("B".$startIndex)->getFont()->setSize(12);
    $sheet4->setCellValue('B'.$startIndex, $year.'-'.($year + 1));

    $startIndex++;
    $sheet4->getStyle("B".$startIndex)->getFont()->setSize(12);
    $sheet4->setCellValue('B'.$startIndex, '№');

    $sheet4->getStyle("C".$startIndex)->getFont()->setSize(12);
    $sheet4->setCellValue('C'.$startIndex, 'Направление деятельности');

    foreach (range('D', 'G') as $key=>$column){
        $sheet4->getStyle($column.$startIndex)->getFont()->setSize(12);
        $sheet4->setCellValue($column.$startIndex, $month[$key + 8]);
    }
    foreach (range('H', 'L') as $key=>$column){
        $sheet4->getStyle($column.$startIndex)->getFont()->setSize(12);
        $sheet4->setCellValue($column.$startIndex, $month[$key]);
    }

    $startIndex++;
    $startDate = strtotime($year.'-08-31 00:00:00');
    $endDate = strtotime(($year + 1).'-06-01 00:00:00');


    foreach ($events as $key=>$event){

        $sheet4->getStyle("B".$startIndex)->getFont()->setSize(12);
        $sheet4->setCellValue('B'.$startIndex, $key + 1);

        $sheet4->getStyle("C".$startIndex)->getFont()->setSize(12);
        $sheet4->setCellValue('C'.$startIndex, $event->type);

        $maxRow = 0;
        foreach (range('D', 'G') as $key2=>$column){

            $currRow = 0;

            foreach ($event->events as $event2){

                $nextMonth = (mb_strlen($key2 + 10) == 1 ? '0'.($key2 + 10) : $key2 + 10);
                $startDate = new DateTime($year.'-'.(mb_strlen($key2 + 9) == 1 ? '0'.($key2 + 9) : ($key2 + 9)).'-01');
                $endDate = new DateTime(($nextMonth > 12 ? $year + 1 : $year).'-'.($nextMonth > 12 ? '01' : $nextMonth).'-01');

                $dateEvent = new DateTime($event2->id);

                if ($dateEvent->diff($startDate)->format('%R') === '-' && $dateEvent->diff($endDate)->format('%R') === '+' ){
                    $sheet4->getStyle($column.($startIndex + $currRow))->getFont()->setSize(12);
                    $sheet4->setCellValue($column.($startIndex + $currRow), $event2->name);
                    $currRow++;
                }
            }

            if ($currRow > $maxRow){
                $maxRow = $currRow;
            }
        }

        foreach (range('H', 'L') as $key2=>$column){

            $currRow = 0;

            foreach ($event->events as $event2){

                $startDate = new DateTime(($year+1).'-'.(mb_strlen($key2 + 1) == 1 ? '0'.($key2 + 1) : $key2).'-01');
                $endDate = new DateTime(($year+1).'-'.(mb_strlen($key2 + 2) == 1 ? '0'.($key2 + 2) : $key2).'-01');
                $dateEvent = new DateTime($event2->id);

                if ($dateEvent->diff($startDate)->format('%R') === '-' && $dateEvent->diff($endDate)->format('%R') === '+' ){

                    $sheet4->getStyle($column.($startIndex + $currRow))->getFont()->setSize(12);
                    $sheet4->setCellValue($column.($startIndex + $currRow), $event2->name);
                    $currRow++;
                }
            }

            if ($currRow > $maxRow){
                $maxRow = $currRow;
            }
        }
        $startIndex = $startIndex + ($maxRow == 0 ? 1 : $maxRow);

    }

    $sheet4->getStyle('B'.($startRange + 1).':L'.($startIndex - 1))
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN)
    ->setColor(new Color('00000000'));

    $startIndex = $startIndex + 2 ;
}

foreach (range('B', 'L') as $value){
    $sheet4->getColumnDimension($value)->setAutoSize(true);
}

//////////////////////////////////////////
/// Соц паспорт
//////////////////////////////////////////

$columnStudents = [];

//Получение видов проживания
$residenceQuery = "SELECT * FROM residences";
$residences =  $dbConnect::$mysqli->query($residenceQuery);

while ($row = $residences->fetch_assoc()){
    $columnStudents[$row['id_residence']] = $row['name_residence'];
}

//Спопставление колонок и полей таблицы
$columnStudents['isDormitory'] = "Проживают в общежитии";
$columnStudents['IsLargeFamily'] = "Из многодетной семьи";
$columnStudents['IsPoorFamily'] = "Из малообеспеченной семьи";
$columnStudents['NoFather'] = "Нет отца";
$columnStudents['NoMother'] = "Нет матери";
$columnStudents['orphan'] = "Дети-сироты, дети оставшиеся без.попеч. род.";
$columnStudents['IsAcademicScholarShip'] = "Получают академ. стипендию";
$columnStudents['IsSocialScholarShip'] = "Получают соц. стипендию";
$columnStudents['IsScholarShip'] = "Получают пенсию по потери .кормильца (и др. выплаты).";
$columnStudents['IsDispensaryAcc'] = "Состоит на  диспансерном учете.";
$columnStudents['HasChildren'] = "Имеют детей";
$columnStudents['HaveDisPerson'] = "Инвалиды в семье.";
$columnStudents['IntAccCollege'] = "Внутри колледжный учет.";
$columnStudents['KDN'] = "Учёт КДН (до 18 лет).";
$columnStudents['DisabledChildren'] = "Дети инвалидов";
$columnStudents['ChildrenUnemploy'] = "Дети безработных.";
$columnStudents['ChildrenPension'] = "Дети пенсионеров.";


//Получение студентов
$studentsQuery = "SELECT * FROM students LEFT JOIN residences r on students.id_residence = r.id_residence  WHERE id_group=".$_POST['group']." ORDER BY FIO";

$students = $dbConnect::$mysqli->query($studentsQuery);


//Формирование читабельного объекта


//Формирование страницы с активами

$sheet5 = $spreadsheet->createSheet();

$sheet5->setTitle('Соц. паспорт');

$sheet5->mergeCells('C3:K3');
$sheet5->getStyle("C3")->getFont()->setSize(24);
$sheet5->setCellValue('C3', 'Социальный паспорт');


$startIndex = 5;
$startTable = $startIndex;
$startWord = 'B';
//Формирование заголовков колонок
$sheet5->getStyle($startWord.$startIndex)->getFont()->setSize(20);
$sheet5->getStyle($startWord.$startIndex)->getFont()->setBold(true);
$sheet5->getStyle($startWord.$startIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet5->getStyle($startWord.$startIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet5->setCellValue($startWord.$startIndex, 'Ф.И.О.');

$startHeaderWord = $startWord;

$countArr = 0;
foreach ($columnStudents as $key=>$column){
    $startHeaderWord++;

    $sheet5->getStyle($startHeaderWord.$startIndex)->getFont()->setSize(12);
    $sheet5->setCellValue($startHeaderWord.$startIndex, $column);
    $sheet5->getStyle($startHeaderWord.$startIndex)->getAlignment()->setTextRotation(90);
    $sheet5->getStyle($startHeaderWord.$startIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet5->getStyle($startHeaderWord.$startIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet5->getStyle($startHeaderWord.$startIndex)->getFont()->setBold(true);
    $sheet5->getStyle($startHeaderWord.$startIndex)->getAlignment()->setWrapText(true);

    if ($countArr >= (count($columnStudents) - 3) ){
        $sheet5->getStyle($startHeaderWord.$startIndex)->getFont()->getColor()->setRGB('ff0000');
    }
    $countArr++;
}

//Разбор строк
while ($row = $students->fetch_assoc()){
    $startIndex++;
    $rowWord = $startWord;

    $sheet5->getStyle($rowWord.$startIndex)->getFont()->setSize(12);
    $sheet5->setCellValue($rowWord.$startIndex, $row['FIO']);


    foreach($columnStudents as $key=>$column){
        $rowWord++;
        $sheet5->getStyle($rowWord.$startIndex)->getFont()->setSize(12);
        switch ($key){
            case is_numeric($key):
                if ($key == $row['id_residence']){
                    $sheet5->setCellValue($rowWord.$startIndex, 'да');
                }else{
                    $sheet5->setCellValue($rowWord.$startIndex, 'нет');
                }
                break;
            case 'NoFather':
                if (mb_strlen($row['father'])){
                    $sheet5->setCellValue($rowWord.$startIndex, 'да');
                }else{
                    $sheet5->setCellValue($rowWord.$startIndex, 'нет');
                }
                break;
            case 'NoMother':
                if (mb_strlen($row['mother'])){
                    $sheet5->setCellValue($rowWord.$startIndex, 'да');
                }else{
                    $sheet5->setCellValue($rowWord.$startIndex, 'нет');
                }
                break;
            default:
                $sheet5->setCellValue($rowWord.$startIndex, $row[$key] ? 'да' : 'нет');
        }
    }
}

$endWord = chr(ord($startWord)+(count($columnStudents)));

$sheet5->getStyle($startWord.$startTable.':'.$endWord.$startIndex)
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN)
    ->setColor(new Color('00000000'));

$sheet5->getRowDimension($startTable)->setRowHeight(150);

$sheet5->getColumnDimension($startWord)->setAutoSize(true);


/////////////////////////////////////////////
/// Соц активность
/////////////////////////////////////////////


//Получение студентов
$students  = $dbConnect::$mysqli->query($studentsQuery);

$eventsQuery = "SELECT * FROM events LEFT JOIN studentsandevents s on events.id_event = s.id_event";
$events = [];

$eventsSQL  = $dbConnect::$mysqli->query($eventsQuery);

while ( $row = $eventsSQL->fetch_assoc()){
    $foundEvent = false;
    foreach ($events as $event){
        if (mb_strtolower($event->type) == mb_strtolower($row['name_event'])){
            $event->addEvent($row['id_student'], $row['prize_event']);
            $foundEvent = true;
        }
    }

    if (!$foundEvent){
        $events[] = new Events($row['name_event'], $row['id_student'], $row['prize_event']);
    }
}

//Формирование страницы

$sheet6 = $spreadsheet->createSheet();

$sheet6->setTitle('соц. активность');

$sheet6->mergeCells('C3:G3');
$sheet6->getStyle("C3")->getFont()->setSize(20);
$sheet6->setCellValue('C3', 'Социальная активность');


$startIndex = 5;
$startWord = 'B';
$startHeaderWord = $startWord;

$sheet6->getStyle($startHeaderWord.$startIndex)->getFont()->setSize(16);
$sheet6->getStyle($startHeaderWord.$startIndex)->getFont()->setBold(true);
$sheet6->getStyle($startHeaderWord.$startIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet6->getStyle($startHeaderWord.$startIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet6->setCellValue($startHeaderWord.$startIndex, '№');

$startHeaderWord++;

$sheet6->getStyle($startHeaderWord.$startIndex)->getFont()->setSize(20);
$sheet6->getStyle($startHeaderWord.$startIndex)->getFont()->setBold(true);
$sheet6->getStyle($startHeaderWord.$startIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet6->getStyle($startHeaderWord.$startIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet6->setCellValue($startHeaderWord.$startIndex, 'Ф.И.О.');

foreach ($events as $event){
    $startHeaderWord++;

    $sheet6->getStyle($startHeaderWord.$startIndex)->getFont()->setSize(12);
    $sheet6->getStyle($startHeaderWord.$startIndex)->getFont()->setBold(true);
    $sheet6->getStyle($startHeaderWord.$startIndex)->getAlignment()->setTextRotation(90);
    $sheet6->getStyle($startHeaderWord.$startIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet6->getStyle($startHeaderWord.$startIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet6->getStyle($startHeaderWord.$startIndex)->getAlignment()->setWrapText(true);
    $sheet6->setCellValue($startHeaderWord.$startIndex, $event->type);
}


$rowIndex = $startIndex;

$countRow = 0;
while ($row = $students->fetch_assoc()){
    $rowWord = $startWord;
    $rowIndex++;
    $countRow++;

    $sheet6->getStyle($rowWord.$rowIndex)->getFont()->setSize(12);
    $sheet6->setCellValue($rowWord.$rowIndex, $countRow);

    $rowWord++;

    $sheet6->getStyle($rowWord.$rowIndex)->getFont()->setSize(12);
    $sheet6->setCellValue($rowWord.$rowIndex, $row['FIO']);


    foreach ($events as $event){
        $rowWord++;


        foreach ($event->events as $members){
            if ($members->name == $row['id_student']){
                $sheet6->getStyle($rowWord.$rowIndex)->getFont()->setSize(12);
                $sheet6->getStyle($rowWord.$rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet6->getStyle($rowWord.$rowIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet6->setCellValue($rowWord.$rowIndex, '+');
                break;
            }
        }
    }

}

$endWord = chr(ord($startWord)+(count($events) + 1));

$sheet6->getStyle($startWord.$startIndex.':'.$endWord.($startIndex + $countRow ))
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN)
    ->setColor(new Color('00000000'));


$sheet6->getRowDimension($startIndex)->setRowHeight(100);
$sheet6->getColumnDimension($startWord)->setAutoSize(true);
$startWord++;
$sheet6->getColumnDimension($startWord)->setAutoSize(true);


$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="FullReport.xlsx"');

$writer->save("php://output");
