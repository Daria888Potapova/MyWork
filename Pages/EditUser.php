
<?php
    require_once('../Classes/dbConnect.php');
    require_once('../Settings/params.php');
    require_once('../Classes/SimpleObject.php');
    use DB\dbConnect;
    use params\Params;

    session_start();

    $rows = '';
    $avatar = '';
    $fio ='';
    $id_role = '';
    $role = '';
    $email = '';
    $password = '';
    $avatarPath = $_SERVER['DOCUMENT_ROOT'].Params::$AvatarPath;
    $roles = [];

    $dbConnect = new dbConnect();
    $error = '';

    if ((isset($_POST['id_form']) && $_POST['id_form'] >= 0) || (isset($_POST['user_id']) && $_POST['user_id'] >= 0)){

        if (isset($_POST['user_id']) && $_POST['user_id'] >= 0){

            $oldAvatar =$_POST['avatar_name'];
            //Если аватарка изменилась
            if($_POST['avatar_changed'] === '1'){

                require_once('../Classes/FileName.php');

                $oldAvatar = FileName::saveFile($_POST['newAvatar_base64'], $avatarPath);

                if ($_POST['user_id'] == $_SESSION['id']){
                    $_SESSION['avatar'] = $oldAvatar;
                }

            }
            $result_query_select = $dbConnect::$mysqli->query(sprintf("UPDATE users SET fio='%s', email='%s', avatar='%s', id_role=%d WHERE id_user=%d", $_POST['fio'], $_POST['email'],$oldAvatar, $_POST['role'] ,$_POST['user_id']));

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
                echo sprintf("<script> let temp = openPage('EditUser', %d, 'view', 'Информация о пользователе') </script>", $_POST["user_id"]);
                exit();
            }else{
                echo mysqli_error($result_query_select);
                echo 'Не удалось сохранить изменения';
            }

        }else{

            $result_query_select = $dbConnect::$mysqli->query(sprintf("SELECT * FROM `users` LEFT JOIN `role` ON `users`.id_role = `role`.id_role WHERE id_user=%s", $_POST['id_form']));
            if(!$result_query_select->num_rows){
                $error = 'Не удалось получить пользователя';
            }else{
                if ($_POST['open_type'] !== 'new'){
                    $rows = $result_query_select->fetch_assoc();
                    if (!is_null($rows['avatar'])){
                        $avatar = $rows['avatar'];
                    }
                    $fio = $rows['fio'];
                    $role = $rows['name_role'];
                    $id_role = $rows['id_role'];
                    $email = $rows['email'];
                    $password = $rows['password'];
                }

            }
        }

    }else{

        if (isset($_POST['fio']) && !empty($_POST['fio'])){

            $oldAvatar = $_POST['avatar_name'];
            //Если аватарка изменилась
            if($_POST['avatar_changed'] === '1') {

                require_once('../Classes/FileName.php');
                $oldAvatar = FileName::saveFile($_POST['newAvatar_base64'], $avatarPath);
            }

            if (strlen($oldAvatar) == 0){
                $oldAvatar = 'NoAvatar.jpg';
            }

            $password = trim($_POST['password']);
            $password = htmlspecialchars($password, ENT_QUOTES);

            //Шифруем пароль
            $password = md5($password.Params::$keyEncrypt);


            $query = sprintf("INSERT INTO users(fio, email, id_role, avatar, password) VALUES ('%s', '%s', %d, '%s', '%s')", $_POST['fio'], $_POST['email'],$_POST['role'], $oldAvatar, $password);
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
                echo sprintf("<script> let temp = openPage('EditUser', %d, 'view', '%s' ) </script>", $dbConnect::$mysqli->insert_id, 'Информация о пользователе');
                exit();
            }else{
                echo mysqli_error($result_query_select);
                echo 'Не удалось создать нового пользователя';
            }

        }
    }

    if ($_POST['open_type'] !== 'view'){
        $result_query_select = $dbConnect::$mysqli->query("SELECT * FROM role");
        if(!$result_query_select->num_rows){
            $error = 'Не удалось получить пользователя';
        }else{
            while ($rows = $result_query_select->fetch_assoc()){
                $roles[] = new SimpleObject($rows['id_role'], $rows['name_role']);
            }
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

<div class="editUser">
    <h1 class="editUser_header">Информация о пользователе</h1>

    <form class="editUser_form" action="EditUser.php" method="post" name="editUser_form">
        <label class="editUser_hidden">
            <input  name="user_id" type="text" value="<?php echo $_POST['id_form'] ?>">
        </label>
        <div class="editUser_formPart">
            <div class="editUser_form_InfoPart">
                <div>
                    <label for="fio"><b>ФИО:</b> <?php echo $_POST['open_type'] === 'view'  ? $fio : ''   ?></label>
                    <?php echo $_POST['open_type'] !== 'view' ? '<input type=\'text\' name="fio" value="'.$fio.'" />' : '' ?>
                </div>
                <div>
                    <label for="email"><b>E-Mail:</b> <?php echo $_POST['open_type'] === 'view'  ? $email: ''   ?></label>
                    <?php echo $_POST['open_type'] !== 'view' ? '<input type=\'email\' name="email" value="'.$email.'" />' : '' ?>
                </div>
                <?php
                    if($_POST['open_type'] == 'new'){
                        echo "<div>";
                        echo "<label for=\"password\">Пароль:</label>";
                        echo "<input type='password' name='password' value='".$email."' />";
                        echo "</div>";
                    }
                ?>

                <div>
                    <?php
                    if ($_POST['open_type'] === 'view') {
                        echo sprintf("<label><b>Роль:</b> %s</label>", $role);
                    }else{
                        echo '<label><b>Выберите роль</b></label>';
                        echo '<select class="editUser_form_Select" title="Роль" name="role">';
                        echo sprintf("<option %s disabled>Выберите группу</option>", count($roles) == 0 ? 'selected' : '');
                        foreach($roles as $role){
                            echo sprintf("<option %s value=\"%s\">%s</option>", $role->id == $id_role ? 'selected' : '', $role->id, $role->name);
                        }
                        echo '</select>';
                    }

                    ?>
                </div>
            </div>

            <div class="editUser_form_avatar">
                <img id="avatar_preview" src="<?php echo '../'.Params::$AvatarPath.(strlen($avatar) != 0 ? $avatar : 'NoAvatar.jpg') ?>"  alt="Предпросмотр аватарки"/>

                <label for="avatar_name"></label><input type="text" id="avatar_name" name="avatar_name" value="<?php echo $avatar ?>" class="editUser_hidden"/>

                <label for="newAvatar_base64"></label><input type="text" id="newAvatar_base64" name="newAvatar_base64" class="editUser_hidden"/>
                <label for="avatar_changed"></label><input type="text" id="avatar_changed" name="avatar_changed" value=0 class="editUser_hidden"/>
                <?php echo ($_POST['open_type'] !== 'view') ? '<input type="file" id="avatar" class="avatar_load" name="avatar" value="Выберите файл">': '' ?>
            </div>
        </div>

        <input <?php echo  $_POST['open_type'] === 'view' ? 'type="button" onclick="openPage(\'EditUser\','.$_POST['id_form'].', \'edit\', \'Редактирование пользователя\')"' : 'type="submit"' ?> value="<?php echo  $_POST['open_type'] === 'view' ? 'Редактировать' : 'Сохранить' ?>">

    </form>
</div>
