<?php
require_once ('../Classes/dbConnect.php');
require_once ('../Classes/SimpleObject.php');
require_once ('../Classes/Score.php');

use DB\dbConnect;

$dbConnect = new dbConnect();
$error = '';
$info = [];
$oldScores = [];

if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['id_academperformance']) && $_POST['id_academperformance'] >= 0)){

    if (isset($_POST['id_academperformance']) && $_POST['id_academperformance'] >= 0){

        $discipl = $_POST['discipline'];
        $students = $_POST['students'];

        $query = sprintf("DELETE FROM academperfomancescore WHERE id_academPerfomance=%d;", $_POST['id_academperformance']);
        $query .= "INSERT INTO academperfomancescore(id_academPerfomance, id_student, id_discipline, score, NoVisited, GoodReason) VALUES ";

        foreach ($students as $student){
            foreach ($discipl as $discipline) {
                    if ($_POST['score_'.$student.'_'.$discipline] != 0){
                        $query .= sprintf( "('%d', '%d', '%d', '%d', '%d', '%d'),", $_POST['id_academperformance'], $student, $discipline, $_POST['score_'.$student.'_'.$discipline], $_POST['NoVisited_'.$student.'_'.$discipline], $_POST['GoodReason_'.$student.'_'.$discipline]);

                    }
            }
        }

		if (count($students) > 0){
			$query = mb_substr($query, 0, -1);
		}else{
			$query .= "()";
		}


        $dbConnect::$mysqli->multi_query($query);


        if($dbConnect::$mysqli->next_result()){
            echo '<script src="../js/Scripts.js"></script>';
            echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
            echo '<label>';
            echo '<input type="text" name="page"/>';
            echo '<input type="text" name="id_form"/>';
            echo '<input type="text" name="open_type"/>';
            echo '<input type="text" name="form_title"/>';
            echo '</label>';
            echo '</form>';
            echo sprintf("<script> let temp = openPage('EditPerformance', %d, 'view', 'Информация об успеваемости') </script>", $_POST['id_academperformance']);
            exit();
        }else{
            mysqli_error($dbConnect::$mysqli);
        }

    }else{
        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM academperfomancescore WHERE id_academPerfomance=%d", $_POST['id_form'])) or die(mysqli_error($dbConnect::$mysqli));
        if($result_query_select->num_rows > 0){
            while( $row = $result_query_select->fetch_assoc()){
                $oldScores[] = new Score($row['id_student'], $row['score'], $row['id_discipline'], $row['NoVisited'], $row['GoodReason']);
            }

        }

        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM academperformance LEFT JOIN `groups` g on academperformance.id_group = g.id_group WHERE id_academPerformance=%d", $_POST['id_form']));
        if($result_query_select->num_rows > 0){
            $info = $result_query_select->fetch_assoc();
        }else{
            $error = 'Не удалось получить информацию';
        }
    }

}else{
    if (isset($_POST['group']) && !empty($_POST['group'])){


        $query = sprintf("INSERT INTO academperformance(id_group, term) VALUES ('%s', '%s')", $_POST['group'], $_POST['term']);
        $result_query_select = $dbConnect::$mysqli->query($query);
        if ($dbConnect::$mysqli->insert_id > 0) {
            echo '<script src="../js/Scripts.js"></script>';
            echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
            echo '<label>';
            echo '<input type="text" name="page"/>';
            echo '<input type="text" name="id_form"/>';
            echo '<input type="text" name="form_title"/>';
            echo '<input type="text" name="open_type"/>';
            echo '</label>';
            echo '</form>';
            echo sprintf("<script> let temp = openPage('EditPerformance', %d, 'view', 'Информация об успеваемости' ) </script>", $dbConnect::$mysqli->insert_id );
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось добавить новую успеваемость';
        }

    }
}


if ($_POST['open_type'] === 'new'){

    $query = "SELECT * FROM `groups`";

    if (strtolower($_SESSION['name_role']) != 'admin'){
        $query .= " WHERE id_user=".$_SESSION['id'];
    }
    $groups = $dbConnect::$mysqli->query($query);
}


//Получение перечня типа оценок
$query = "SELECT * FROM typescore";

$result = $dbConnect::$mysqli->query($query);

$typeScores = [];
while ($row = $result -> fetch_assoc()){
    $typeScores[] = new SimpleObject($row['id_typeScore'], $row['name_typeScore']);
}

//Получение списка предметов на семестр
$query = sprintf("SELECT * FROM academplan_detail LEFT JOIN academplan ON academplan.id_academplan = academplan_detail.id_academplan 
    LEFT JOIN academperformance ON academperformance.id_group = academplan.id_group 
    LEFT JOIN disciplines ON academplan_detail.id_discipline = disciplines.id_discipline
    WHERE academperformance.id_academPerformance = %d AND academplan_detail.term=%d", $_POST['id_form'] ?? $_POST["id_academperformance"], $info['term']);

$disciplines = $dbConnect::$mysqli->query($query);
$myDisciple = [];

while ($row = $disciplines -> fetch_assoc()){
    $discipli = new SimpleObject($row['id_discipline'], $row['name_discipline']);
    $discipli->addFirstParam($row['id_typeScore']);
    $myDisciple[] = $discipli;
}


//Получение списка студентов
$query = sprintf("SELECT * FROM students 
        LEFT JOIN academperformance ON students.id_group = academperformance.id_group 
         WHERE academperformance.id_academPerformance=%d", $_POST['id_form'] ?? $_POST["id_academperformance"]);

$students = $dbConnect::$mysqli->query($query);

$myStudents = [];

if ($students -> num_rows){
    while ($row = $students -> fetch_assoc()){
        $myStudents[] = new SimpleObject($row['id_student'], $row['FIO']);
    }
}


function getScore($score){
    switch($score){
    case '1':
        return 'Кол';
        break;
    case '2':
        return 'Неуд.';
        break;
    case '3':
        return 'Удовл.';
        break;
    case '4':
        return 'Хор.';
        break;
    case '5':
        return 'Отл.';
        break;
    }
}

?>


<div class="apEdit">

    <form class="apEdit_form" action="EditPerformance.php" method="post">
        <label>
            <input hidden name="id_academperformance" value="<?php echo $_POST['id_form'] ?>" />
        </label>
        <label>
            <input hidden name="title_academperformance" value="<?php echo $_POST['form_title'] ?>" />
        </label>

        <h1 class="apEdit_header"><?php echo $_POST['form_title'] ?></h1>

        <div class="infoContainer">


            <?php
            if ($_POST['open_type'] !== 'new') {
                echo sprintf("<label><b>Группа:</b> %s</label>", $info['name_group']);
            }else{
                echo '<label>Выберите группу</label>';
                echo '<select class="apEdit_form_Select" title="Группа" name="group">';
                echo sprintf("<option %s disabled>Выберите группу</option>", $groups -> num_rows == 0 ? 'selected' : '');
                while( $result = $groups-> fetch_assoc()){
                    echo sprintf("<option %s value=\"%s\">%s</option>", $result['id_group'] == $info['id_group'] ? 'selected' : '', $result['id_group'], $result['name_group']);
                }
                echo '</select>';
            }
            ?>
        </div>
        <div class="infoContainer">
        <?php
            if ($_POST['open_type'] !== 'new') {
                echo sprintf("<label><b>Семестр:</b> %s</label>", $info['term']);
            }else{
                echo '<label>Выберите семестр</label>';
                echo '<select class="apEdit_form_Select" title="Семестр" name="term">';
                echo "<option disabled>Выберите семестр</option>";
                foreach (range(1,8) as $number){
                    echo sprintf("<option value=\"%s\">%s</option>",  $number, $number);
                }
                echo '</select>';
            }
            ?>
        </div>


            <table class="apEdit_table">
                <thead>
                <tr>
                    <th></th>
                    <?php
                    foreach ($myDisciple as $item){
                        echo '<th>';
                        echo $item->name;
                        echo '<input type="hidden" name="discipline[]" value="'.$item->id.'"/>';
                        echo '</th>';
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($myStudents as $student){
                        echo '<tr>';

                        echo '<td>';
                        echo '<input type="hidden" name="students[]" value="'.$student->id.'" />';
                        echo $student->name;
                        echo '</td>';

                        foreach ($myDisciple as $item){

                            $found = false;
                            foreach ($oldScores as $score){
                                if ($score->id == $student->id && $score->discipline == $item->id){

                                    echo '<td>';
                                    echo '<label>Оценка: </label>';
                                    if ($_POST['open_type'] === 'view'){
                                        foreach ($typeScores as $typeScore){
                                            if ($typeScore->id == $item->firstParam){

                                                if(mb_strtolower($typeScore->name) == 'зачет'){
                                                    echo $score->name == '0' ? 'Не зачет' : 'Зачет';
                                                }else{
                                                    echo getScore($score->name);
                                                }
                                            }
                                        }

                                        echo '<br/><b>Пропуски занятий:</b>';
                                        echo '<br/>Неуважительные - '.$score->NoVisited;
                                        echo '<br/>Уважительные - '.$score->GoodReason;
                                    }else{
                                        foreach ($typeScores as $typeScore){
                                            if ($typeScore->id == $item->firstParam){
                                                if(mb_strtolower($typeScore->name) == 'зачет'){
                                                    echo sprintf('<select name="%s">', "score_".$student->id.'_'.$item->id);
                                                    echo sprintf('<option %s value="0">Не зачет</option>', $score->name == '0' ? 'selected' : '');
                                                    echo sprintf('<option %s value="1">Зачет</option>', $score->name == '1' ? 'selected' : '');
                                                }else{
                                                    echo sprintf('<input type="number" min="0" max="5" name="%s" value="%s">', "score_".$student->id.'_'.$item->id, $score->name);
                                                }
                                            }
                                        }
                                        echo '</select>';
                                        echo '<br/><b>Пропуски занятий:</b>';
                                        echo '<div>';
                                        echo '<label>Неуважительные </label>';
                                        echo sprintf('<input type="number" name="%s" value="%s">', "NoVisited_".$student->id.'_'.$item->id, $score->NoVisited);
                                        echo '</div>';
                                        echo '<div>';
                                        echo '<label>Уважительные </label>';
                                        echo sprintf('<input type="number" name="%s" value="%s">', "GoodReason_".$student->id.'_'.$item->id, $score->GoodReason);
                                        echo '</div>';
                                    }

                                    echo '</td>';
                                    $found = true;
                                    break;
                                }
                            }

                            if (!$found){
                                echo '<td>';
                                echo '<label>Оценка: </label>';
                                if ($_POST['open_type'] === 'view'){
                                    foreach ($typeScores as $typeScore){
                                        if ($typeScore->id == $item->firstParam){
                                            if(mb_strtolower($typeScore->name) == 'зачет'){
                                                echo 'Не зачет';
                                            }else{
                                                echo '0';
                                            }
                                        }
                                    }

                                    echo '<br/><b>Пропуски занятий:</b>';
                                    echo '<br/>Неуважительные - 0';
                                    echo '<br/>Уважительные - 0';
                                }else{
                                    foreach ($typeScores as $typeScore){
                                        if ($typeScore->id == $item->firstParam){
                                            if(mb_strtolower($typeScore->name) == 'зачет'){
                                                echo sprintf('<select name="%s">', "score_".$student->id.'_'.$item->id);
                                                echo '<option selected value="0">Не зачет</option>';
                                                echo '<option  value="1">Зачет</option>';
                                            }else{
                                                echo sprintf('<input type="number" name="%s" value="%s">', "score_".$student->id.'_'.$item->id, 0);
                                            }
                                        }
                                    }
                                    echo '</select>';
                                    echo '<br/><b>Пропуски занятий:</b>';
                                    echo '<div>';
                                    echo '<label>Неуважительные </label>';
                                    echo sprintf('<input type="number" name="%s" value="%s">', "NoVisited_".$student->id.'_'.$item->id, 0);
                                    echo '</div>';
                                    echo '<div>';
                                    echo '<label>Уважительные </label>';
                                    echo sprintf('<input type="number" name="%s" value="%s">', "GoodReason_".$student->id.'_'.$item->id, 0);
                                    echo '</div>';

                                }
                                echo '</td>';
                            }

                        }

                        echo '</tr>';
                    }
                ?>
                </tbody>
            </table>

        <?php

        //Если страница открыта для просмотра, то заменяем input на текстовое отображение
        if ($_POST['open_type'] === 'view'){
            echo sprintf("<input type='button' value='Изменить' onclick=\"openPage('%s', %d, '%s', '%s')\"/>", 'EditPerformance', $_POST['id_form'], 'edit',$_POST['form_title']);
        }else{
            echo '<input type="submit" value="Сохранить"/>';
        }
        ?>
    </form>

    <?php
    if (!empty($error)){
        echo $error;
    }
    ?>
</div>
