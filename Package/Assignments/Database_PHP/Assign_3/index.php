<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['logout']) ) {
    unset($_SESSION['name']);
    header('Location: index.php');
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
<h1>Welcome to Autos Database</h1>

<p>
<a href="login.php">Please Log In</a>
</p>
<p>
Attempt to go to 
<a href="view.php">view.php</a> without logging in - it will fail with an error message.
</p>
<p>
Attempt to go to 
<a href="add.php">add.php</a> without logging in - it will fail with an error message.
</p>
</div>
</body>


