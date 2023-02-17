<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;

$dbConnect = new dbConnect();
$error = '';
$result_query_select = $dbConnect::$mysqli->query("SELECT * FROM events 
    LEFT JOIN levelevent l on l.id_levelEvent = events.id_levelEvent
    LEFT JOIN typeevent t on t.id_typeEvent = events.id_typeEvent
    LEFT JOIN resultevent r on r.id_resultEvent = events.id_resultEvent");
if(!$result_query_select->num_rows){
    $error = 'Не удалось получить события';
}
?>


<div class="events">
    <div class="events_headerContainer" >
        <h1 class="events_header">Мероприятия</h1>
        <?php
        if (strtolower($_SESSION['name_role']) == 'teacher'){
            echo "<button onclick=\"openPage('EditEvents', -1, 'edit', 'Новое мероприятие')\">Добавить</button>";
        }
        ?>

    </div>
    <table class="events_table">
        <thead>
        <tr>
            <td class="events_hidden" name="id">id</td>
            <td name="date">Дата</td>
            <td name="event">Мероприятие</td>
            <td name="level">Уровень</td>
            <td name="type">Тип</td>
            <td name="result">Результат</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            $date = explode('-', $rows['date_event']);
            echo '<tr onclick="openPage(\'EditEvents\','.$rows['id_event'].',\'view\', \'Информация о мероприятии\')">';
            echo '<td class="events_hidden">'.$rows['id_event'].'</td>';
            echo '<td>'.$date[2].'.'.$date[1].'.'.$date[0].'</td>';
            echo '<td>'.$rows['name_event'].'</td>';
            echo '<td>'.$rows['name_levelEvent'].'</td>';
            echo '<td>'.$rows['name_typeEvent'].'</td>';
            echo '<td>'.$rows['name_resultEvent'].'</td>';
            echo '</tr>';
        }
        ?>

        </tbody>

    </table>
</div>
