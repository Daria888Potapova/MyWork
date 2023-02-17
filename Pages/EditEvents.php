
<?php
require_once('../Classes/dbConnect.php');
require_once ('../Classes/SimpleObject.php');
require_once ('../Settings/params.php');
use DB\dbConnect;
use params\Params;


$dbConnect = new dbConnect();
$error = '';
$levelEvents = [];
$typeEvents = [];
$resultEvents = [];
$event = [];
$members = [];
$students = [];
$eventPath = $_SERVER['DOCUMENT_ROOT'].Params::$EventsPath;
$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
$downloadPath= $protocol.'://'.$_SERVER['SERVER_NAME'].Params::$EventsPath;


if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['id_event']) && $_POST['id_event'] >= 0)){

    if (isset($_POST['id_event']) && $_POST['id_event'] >= 0){


        $oldFile =$_POST['file_name'];

        //Если аватарка изменилась
        if($_POST['file_changed'] === '1'){
            require_once('../Classes/FileName.php');
            $oldFile = FileName::saveFile($_POST['newFile_base64'], $eventPath);
        }
        $result_query_select = $dbConnect::$mysqli->query(sprintf("UPDATE events SET date_event='%s', name_event='%s', id_levelEvent='%d', id_typeEvent='%d', id_resultEvent='%d', fileEvent='%s', id_activity='%d' WHERE id_event='%d'", $_POST['date_event'], $_POST['name_event'], $_POST['level'],$_POST['type'], $_POST['result'],$oldFile, $_POST['activity'] ,$_POST['id_event']));

        if ($result_query_select){
            $users = $_POST['member'];
            $prizes = $_POST['prize'];

            $query = sprintf("DELETE FROM studentsandevents WHERE id_event=%d;", $_POST['id_event']);
            $query .= "INSERT INTO studentsandevents(id_student, id_event, prize_event) VALUES ";
            $existsUsers = false;
            foreach ($users as $key=>$user) {
                $query .= sprintf( "('%d', '%d', '%s'),", $user, $_POST['id_event'], $prizes[$key]);
                $existsUsers = true;
            }

            $exit = true;
            if ($existsUsers) {
                $query = mb_substr($query, 0, -1);
                $dbConnect::$mysqli->multi_query($query);
                $exit = $dbConnect::$mysqli->next_result();
            }

            if($exit){
                echo '<script src="../js/Scripts.js"></script>';
                echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
                echo '<label>';
                echo '<input type="text" name="page"/>';
                echo '<input type="text" name="id_form"/>';
                echo '<input type="text" name="open_type"/>';
                echo '<input type="text" name="form_title"/>';
                echo '</label>';
                echo '</form>';
                echo sprintf("<script> let temp = openPage('EditEvents', %d, 'view', 'Информация о мероприятии') </script>", $_POST["id_event"]);
                exit();
            }
        }


    }else{

        $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM events
    LEFT JOIN typeevent t on t.id_typeEvent = events.id_typeEvent
    LEFT JOIN levelevent l on l.id_levelEvent = events.id_levelEvent
    LEFT JOIN resultevent r on r.id_resultEvent = events.id_resultEvent
    LEFT JOIN activity a on events.id_activity = a.id_activity WHERE events.id_event=%d", $_POST['id_form']));
        if($result_query_select->num_rows > 0){
            $event = $result_query_select->fetch_assoc();
        }else{
            $error = 'Не удалось получить мероприятие';
        }

        $result_query = $dbConnect::$mysqli->query(sprintf("SELECT * FROM studentsandevents 
        LEFT JOIN students s on s.id_student = studentsandevents.id_student WHERE id_event=%d", $_POST['id_form']));
        if($result_query->num_rows > 0){
            while( $row = $result_query->fetch_assoc()){
                $member=  new SimpleObject($row['id_student'], $row['FIO']);
                $member->addFirstParam($row['prize_event']);
                $members[] = $member;
            }
        }else{
            $error = 'Не удалось получить участников';
        }
    }

}else{

    if (isset($_POST['name_event']) && !empty($_POST['name_event'])){

        $oldFile =$_POST['file_name'];

        //Если аватарка изменилась
        if($_POST['file_changed'] === '1'){
            require_once('../Classes/FileName.php');
            $oldFile = FileName::saveFile($_POST['newFile_base64'], $eventPath);
        }

        $query = sprintf("INSERT INTO events(date_event, name_event, id_levelEvent, id_typeEvent, id_resultEvent, fileEvent, id_activity) VALUES ( '%s', '%s', '%d', '%d', '%d', '%s', '%d')", $_POST['date_event'], $_POST['name_event'], $_POST['level'], $_POST['type'], $_POST['result'],$oldFile, $_POST['activity']);
        $result_query_select = $dbConnect::$mysqli->query($query);

        $newId = $dbConnect::$mysqli->insert_id;
        $users = $_POST['member'];
        $prizes = $_POST['prize'];
        $query = "INSERT INTO studentsandevents(id_student, id_event, prize_event) VALUES ";
        foreach ($users as $key=>$user) {
            $query .= sprintf( "('%d', '%d', '%s'),", $user, $newId, $prizes[$key]);
        }
        $query = mb_substr($query, 0, -1);

        echo $query;
        $dbConnect::$mysqli->multi_query($query);

            if ($newId > 0) {
                echo '<script src="../js/Scripts.js"></script>';
                echo '<form action="Main.php" method="post" name="form_page" class="main_formRedirect" >';
                echo '<label>';
                echo '<input type="text" name="page"/>';
                echo '<input type="text" name="id_form"/>';
                echo '<input type="text" name="form_title"/>';
                echo '<input type="text" name="open_type"/>';
                echo '</label>';
                echo '</form>';
                echo sprintf("<script> let temp = openPage('EditEvents', %d, 'view', 'Информация о мероприятии' ) </script>", $newId);
                exit();
            } else {
                echo mysqli_error($result_query_select);
                echo 'Не удалось создать новое событие';
            }
        }
}

if ($_POST['open_type'] !== 'view'){
    $levelEvents = $dbConnect::$mysqli->query("SELECT * FROM levelevent");
    if(!$levelEvents->num_rows){
        $error .= 'Не удалось получить уровень';
    }

    $typeEvents = $dbConnect::$mysqli->query("SELECT * FROM typeevent");
    if(!$typeEvents->num_rows){
        $error .= 'Не удалось получить тип';
    }

    $resultEvents = $dbConnect::$mysqli->query("SELECT * FROM resultevent");
    if(!$resultEvents->num_rows){
        $error .= 'Не удалось получить результат';
    }

    $activities = $dbConnect::$mysqli->query("SELECT * FROM activity");
    if(!$activities->num_rows){
        $error .= 'Не удалось получить направления';
    }

    $query = "SELECT * FROM students";

    if (strtolower($_SESSION['name_role']) !== 'teacher'){
        $query .= " LEFT JOIN groups ON students.id_group = groups.id_group WHERE groups.id_user=".$_SESSION['id'];
    }
    $result_query = $dbConnect::$mysqli->query($query);
    if($result_query->num_rows > 0){
        while( $row = $result_query->fetch_assoc()){
            $students[] =  new SimpleObject($row['id_student'], $row['FIO']);
        }
    }else{
        $error = 'Не удалось получить студентов';
    }
}
?>

<script>

    let students = <?php echo json_encode($students) ?>

    function addMember(){

        let result = document.createElement('div')

        let container1 = document.createElement('div')
        container1.setAttribute('class', 'editEvents_form_member')

        let newLabel = document.createElement('label')

        let count = document.getElementsByClassName('editEvents_form_Select').length - 3
        newLabel.innerHTML = 'Участник №' + (count + 1)

        container1.appendChild(newLabel)


        let container = document.getElementsByClassName('editEvents_form_membersContainer')[0]

        let item = document.createElement('select')
        item.setAttribute('class', 'editEvents_form_Select')
        item.setAttribute('title', 'Участник')
        item.setAttribute('name', 'member[]')

        students.forEach((student, index) => {
            let value = document.createElement('option')
            value.setAttribute('value', student.id)
            if (index === 0){
                value.setAttribute('selected', 'true')
            }
            value.innerHTML = student.name
            item.appendChild(value)
        })

        container1.appendChild(item)

        result.appendChild(container1)


        let container2 = document.createElement('div')
        container2.setAttribute('class', 'editEvents_form_member')

        let newLabel2 = document.createElement('label')
        newLabel2.innerHTML = 'Награда за мероприятие'
        container2.appendChild(newLabel2)

        let input2 = document.createElement('input')
        input2.setAttribute('name', 'prize[]')
        input2.setAttribute('type', 'text')
        container2.appendChild(input2)

        result.appendChild(container2)

        container.insertBefore(result, container.childNodes[container.childNodes.length - 1])

    }



    function loadFile(evt){
        let files = evt.target.files;

        if (files[0]){
            let fr = new FileReader();

            fr.addEventListener('load', (e) => {
                console.log(document.getElementsByName('file_changed'))
                document.getElementsByName('newFile_base64')[0].value = e.target.result;

                document.getElementsByName('file_changed')[0].value = '1';

            }, false);

            fr.readAsDataURL(files[0]);
        }
    }

    window.onload = function() {
        document.getElementById('file').addEventListener('change', loadFile, false);
    };



</script>

<div class="editEvents">
    <h1 class="editEvents_header">Информация о мероприятии</h1>

    <form class="editEvents_form" action="EditEvents.php" method="post" name="editEvents_form">
        <label class="editEvents_hidden">
            <input  name="id_event" type="text" value="<?php echo $_POST['id_form'] ?>">
        </label>

        <label for="name_event"><b>Название:</b> <?php echo $_POST['open_type'] === 'view'  ? $event['name_event'] : ''   ?></label>
        <?php echo $_POST['open_type'] !== 'view' ? '<input id="name_event" type="text" name="name_event" value="'.$event['name_event'].'" />' : '' ?>

        <label for="date_event"><b>Дата:</b> <?php echo $_POST['open_type'] === 'view'  ? $event['date_event'] : ''   ?></label>
        <?php echo $_POST['open_type'] !== 'view' ? '<input id="date_event" type="date" name="date_event" value="'.$event['date_event'].'" />' : '' ?>

        <?php
        if ($_POST['open_type'] === 'view') {
            echo sprintf("<label><b>Направление деятельности:</b> %s</label>", $event['name_activity']);
        }else{
            echo '<label><b>Выберите направление деятельности</b></label>';
            echo '<select class="editEvents_form_Select" title="Направление" name="activity">';
            echo sprintf("<option %s disabled>Выберите направление</option>", $activities -> num_rows == 0 ? 'selected' : '');
            while( $activity = $activities-> fetch_assoc()){
                echo sprintf("<option %s value=\"%s\">%s</option>", $activity['id_activity'] == $event['id_activity'] ? 'selected' : '', $activity['id_activity'], $activity['name_activity']);
            }
            echo '</select>';
        }
        ?>

        <?php
        if ($_POST['open_type'] === 'view') {
            echo sprintf("<label><b>Уровень:</b> %s</label>", $event['name_levelEvent']);
        }else{
            echo '<label><b>Выберите уровень мероприятия</b></label>';
            echo '<select class="editEvents_form_Select" title="Уровень" name="level">';
            echo sprintf("<option %s disabled>Выберите уровень</option>", $levelEvents -> num_rows == 0 ? 'selected' : '');
            while( $level = $levelEvents-> fetch_assoc()){
                echo sprintf("<option %s value=\"%s\">%s</option>", $level['id_levelEvent'] == $event['id_levelEvent'] ? 'selected' : '', $level['id_levelEvent'], $level['name_levelEvent']);
            }
            echo '</select>';
        }
        ?>

        <?php
        if ($_POST['open_type'] === 'view') {
            echo sprintf("<label><b>Тип:</b> %s</label>", $event['name_typeEvent']);
        }else{
            echo '<label><b>Выберите тип мероприятия</b></label>';
            echo '<select class="editEvents_form_Select" title="Тип" name="type">';
            echo sprintf("<option %s disabled>Выберите тип</option>", $typeEvents -> num_rows == 0 ? 'selected' : '');
            while( $type = $typeEvents-> fetch_assoc()){
                echo sprintf("<option %s value=\"%s\">%s</option>", $type['id_typeEvent'] == $event['id_typeEvent'] ? 'selected' : '', $type['id_typeEvent'], $type['name_typeEvent']);
            }
            echo '</select>';
        }
        ?>

        <?php
        if ($_POST['open_type'] === 'view') {
            echo sprintf("<label><b>Результат:</b> %s</label>", $event['name_resultEvent']);
        }else{
            echo '<label><b>Выберите результат мероприятия</b></label>';
            echo '<select class="editEvents_form_Select" title="Результат" name="result">';
            echo sprintf("<option %s disabled>Выберите результат</option>", $resultEvents -> num_rows == 0 ? 'selected' : '');
            while( $result = $resultEvents-> fetch_assoc()){
                echo sprintf("<option %s value=\"%s\">%s</option>", $result['id_resultEvent'] == $event['id_resultEvent'] ? 'selected' : '', $result['id_resultEvent'], $result['name_resultEvent']);
            }
            echo '</select>';
        }
        ?>
        <div class="editEvents_imageBlock">
            <label for="file_name"></label><input type="text" id="file_name" name="file_name" value="<?php echo ($event['fileEvent'] ?? '') ?>" class="editEvents_hidden"/>
            <label for="newFile_base64"></label><input type="text" id="newFile_base64" name="newFile_base64" class="editEvents_hidden"/>
            <label for="file_changed"></label><input type="text" id="file_changed" name="file_changed" value=0 class="editEvents_hidden"/>
            <?php echo $_POST['open_type'] !== 'view' ? '<input type="file" id="file" class="file_load" name="file" value="Выберите файл">':
                sprintf("<label><b>Файл:</b> <a href='%s' download='%s'>%s</a></label>", $downloadPath.$event['fileEvent'],$event['fileEvent'], $event['fileEvent']);

            ?>
        </div>

        <div class="editEvents_form_membersContainer">
            <?php

            echo "<h3>Участники:</h3>";
            foreach ($members as $key=>$member) {
                if ($_POST['open_type'] === 'view') {
                    echo '<div>';
                    echo '<div class="editEvents_form_member">';
                    echo sprintf("<p class='editEvents_form_members' onclick=\"openPage('EditStudent', %d, 'view', 'Редактирование студента')\">%s </p>", $member->id, $member->name);
                    echo sprintf("<p> - %s</p>", mb_strlen($member->firstParam) ? $member->firstParam : 'Ничего');
                    echo '</div>';
                    echo '</div>';
                }else{
                    echo '<div>';
                    echo '<div class="editEvents_form_member">';
                    echo sprintf('<label>Участник №%d</label>', $key + 1);
                    echo '<select class="editEvents_form_Select" title="Участник" name="member[]">';
                    foreach ($students as $student){
                        echo sprintf("<option %s value=\"%s\">%s</option>", $student->id == $member->id ? 'selected' : '', $student->id, $student->name);
                    }
                    echo '</select>';
                    echo '</div>';
                    echo '<div class="editEvents_form_member">';
                    echo '<label>Награда за мероприятие</label>';
                    echo sprintf('<input name="prize[]" type="text" value="%s"/>', $member->firstParam);
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>

        <?php
        if ($_POST['open_type'] !== 'view'){
            echo '<button type="button" class="editEvents_form_addMember" onclick="addMember()">Добавить участника</button>';
        }
        ?>



        <input <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditEvents\','.$_POST['id_form'].', \'edit\', \'Редактирование мероприятия\')"' : 'type="submit"' ?> value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">
    </form>
</div>
