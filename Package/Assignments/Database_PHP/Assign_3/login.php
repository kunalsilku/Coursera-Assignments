<?php // Do not put any HTML above this line

require_once "pdo.php";
session_start();


if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

$failure = false;  // If we have no POST data
$name='';

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) 
{

    $name = $_POST['email'];
    $pass = $_POST['pass'];
    $Proper_username = 0;

    if ( strlen($name) < 1 || strlen($pass) < 1 ) 
        {
            $_SESSION['error'] = "User name and password are required";
            header("Location: login.php");
            return;
        } 
    else 
        {
            for($i=0;$i<strlen($name); $i++)
            {
                if($name[$i] == '@')
                {
                $Proper_username =1;
                break;
                }
            }

            if($Proper_username == 0) 
            {
                $_SESSION['error'] = "Email must have an at-sign (@)";
                header("Location: login.php");
                return;
            } 
            else
            {
                $check = hash('md5', $salt.$_POST['pass']);
                if ( $check == $stored_hash ) 
                {
                // Redirect the browser to view.php
                error_log("Login success ".$_POST['email']);
                $_SESSION['name']=$_POST['email'];
                header("Location: view.php");
                return;
                } 
                else 
                {
                    error_log("Login fail ".$_POST['email']." $check");
                    $_SESSION['error'] = "Incorrect password";
                    header("Location: login.php");
                    return;
                }
            }
        }
}


// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Kunal Silku a8681f25</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
$error = isset($_SESSION['error']) ? $_SESSION['error']: false;
unset($_SESSION['error']);

if ( $error !== false ) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($error)."</p>\n");
}
?>
<form method="POST">
<label for="nam">User Name</label>
<input type="text" name="email" id="nam" value="<?php echo(htmlentities($name))?>"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
</p>
</div>
</body>
</html>