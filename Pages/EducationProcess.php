<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;

$dbConnect = new dbConnect();
$error = '';

$query = "SELECT * FROM academplan LEFT JOIN `groups` g on g.id_group = academplan.id_group";

if (strtolower($_SESSION['name_role']) != 'admin'){
    $query .= sprintf(" WHERE id_user=%s", $_SESSION['id']);
}

$result_query_select = $dbConnect::$mysqli->query($query);
if(!$result_query_select->num_rows){
    $error = 'Не удалось получить учебный процесс';
}
?>


<div class="educationProcess">
    <div class="educationProcess_headerContainer" >
        <h1 class="educationProcess_header">Учебный процесс</h1>
        <?php
        if (strtolower($_SESSION['name_role']) == 'teacher'){
            echo "<button onclick=\"openPage('EditEducationProcess', -1, 'new', 'Новый учебный процесс')\">Добавить</button>";
        }
        ?>

    </div>
    <table class="educationProcess_table">
        <thead>
        <tr>
            <td class="educationProcess_hidden" name="id">id</td>
            <td name="group">Группа</td>
            <td name="dateCreate">Дата создания</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            $date = explode('-', $rows['date_academplan']);
            echo '<tr onclick="openPage(\'EditEducationProcess\','.$rows['id_academplan'].',\'view\', \'Информация об учебном процессе\')">';
            echo '<td class="educationProcess_hidden">'.$rows['id_academplan'].'</td>';
            echo '<td>'.$rows['name_group'].'</td>';
            echo '<td>'.$date[2].'.'.$date[1].'.'.$date[0].'</td>';
            echo '</tr>';
        }
        ?>

        </tbody>

    </table>
</div>
