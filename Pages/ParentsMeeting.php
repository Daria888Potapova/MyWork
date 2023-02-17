<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;

$dbConnect = new dbConnect();
$error = '';
$result_query_select = $dbConnect::$mysqli->query("SELECT * FROM parentsmeeting");
if(!$result_query_select->num_rows){
    $error = 'Не удалось получить собрания';
}
?>


<div class="meeting">
    <div class="meeting_headerContainer" >
        <h1 class="meeting_header">Родительские собрания</h1>
        <?php
        if (strtolower($_SESSION['name_role']) == 'teacher'){
            echo "<button onclick=\"openPage('EditParentsMeeting', -1, 'edit', 'Новое собрание')\">Добавить</button>";
        }
        ?>

    </div>
    <table class="meeting_table">
        <thead>
        <tr>
            <td class="meeting_hidden" name="id">id</td>
            <td name="date">Дата</td>
            <td name="event">Тема</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            $date = explode('-', $rows['date_parentmeeting']);
            echo '<tr onclick="openPage(\'EditParentsMeeting\','.$rows['id_parentmeeting'].',\'view\', \'Информация о собрании\')">';
            echo '<td class="meeting_hidden">'.$rows['id_parentmeeting'].'</td>';
            echo '<td>'.$date[2].'.'.$date[1].'.'.$date[0].'</td>';
            echo '<td>'.$rows['theme_parentmeeting'].'</td>';

            echo '</tr>';
        }
        ?>

        </tbody>

    </table>
</div>
