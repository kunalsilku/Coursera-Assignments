<?php
require_once "pdo.php";
// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

//session_start();

if ( isset($_POST['Auto_Make']) || isset($_POST['Auto_Year']) || isset($_POST['Auto_Milege'])  ) {
    //$_SESSION['data'] = true;
    $failure = false;

    if ( strlen($_POST['Auto_Make']) < 1 ) 
        {
            $failure = "Make required";
        } 

    if (!(is_numeric($_POST['Auto_Year']) && is_numeric($_POST['Auto_Milege'])))
    {
        $failure = "Mileage and Year must be numeric";
    }

    if ( $failure === false ) 
    { 
    try{
        $sql_1 = "INSERT INTO autos(make,year,mileage) VALUES(:mk,:yr,:ml)";
        $stmt_1 = $pdo->prepare($sql_1);
        $stmt_1->execute(array(
            ':mk' => $_POST['Auto_Make'], 
            ':yr' => $_POST['Auto_Year'],
            ':ml' => $_POST['Auto_Milege']));
        $Ok_message = "Record Inserted Successfully";
        }catch (Exception $ex ) { 
    echo("Internal error, please contact support");
    error_log("error4.php, SQL error=".$ex->getMessage());
    return;
    }

    //$_SESSION['failure'] = $failure;
    //$_SESSION['Ok_message'] = $Ok_message;
    //header("Location: autos.php");
    //return;
}

// SQL Query for Fetch
try{
    $sql_2 = "SELECT * FROM autos";
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
if ( isset($_REQUEST['name']) ) {
    echo "<p>Welcome: ";
    echo htmlentities($_REQUEST['name']);
    echo "</p>\n";
}

if ( isset($_POST['Auto_Make']) || isset($_POST['Auto_Year']) || isset($_POST['Auto_Milege'])  ){
//if ( isset($_SESSION['data']) ){
//if ( $_SESSION['failure'] !== false ) {
    if ( $failure !== false ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
}
else{
    echo('<p style="color: green;">'.htmlentities($_SESSION['Ok_message'])."</p>\n");
    }
}

?>

<form method="post">
<label for="make">Make</label>
<input type="text" name="Auto_Make" id="make"><br/>
<label for="year">Year</label>
<input type="text" name="Auto_Year" id="year"><br/>
<label for="mileage">Mileage</label>
<input type="text" name="Auto_Milege" id="mileage"><br/>
<input type="submit" name="add" value="Add">
<input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>

<table style="width:100%">
  <tr>
    <th>Auto ID</th>
    <th>Auto Make</th>
    <th>Year of Production</th>
    <th>Mileage</th>
  </tr>
  
<pre>
<?php

while ($result = $stmt_2->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>".$result['auto_id']."</td>";
    echo "<td>".$result['make']."</td>";
    echo "<td>".$result['year']."</td>";
    echo "<td>".$result['mileage']."</td>";
    echo "</tr>";
  
}

?>
</pre>
</table>

</div>
</body>
</html>