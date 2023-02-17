<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;


//Получаем список пользователей системы
$dbConnect = new dbConnect();
$error = '';
$query = "SELECT * FROM classtime LEFT JOIN `groups` g on g.id_group = classtime.id_group";

if (strtolower($_SESSION['name_role']) != 'admin'){
    $query .= " WHERE g.id_user=".$_SESSION['id'];
}

$result_query_select = $dbConnect::$mysqli->query($query);
if(!$result_query_select->num_rows){
    $error = 'Тут пусто!';
}
?>


<div class="classTime">
    <div class="classTime_headerContainer" >
        <h1 class="classTime_header">Классные часы</h1>
        <button onclick="openPage('EditClassTime', -1 , 'new')">Добавить</button>
    </div>

    <table class="classTime_table">
        <thead>
        <tr>
            <td class="classTime_hidden" name="id">id</td>
            <td name="data">Дата</td>
            <td name="theme">Тема</td>
            <td name="group">Группа</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            echo '<tr onclick="openPage(\'EditClassTime\','.$rows['id_classtime'].',\'view\', \'Классный час\')">';
            echo '<td class="classTime_hidden">'.$rows['id_classtime'].'</td>';
            echo '<td>'.$rows['date_classtime'].'</td>';
            echo '<td>'.$rows['theme_classtime'].'</td>';
            echo '<td>'.$rows['name_group'].'</td>';
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
