<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['Cancel']) ) {
    header('Location: index.php');
    return;
}


if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

    // Data validation
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 
         || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: edit.php");
        return;
    }

    if( !strpos($_POST['email'],"@") ){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: edit.php");
        return;
    }


    try{

    $sql_2 = "UPDATE profile SET first_name=:fn, last_name=:ln, email=:email, headline=:hd, summary=:sm WHERE profile_id=:pid";
        $stmt_2 = $pdo->prepare($sql_2);
        $stmt_2->execute(array(
            ':fn' => $_POST['first_name'], 
            ':ln' => $_POST['last_name'], 
            ':email' => $_POST['email'],
            ':hd' => $_POST['headline'],
            ':sm' => $_POST['summary'],
            ':pid' => $_SESSION['profile_id']));
        

        $_SESSION['success'] = "Record Updated";
        unset($_SESSION['profile_id']);
        header( 'Location: index.php' ) ;
        return;
        }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

}

// Guardian: Make sure that user_id is present
if(isset($_GET['profile_id']))
{
    $_SESSION['profile_id'] = $_GET['profile_id'];
}

if ( ! isset($_SESSION['profile_id']) )  {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}


$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_SESSION['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$profile_id = $row['profile_id'];


?>

<!DOCTYPE html>
<html>
<head>
<title>Kunal Silku a8681f25</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Editing Profile for <?php echo(htmlentities($email)); ?></h1>


<?php
// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>

<form method="post">
<p>First Name: 
<input type="text" name="first_name" id="first_name" size="60" value="<?= $first_name ?>"><p/>
<p>Last Name:
<input type="text" name="last_name" id="last_name" size="60" value="<?= $last_name?>"><p/>
<p>Email
<input type="text" name="email" id="email" size="30" value="<?= $email?>"><p/>
<p>Headline<br/>
<input type="text" name="headline" id="headline" size="80" value="<?= $headline ?>"><p/>
<p>Summary<br/>
<textarea name="summary" id="summary" rows="8" cols="80"><?= $summary ?></textarea><p/>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<input type="submit" name="Save" value="Save">
<input type="submit" name="Cancel" value="Cancel">
</form>

</div>
</body>
</html>
