<?

    require('phpQuery.php');

    try {
        $page = file_get_contents('http://detal77.ru/price/CAT_ALL.html');
    }
    catch (Exception $e) {
        echo 'When file get contents, exception is: <br>';
        echo $e->getMessage();
        die;
    }


    //Обрабатываем переменную с помощью phpQuery:
    try {
        $document = phpQuery::newDocument($page); //Загружаем полученную страницу в phpQuery
    }
    catch (Exception $e) {
        echo 'When load in phpquery, exception is: <br>';
        echo $e->getMessage();
        die;
    }

    /*PARSING SECTION*/

        $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');


        set_time_limit(180);
        foreach($document->find('#center_block table td a[title^="'.$cur_mark_name.'"]') as $key => $value){
            $elem_pq = pq($value); //pq - аналог $ в jQuery
            $url = $elem_pq->attr('href');
            $text = trim($elem_pq->text());
            $stoke = 'http://detal77.ru'.$url.' -||- '.str_replace('  ', ' ', $text);
            $query ="INSERT INTO products_current (product_name, product_mark) VALUES ('$stoke', '$cur_mark_name')";
            if (!$mysqli->query($query)) {
                echo "Cannot connect DB - 2";
                die;
            }
        }

        $mysqli->close();

    /*PARSING SECTION END*/

    unset($document);
    unset($page);


?>
