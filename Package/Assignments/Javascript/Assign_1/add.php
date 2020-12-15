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

//$salt = 'XyZzy12*_';

if (! isset($_SESSION['first_name'])){ $_SESSION['first_name'] ='';}
if (! isset($_SESSION['last_name'])){ $_SESSION['last_name'] ='';}
if (! isset($_SESSION['user_email'])){ $_SESSION['user_email'] ='';}
if (! isset($_SESSION['headline'])){ $_SESSION['headline'] ='';}
if (! isset($_SESSION['summary'])){ $_SESSION['summary'] ='';}

$_SESSION['first_name'] = isset($_POST['first_name']) ? $_POST['first_name'] : $_SESSION['first_name'];
$_SESSION['last_name'] = isset($_POST['last_name']) ? $_POST['last_name'] : $_SESSION['last_name'];
$_SESSION['user_email'] = isset($_POST['user_email']) ? $_POST['user_email'] : $_SESSION['user_email'];
$_SESSION['headline'] = isset($_POST['headline']) ? $_POST['headline'] : $_SESSION['headline'];
$_SESSION['summary'] = isset($_POST['summary']) ? $_POST['summary'] : $_SESSION['summary'];


if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['user_email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

    // Data validation
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['user_email']) < 1 
        || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    }

    if( !strpos($_POST['user_email'],"@") ){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: add.php");
        return;
    }  

    try{

   $sql_3 = "INSERT INTO profile(user_id,first_name,last_name,email,headline,summary) VALUES(:uid,:fn,:ln,:email,:hd,:sm)";
        $stmt_3 = $pdo->prepare($sql_3);
        $stmt_3->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'], 
            ':ln' => $_POST['last_name'], 
            ':email' => $_POST['user_email'],
            ':hd' => $_POST['headline'], 
            ':sm' => $_POST['summary'] ));

        $_SESSION['success'] = "Record added";
        //unset($_SESSION['user_id']);

        $_SESSION['first_name'] = '';
        $_SESSION['last_name'] = '';
        $_SESSION['user_email'] = '';
        $_SESSION['headline'] = '';
        $_SESSION['summary'] = '';
        header( 'Location: index.php' ) ;
        return;
        }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

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
<h1>Adding Profile for 
<?php
if ( isset($_SESSION['name']) ) {
    echo htmlentities($_SESSION['name']);
}
?>
</h1>

<?php

$failure = isset($_SESSION['error']) ? $_SESSION['error']: false;
unset($_SESSION['error']);

$Session_first_name = $_SESSION['first_name'];
unset($_SESSION['first_name']);

$Session_last_name = $_SESSION['last_name'];
unset($_SESSION['last_name']);

$Session_user_email = $_SESSION['user_email'];
unset($_SESSION['user_email']);

$Session_headline = $_SESSION['headline'];
unset($_SESSION['headline']);

$Session_summary = $_SESSION['summary'];
unset($_SESSION['summary']);

if ( $failure !== false ) {
        echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
    }
    
?>

<form method="post">
<p>First Name: 
<input type="text" name="first_name" id="first_name" size="60" value="<?php echo(htmlentities($Session_first_name)) ?>"><p/>
<p>Last Name:
<input type="text" name="last_name" id="last_name" size="60" value="<?php echo(htmlentities($Session_last_name)) ?>"><p/>
<p>Email
<input type="text" name="user_email" id="user_email" size="30" value="<?php echo(htmlentities($Session_user_email)) ?>"><p/>
<p>Headline<br/>
<input type="text" name="headline" id="headline" size="80" value="<?php echo(htmlentities($Session_headline)) ?>"><p/>
<p>Summary<br/>
<textarea name="summary" id="summary" rows="8" cols="80"><?php echo(htmlentities($Session_summary)); ?></textarea><p/>
<input type="submit" name="Add" value="Add">
<input type="submit" name="Cancel" value="Cancel">
</form>


</div>
</body>
</html>