<?php
    require_once ('../Classes/dbConnect.php');
    require_once ('../Settings/params.php');
    use DB\dbConnect;
    use params\Params;

    //Получаем список студентов
    $dbConnect = new dbConnect();
    $error = '';
    $query = "SELECT * FROM `students` LEFT JOIN `groups` ON `students`.id_group = `groups`.id_group";

    if (strtolower($_SESSION['name_role']) != 'admin'){
        $query .= sprintf(" WHERE `groups`.id_user=%d", $_SESSION['id']);
    }
    $result_query_select = $dbConnect::$mysqli->query($query);
    if(!$result_query_select->num_rows){
        $error = 'Студенты отсутствуют';
    }
?>

<div class="students">
    <div class="students_headerContainer" >
        <h1 class="students_header">Студенты</h1>
        <button onclick="openPage('EditStudent', -1 , 'new')">Добавить</button>
    </div>

    <table class="students_table">
        <thead>
        <tr>
            <td class="students_hidden" name="id">id</td>
            <td name="fio">ФИО</td>
            <td name="group">Группа</td>
            <td name="birthday">День рождения</td>
            <td name="mobile">Телефон</td>
        </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            $birthday = explode('-', $rows['birthday']);
            echo '<tr onclick="openPage(\'EditStudent\','.$rows['id_student'].', \'view\', \'Информация о студенте\')">';
            echo '<td class="students_hidden">'.$rows['id_student'].'</td>';
            echo '<td class="students_table_FIO">'.'<img src="'.Params::$AvatarPath.$rows['Avatar'].'" alt="avatar"/><p>'.$rows['FIO'].'</p></td>';
            echo '<td>'.$rows['name_group'].'</td>';
            echo '<td>'.$birthday[2].'.'.$birthday[1].'.'.$birthday[0].'</td>';
            echo '<td>'.$rows['phone'].'</td>';
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
