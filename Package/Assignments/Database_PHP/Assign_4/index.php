<?php
require_once "pdo.php";
session_start();



?>

<!DOCTYPE html>
<html>
<head>
<title>Kunal Silku a8681f25</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Welcome to Automobiles Database</h1>

<?php

if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1) {
	echo("<p>");
	echo('<a href="login.php">Please log in</a>');
	echo("</p>");
    
}

else
{

	if ( isset($_SESSION['error']) ) {
	    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
	    unset($_SESSION['error']);
	}
	if ( isset($_SESSION['success']) ) {
	    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
	    unset($_SESSION['success']);
	}


$Rows = $pdo->query("SELECT * FROM autos");
if($Rows->rowCount() > 0)
	{
	echo('<table border="1" style="width:100%">'."\n");
	echo('<tr>');
	echo('<th>Make</th>');
	echo('<th>Model</th>');
	echo('<th>Year</th>');
	echo('<th>Mileage</th>');
	echo('<th>Action</th>');
	echo('</tr>');
	$stmt = $pdo->query("SELECT autos_id,make, model, year, mileage FROM autos");
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	    echo "<tr><td>";
	    echo(htmlentities($row['make']));
	    echo("</td><td>");
	    echo(htmlentities($row['model']));
	    echo("</td><td>");
	    echo(htmlentities($row['year']));
	    echo("</td><td>");
	    echo(htmlentities($row['mileage']));
	    echo("</td><td>");
	    echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
	    echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
	    echo("</td></tr>\n");
		}
	echo("</table>");
	}
	else{
		echo("No rows found");
	}
	echo("<br><br>");
	echo('<a href="add.php">Add New Entry</a><br><br>');
	echo('<a href="logout.php">Logout</a><br>');

}

?>


</div>
</body>
</html>


