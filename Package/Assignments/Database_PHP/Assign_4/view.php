<?php
require_once "pdo.php";
session_start();


// Demand a SESSION parameter
if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1) {
    die('Not logged in');
}


// SQL Query for Fetch
try{
    $sql_2 = "SELECT * FROM autos ORDER BY make";
    $stmt_2 = $pdo->prepare($sql_2);
    $stmt_2->execute();
    }catch (Exception $ex ) { 
    echo("Internal error, please contact support");
    error_log("error4.php, SQL error=".$ex->getMessage());
    return;
    }

?>


<!DOCTYPE html>
<html>
<head>
<title>Kunal Silku a8681f25</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Tracking Autos for csev@umich.edu</h1>
<?php
if ( isset($_SESSION['name']) ) {
    echo "<p>Welcome: ";
    echo htmlentities($_SESSION['name']);
    echo "</p>\n";
}


$Ok_message = isset($_SESSION['success']) ? $_SESSION['success']: '';
unset($_SESSION['success']);

echo('<p style="color: green;">'.htmlentities($Ok_message)."</p>\n");


?>

<h2>Automobiles</h2>

<table style="width:100%">
  <tr>
    <th>Auto make</th>
    <th>Mileage</th>
    <th>Year of Production</th>
  </tr>
  
<pre>
<?php

while ($result = $stmt_2->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>".htmlentities($result['make'])."</td>";
    echo "<td>".htmlentities($result['mileage'])."</td>";
    echo "<td>".htmlentities($result['year'])."</td>";
    echo "</tr>";
  
}

?>
</pre>
</table>

<p> 
<a href="add.php">Add New</a> | <a href="logout.php">Logout</a>
</p>


</div>
</body>
</html>