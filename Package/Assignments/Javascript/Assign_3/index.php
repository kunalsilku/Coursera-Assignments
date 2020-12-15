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
<h1>Resume Registry</h1>


<?php

$error = isset($_SESSION['error']) ? $_SESSION['error']: false;
unset($_SESSION['error']);

if ( $error !== false ) {
    echo('<p style="color: red;">'.htmlentities($error)."</p>\n");
}

if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1) {
	echo("<p>");
	echo('<a href="login.php">Please log in</a>');
	echo("</p>");

	$Rows = $pdo->query("SELECT * FROM profile");
	if($Rows->rowCount() > 0)
	{
	echo('<table border="1" style="width:50%">'."\n");
	echo('<tr>');
	echo('<th>Name</th>');
	echo('<th>Headline</th>');
	echo('<th>Details</th>');
	echo('</tr>');
	//$stmt = $pdo->query("SELECT autos_id,make, model, year, mileage FROM autos");
	while ( $row = $Rows->fetch(PDO::FETCH_ASSOC) ) {
	    echo "<tr><td>";
	    echo(htmlentities($row['first_name']." ".$row['last_name']));
	    echo("</td><td>");
	    echo(htmlentities($row['headline']));
	    echo("</td><td>");
	    echo('<a href="view.php?profile_id='.$row['profile_id'].'">Details</a>  ');
	    echo("</td></tr>\n");
		}
	echo("</table>");
	}
    
}

else
{

	echo("<p>");
	echo('<a href="logout.php">Logout</a>');
	echo("</p>");

    echo "<p>Welcome: ";
    echo htmlentities($_SESSION['name']);
    echo "</p>\n";


	if ( isset($_SESSION['error']) ) {
	    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
	    unset($_SESSION['error']);
	}
	if ( isset($_SESSION['success']) ) {
	    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
	    unset($_SESSION['success']);
	}

try {
$stmt = $pdo->prepare("SELECT * FROM profile where user_id = :xyz");
$stmt->execute(array(":xyz" => $_SESSION['user_id']));
if($stmt->rowCount() > 0)
{
	echo('<table border="1" style="width:75%">'."\n");
	echo('<tr>');
	echo('<th>Name</th>');
	echo('<th>Headline</th>');
	echo('<th>Details</th>');
	echo('<th>Action</th>');
	echo('</tr>');

while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
	{
		echo "<tr><td>";
	    echo(htmlentities($row['first_name']." ".$row['last_name']));
	    echo("</td><td>");
	    echo(htmlentities($row['headline']));
	    echo("</td><td>");
	    echo('<a href="view.php?profile_id='.$row['profile_id'].'">Detail</a> ');
	    echo("</td><td>");
	    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
	    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
	    echo("</td></tr>\n");
	    }
	echo("</table>");
	}
else{
	echo("No rows found");
	}
}catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

echo("<br>");
echo('<a href="add.php">Add New Entry</a><br><br>');
}

?>


</div>
</body>
</html>


