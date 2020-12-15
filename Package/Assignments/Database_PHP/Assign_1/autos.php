<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION['name']) && ! isset($_GET['name']) ){
    die('Name parameter missing');
}

$_SESSION['name'] = isset($_GET['name']) ? $_GET['name'] : $_SESSION['name'];
// Demand a GET parameter
if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    unset($_SESSION['name']);
    header('Location: index.php');
    return;
}

if (! isset($_SESSION['make'])){ $_SESSION['make'] ='';}
if (! isset($_SESSION['year'])){ $_SESSION['year'] ='';}
if (! isset($_SESSION['mileage'])){ $_SESSION['mileage'] ='';}

$_SESSION['make'] = isset($_POST['make']) ? $_POST['make'] : $_SESSION['make'];
$_SESSION['year'] = isset($_POST['year']) ? $_POST['year'] : $_SESSION['year'];
$_SESSION['mileage'] = isset($_POST['mileage']) ? $_POST['mileage'] : $_SESSION['mileage'];


if ( isset($_POST['make']) || isset($_POST['year']) || isset($_POST['Auto_Milege'])  ) {
    $_SESSION['data'] = true;
    $failure = false;
    echo("Present 1");
 

    if (!(is_numeric($_POST['year']) && is_numeric($_POST['mileage'])))
    {
        $failure = "Mileage and Year must be numeric";
    }

    if ( strlen($_POST['make']) < 1 ) 
        {
            $failure = "Make is required";
        }

    if ( $failure === false ) 
    { 

    try{
    $sql_1 = "INSERT INTO autos(make,year,mileage) VALUES(:mk,:yr,:ml)";
        $stmt_1 = $pdo->prepare($sql_1);
        $stmt_1->execute(array(
            ':mk' => $_POST['make'], 
            ':yr' => $_POST['year'],
            ':ml' => $_POST['mileage']));
        $Ok_message = "Record Inserted Successfully";
        $_SESSION['make'] = '';
        $_SESSION['year'] = '';
        $_SESSION['mileage'] = '';
        }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

    
    }

    $_SESSION['failure'] = $failure;
    $_SESSION['Ok_message'] = $Ok_message;
    header("Location: autos.php");
    return;
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
if ( isset($_REQUEST['name']) ) {
    echo "<p>Welcome: ";
    echo htmlentities($_REQUEST['name']);
    echo "</p>\n";
}

//if ( isset($_POST['Auto_make']) || isset($_POST['year']) || isset($_POST['Auto_Milege'])  ){
$data = isset($_SESSION['data']) ? $_SESSION['data']: false;
unset($_SESSION['data']);

$failure = isset($_SESSION['failure']) ? $_SESSION['failure']: false;
unset($_SESSION['failure']);

$Ok_message = isset($_SESSION['Ok_message']) ? $_SESSION['Ok_message']: '';
unset($_SESSION['Ok_message']);

$Session_make = $_SESSION['make'];
unset($_SESSION['make']);

$Session_year = $_SESSION['year'];
unset($_SESSION['year']);

$Session_mileage = $_SESSION['mileage'];
unset($_SESSION['mileage']);

if ($data !== false){
    if ( $failure !== false ) {
        echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
    }
    else{
        echo('<p style="color: green;">'.htmlentities($Ok_message)."</p>\n");
    }
}

?>

<form method="post">
<label for="make">make</label>
<input type="text" name="make" id="make" value="<?php echo(htmlentities($Session_make)) ?>"><br/>
<label for="mileage">Mileage</label>
<input type="text" name="mileage" id="mileage" value="<?php echo(htmlentities($Session_mileage)) ?>"><br/>
<label for="year">Year</label>
<input type="text" name="year" id="year" value="<?php echo(htmlentities($Session_year)) ?>"><br/>
<input type="submit" name="add" value="Add">
<input type="submit" name="logout" value="Logout">
</form>

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

</div>
</body>
</html>