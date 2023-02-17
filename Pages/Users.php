<?php
    require_once ('../Classes/dbConnect.php');
    require_once ('../Settings/params.php');
    use params\Params;
    use DB\dbConnect;


    //Получаем список пользователей системы
    $dbConnect = new dbConnect();
    $error = '';
    $query = sprintf("SELECT * FROM `users` LEFT JOIN `role` ON `users`.id_role = `role`.id_role WHERE NOT id_user = %d", $_SESSION['id']);
    $result_query_select = $dbConnect::$mysqli->query($query);
    if(!$result_query_select->num_rows){
        $error = 'Не удалось получить пользователей';
    }
?>


<div class="users">
    <div class="users_headerContainer" >
        <h1 class="users_header">Пользователи</h1>
        <button onclick="openPage('EditUser', -1 , 'new')">Добавить</button>
    </div>

    <table class="users_table">
        <thead>
            <tr>
                <td class="users_hidden" name="id">id</td>
                <td name="fio">ФИО</td>
                <td name="email">E-Mail</td>
                <td name="role">Роль</td>
            </tr>
        </thead>
        <tbody>
        <?php
        while ( $rows = $result_query_select->fetch_assoc() ) {
            echo '<tr onclick="openPage(\'EditUser\','.$rows['id_user'].',\'view\', \'Информация о пользователе\')">';
            echo '<td class="users_hidden">'.$rows['id_user'].'</td>';
            echo '<td class="users_table_FIO">'.'<img src="'.Params::$AvatarPath.$rows['avatar'].'" alt="avatar"/><p>'.$rows['fio'].'</p></td>';
            echo '<td>'.$rows['email'].'</td>';
            echo '<td>'.$rows['name_role'].'</td>';
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
