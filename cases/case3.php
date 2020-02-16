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



    $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');

    $all_array = array();
    $all_itt= 0;

    $query ="SELECT `product_name` FROM products_current WHERE `product_name` NOT IN (SELECT `product_name` FROM products)";
    if ($result = $mysqli->query($query)) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $all_array[$all_itt] = str_replace('  ', ' ', $row['product_name']);
            echo $all_array[$all_itt].'<br>';
            $all_itt++;
        }
    }
    else {
        echo "Cannot connect DB - 3";
        die;
    }
    $query ="TRUNCATE TABLE products";
    if ($result = $mysqli->query($query)) {
    }
    else {
        echo "Cannot connect DB - 4";
        die;
    }
    $query ="INSERT INTO products (product_name) SELECT `product_name` FROM products_current";
    if ($result = $mysqli->query($query)) {
    }
    else {
        echo "Cannot connect DB - 5";
        die;
    }
    $query ="TRUNCATE TABLE products_current";
    if ($result = $mysqli->query($query)) {
    }
    else {
        echo "Cannot connect DB - 6";
        die;
    }

    $mysqli->close();

    /*SENDING MESSAGE TO TELEGRAM BOT SECTION*/
        define('TELEGRAM_TOKEN', '922432078:AAEVzPT6ZH4CXo-BYqsVsYrtxUNluxPsgTA');

        define('TELEGRAM_CHATID', '436106286');
        $sssss = 0;
        if(count($all_array) > 0){
            message_to_telegram('Добавлено : '.date("m.d.y H:i:s"));
            foreach ($all_array as $res) {
                //message_to_telegram($res);
                $sssss++;
            }
            message_to_telegram($sssss);
            message_to_telegram('---------------------');
            unset($all_array);
        }

        function message_to_telegram($text)
        {
            $ch = curl_init();
            curl_setopt_array(
                $ch,
                array(
                    CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/sendMessage',
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_POSTFIELDS => array(
                        'chat_id' => TELEGRAM_CHATID,
                        'text' => $text,
                    ),
                )
            );
            curl_exec($ch);
        }
    /*SENDING MESSAGE TO TELEGRAM BOT SECTION END*/

?>
