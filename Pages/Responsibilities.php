<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;

//Получаем список обязанностей
$dbConnect = new dbConnect();
$error = '';

$query = "SELECT * FROM responsibilities LEFT JOIN students s on s.id_student = responsibilities.id_student LEFT JOIN `groups` g on s.id_group = g.id_group";

if (strtolower($_SESSION['name_role']) != 'admin'){
    $query .= sprintf(" WHERE id_user=%d", $_SESSION['id']);
}
$result_query_select = $dbConnect::$mysqli->query($query);
if(!$result_query_select->num_rows){
    $error = 'Не удалось получить активистов';
}
?>


<div class="responsibilities">
    <div class="responsibilities_headerContainer" >
        <h1 class="responsibilities_header">Актив групп</h1>
        <?php
        if (strtolower($_SESSION['name_role']) == 'teacher'){
            echo "<button onclick=\"openPage('EditResponsibilities', -1, 'new', 'Новый актив группы')\">Добавить</button>";
        }
        ?>

    </div>
    <table class="responsibilities_table">
        <thead>
        <tr>
            <td class="responsibilities_hidden" name="id">id</td>
            <td name="name">Группа</td>
            <td name="student">Студент</td>
            <td name="responsibility">Обязанность</td>
            <td name="date">Дата создания</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            echo '<tr onclick="openPage(\'EditResponsibilities\','.$rows['id_responsibility'].',\'view\', \'Информация о выбранном активисте\')">';
            echo '<td class="responsibilities_hidden">'.$rows['id_responsibility'].'</td>';
            echo '<td>'.$rows['name_group'].'</td>';
            echo '<td>'.$rows['FIO'].'</td>';
            echo '<td>'.$rows['name_responsibility'].'</td>';
            echo '<td>'.$rows['date_responsibility'].'</td>';
            echo '</tr>';
        }
        ?>

        </tbody>

    </table>
</div>
