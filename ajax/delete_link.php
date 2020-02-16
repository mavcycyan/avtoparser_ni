<?
$id = substr(htmlspecialchars(trim($_POST['id'])), 0, 100);

if ($id == ''){
	echo "false";
    return false;
}
else { 
    $mysqli = new mysqli('localhost', 'root', '', 'parser_ni');

    $query ="DELETE FROM links WHERE id=$id";
    if ($mysqli->query($query)) {
		echo "true";
	}
    else {
        //echo "Error: " . $sql . "<br>" . mysqli_error($mysqli);
		echo "false";
	}

    $mysqli->close();
}
?>
