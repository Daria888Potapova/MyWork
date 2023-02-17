<?php
   require_once ('../Classes/LeftMenuItem.php');


    $role = $_SESSION['name_role'];

    //левое меню
    $arrayItems = array(new LeftMenuItem('Пользователи', ['Admin'], '../Images/svg/users.svg', 'Users'),
        new LeftMenuItem('Учебные группы', ['Admin'], '../Images/svg/groups.svg', 'Groups'),
        new LeftMenuItem('Студенты', ['Teacher'], '../Images/svg/student.svg', 'Students'),
        new LeftMenuItem('Актив группы', ['Teacher'], '../Images/svg/activist.svg', 'Responsibilities'),
        new LeftMenuItem('Дисциплины', ['Teacher'], '../Images/svg/disciplines.svg', 'Disciplines'),
        new LeftMenuItem('Учебный процесс', ['Teacher'], '../Images/svg/disciplines.svg', 'EducationProcess'),
        new LeftMenuItem('Успеваемость', ['Teacher'], '../Images/svg/performance.svg', 'Performance'),
        new LeftMenuItem('Мероприятия', ['Teacher'], '../Images/svg/events.svg', 'Events'),
        new LeftMenuItem('Классные часы', ['Teacher'], '../Images/svg/classTime.svg', 'ClassTime'),
        new LeftMenuItem('Родительские собрания', ['Teacher'], '../Images/svg/classTime.svg', 'ParentsMeeting'),
        new LeftMenuItem('Отчеты', ['Teacher'], '../Images/svg/reports.svg', 'Reports'),
    );


?>

<div class="leftMenu">
   <ul class="leftMenu_list">
       <?php
       foreach ($arrayItems as $item){
           $active = '';

           //Если текущий пункт меню совпадает с хранящимся в переданной переменной page, то делаем его активным
           if ($item->namePage == $_POST['page']){
               $active = 'active';
           }

           //Выводим пункт меню

           if ($item->haveRole($role) || strtolower($role) == 'admin'){
               echo '<li class="leftMenu_item '.$active.'" onclick="openPage(\''.$item->namePage.'\',-1,\'view\',\''.$item->name.'\')">';
               echo '<div class="leftMenu_itemSVG">';
               echo file_get_contents($item->path);
               echo  '</div>';
               echo '<p class="leftMenu_itemText">'.$item->name.'</p>';
               echo '</li>';
           }
       }

      ?>
   </ul>

</div>
