<?php
session_start();

    $page = '';
    $title = '';

    if (isset($_POST['page']) && !empty($_POST['page'])){
        $page = $_POST['page'];
    }

    if (isset($_POST['form_title']) && !empty($_POST['form_title'])){
        $title = $_POST['form_title'];
    }
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Fonts.css">
    <link rel="stylesheet" href="../Styles/Header.css"/>
    <link rel="stylesheet" href="../Styles/LeftMenu.css"/>
    <link rel="stylesheet" href="../Styles/Main.css"/>
    <?php

    if (strlen($page) != 0){
        echo '<link rel="stylesheet" href="../Styles/'.$page.'.css"/>';
    }
    ?>

    <title><?php echo $title ?></title>
</head>
<body>

<script src="../js/Scripts.js"></script>

<?php
require_once '../Components/Header.php'
?>
<div class="main">

    <form action="Main.php" method="post" name="form_page" class="main_formRedirect" >
        <label>
            <input type="text" name="page"/>
            <input type="text" name="form_title"/>
            <input type="text" name="id_form"/>
            <input type="text" name="open_type"/>
        </label>
    </form>
    <?php require_once('../Components/LeftMenu.php') ?>
    <div class="main_content">
        <?php
        if (strlen($page) != 0){
            require_once (sprintf('%s.php', $page));
        }else{
            echo '<h1>Выберите пункт меню</h1>';
        }
        ?>
    </div>

</div>
</body>
</html>
