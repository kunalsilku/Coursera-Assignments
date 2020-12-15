<?php
require_once "pdo.php";
session_start();


// Demand a SESSION parameter
if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1) {
    die('Not logged in');
}

// If the user requested Cancel go back to view.php
if ( isset($_POST['Cancel']) ) {
    header('Location: index.php');
    return;
}

if (! isset($_SESSION['make'])){ $_SESSION['make'] ='';}
if (! isset($_SESSION['model'])){ $_SESSION['model'] ='';}
if (! isset($_SESSION['year'])){ $_SESSION['year'] ='';}
if (! isset($_SESSION['mileage'])){ $_SESSION['mileage'] ='';}

$_SESSION['make'] = isset($_POST['make']) ? $_POST['make'] : $_SESSION['make'];
$_SESSION['model'] = isset($_POST['model']) ? $_POST['model'] : $_SESSION['model'];
$_SESSION['year'] = isset($_POST['year']) ? $_POST['year'] : $_SESSION['year'];
$_SESSION['mileage'] = isset($_POST['mileage']) ? $_POST['mileage'] : $_SESSION['mileage'];


/*if ( isset($_POST['make']) || isset($_POST['model']) || isset($_POST['year']) || isset($_POST['mileage'])  ) {
    $_SESSION['data'] = true;
    $failure = false;
 

    if (!(is_numeric($_POST['year']) && is_numeric($_POST['mileage'])))
    {
        $failure = "Mileage and Year must be numeric";
    }

    if ( strlen($_POST['make']) < 1 ) 
        {
            $failure = "Make is required";
        }
*/

if ( isset($_POST['make']) && isset($_POST['model'])
     && isset($_POST['year']) && isset($_POST['mileage']) ) {

    // Data validation
    if ( strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1 ) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    }

    if (!is_numeric($_POST['year']))
    {
        $_SESSION['error'] = "Year must be an integer";
        header("Location: add.php");
        return;
    }

    if (!is_numeric($_POST['mileage']))
    {
        $_SESSION['error'] = "Mileage must be an integer";
        header("Location: add.php");
        return;
    }

  
    try{
    $sql_1 = "INSERT INTO autos(make,model,year,mileage) VALUES(:mk,:md,:yr,:ml)";
        $stmt_1 = $pdo->prepare($sql_1);
        $stmt_1->execute(array(
            ':mk' => $_POST['make'], 
            ':md' => $_POST['model'], 
            ':yr' => $_POST['year'],
            ':ml' => $_POST['mileage']));
        $_SESSION['success'] = "Record added";
        $_SESSION['make'] = '';
        $_SESSION['model'] = '';
        $_SESSION['year'] = '';
        $_SESSION['mileage'] = '';
        header( 'Location: index.php' ) ;
        return;
        }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

    

    //$_SESSION['failure'] = $failure;
    //$_SESSION['success'] = $Ok_message;
    //header("Location: add.php");
    //return;
}

if ( isset($_POST['Add']) ) 
    {
        if ( $_SESSION['error'] !== false ) {
            header("Location: add.php");
            return;
        }
        if (isset($_SESSION['success'])) {
        header('Location: index.php');
        return;
        }
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
<h1>Tracking Autos for 
<?php
if ( isset($_SESSION['name']) ) {
    echo htmlentities($_SESSION['name']);
}
?>
</h1>

<?php

//if ( isset($_POST['Auto_make']) || isset($_POST['year']) || isset($_POST['Auto_Milege'])  ){
$failure = isset($_SESSION['error']) ? $_SESSION['error']: false;
unset($_SESSION['error']);

$Session_make = $_SESSION['make'];
unset($_SESSION['make']);

$Session_model = $_SESSION['model'];
unset($_SESSION['model']);

$Session_year = $_SESSION['year'];
unset($_SESSION['year']);

$Session_mileage = $_SESSION['mileage'];
unset($_SESSION['mileage']);

if ( $failure !== false ) {
        echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
    }


?>

<form method="post">
<label for="make">Make</label>
<input type="text" name="make" id="make" value="<?php echo(htmlentities($Session_make)) ?>"><br/>
<label for="make">Model</label>
<input type="text" name="model" id="model" value="<?php echo(htmlentities($Session_model)) ?>"><br/>
<label for="year">Year</label>
<input type="text" name="year" id="year" value="<?php echo(htmlentities($Session_year)) ?>"><br/>
<label for="mileage">Mileage</label>
<input type="text" name="mileage" id="mileage" value="<?php echo(htmlentities($Session_mileage)) ?>"><br/>
<input type="submit" name="Add" value="Add">
<input type="submit" name="Cancel" value="Cancel">
</form>


</div>
</body>
</html>