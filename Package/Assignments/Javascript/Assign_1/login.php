<?php // Do not put any HTML above this line

require_once "pdo.php";
session_start();


if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
//$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

$failure = false;  // If we have no POST data
$name='';

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) 
{

    $email = $_POST['email'];
    $pass = $_POST['pass'];

    $pass_hash = hash('md5', $salt.$pass);

    try{

    $sql_2 = "SELECT * FROM users WHERE email=:email AND password=:pass_hash";
        $stmt_2 = $pdo->prepare($sql_2);
        $stmt_2->execute(array( 
            ':email' => $email, 
            ':pass_hash' => $pass_hash));
        $row = $stmt_2->fetch(PDO::FETCH_ASSOC);

        if($row !== false)
            {
            error_log("Login success ".$_POST['email']);
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return;
            }
        else
            {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['error'] = "Incorrect Email or password";
            header("Location: login.php");
            return;
            }       
        }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }
       
}
        


?>

<!DOCTYPE html>
<html>
<head>
<title>Kunal Silku's Login Page</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

</head>
<body>
<div class="container">
<h1>Please Log In</h1>

<?php

$error = isset($_SESSION['error']) ? $_SESSION['error']: false;
unset($_SESSION['error']);

if ( $error !== false ) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($error)."</p>\n");
}
?>

<form method="POST" action="login.php">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>

<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>

</div>
</body>
