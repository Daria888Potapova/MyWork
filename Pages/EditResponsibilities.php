
<?php
require_once('../Classes/dbConnect.php');
require_once('../Classes/SimpleObject.php');
use DB\dbConnect;


$dbConnect = new dbConnect();
$error = '';
$students = [];
$groups = [];
$activeGroup = [];


if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['id_responsibility']) && $_POST['id_responsibility'] >= 0)){

    if (isset($_POST['id_responsibility']) && $_POST['id_responsibility'] >= 0){

        $result_query_select = $dbConnect::$mysqli->query(sprintf("UPDATE responsibilities SET name_responsibility='%s', desc_responsibility='%s', date_responsibility='%s' WHERE id_responsibility='%d'", $_POST['name_responsibility'], $_POST['desc_responsibility'], $_POST['date_responsibility'] ,$_POST['id_responsibility']));

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
            echo sprintf("<script> let temp = openPage('EditResponsibilities', %d, 'view', 'Информация об активе группы') </script>", $_POST["id_responsibility"]);
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось сохранить изменения';
        }

    }else{

        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM responsibilities LEFT JOIN students s on s.id_student = responsibilities.id_student LEFT JOIN `groups` ON s.id_group = `groups`.id_group WHERE id_responsibility=%d", $_POST['id_form']));
        if($result_query_select->num_rows > 0){
            $activeGroup = $result_query_select->fetch_assoc();
        }else{
            $error = 'Не удалось получить выбранного актива';
        }
    }

}else{

    if (isset($_POST['name_responsibility']) && !empty($_POST['name_responsibility'])){

        $query = sprintf("INSERT INTO responsibilities(id_student,name_responsibility, desc_responsibility, date_responsibility) VALUES (%d ,'%s', '%s', '%s')", $_POST['id_student'], $_POST['name_responsibility'], $_POST['desc_responsibility'], $_POST['date_responsibility']);
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
            echo sprintf("<script> let temp = openPage('EditResponsibilities', %d, 'view', 'Информация о текущем активе' ) </script>", $dbConnect::$mysqli->insert_id );
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось добавить нового актива';
        }

    }
}


if ($_POST['open_type'] === 'new'){

    $query = "SELECT * FROM `groups`";

    if (strtolower($_SESSION['name_role']) != 'admin'){
        $query .= " WHERE id_user=".$_SESSION['id'];
    }

    $groups = $dbConnect::$mysqli->query($query);

    $query2 = "SELECT * FROM students LEFT JOIN `groups` g on students.id_group = g.id_group";
    if (strtolower($_SESSION['name_role']) != 'admin'){
        $query2 .= " WHERE g.id_user=".$_SESSION['id'];
    }

    $studentsQuery = $dbConnect::$mysqli->query($query2);

    if ($studentsQuery -> num_rows){
        while ($row = $studentsQuery -> fetch_assoc()){
            $student = new SimpleObject($row['id_student'], $row['FIO']);
            $student->addFirstParam($row['id_group']);
            $students[] = $student;
        }
    }
}

?>

<script>

    let students = <?php echo json_encode($students) ?>

    function refreshStudents(){
        let groupValue = document.getElementsByName('group')[0].value
        let listStudents = document.getElementsByName('id_student')[0]
        listStudents.innerHTML = '';

        let elStudent = document.createElement('option');
        elStudent.setAttribute('disabled', "true");
        elStudent.innerHTML = "Выберите студента"
        listStudents.appendChild(elStudent);


        students.forEach((student)=>{
            if (student.firstParam === groupValue){
                let elStudent = document.createElement('option');

                elStudent.setAttribute('value', student.id);
                elStudent.innerHTML = student.name
                listStudents.appendChild(elStudent);
            }
        })

        console.log(listStudents.value)

    }
</script>

<div class="editResponsibility">
    <h1 class="editResponsibility_header">Информация о выбранном активе</h1>

    <form class="editResponsibility_form" action="EditResponsibilities.php" method="post" name="editResponsibility_form">
        <label class="editResponsibility_hidden">
            <input  name="id_responsibility" type="text" value="<?php echo $_POST['id_form'] ?>">
        </label>
        <div class="editResponsibility_form_InfoPart">
            <div>
                <label for="date_responsibility"><b>Дата:</b> <?php echo $_POST['open_type'] === 'view'  ? $activeGroup['date_responsibility'] : ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input id="date_responsibility" type="date" name="date_responsibility" value="'.$activeGroup['date_responsibility'].'" />' : '' ?>
            </div>
            <div>
                <?php
                if ($_POST['open_type'] !== 'new') {
                    echo sprintf("<label><b>Группа:</b> %s</label>", $activeGroup['name_group']);
                }else{
                    echo '<label>Выберите группу</label>';
                    echo '<select class="editResponsibility_form_Select" title="Группа" name="group" onchange="refreshStudents()">';
                    echo sprintf("<option %s disabled>Выберите группу</option>", $groups -> num_rows == 0 ? 'selected' : '');
                    while( $group = $groups-> fetch_assoc()){
                        echo sprintf("<option %s value=\"%s\">%s</option>", $group['id_group'] == $activeGroup['id_group'] ? 'selected' : '', $group['id_group'], $group['name_group']);
                    }
                    echo '</select>';
                }
                ?>
            </div>
            <div>
                <?php
                if ($_POST['open_type'] !== 'new') {
                    echo sprintf("<label><b>Студент:</b> %s</label>", $activeGroup['FIO']);
                }else{
                    echo '<label>Выберите студента</label>';
                    echo '<select required  class="editResponsibility_form_Select" title="Студент" name="id_student">';
                    echo "<option selected disabled>Выберите студента</option>";
                    echo '</select>';

                }
                ?>
                <script>refreshStudents()</script>
            </div>

            <div>
                <label for="name_responsibility"><b>Обязанность:</b> <?php echo $_POST['open_type'] === 'view'  ? $activeGroup['name_responsibility'] : ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input id="name_responsibility" type="text" name="name_responsibility" value="'.$activeGroup['name_responsibility'].'" />' : '' ?>
            </div>
            <div>
                <label for="desc_responsibility"><b>Описание обязанности:</b><br/> <?php echo $_POST['open_type'] === 'view'  ? str_replace(array("\r\n", "\n", "\r"), "<br/>",$activeGroup['desc_responsibility']) : ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<textarea id="desc_responsibility" rows="15" cols="50" type="text" name="desc_responsibility">'.$activeGroup['desc_responsibility'].'</textarea>' : '' ?>
            </div>

        </div>
        <input <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditResponsibilities\','.$_POST['id_form'].', \'edit\', \'Редактирование актива группы\')"' : 'type="submit"' ?> value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">
    </form>
</div>
