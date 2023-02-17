<?php
require_once ('../vendor/autoload.php');
require_once ('../Classes/dbConnect.php');
require_once ('../Classes/Score.php');
require_once ('../Classes/SimpleObject.php');

use DB\dbConnect;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet()->setTitle('Сводная ведомость');

//Получаем список групп для заполнения select
$dbConnect = new dbConnect();

//Получение дисциплин за семестр
$disciplineQuery = sprintf("SELECT * FROM academplan_detail 
LEFT JOIN academplan ON academplan_detail.id_academplan = academplan.id_academplan 
LEFT JOIN disciplines ON disciplines.id_discipline= academplan_detail.id_discipline      
WHERE academplan.id_group=%d AND academplan_detail.term=%d",$_POST['group'], $_POST['term']);

$disciplineQuery = $dbConnect::$mysqli->query($disciplineQuery);

$disciplines = [];
while ($row = $disciplineQuery->fetch_assoc()){
	 $temp= new SimpleObject($row['id_discipline'], $row['name_discipline']);
	 $temp->addFirstParam($row['id_typeScore']);
	 $disciplines[] = $temp;
}

// Получение студентов группы
$studentsQuery = sprintf("SELECT * FROM students WHERE id_group=%d", $_POST['group']);
$studentsQuery = $dbConnect::$mysqli->query($studentsQuery);

$students = [];
while ($row = $studentsQuery->fetch_assoc()){
	$students[] = new SimpleObject($row['id_student'], $row['FIO']);
}

//Получение оценок
$scoreQuery = sprintf("SELECT * FROM academperfomancescore
LEFT JOIN students ON academperfomancescore.id_student = students.id_student
LEFT JOIN academperformance ON academperfomancescore.id_academPerfomance = academperformance.id_academPerformance
 WHERE students.id_group=%d AND academperformance.term=%d", $_POST['group'], $_POST['term']);

$scoreQuery = $dbConnect::$mysqli->query($scoreQuery);

$scores = [];
while ($row = $scoreQuery->fetch_assoc()){
	$scores[] = new Score($row['id_student'], $row['score'], $row['id_discipline'], $row['NoVisited'], $row['GoodReason']);
}

//Получение видов оценок
$activityQuery = $dbConnect::$mysqli->query("SELECT * FROM typescore");

$typeScores = [];
while ($row = $activityQuery->fetch_assoc()){
    $typeScores[] = new SimpleObject($row['id_typeScore'], $row['name_typeScore']);
}

//Текущая группа
$groupQuery = $dbConnect::$mysqli->query("SELECT * FROM `groups` WHERE id_group=".$_POST['group']);

$row = $groupQuery->fetch_assoc();
$currGroup = new SimpleObject($row['id_group'], $row['name_group']);

//Формирование таблицы

//Параметры для точки формирования таблицы
$startIndex = 2;
$startWord = 'B';

//Количество колонок
$countColumns = 6 + (count($disciplines) - 1); 

//Заголовок 
$sheet->mergeCells($startWord.$startIndex.':'.(chr(ord($startWord)+$countColumns)).$startIndex);

$sheet->getStyle($startWord.$startIndex)->getFont()->setSize(12);
$sheet->getStyle($startWord.$startIndex)->getFont()->setBold(true);
$sheet->getStyle($startWord.$startIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($startWord.$startIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->setCellValue($startWord.$startIndex, sprintf('Сводная ведомость успеваемости группы %s за %s семестр %s-%s уч. года', $currGroup->name, $_POST['term'], 
	mb_substr($_POST['startPeriod'], 0, 4), mb_substr($_POST['endPeriod'], 0, 4)));

/////////////////////////////////////
//Колонки
/////////////////////////////////////

$headerIndex = $startIndex + 1;
$headerWord = $startWord;

//Заголовок 
$sheet->mergeCells($headerWord.$headerIndex.':'.$headerWord.($headerIndex + 1));
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setSize(12);
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setBold(true);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->setCellValue($headerWord.$headerIndex, '№');

$headerWord++;

$sheet->mergeCells($headerWord.$headerIndex.':'.$headerWord.($headerIndex + 1));
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setSize(12);
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setBold(true);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->setCellValue($headerWord.$headerIndex, 'Фамилия и инициалы обучающегося');


//Заголовк дисциплин
$headerWord++;

$rangeDisciplines = $headerWord.$headerIndex.':'.chr(ord($headerWord)+(count($disciplines) - 1)).$headerIndex;

$sheet->mergeCells($rangeDisciplines);
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setSize(12);
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setBold(true);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setWrapText(true);
$sheet->setCellValue($headerWord.$headerIndex, 'Наименование предмета');
$sheet->getColumnDimension($headerWord)->setAutoSize(true);

//Дисциплины
foreach($disciplines as $key=>$discipline){
	$sheet->getStyle($headerWord.($headerIndex + 1))->getFont()->setSize(12);
    $sheet->getStyle($headerWord.($headerIndex + 1))->getFont()->setBold(true);
    $sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setTextRotation(90);
    $sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setWrapText(true);
	$sheet->setCellValue($headerWord.($headerIndex + 1), $discipline->name);
	$headerWord++;
}

$sheet->mergeCells($headerWord.$headerIndex.':'.$headerWord.($headerIndex + 1));
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setSize(12);
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setBold(true);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setTextRotation(90);
$sheet->setCellValue($headerWord.$headerIndex, 'Средний балл');

$headerWord++;

$rangeNoVisited = $headerWord.$headerIndex.':'.chr(ord($headerWord)+2).$headerIndex;
$sheet->mergeCells($rangeNoVisited);
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setSize(12);
$sheet->getStyle($headerWord.$headerIndex)->getFont()->setBold(true);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle($headerWord.$headerIndex)->getAlignment()->setWrapText(true);
$sheet->setCellValue($headerWord.$headerIndex, 'Пропуск занятий');

$sheet->getStyle($headerWord.($headerIndex + 1))->getFont()->setSize(12);
$sheet->getStyle($headerWord.($headerIndex + 1))->getFont()->setBold(true);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setTextRotation(90);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setWrapText(true);
$sheet->setCellValue($headerWord.($headerIndex + 1), 'По уважительной причине');

$headerWord++;

$sheet->getStyle($headerWord.($headerIndex + 1))->getFont()->setSize(12);
$sheet->getStyle($headerWord.($headerIndex + 1))->getFont()->setBold(true);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setTextRotation(90);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setWrapText(true);
$sheet->setCellValue($headerWord.($headerIndex + 1), 'По неуважительной причине');

$headerWord++;

$sheet->getStyle($headerWord.($headerIndex + 1))->getFont()->setSize(12);
$sheet->getStyle($headerWord.($headerIndex + 1))->getFont()->setBold(true);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle($headerWord.($headerIndex + 1))->getAlignment()->setTextRotation(90);
$sheet->setCellValue($headerWord.($headerIndex + 1), 'Всего');

$sheet->getRowDimension($headerIndex + 1)->setRowHeight(100);


////////////////////////////////////////
//Заполнение информации
////////////////////////////////////////

$contentIndex = $headerIndex + 1;

foreach($students as $key=>$student){

    $NoVisited = 0;
    $GoodReason = 0;
    $studentScore= [];

	$contentIndex++;
	$rowWord = $startWord;

	//Колонка с номером
	$sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
	$sheet->setCellValue($rowWord.$contentIndex, $key + 1);

	//Колонка со студентом
	$rowWord++;
	$sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
	$sheet->setCellValue($rowWord.$contentIndex, $student->name);

	//Предметы
	foreach($disciplines as $discipline){
        $rowWord++;

		$foundScore = false;
		foreach($scores as $score){
			if ($score->id == $student->id && $score->discipline == $discipline->id){

                foreach($typeScores as $typeScore){
                    if ($typeScore->id == $discipline->firstParam){
                        $foundScore = true;
                        if (mb_strtolower($typeScore->name) == 'зачет'){
                            $sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
                            $sheet->setCellValue($rowWord.$contentIndex, ($score->name == '0' ? '2' : '5'));
                            $NoVisited = $NoVisited + $score->NoVisited;
                            $GoodReason = $GoodReason + $score->GoodReason;
                            break;
                        }else{
                            $sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
                            $sheet->setCellValue($rowWord.$contentIndex, $score->name);
                            $studentScore[] = $score->name;
                            $NoVisited = $NoVisited + $score->NoVisited;
                            $GoodReason = $GoodReason + $score->GoodReason;
                            break;
                        }
                    }
                }
            }
            if ($foundScore){
                break;
            }
		}

        if (!$foundScore){
            foreach($typeScores as $typeScore) {
                if ($typeScore->id == $discipline->firstParam) {

                    if (mb_strtolower($typeScore->name) == 'зачет'){
                        $sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
                        $sheet->setCellValue($rowWord.$contentIndex, '2');
                    }else{
                        $sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
                        $sheet->setCellValue($rowWord.$contentIndex, '0');
                        $studentScore[] = '0';
                    }
                }
            }
        }
        $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
	}

    //Колонка со средним баллом
    $rowWord++;
    $sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
    $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->setCellValue($rowWord.$contentIndex, array_sum($studentScore) / (count($studentScore) == 0 ? 1 : count($studentScore)));

    //Неуважительные пропуски
    $rowWord++;
    $sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
    $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->setCellValue($rowWord.$contentIndex, $NoVisited);

    //Уважительные пропуски
    $rowWord++;
    $sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
    $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->setCellValue($rowWord.$contentIndex, $GoodReason);

    //Общее количество пропусков
    $rowWord++;
    $sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
    $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($rowWord.$contentIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->setCellValue($rowWord.$contentIndex, $GoodReason + $NoVisited);
}


//Строчка с итогами
$contentIndex++;
$rowWord = $startWord;

$rowWord++;
$sheet->getStyle($rowWord.$contentIndex)->getFont()->setSize(12);
$sheet->setCellValue($rowWord.$contentIndex, 'Средний балл');

$rowWord++;

foreach (range($rowWord, chr(ord($rowWord)+(count($disciplines)))) as $column){
    $score1 = [];
    foreach (range($headerIndex + 2, $contentIndex - 1) as $score){
       $score1[] = $sheet->getCell($column.$score)->getValue();
    }

    $sheet->getStyle($column.$contentIndex)->getFont()->setSize(12);
    $sheet->getStyle($column.$contentIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($column.$contentIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->setCellValue($column.$contentIndex, array_sum($score1) / count($score1));
}

foreach (range(chr(ord($rowWord)+(count($disciplines) + 1)), chr(ord($rowWord)+(3 + count($disciplines)))) as $column){
    $score1 = [];
    foreach (range($headerIndex + 2, $contentIndex - 1) as $score){
        $score1[] = $sheet->getCell($column.$score)->getValue();
    }

    $sheet->getStyle($column.$contentIndex)->getFont()->setSize(12);
    $sheet->getStyle($column.$contentIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($column.$contentIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->setCellValue($column.$contentIndex, array_sum($score1));
}

$allRange = $startWord.$startIndex.':'.chr(ord($headerWord)+(count($disciplines) - 2)).$contentIndex ;

$sheet->getStyle($allRange)
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN)
    ->setColor(new Color('00000000'));


foreach (range($startWord, chr(ord($startWord) + $countColumns - 1)) as $column){
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

foreach (range(chr(ord($startWord) + 3 + count($disciplines)), chr(ord($startWord) + 4 + count($disciplines))) as $column){
    $sheet->getColumnDimension($column)->setAutoSize(false);
    $sheet->getColumnDimension($column)->setWidth(10);
}



$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Statement.xlsx"');

$writer->save("php://output");
