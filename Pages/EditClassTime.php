
<?php
require_once('../Classes/dbConnect.php');
require_once('../Classes/SimpleObject.php');
use DB\dbConnect;



$rows = '';
$groups = [];

$dbConnect = new dbConnect();
$error = '';

if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['id_classtime']) && $_POST['id_classtime'] >= 0)){


    if (isset($_POST['id_classtime']) && $_POST['id_classtime'] >= 0){


        $result_query_select = $dbConnect::$mysqli->query(sprintf("UPDATE classtime SET date_classtime='%s', theme_classtime='%s', schedule_classtime='%s' WHERE id_classtime=%d", $_POST['date_classtime'], $_POST['theme_classtime'], $_POST['schedule_classtime'] ,$_POST['id_classtime']));

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
            echo sprintf("<script> let temp = openPage('EditClassTime', %d, 'view', 'Классный час') </script>", $_POST["id_classtime"]);
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось сохранить изменения';
        }

    }else{

        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM classtime LEFT JOIN `groups` g on g.id_group = classtime.id_group WHERE id_classtime=%s", $_POST['id_form']));
        if(!$result_query_select->num_rows){
            $error = 'Не удалось получить классный час';
        }else{
            if ($_POST['open_type'] !== 'new'){
                $rows = $result_query_select->fetch_assoc();
            }
        }
    }

}else{

    if (isset($_POST['theme_classtime']) && !empty($_POST['theme_classtime'])){

        $query = sprintf("INSERT INTO classtime(id_group, date_classtime, theme_classtime, schedule_classtime) VALUES ('%d', '%s', '%s', '%s')", $_POST['group'], $_POST['date_classtime'],$_POST['theme_classtime'],$_POST['schedule_classtime']);
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
            echo sprintf("<script> let temp = openPage('EditClassTime', %d, 'view', '%s' ) </script>", $dbConnect::$mysqli->insert_id, 'Классный час');
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось записать классный час';
        }

    }
}

if ($_POST['open_type'] === 'new'){
    $result_query_select = $dbConnect::$mysqli->query("SELECT * FROM `groups`");
    if(!$result_query_select->num_rows){
        $error = 'Не удалось получить группу';
    }else{
        while ($rows = $result_query_select->fetch_assoc()){
            $groups[] = new SimpleObject($rows['id_group'], $rows['name_group']);
        }
    }
}
?>

<div class="editClassTime">
    <h1 class="editClassTime_header">Классный час</h1>

    <form class="editClassTime_form" action="editClassTime.php" method="post" name="editClassTime_form">
        <label class="editClassTime_hidden">
            <input  name="id_classtime" type="text" value="<?php echo $_POST['id_form'] ?>">
        </label>

            <?php
            if ($_POST['open_type'] === 'view') {
                echo sprintf("<label><b>Группа:</b> %s</label>", $rows['name_group']);
            }else{
                if ($_POST['open_type'] === 'new'){
                    echo '<label>Выберите группу</label>';
                    echo '<select class="editClassTime_form_Select" title="Группа" name="group">';
                    echo sprintf("<option %s disabled>Выберите группу</option>", count($groups) == 0 ? 'selected' : '');
                    foreach($groups as $group){
                        echo sprintf("<option %s value=\"%s\">%s</option>", $group->id == $rows['id_group'] ? 'selected' : '', $group->id, $group->name);
                    }
                    echo '</select>';
                }
            }
                ?>

            <label for="date"><b>Дата:</b> <?php echo $_POST['open_type'] === 'view'  ? $rows['date_classtime'] : ''   ?></label>
            <?php echo $_POST['open_type'] !== 'view' ? '<input required id=\'date\' type=\'date\' name="date_classtime" value="'.$rows['date_classtime'].'" />' : '' ?>

            <label for="theme"><b>Тема:</b> <?php echo $_POST['open_type'] === 'view'  ? $rows['theme_classtime'] : ''   ?></label>
            <?php echo $_POST['open_type'] !== 'view' ? '<input required id=\'theme\' type=\'text\' name="theme_classtime" value="'.$rows['theme_classtime'].'" />' : '' ?>

            <label for="schedule"><b>Краткий план: <br></b> <?php
                if ($_POST['open_type'] === 'view'){
                    $rows = str_replace(array("\r\n", "\r", "\n"), '<br>',$rows['schedule_classtime']);
                    echo $rows;
                }

                ?></label>
            <?php echo $_POST['open_type'] !== 'view' ? '<textarea required rows="15" id=\'schedule\' name="schedule_classtime">'.$rows['schedule_classtime'].'</textarea>' : '' ?>


        <input <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditClassTime\','.$_POST['id_form'].', \'edit\', \'Классный час\')"' : 'type="submit"' ?> value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">

    </form>
</div>
