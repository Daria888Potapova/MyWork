<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;

$dbConnect = new dbConnect();
$error = '';
$query = "SELECT * FROM `groups`";

if (strtolower($_SESSION['name_role']) != 'admin'){
    $query .= sprintf(" WHERE id_user=%d", $_SESSION['id']);
}
$result_query_select = $dbConnect::$mysqli->query($query);
if(!$result_query_select->num_rows){
    $error = 'Вы не являетесь куратором ни одной группы';
}
?>


<div class="groups">
    <div class="groups_headerContainer" >
        <h1 class="groups_header">Учебные группы</h1>
        <?php
        if (strtolower($_SESSION['name_role']) == 'admin'){
           echo "<button onclick=\"openPage('EditGroup', -1, 'new', 'Новая группа')\">Добавить</button>";
        }
        ?>

    </div>
    <table class="groups_table">
        <thead>
        <tr>
            <td class="groups_hidden" name="id">id</td>
            <td name="name">Название группы</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            echo '<tr onclick="openPage(\'EditGroup\','.$rows['id_group'].',\'view\', \'Информация о группе\')">';
            echo '<td class="groups_hidden">'.$rows['id_group'].'</td>';
            echo '<td>'.$rows['name_group'].'</td>';
            echo '</tr>';
        }
        ?>

        </tbody>

    </table>
</div>
