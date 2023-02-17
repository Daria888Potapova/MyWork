<?php
require_once ('../Classes/dbConnect.php');
use DB\dbConnect;

//Получаем перечень дисциплин для отображения
$dbConnect = new dbConnect();
$error = '';

$query = "SELECT * FROM `disciplines`";

$result_query_select = $dbConnect::$mysqli->query($query);
if(!$result_query_select->num_rows){
    $error = 'Дисциплины отсутствуют';
}
?>


<div class="disciplines">
    <div class="disciplines_headerContainer">
        <h1 class="disciplines_header">Диcциплины</h1>
        <?php
        if (strtolower($_SESSION['name_role']) == 'teacher'){
            echo "<button onclick=\"openPage('EditDisciplines', -1, 'new', 'Новая дисциплина')\">Добавить</button>";
        }
        ?>

    </div>
    <table class="disciplines_table">
        <thead>
        <tr>
            <td class="disciplines_hidden" name="id">id</td>
            <td name="name">Название дисциплины</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            $birthday = explode('-', $rows['birthday']);
            echo '<tr onclick="openPage(\'EditDisciplines\','.$rows['id_discipline'].',\'view\', \'Дисциплина\')">';
            echo '<td class="disciplines_hidden">'.$rows['id_discipline'].'</td>';
            echo '<td>'.$rows['name_discipline'].'</td>';
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
