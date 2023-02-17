<?php
require_once ('../Settings/params.php');
use params\Params;

    //Если в сессии не существует страница для отображения, то присваиваем основную страницу
    if (!isset($_SESSION['page'])){
        $_SESSION['page'] = 'main';
    }
    $fio = explode(' ', $_SESSION['fio']);

    //Формируем ФИО в правой верхней части системы
    $name_profile = $fio[0].' '.mb_substr($fio[1], 0,1).'.';
    $imagePath = '';
    //Если в сессии существует аватар, то выводим его
    if (!empty($_SESSION['avatar'])){
        $imagePath = Params::$AvatarPath.$_SESSION['avatar'];
    }

?>
<script>
    let exitAcc = () => {
        window.location.href = '<?php echo '/Logout.php'; ?>';
    }
</script>
<header class="header">
    <div class="header_logoContainer">
        <img class="header_logo" src="../Images/logo.png" alt="Лого"/>
        <p  class="header_nameCollege">ИРКПО</p>
    </div>
    <div class="header_rightContainer">
        <div class="header_profileContainer" onclick="openPage('EditUser',<?php echo $_SESSION['id'] ?>, 'view' ,'Информация о пользователе')">
            <p class="header_profileName"><?php echo $name_profile ?></p>
            <img class="header_profileAvatar" src="<?php echo $imagePath ?>" alt="Профиль"/>
        </div>
        <div onclick="exitAcc()" class="header_exitContainer">
            <img src="../Images/svg/exit.svg" alt="Выход"/>
        </div>

    </div>


</header>
