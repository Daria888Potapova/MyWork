
<?php
require_once('../Classes/dbConnect.php');
require_once ('../Classes/SimpleObject.php');
require_once ('../Classes/AcademDiscipline.php');
use DB\dbConnect;


$dbConnect = new dbConnect();
$error = '';
$levelEvents = [];
$typeEvents = [];
$resultEvents = [];

$educProcess = [];
$disciplines = [];
$allDisciplines = [];
$typeScores = [];


if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['id_academPlan']) && $_POST['id_academPlan'] >= 0)){

    if (isset($_POST['id_academPlan']) && $_POST['id_academPlan'] >= 0){

            $discipl = $_POST['disciplines'];

            $query = sprintf("DELETE FROM academplan_detail WHERE id_academplan=%d;", $_POST['id_academPlan']);
            $query .= "INSERT INTO academplan_detail(id_academplan, term, id_discipline, id_typeScore, timeTerm) VALUES ";
            foreach ($discipl as $key=>$discipline) {
                for ($term=1; $term<=8; $term++){
                    if ($_POST['term_'.($key+1).'_'.$term] != 0){
                        $query .= sprintf( "('%d', '%d', '%d', '%d', '%d'),", $_POST['id_academPlan'], $term, $discipline, $_POST[($key+1).'_'.$term.'_select'], $_POST['term_'.($key+1).'_'.$term]);

                    }

                }

            }
            $query = mb_substr($query, 0, -1);

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
                echo sprintf("<script> let temp = openPage('EditEducationProcess', %d, 'view', 'Информация об учебной процессе') </script>", $_POST["id_academPlan"]);
                exit();
            }else{
                mysqli_error($dbConnect::$mysqli);
            }



    }else{

        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM  academplan LEFT JOIN `groups` g on g.id_group = academplan.id_group WHERE academplan.id_academplan=%d", $_POST['id_form']));
        if($result_query_select->num_rows > 0){
            $educProcess = $result_query_select->fetch_assoc();
        }else{
            $error = 'Не удалось получить мероприятие';
        }

        $result_query = $dbConnect::$mysqli->query(sprintf("SELECT * FROM academplan_detail LEFT JOIN disciplines d on d.id_discipline = academplan_detail.id_discipline LEFT JOIN typescore t on t.id_typeScore = academplan_detail.id_typeScore WHERE academplan_detail.id_academplan=%d", $_POST['id_form']));
        if($result_query->num_rows){
            while( $row = $result_query->fetch_assoc()){
                $addDiscipline = true;
                $foundKey = -1;
                foreach ($disciplines as $key=>$discipline){
                    if ($discipline->id_discipline == $row['id_discipline']){
                        $addDiscipline = false;
                        $foundKey = $key;
                        break;
                    }
                }

                if ($addDiscipline){
                    $disciplines[] =  new AcademDiscipline($row['id_academPlanDetail'], $row['term'], $row['id_discipline'], $row['name_discipline'],$row['id_typeScore'], $row['name_typeScore'], $row['timeTerm']);

                }else{
                    $disciplines[$foundKey]->addTerm($row['term'], $row['timeTerm'], $row['id_typeScore'], $row['name_typeScore']);
                }
            }
        }else{
            $error = 'Не удалось получить предметы';
        }
    }

}else{

    if (isset($_POST['group']) && !empty($_POST['group'])){

        $query = sprintf("INSERT INTO academplan(id_group, date_academplan) VALUES('%d', '%s')", $_POST['group'], $_POST['date_academplan']);
        $result_query_select = $dbConnect::$mysqli->query($query);
        $newId = $dbConnect::$mysqli->insert_id;

        if ($newId){
            $discipl = $_POST['disciplines'];
            $query = "INSERT INTO academplan_detail(id_academplan, term, id_discipline, id_typeScore, timeTerm) VALUES ";
            foreach ($discipl as $key=>$discipline) {
                for ($term=1; $term<=8; $term++){
                    if ($_POST['term_'.($key+1).'_'.$term] != 0){
                        $query .= sprintf( "('%d', '%d', '%d', '%d', '%d'),", $newId, $term, $discipline, $_POST[($key+1).'_'.$term.'_select'], $_POST['term_'.($key+1).'_'.$term]);
                    }

                }

            }
            $query = mb_substr($query, 0, -1);

            $dbConnect::$mysqli->multi_query($query);

            echo '<script src="../js/Scripts.js"></script>';
            echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
            echo '<label>';
            echo '<input type="text" name="page"/>';
            echo '<input type="text" name="id_form"/>';
            echo '<input type="text" name="form_title"/>';
            echo '<input type="text" name="open_type"/>';
            echo '</label>';
            echo '</form>';
            echo sprintf("<script> let temp = openPage('EditEducationProcess', %d, 'view', 'Информация об учебном процессе' ) </script>", $newId);
            exit();

        }

    }
}

if ($_POST['open_type'] !== 'view'){

    if ($_POST['open_type'] == 'new'){
        $groups = [];
        $query = "SELECT * FROM `groups`";

        if (strtolower($_SESSION['name_role']) != 'admin'){
            $query .= sprintf(" WHERE id_user=%s", $_SESSION['id']);
        }

        $group = $dbConnect::$mysqli->query($query);
        if($group->num_rows){
            while( $row = $group->fetch_assoc()) {
                $groups[] = new SimpleObject($row['id_group'], $row['name_group']);
            }
        }else{
            $error .= 'Не удалось получить группы';
        }
    }

    $result = $dbConnect::$mysqli->query("SELECT * FROM typescore");
    if($result->num_rows){
        while( $row = $result->fetch_assoc()) {
            $typeScores[] = new SimpleObject($row['id_typeScore'], $row['name_typeScore']);
        }
    }else{
        $error .= 'Не удалось получить часы';
    }

    $result = $dbConnect::$mysqli->query("SELECT * FROM disciplines");
    if($result->num_rows){
        while( $row = $result->fetch_assoc()) {
            $allDisciplines[] = new SimpleObject($row['id_discipline'], $row['name_discipline']);
        }
    }else{
        $error .= 'Не удалось получить предметы';
    }

}
?>

 <script>

     let disciplines = <?php echo json_encode($allDisciplines) ?>;
     let types = <?php echo json_encode($typeScores) ?> ;


        function addDiscipline(){

            let table = document.querySelector('.editEducationProcess_disciplinesTable tbody')
            console.log(table.children.length)

            let row = document.createElement('tr')

            //Добавление предмета
            let disciple = document.createElement('td')

            let selectDisciple = document.createElement('select')
            selectDisciple.setAttribute('class', 'editEducationProcess_form_Select');
            selectDisciple.setAttribute('title', 'Предмет');
            selectDisciple.setAttribute('name', 'disciplines[]');

            disciplines.forEach((elem)=> {
                let option = document.createElement('option');
                option.setAttribute('value', elem.id);
                option.innerHTML = elem.name;

                selectDisciple.appendChild(option)
            })

            disciple.appendChild(selectDisciple)
            row.appendChild(disciple)


            //Добавление ячеек семестра
            for (let i= 1; i<=8; i++){
                let td = document.createElement('td');
                let cell = document.createElement('div')

                let label1 = document.createElement('label');
                label1.innerHTML = "Тип зачета";

                let select = document.createElement('select');
                select.setAttribute('name', (table.children.length+1) + '_' + i + '_select')

                types.forEach((type) => {
                    let option = document.createElement('option');
                    option.setAttribute('value', type.id);
                    option.innerHTML = type.name
                    select.appendChild(option)
                })

                let label2 = document.createElement('label');
                label2.innerHTML = "Часы";

                let input = document.createElement('input')
                input.setAttribute('name', 'term_'+ (table.children.length+1) + '_' + i);
                input.setAttribute('type', 'number');

                cell.appendChild(label1)
                cell.appendChild(select)
                cell.appendChild(label2)
                cell.appendChild(input)
                td.appendChild(cell)
                row.appendChild(td)
            }

            table.appendChild(row)
        }
</script>


<div class="editEducationProcess">
    <h1 class="editEducationProcess_header">Информация об учебном процессе</h1>

    <form class="editEducationProcess_form" action="EditEducationProcess.php" method="post" name="editEducationProcess_form">
        <label class="editEducationProcess_hidden">
            <input  name="id_academPlan" type="text" value="<?php echo $_POST['id_form'] ?>">
        </label>

        <?php
        if ($_POST['open_type'] !== 'new') {
            echo sprintf("<label><b>Группа:</b> %s</label>", $educProcess['name_group']);
        }else{
            echo '<label><b>Выберите группу</b></label>';
            echo '<select class="editEducationProcess_form_Select" title="Группа" name="group">';
            echo sprintf("<option %s disabled>Выберите группу</option>", count($groups)  == 0 ? 'selected' : '');
            foreach($groups as $group){
                echo sprintf("<option %s value=\"%s\">%s</option>", $group->id == $educProcess['id_group'] ? 'selected' : '', $group->id, $group->name);
            }
            echo '</select>';
        }
        ?>

        <label for="date_academplan"><b>Дата создания:</b> <?php echo $_POST['open_type'] !== 'new'  ? $educProcess['date_academplan'] : ''   ?></label>
        <?php echo $_POST['open_type'] == 'new' ? '<input id="date_academplan" type="date" name="date_academplan" value="'.$educProcess['date_academplan'].'" />' : '' ?>

        <div class="editEducationProcess_form_membersContainer">

            <h3>Предметы</h3>

            <table class="editEducationProcess_disciplinesTable">
                <thead>
                    <tr>
                        <th rowspan="2">Предмет</th>
                        <th colspan="2">1 курс</th>
                        <th colspan="2">2 курс</th>
                        <th colspan="2">3 курс</th>
                        <th colspan="2">4 курс</th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th>6</th>
                        <th>7</th>
                        <th>8</th>
                    </tr>
                </thead>
                <tbody>
                <?php

                            foreach ($disciplines as $key=>$discipline) {

                                echo '<tr>';

                                if ($_POST['open_type'] === 'view') {

                                    echo '<td>';
                                    echo sprintf("<p class='editEducationProcess_form_members'('EditDiscipline', %d, 'view', 'Просмотр дисциплины')\">%s </p>", $discipline->id_discipline, $discipline->name_discipline);
                                    echo '</td>';

                                    $terms = $discipline->terms;

                                    for ($index= 1; $index <= 8; $index++){
                                        echo '<td>';
                                        $found = false;
                                        foreach ($terms as $term){
                                            if ((int)$term['term'] == $index){
                                                echo $term['name_score'].', ';
                                                echo $term['value'];
                                                $found = true;
                                            }
                                        }

                                        if (!$found){
                                            echo 0;
                                        }
                                        echo '</td>';
                                    }



                                }else{
                                    echo '<td>';
                                    echo '<select class="editEducationProcess_form_Select" title="Предмет" name="disciplines[]">';
                                    foreach ($allDisciplines as $discipline1){
                                        echo sprintf("<option %s value=\"%s\">%s</option>", $discipline1->id == $discipline->id_discipline ? 'selected' : '', $discipline1->id, $discipline1->name);
                                    }
                                    echo '</select>';
                                    echo '</td>';


                                    $terms = $discipline->terms;

                                    for ($index= 1; $index <= 8; $index++){
                                        echo '<td>';
                                        echo '<div>';
                                        $found = false;
                                        foreach ($terms as $term){
                                            if ((int)$term['term'] == $index){
                                                echo '<label>Тип зачета</label>';
                                                echo '<select name="'.($key+1).'_'.$index.'_select">';
                                                foreach ($typeScores as $typeScore){
                                                    echo sprintf("<option  %s value=\"%s\">%s</option>", $typeScore->id ==  $term['typeScore'] ? 'selected' : '', $typeScore->id, $typeScore->name);
                                                }
                                                echo '<select>';
                                                echo '<label>Часы</label>';
                                                echo '<input name="term_'.$discipline->id_discipline.'_'.$term['term'].'" type="number" value="'.$term['value'].'"/>';
                                                $found = true;
                                            }
                                        }

                                        if (!$found){
                                            echo '<label>Тип зачета</label>';
                                            echo '<select name="'.($key+1).'_'.$index.'_select">';
                                            foreach ($typeScores as $typeScore){
                                                echo sprintf("<option   value=\"%s\">%s</option>", $typeScore->id, $typeScore->name);
                                            }
                                            echo '<select>';
                                            echo '<label>Часы</label>';
                                            echo '<input name="term_'.$discipline->id_discipline.'_'.$index.'" type="number" value="0"/>';
                                        }

                                        echo '</div>';
                                        echo '</td>';
                                    }

                                }

                                echo '</tr>';
                            }
                ?>
                </tbody>
            </table>

        </div>

        <?php
        if ($_POST['open_type'] !== 'view'){
            echo '<button type="button" class="editEducationProcess_form_addMember" onclick="addDiscipline()">Добавить предмет</button>';
        }
        ?>



        <input <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditEducationProcess\','.$_POST['id_form'].', \'edit\', \'Редактирование мероприятия\')"' : 'type="submit"' ?> value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">
    </form>
</div>
