<?php

require_once ('../Classes/dbConnect.php');
use DB\dbConnect;


//Получаем список групп для заполнения select
$dbConnect = new dbConnect();


$query = "SELECT * FROM `groups`";

if (strtolower($_SESSION['name_role'] != 'admin')){
    $query .= sprintf(" WHERE id_user=%d", $_SESSION['id']);
}
$groups = $dbConnect::$mysqli->query($query);

?>

<script>
    function getReport(nameReport){
        let form = document.getElementsByName('reports_form')[0];
        form.action = '../Reports/' + nameReport + '.php';
        form.submit();
    }
</script>

<div class="reports">
    <h1 class="reports_header">Отчеты</h1>

    <h3>Настройки для отчетов</h3>
    <form action="Reports.php" method="post" name="reports_form" class="reports_form">
        <div class="reports_horizontalElem">
            <input type="hidden" name="report" value=""/>
            <div>
                <label for="startPeriod">Начало периода</label>
                <input name="startPeriod" type="date" value="<?php echo date('Y-m-d') ?>"/>
            </div>
            <div>
                <label for="endPeriod">Конец периода</label>
                <input name="endPeriod" type="date" value="<?php echo date('Y-m-d') ?>"/>
            </div>
            <div>
                <label for="group">Группа</label>
                <?php

                echo '<select title="Группа" name="group">';
                echo '<option '.($groups->num_rows == 0 ? 'selected' : '').' disabled>Выберите группу</option>';
                while ($rows = $groups->fetch_assoc()){
                    echo '<option value="'.$rows['id_group'].'">'.$rows['name_group'].'</option>';
                }
                echo '</select>';

                ?>
            </div>
            <div>
                 <label for="term">Семестр</label>
                 <?php
                 echo '<select title="Семестр" name="term">';
                 echo '<option disabled>Выберите семестр</option>';
                 foreach (range(1,8) as $number){
                     echo sprintf("<option value=\"%s\">%s</option>",  $number, $number);
                 }
                 echo '</select>';
                 ?>
            </div>
        </div>

    <h3>Перечень отчетов</h3>
        <div class="reports_report">
<!--            <a href="#" onclick="getReport('InfoGroup')">Сводный отчет по группе</a>-->
            <a href="#" onclick="getReport('FullReport')">Полный отчет</a>
            <a href="#" onclick="getReport('Statement')">Сводная ведомость</a>
        </div>

    </form>
</div>
