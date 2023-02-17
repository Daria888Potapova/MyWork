
<?php
require_once('../Classes/dbConnect.php');
require_once('../Settings/params.php');
require_once('../Classes/SimpleObject.php');
use DB\dbConnect;
use params\Params;


$info_student = '';
$avatarPath = '';

$residences = [];
$groups = [];

$dbConnect = new dbConnect();
$error = '';

function getCheckbox($value){
    return !empty($value) ? $value : '0';
}

function getSelectLists(){

    global $residences, $groups, $dbConnect;

    $query = "SELECT * FROM residences;";
    $query .= "SELECT * FROM `groups`";
    if (strtolower($_SESSION['name_role']) != 'admin'){
        $query .= sprintf(" WHERE `groups`.id_user='%d'", $_SESSION['id'] );
    }

    $dbConnect::$mysqli->multi_query($query);

    //Первый запрос
    $dbConnect::$mysqli->next_result();
    $secondQuery = $dbConnect::$mysqli->store_result();
    while ($row = $secondQuery->fetch_row()){
        $residences[] = new SimpleObject($row[0], $row[1]);
    }

    //Второй запрос
    $dbConnect::$mysqli->next_result();
    $thirdQuery = $dbConnect::$mysqli->store_result();
    while ($row = $thirdQuery->fetch_row()){
        $groups[] = new SimpleObject($row[0], $row[1]);
    }
}

if (isset($_POST['id_form']) || (!empty($_POST['id_student']) && $_POST['id_form'] >= 0)){

    $avatarPath = $_SERVER['DOCUMENT_ROOT'].Params::$AvatarPath;

    if (isset($_POST['id_student']) && !empty($_POST['id_student'])){

        $oldAvatar =$_POST['avatar_name'];

        //Если аватарка изменилась
        if($_POST['avatar_changed'] === '1'){
            require_once('../Classes/FileName.php');
            $oldAvatar = FileName::saveFile($_POST['newAvatar_base64'], $avatarPath);
        }

        $query = sprintf("UPDATE students SET id_group=%d, Avatar='%s', FIO='%s', phone='%s', birthday='%s', isDormitory=%s, dormitoryRoom='%s', id_residence=%d, IsLargeFamily=%s, IsPoorFamily=%s, orphan=%s, IsBudget=%s, IsAcademicScholarShip=%s, IsSocialScholarShip=%s, IsScholarship=%s, IsDispensaryAcc=%s, HasChildren=%s, HaveDisPerson=%s, IntAccCollege=%s, KDN=%s, DisabledChildren=%s, ChildrenUnemploy=%s, ChildrenPension=%s, father='%s', father_workplace='%s', mother='%s', mother_workplace='%s', mother_education='%s', mother_profession='%s', mother_number='%s', father_education='%s', father_profession='%s', father_number='%s'  WHERE id_student=%d",
            $_POST['group'],$oldAvatar, $_POST['FIO'], $_POST['phone'], $_POST['birthday'], getCheckbox($_POST['isDormitory']), $_POST['dormitoryRoom'], $_POST['residence'], getCheckbox($_POST['IsLargeFamily']), getCheckbox($_POST['IsPoorFamily']), getCheckbox($_POST['orphan']), getCheckbox($_POST['IsBudget']), getCheckbox($_POST['IsAcademicScholarShip']), getCheckbox($_POST['IsSocialScholarShip']), getCheckbox($_POST['IsScholarship']), getCheckbox($_POST['IsDispensaryAcc']), getCheckbox($_POST['HasChildren']), getCheckbox($_POST['HaveDisPerson']), getCheckbox($_POST['IntAccCollege']), getCheckbox($_POST['KDN']), getCheckbox($_POST['DisabledChildren']), getCheckbox($_POST['ChildrenUnemploy']), getCheckbox($_POST['ChildrenPension']), $_POST['father'], $_POST['father_workplace'], $_POST['mother'], $_POST['mother_workplace'], $_POST['mother_education'], $_POST['mother_profession'], $_POST['mother_number'], $_POST['father_education'], $_POST['father_profession'], $_POST['father_number'] ,  $_POST['id_student']);
        $result_query_select = $dbConnect::$mysqli->query($query);
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
            echo '<script> let temp = openPage("EditStudent",' .$_POST["id_student"].', "view", "Информация о студенте") </script>';
            exit();
        }else{
            echo mysqli_error($result_query_select);
            echo 'Не удалось сохранить изменения';
        }

    }else{

        $query = sprintf("SELECT * FROM students LEFT JOIN `groups` ON students.id_group = `groups`.id_group
         LEFT JOIN residences ON students.id_residence = residences.id_residence WHERE id_student=%s;", $_POST['id_form']);

        $result_query_select = $dbConnect::$mysqli->query($query);

        if ($result_query_select){
            if ($_POST['open_type'] !== 'new'){
                $info_student = $result_query_select->fetch_assoc();;
            }
        }else{
            echo 'не удалось получить студента';
        }

        getSelectLists();

    }

}else{

    if (isset($_POST['FIO']) && !empty($_POST['FIO'])){

        $oldAvatar = $_POST['avatar_name'];
        //Если аватарка изменилась
        if($_POST['avatar_changed'] === '1') {

            require_once('../Classes/FileName.php');
            $oldAvatar = FileName::saveFile($_POST['newAvatar_base64'], $avatarPath);
        }

        if (strlen($oldAvatar) == 0){
            $oldAvatar = 'NoAvatar.jpg';
        }


        $query = sprintf("INSERT INTO students(id_group, Avatar, FIO, phone, birthday, isDormitory, dormitoryRoom, id_residence, IsLargeFamily, IsPoorFamily, orphan, IsBudget, IsAcademicScholarShip, IsSocialScholarShip, IsScholarship, IsDispensaryAcc, HasChildren, HaveDisPerson, IntAccCollege, KDN, DisabledChildren, ChildrenUnemploy, ChildrenPension, father, father_workplace, mother, mother_workplace, mother_education, mother_profession, mother_number, father_education, father_profession, father_number) 
        VALUES(%d, '%s', '%s', '%s', '%s', '%s', '%s', %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,%s,%s, %s, %s ,%s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
            $_POST['group'],$oldAvatar, $_POST['FIO'], $_POST['phone'], $_POST['birthday'], getCheckbox($_POST['isDormitory']), $_POST['dormitoryRoom'], $_POST['residence'], getCheckbox($_POST['IsLargeFamily']), getCheckbox($_POST['IsPoorFamily']), getCheckbox($_POST['orphan']), getCheckbox($_POST['IsBudget']), getCheckbox($_POST['IsAcademicScholarShip']), getCheckbox($_POST['IsSocialScholarShip']), getCheckbox($_POST['IsScholarship']), getCheckbox($_POST['IsDispensaryAcc']), getCheckbox($_POST['HasChildren']), getCheckbox($_POST['HaveDisPerson']), getCheckbox($_POST['IntAccCollege']), getCheckbox($_POST['KDN']), getCheckbox($_POST['DisabledChildren']), getCheckbox($_POST['ChildrenUnemploy']), getCheckbox($_POST['ChildrenPension']), $_POST['father'], $_POST['father_workplace'], $_POST['mother'], $_POST['mother_workplace'], $_POST['mother_education'], $_POST['mother_profession'], $_POST['mother_number'], $_POST['father_education'], $_POST['father_profession'], $_POST['father_number'] );
        $dbConnect::$mysqli->query($query) or die(mysqli_error($dbConnect::$mysqli));

        if ($dbConnect::$mysqli->insert_id > 0) {
            echo '<script src="../js/Scripts.js"></script>';
            echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
            echo '<label>';
            echo '<input type="text" name="page"/>';
            echo '<input type="text" name="id_form"/>';
            echo '<input type="text" name="open_type"/>';
            echo '<input type="text" name="form_title"/>';
            echo '</label>';
            echo '</form>';
            echo '<script> let temp = openPage("EditStudent",'.$dbConnect::$mysqli->insert_id.', "view", "информация о студенте") </script>';
            exit();
        }else{
            echo 'Не удалось сохранить изменения';
        }

    }else{

        getSelectLists();
    }

}
?>

<script>
    function loadAvatar(evt){
        let files = evt.target.files;

        if (files[0]){
            let fr = new FileReader();

            fr.addEventListener('load', (e) => {
                document.getElementById('avatar_preview').src = e.target.result;
                document.getElementsByName('newAvatar_base64')[0].value = e.target.result;

                document.getElementsByName('avatar_changed')[0].value = '1';

            }, false);

            fr.readAsDataURL(files[0]);
        }
    }

    window.onload = function() {
        document.getElementById('avatar').addEventListener('change', loadAvatar, false);
    };

</script>

<div class="editStudent">
    <h1 class="editStudent_header">Информация о студенте</h1>

    <form class="editStudent_form" action="EditStudent.php" method="post" name="editStudent_form">
        <div class="editStudent_formPart">

        <div class="editStudent_form_InfoPart">

            <div class="editStudent_hidden">
                <label for="id_student">id_student: <?php echo $_POST['open_type'] === 'view'  ? ($info_student['id_student'] ?? ''): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="number" name="id_student" value="'.($info_student['id_student'] ?? '').'" />' : '' ?>
            </div>

            <div>
                <label for="name_group"><b>Группа:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['name_group']: ''   ?></label>
                <?php
                if ($_POST['open_type'] !== 'view'){
                    echo '<select title="Группа" name="group">';
                    echo '<option '.(count($groups) == 0 || empty($info_student['id_group']) ? 'selected' : '').' disabled>Выберите группу</option>';
                    foreach($groups as $group){
                        echo '<option '.($group->id == $info_student['id_group'] ? 'selected' : '').' value="'.$group->id.'">'.$group->name.'</option>';
                    }
                    echo '</select>';
                }
                ?>
            </div>
            <div>
                <label for="FIO"><b>ФИО:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['FIO']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="ФИО" type="text" name="FIO" value="'.($info_student['FIO'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="phone"><b>Телефон:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['phone']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Телефон" type="tel" name="phone" value="'.($info_student['phone'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="birthday"><b>День рождения:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['birthday']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Дата рождения" type="date" name="birthday" value="'.($info_student['birthday'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="father"><b>Отец:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['father']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Отец" type="text" name="father" value="'.($info_student['father'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="father_education"><b>Образование отца:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['father_education']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Образование отца" type="text" name="father_education" value="'.($info_student['father_education'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="father_profession"><b>Профессия отца:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['father_profession']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Профессия отца" type="text" name="father_profession" value="'.($info_student['father_profession'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="father_workplace"><b>Должность отца:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['father_workplace']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Должность отца" type="text" name="father_workplace" value="'.($info_student['father_workplace'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="father_number"><b>Телефон отца:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['father_number']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Телефон отца" type="number" name="father_number" value="'.($info_student['father_number'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="mother"><b>Мать:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['mother']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Мать" type="text" name="mother" value="'.($info_student['mother'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="mother_education"><b>Образование матери:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['mother_education']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Образование матери" type="text" name="mother_education" value="'.($info_student['mother_education'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="mother_profession"><b>Профессия матери:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['mother_profession']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Профессия матери" type="text" name="mother_profession" value="'.($info_student['mother_profession'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="mother_workplace"><b>Должность матери:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['mother_workplace']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Должность матери" type="text" name="mother_workplace" value="'.($info_student['mother_workplace'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="mother_number"><b>Телефон матери:</b><?php echo $_POST['open_type'] === 'view'  ? $info_student['mother_number']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Телефон матери" type="number" name="mother_number" value="'.($info_student['mother_number'] ?? '').'" />' : '' ?>
            </div>
            <div>
                <label for="dormitoryRoom"><b>Комната в общежитии:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['dormitoryRoom']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input placeholder="Номер комнаты" type="text" name="dormitoryRoom" value="'.($info_student['dormitoryRoom'] ?? '').'" />' : '' ?>
            </div>
            <div class="editStudent_hidden">
                <label for="id_residence">id_residence: <?php echo $_POST['open_type'] === 'view'  ? $info_student['id_residence']: ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input  type="number" name="id_residence" value="'.$info_student['id_residence'].'" />' : '' ?>
            </div>
            <div>
                <label for="name_residence"><b>Место проживания:</b> <?php echo $_POST['open_type'] === 'view'  ? $info_student['name_residence']: ''   ?></label>
                <?php
                if ($_POST['open_type'] !== 'view'){
                    echo '<select title="Проживание" name="residence">';
                    echo '<option '.(count($residences) == 0 || empty($info_student['id_residence']) ? 'selected' : '').' disabled>Выберите место проживания</option>';
                    foreach($residences as $residence){
                        echo '<option '.($residence->id == $info_student['id_residence'] ? 'selected' : '').' value="'.$residence->id.'">'.$residence->name.'</option>';
                    }
                    echo '</select>';
                }
                ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="isDormitory"><b>Проживает в общежитии:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['isDormitory'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="isDormitory" '.(($info_student['isDormitory'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="isLargeFamily"><b>Из многодетной семьи:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['IsLargeFamily'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="IsLargeFamily" '.(($info_student['IsLargeFamily'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="isPoorFamily"><b>Из малообеспеченной семьи:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['IsPoorFamily'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="IsPoorFamily" '.(($info_student['IsPoorFamily'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="orphan"><b>Сирота:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['orphan'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="orphan" '.(($info_student['orphan'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="isBudget"><b>На бюджете:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['IsBudget'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="IsBudget" '.(($info_student['IsBudget'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="isAcademicScholarShip"><b>Получает академическую стипендию:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['IsAcademicScholarShip'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="IsAcademicScholarShip" '.(($info_student['IsAcademicScholarShip'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="isSocialScholarShip"><b>Получает социальную стипендию:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['IsSocialScholarShip'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="IsSocialScholarShip" '.(($info_student['IsSocialScholarShip'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="isScholarship"><b>Получает стипендию:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['IsScholarship'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="IsScholarship" '.(($info_student['IsScholarship'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="isDispensaryAcc"><b>На диспансерном учете:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['IsDispensaryAcc'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="IsDispensaryAcc" '.(($info_student['IsDispensaryAcc'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="HasChildren"><b>Имеет детей:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['HasChildren'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="HasChildren" '.(($info_student['HasChildren'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="HaveDisPerson"><b>Инвалиды в семье:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['HaveDisPerson'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="HaveDisPerson" '.(($info_student['HaveDisPerson'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="IntAccCollege"><b>Внутренний учет колледжа:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['IntAccCollege'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="IntAccCollege" '.(($info_student['IntAccCollege'] ?? '')=== '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="KDN"><b>Учет КДН до 18 лет:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['KDN'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="KDN" '.(($info_student['KDN'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="DisabledChildren"><b>Дети инвалидов:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['DisabledChildren'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="DisabledChildren" '.(($info_student['DisabledChildren'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="ChildrenUnemploy"><b>Дети безработных:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['ChildrenUnemploy'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="ChildrenUnemploy" '.(($info_student['ChildrenUnemploy'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>
            <div class="editStudent_checkbox">
                <label for="ChildrenPension"><b>Дети пенсионеров:</b> <?php echo $_POST['open_type'] === 'view'  ? ($info_student['ChildrenPension'] == '1' ? 'Да': 'Нет'): ''   ?></label>
                <?php echo $_POST['open_type'] !== 'view' ? '<input type="checkbox" value="true" name="ChildrenPension" '.(($info_student['ChildrenPension'] ?? '') === '1' ? 'Checked' : '').' />' : '' ?>
            </div>

        </div>
        <div class="editStudent_rightPart">
            <img id="avatar_preview" src="<?php echo ($_POST['open_type'] == 'new' ? '..'.Params::$AvatarPath.'NoAvatar.jpg' :'../'.Params::$AvatarPath.$info_student['Avatar'])  ?>" />
            <label for="avatar_name"></label><input type="text" id="avatar_name" name="avatar_name" value="<?php echo ($info_student['Avatar'] ?? '') ?>" class="editStudent_hidden"/>
            <label for="newAvatar_base64"></label><input type="text" id="newAvatar_base64" name="newAvatar_base64" class="editStudent_hidden"/>
            <label for="avatar_changed"></label><input type="text" id="avatar_changed" name="avatar_changed" value=0 class="editStudent_hidden"/>
            <?php echo $_POST['open_type'] !== 'view' ? '<input type="file" id="avatar" class="avatar_load" name="avatar" value="Выберите файл">': '' ?>
        </div>
        </div>

        <label for="submit"></label><input id="submit" <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditStudent\','.$_POST['id_form'].', '.($_POST['id_form'] >=0 ? '\'edit\'' : '\'view\'').', \'Редактирование пользователя\')"' : 'type="submit"' ?>value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">

    </form>
</div>