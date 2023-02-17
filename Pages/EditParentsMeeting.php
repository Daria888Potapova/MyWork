
<?php
require_once('../Classes/dbConnect.php');
use DB\dbConnect;


$dbConnect = new dbConnect();
$error = '';
$users = [];
$parentsMeeting = [];


if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['id_parentMeeting']) && $_POST['id_parentMeeting'] >= 0)){

    if (isset($_POST['id_parentMeeting']) && $_POST['id_parentMeeting'] >= 0){


        $result_query_select = $dbConnect::$mysqli->query(sprintf("UPDATE parentsmeeting SET date_parentmeeting='%s', theme_parentmeeting='%s', count_parentmeeting='%d', result_parentmeeting='%s'  WHERE id_parentmeeting='%d'", $_POST['date_parentMeeting'], $_POST['theme_parentMeeting'],$_POST['count_parentMeeting'], $_POST['result_parentMeeting'], $_POST['id_parentMeeting']));

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
            echo sprintf("<script> let temp = openPage('EditParentsMeeting', %d, 'view', 'Информация о собрании') </script>", $_POST["id_parentMeeting"]);
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось сохранить изменения';
        }

    }else{

        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM parentsmeeting WHERE id_parentmeeting=%d", $_POST['id_form']));
        if($result_query_select->num_rows > 0){
            $parentsMeeting = $result_query_select->fetch_assoc();
        }else{
            $error = 'Не удалось получить собрание';
        }
    }

}else{

    if (isset($_POST['theme_parentMeeting']) && !empty($_POST['theme_parentMeeting'])){


        $query = sprintf("INSERT INTO parentsmeeting(date_parentmeeting, theme_parentmeeting, count_parentmeeting, result_parentmeeting) VALUES ( '%s', '%s', %d, '%s')", $_POST['date_parentMeeting'], $_POST['theme_parentMeeting'],$_POST['count_parentMeeting'], $_POST['result_parentMeeting']);
        $dbConnect::$mysqli->query($query);
        if ($dbConnect::$mysqli->insert_id > 0) {
            echo $dbConnect::$mysqli->insert_id;
            echo '<script src="../js/Scripts.js"></script>';
            echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
            echo '<label>';
            echo '<input type="text" name="page"/>';
            echo '<input type="text" name="id_form"/>';
            echo '<input type="text" name="form_title"/>';
            echo '<input type="text" name="open_type"/>';
            echo '</label>';
            echo '</form>';
            echo sprintf("<script> let temp = openPage('EditParentsMeeting', %d, 'view', 'Информация о собрании' ) </script>", $dbConnect::$mysqli->insert_id );
            exit();
        }else{
            echo mysqli_error($dbConnect::$mysqli);
            echo 'Не удалось создать новое собрание';
        }

    }
}
?>

<div class="parentsMeeting">
    <h1 class="parentsMeeting_header">Информация о собрании</h1>

    <form class="parentsMeeting_form" action="EditParentsMeeting.php" method="post" name="parentsMeeting_form">
        <label class="parentsMeeting_hidden">
            <input  name="id_parentMeeting" type="text" value="<?php echo $_POST['id_form'] ?>">
        </label>
        <div class="parentsMeeting_form_InfoPart">
            <div>
                <label for="date_parentMeeting"><b>Дата собрания:</b> <?php echo $_POST['open_type'] === 'view'  ? $parentsMeeting['date_parentmeeting'] : ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input id="date_parentMeeting" type="date" name="date_parentMeeting" value="'.$parentsMeeting['date_parentmeeting'].'" />' : '' ?>
            </div>
            <div>
                <label for="theme_parentMeeting"><b>Тема собрания:</b> <?php echo $_POST['open_type'] === 'view'  ? $parentsMeeting['theme_parentmeeting'] : ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input id="theme_parentMeeting" type="text" name="theme_parentMeeting" value="'.$parentsMeeting['theme_parentmeeting'].'" />' : '' ?>
            </div>
            <div>
                <label for="count_parentMeeting"><b>Количество присутствовавших:</b> <?php echo $_POST['open_type'] === 'view'  ? $parentsMeeting['count_parentmeeting'] : ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input id="count_parentMeeting" type="number" name="count_parentMeeting" value="'.$parentsMeeting['count_parentmeeting'].'" />' : '' ?>
            </div>
            <div>
                <label for="result_parentMeeting"><b>Результат собрания:</b><br/> <?php echo $_POST['open_type'] === 'view'  ? str_replace(array("\r\n", "\n", "\r"), "<br/>",$parentsMeeting['result_parentmeeting']) : ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<textarea id="result_parentMeeting" rows="15" cols="50" type="text" name="result_parentMeeting">'.$parentsMeeting['result_parentmeeting'].'</textarea>' : '' ?>
            </div>



        </div>
        <label>
            <input <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditParentsMeeting\','.$_POST['id_form'].', \'edit\', \'Редактирование собрания\')"' : 'type="submit"' ?> value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">
        </label>
    </form>
</div>
