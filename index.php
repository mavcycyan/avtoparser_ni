<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AvtoParser</title>
    <link href="assets/styles.css" rel="stylesheet" />
</head>
<body>
    <p>Запустить парсер <a href="/parser.php">принудительно</a></p>
    <div id="inputs">
    <?
        $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');

        $query ="SELECT `id`, `link` FROM links";
        if ($result = $mysqli->query($query)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                echo '<div class="input-row">';
                echo '<input type="text" value="'.$row['link'].'" disabled>';
                echo '<button id="'.$row['id'].'" class="delete">-</button>';
                echo '</div>';
            }
        }
        else {
            echo "Cannot connect DB";
        }


        $mysqli->close();    
    ?>
        <div class="input-row">
            <div class="input_add-wp">
                <input type="text" value="" class="input_add">
            </div>
        </div>
    </div>
    <button class="plus">+</button>
    <button class="add">Сохранить</button>
    
    <script src="assets/jquery.min.js"></script>
    <script>
        var someItt = 2;
        $('.plus').click(function(){
            $('#inputs').append('<div class="input-row"> <div class="input_add-wp"> <input type="text" value="" class="input_add"> </div> </div>');
            someItt++;
        });
        $('.add').click(function(){
            var addLinks = new Array();

            var addItt = 0;
            $('.input_add').each(function(){
                if($(this).val() != ''){
                    addLinks[addItt] = {
                        value: $(this).val(),
                    };
                    addItt++;
                }
            });
            if(addLinks.length == 0){
                alert('Нет данных для сохраниения');
                return false;
            }
                
            var jsonLinks = JSON.stringify(addLinks);
            $.ajax({
                url : 'ajax/add_link.php' ,
                method : 'POST' ,
                data : {
                    jsonLinks : jsonLinks
                },
                success : function(data){
                    console.log(data);
                    alert('Данные успешно добавлены!');
                    location.reload();
                }
            });
        });
        $('.delete').click(function(){
            var delThis = $(this);
            $.ajax({
                url : 'ajax/delete_link.php' ,
                method : 'POST' ,
                data : {
                    id : $(this).attr('id')
                },
                success : function(data){
                    delThis.closest('.input-row').remove();
                    alert('Данные успешно удалены!');
                }
            });
        });
    </script>
</body>
</html>
