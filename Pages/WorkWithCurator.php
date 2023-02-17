<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;


//Получаем список работ с куратором
$dbConnect = new dbConnect();
$error = '';
$result_query_select = $dbConnect::$mysqli->query("SELECT * FROM workwithcurator LEFT JOIN users ON workwithcurator.id_user = users.id_user");
if(!$result_query_select->num_rows){
    $error = 'Не удалось получить работы кураторов';
}
?>

<div class="workWithCurator">
    <h1 class="workWithCurator_header">Работы кураторов</h1>
    <table class="workWithCurator_table">
        <thead>
        <tr>
            <td class="workWithCurator_hidden" name="id">id</td>
            <td name="fio">Куратор проекта</td>
            <td name="name">Название работы</td>
            <td name="time">Часы работы</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            echo '<tr>';
            echo '<td class="workWithCurator_hidden">'.$rows['id_workWithCurator'].'</td>';
            echo '<td>'.$rows['fio'].'</td>';
            echo '<td>'.$rows['name_work'].'</td>';
            echo '<td>'.$rows['time_work'].'</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <?php
    if (!empty($error)){
        echo $error;
    }
    ?>
</div>
