<a href="/">На главную</a><br><br>
<?php
    /*проверяем, доступен ли скрипт для крона*/
    $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');
    $query ="SELECT `id`, `value` FROM key_values WHERE name='enable_for_cron'";
    if ($result = $mysqli->query($query)) {
    }
    else {
        echo "Cannot connect DB - 13";
        die;
    }
    $enable_for_cron = 0;
    foreach($result as $val){
        $enable_for_cron = $val['value']; //всего товаров
    }
    /*проверяем, доступен ли скрипт для крона END*/

    if($enable_for_cron == 1) {

        /*блокируем работу по крону*/
        $query ="UPDATE key_values SET value=0 WHERE name='enable_for_cron'";
        if ($result = $mysqli->query($query)) {
        }
        else {
            echo "Cannot connect DB - 13";
            die;
        }
        /*блокируем работу по крону END*/


        $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');

        $query ="SELECT * FROM links";
        if ($result = $mysqli->query($query)) {
        }
        else {
            echo "Cannot connect DB - 1";
            die;
        }

        $mysqli->close();

        if($result->num_rows == 0) {
            echo 'Нет марок для парсинга';
            die;
        }

        /*Собираем ключи*/
        $case = 0;

        $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');

        $query ="SELECT * FROM links";
        if ($links_result = $mysqli->query($query)) {
        }
        else {
            echo "Cannot connect DB - 12";
            die;
        }
        $all_prod_check = $links_result->num_rows; //проверка всего товаров

        $query ="SELECT `id`, `value` FROM key_values WHERE name='current_mark'";
        if ($result = $mysqli->query($query)) {
        }
        else {
            echo "Cannot connect DB - 13";
            die;
        }
        $cur_mark = 0;
        $cur_mark_name = '';
        foreach($result as $val){
            $cur_mark = $val['value']; //текущий товар
        }
        $prod_itt = 1;
        foreach($links_result as $name){
            if($prod_itt == $cur_mark) {
                $cur_mark_name = $name['link']; //запись названия текущего товара
                break;
            }
            $prod_itt++;
        }

        $query ="SELECT `id`, `value` FROM key_values WHERE name='all_marks'";
        if ($result = $mysqli->query($query)) {
        }
        else {
            echo "Cannot connect DB - 13";
            die;
        }
        $all_prod = 0;
        foreach($result as $val){
            $all_prod = $val['value']; //всего товаров
        }


        if($cur_mark == 1) {
            $case = 1;
        }
        else if ($cur_mark > 1 && $cur_mark < $all_prod) {
            $case = 2;
        }
        else if ($cur_mark == $all_prod) {
            $case = 3;
        }

        if($all_prod_check != $all_prod) {
            $case = 4;
            $all_prod = $all_prod_check;
            $query ="UPDATE key_values SET value='$all_prod' WHERE name='all_marks'";
            if ($result = $mysqli->query($query)) {
            }
            else {
                echo "Cannot connect DB - 13";
                die;
            }
        }

        $mysqli->close();

        /*Собираем ключи*/

        switch ($case) {
            case 1:
                require('cases/case1.php');
                break;
            case 2:
                require('cases/case2.php');
                break;
            case 3:
                require('cases/case3.php');
                break;
            case 4:
                $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');
                $query ="SELECT `id`, `value` FROM key_values WHERE name='current_mark'";
                if ($result = $mysqli->query($query)) {
                }
                else {
                    echo "Cannot connect DB - 13";
                    die;
                }
                $cur_mark = 1;
                foreach($result as $name){
                    if($prod_itt == $cur_mark) {
                        $cur_mark_name = $name['link']; //запись названия текущего товара
                        break;
                    }
                    $prod_itt++;
                }
                $mysqli->close();
                require('cases/case1.php');
                break;
        }



        unset($all_array);
        unset($all_itt);


        $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');
        /*разблокируем работу по крону*/
        $query ="UPDATE key_values SET value=1 WHERE name='enable_for_cron'";
        if ($result = $mysqli->query($query)) {
        }
        else {
            echo "Cannot connect DB - 13";
            die;
        }
        /*разблокируем работу по крону END*/

        /*переходим на новую марку*/

        if($case == 3 || $case == 4) {
            $cur_mark = 1;
        }
        else {
            $cur_mark = $cur_mark + 1;
        }
        $query ="UPDATE key_values SET value=$cur_mark WHERE name='current_mark'";
        if ($result = $mysqli->query($query)) {
        }
        else {
            echo "Cannot connect DB - 13";
            die;
        }
        /*переходим на новую марку END*/
        $mysqli->close();
    }
?>
