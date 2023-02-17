
<?php
require_once('../Classes/dbConnect.php');
use DB\dbConnect;


$dbConnect = new dbConnect();
$error = '';
$users = [];
$group = [];


if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['id_group']) && $_POST['id_group'] >= 0)){

    if (isset($_POST['id_group']) && $_POST['id_group'] >= 0){


        $result_query_select = $dbConnect::$mysqli->query(sprintf("UPDATE `groups` SET name_group='%s', id_user='%d' WHERE id_group='%d'", $_POST['name_group'], $_POST['user'], $_POST['id_group']));

        if ($result_query_select){
            echo '<script src="../js/Scripts.js"></script>';
            echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
            echo '<label>';
            echo '<input type="text" name="page"/>';
            echo '<input type="text" name="id_form"/>';
            echo '<input type="text" name="open_type"/>';
            echo '<input type="text" name="form_title"/>';
            echo '</label>';
            echo '</form>';
            echo sprintf("<script> let temp = openPage('EditGroup', %d, 'view', 'Информация о группе') </script>", $_POST["id_group"]);
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось сохранить изменения';
        }

    }else{

        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM `groups` LEFT OUTER JOIN users u on `groups`.id_user = u.id_user WHERE id_group=%d", $_POST['id_form']));
        if($result_query_select->num_rows > 0){
            $group = $result_query_select->fetch_assoc();
        }else{
            $error = 'Не удалось получить пользователя';
        }
    }

}else{

    if (isset($_POST['name_group']) && !empty($_POST['name_group'])){


        $query = sprintf("INSERT INTO `groups`(name_group, id_user) VALUES ( '%s', %d)", $_POST['name_group'], $_POST['user']);
        $result_query_select = $dbConnect::$mysqli->query($query);
        if ($dbConnect::$mysqli->insert_id > 0) {
            echo '<script src="../js/Scripts.js"></script>';
            echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
            echo '<label>';
            echo '<input type="text" name="page"/>';
            echo '<input type="text" name="id_form"/>';
            echo '<input type="text" name="form_title"/>';
            echo '<input type="text" name="open_type"/>';
            echo '</label>';
            echo '</form>';
            echo sprintf("<script> let temp = openPage('EditGroup', %d, 'view', 'Информация о группе' ) </script>", $dbConnect::$mysqli->insert_id );
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось создать новою группу';
        }

    }
}

if ($_POST['open_type'] !== 'view'){
    $users = $dbConnect::$mysqli->query("SELECT * FROM users");
    if(!$users->num_rows){
        $error = 'Не удалось получить пользователей';
    }
}
?>

<div class="editGroup">
    <h1 class="editGroup_header">Информация о группе</h1>

    <form class="editGroup_form" action="EditGroup.php" method="post" name="editGroup_form">
        <label class="editGroup_hidden">
            <input  name="id_group" type="text" value="<?php echo $_POST['id_form'] ?>">
        </label>
            <div class="editGroup_form_InfoPart">
                <div>
                    <label for="name_group"><b>Группа:</b> <?php echo $_POST['open_type'] === 'view'  ? $group['name_group'] : ''   ?></label>
                    <?php echo $_POST['open_type'] !== 'view' ? '<input id="name_group" type="text" name="name_group" value="'.$group['name_group'].'" />' : '' ?>
                </div>

                <div>
                    <?php
                    if ($_POST['open_type'] === 'view') {
                        echo sprintf("<label><b>Куратор:</b> %s</label>", $group['fio']);
                    }else{
                        echo '<label><b>Выберите куратора</b></label>';
                        echo '<select class="editGroup_form_Select" title="Куратор" name="user">';
                        echo sprintf("<option %s disabled>Выберите куратора</option>", $users -> num_rows == 0 ? 'selected' : '');
                        while( $user = $users-> fetch_assoc()){
                            echo sprintf("<option %s value=\"%s\">%s</option>", $user['id_user'] == $group['id_user'] ? 'selected' : '', $user['id_user'], $user['fio']);
                        }
                        echo '</select>';
                    }

                    ?>
                </div>
            </div>
            <input <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditGroup\','.$_POST['id_form'].', \'edit\', \'Редактирование группы\')"' : 'type="submit"' ?> value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">
    </form>
</div>
