<?
$jsonLinks = $_POST['jsonLinks'];

if ($jsonLinks == ''){
	echo "false";
    return false;
}
else { 
    $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');
    
    $linksArr = json_decode($jsonLinks);
    
    foreach($linksArr as $link){
        $query = "INSERT INTO links (link) VALUES ('$link->value')";
        if ($mysqli->query($query)) {
            echo "true";
        }
        else {
            //echo "Error: " . $sql . "<br>" . mysqli_error($mysqli);
            echo "false";
        }
    }

    $mysqli->close();
}
?>
