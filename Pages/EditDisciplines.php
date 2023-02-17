
<?php
require_once('../Classes/dbConnect.php');
use DB\dbConnect;


$dbConnect = new dbConnect();
$error = '';
$discipline = [];


if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['id_discipline']) && $_POST['id_discipline'] >= 0)){

    if (isset($_POST['id_discipline']) && $_POST['id_discipline'] >= 0){


        $result_query_select = $dbConnect::$mysqli->query(sprintf("UPDATE disciplines SET name_discipline='%s' WHERE id_discipline='%d'", $_POST['name_discipline'], $_POST['id_discipline']));

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
            echo sprintf("<script> let temp = openPage('EditDisciplines', %d, 'view', 'Информация о дисциплине') </script>", $_POST["id_discipline"]);
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось сохранить изменения';
        }

    }else{

        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM disciplines WHERE id_discipline=%d", $_POST['id_form']));
        if($result_query_select->num_rows > 0){
            $discipline = $result_query_select->fetch_assoc();
        }else{
            $error = 'Не удалось получить пользователя';
        }
    }

}else{

    if (isset($_POST['name_discipline']) && !empty($_POST['name_discipline'])){


        $query = sprintf("INSERT INTO disciplines(name_discipline) VALUES ( '%s')", $_POST['name_discipline']);
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
            echo sprintf("<script> let temp = openPage('EditDisciplines', %d, 'view', 'Информация о дисциплине' ) </script>", $dbConnect::$mysqli->insert_id );
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось создать новою группу';
        }

    }
}
?>

<div class="editDisciplines">
    <h1 class="editDisciplines_header">Информация о дисциплине</h1>

    <form class="editDisciplines_form" action="EditDisciplines.php" method="post" name="editDisciplines_form">
        <label class="editDisciplines_hidden">
            <input  name="id_discipline" type="text" value="<?php echo $_POST['id_form'] ?>">
        </label>
        <div>
            <label for="name_discipline"><b>Дисциплина:</b> <?php echo $_POST['open_type'] === 'view'  ? $discipline['name_discipline'] : ''   ?></label>
            <?php echo $_POST['open_type'] !== 'view' ? '<input id="name_discipline" type="text" name="name_discipline" value="'.$discipline['name_discipline'].'" />' : '' ?>
        </div>



        <input <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditDisciplines\','.$_POST['id_form'].', \'edit\', \'Редактирование дисциплины\')"' : 'type="submit"' ?> value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">
    </form>
</div>
