<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;

$dbConnect = new dbConnect();
$error = '';

$query = "SELECT * FROM academperformance LEFT JOIN `groups` g on academperformance.id_group = g.id_group";

if (strtolower($_SESSION['name_role']) != 'admin'){
    $query .= sprintf(" WHERE g.id_user='%d'", $_SESSION['id']);
}

$result_query_select = $dbConnect::$mysqli->query($query) or die(mysqli_error($dbConnect::$mysqli));
if(!$result_query_select->num_rows){
    $error = 'Успеваемость пустая';
}
?>

<div class="ap">
    <div class="ap_headerContainer" >
        <h1 class="ap_header">Журнал успеваемости</h1>
        <?php
        if (strtolower($_SESSION['name_role']) == 'teacher'){
            echo "<button onclick=\"openPage('EditPerformance', -1, 'new', 'Новая успеваемость')\">Добавить</button>";
        }
        ?>
    </div>
    <table class="ap_table">
        <thead>
        <tr>
            <td class="ap_hidden" name="id">id</td>
            <td name="group">Группа</td>
            <td name="term">Семестр</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            echo '<tr onclick="openPage(\'EditPerformance\','.$rows['id_academPerformance'].', \'view\', \'Успеваемость по группе '.$rows['name_group'].'\')">';
            echo '<td class="ap_hidden">'.$rows['id_academPerformance'].'</td>';
            echo '<td>'.$rows['name_group'].'</td>';
            echo '<td>'.$rows['term'].'</td>';
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
