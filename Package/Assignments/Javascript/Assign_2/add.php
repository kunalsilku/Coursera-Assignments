<?php
require_once "pdo.php";
session_start();

// Demand a SESSION parameter
if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1) {
    die('Not logged in');
}

// If the user requested Cancel go back to view.php
if ( isset($_POST['Cancel']) ) {
    $res = unset_session();
    header('Location: index.php');
    return;
}

// Session Initialize
for($i=1; $i<=9; $i++) {
    $_SESSION['year'.$i] = isset($_SESSION['year'.$i]) ? $_SESSION['year'.$i]:false;
}


$_SESSION['first_name'] = isset($_SESSION['first_name']) ? $_SESSION['first_name']:false;
$_SESSION['last_name'] = isset($_SESSION['last_name']) ? $_SESSION['last_name']:false;
$_SESSION['email'] = isset($_SESSION['email']) ? $_SESSION['email']:false;
$_SESSION['headline'] = isset($_SESSION['headline']) ? $_SESSION['headline']:false;
$_SESSION['summary'] = isset($_SESSION['summary']) ? $_SESSION['summary']:false;

/*
function validate()
{
    // Validation for Dynamic Form Fields
    for($i=1; $i<=9; $i++) 
    {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) 
        {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
        }

    if ( ! is_numeric($year) ) 
        {
        $_SESSION['error'] = 'Position year must be numeric';
        header("Location: add.php");
        return;
        }
    }

    // Validation for Static Form Fields

  if ( isset($_POST['first_name']) && isset($_POST['last_name']) 
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) 
    {
        if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 
            || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            $_SESSION['error'] = 'All fields are required';
            header("Location: add.php");
            return;
            }

        if( strpos($_POST['email'],"@") === false){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: add.php");
        return;
        }  
    }
    else{
        $_SESSION['error'] = 'Form Fields not set';
        header("Location: add.php");
        return;
    }
return true;
}

function set_session()
{
    if ( isset($_POST['first_name']) && isset($_POST['last_name']) 
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) 
    {
    $_SESSION['first_name']=$_POST['first_name'];
    $_SESSION['last_name']=$_POST['last_name'];
    $_SESSION['email']=$_POST['email'];
    $_SESSION['headline']=$_POST['headline'];
    $_SESSION['summary']=$_POST['summary'];
    }

    for($i=1; $i<=9; $i++) 
    {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    
    $_SESSION['year'.$i] = $_POST['year'.$i];
    $_SESSION['desc'.$i] = $_POST['desc'.$i];
    }
return true;

}

function unset_session()
{
    unset($_SESSION['first_name']);
    unset($_SESSION['last_name']);
    unset($_SESSION['email']);
    unset($_SESSION['headline']);
    unset($_SESSION['summary']);

    for($i=1; $i<=9; $i++) 
    {
    unset($_SESSION['year'.$i]);
    unset($_SESSION['desc'.$i]);
    }
return true;

}

*/

function set_session()
{
    //Setting Sessions
    ////////////////////////////////////////////////////////////////////////////////////////////
    $_SESSION['first_name']=$_POST['first_name'];
    $_SESSION['last_name']=$_POST['last_name'];
    $_SESSION['email']=$_POST['email'];
    $_SESSION['headline']=$_POST['headline'];
    $_SESSION['summary']=$_POST['summary'];
    

    for($i=1; $i<=9; $i++) 
    {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    
    $_SESSION['year'.$i] = $_POST['year'.$i];
    $_SESSION['desc'.$i] = $_POST['desc'.$i];
    }
    ////////////////////////////////////////////////////////////////////////////////////////////
}

function unset_session()
{
    // Unsetting Session
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    unset($_SESSION['first_name']);
    unset($_SESSION['last_name']);
    unset($_SESSION['email']);
    unset($_SESSION['headline']);
    unset($_SESSION['summary']);

    for($i=1; $i<=9; $i++) 
    {
    unset($_SESSION['year'.$i]);
    unset($_SESSION['desc'.$i]);
    }
                
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) 
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {


    $res = set_session();


    // Validations
    ////////////////////////////////////////////////////////////////////////////////////////////
    // Validation for Dynamic Form Fields
    for($i=1; $i<=9; $i++) 
    {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) 
        {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
        }

    if ( ! is_numeric($year) ) 
        {
        $_SESSION['error'] = 'Position year must be numeric';
        header("Location: add.php");
        return;
        }
    }

    // Validation for Static Form Fields

    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 
            || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            $_SESSION['error'] = 'All fields are required';
            header("Location: add.php");
            return;
            }

    if( strpos($_POST['email'],"@") === false){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: add.php");
        return;
        }  
    
    ////////////////////////////////////////////////////////////////////////////////////////////

    // Database Operations
    ////////////////////////////////////////////////////////////////////////////////////////////
    try{

    $sql_1 = "INSERT INTO profile(user_id,first_name,last_name,email,headline,summary) VALUES(:uid,:fn,:ln,:email,:hd,:sm)";
    $stmt = $pdo->prepare($sql_1);
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'], 
            ':ln' => $_POST['last_name'], 
            ':email' => $_POST['email'],
            ':hd' => $_POST['headline'], 
            ':sm' => $_POST['summary'] ));

        $profile_id = $pdo->lastInsertId();

        // Insert the position entries
        $rank = 1;
        for($i=1; $i<=9; $i++) 
        {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];


        $sql_2 = "INSERT INTO position (profile_id, rank, years, description) VALUES ( :pid, :rank, :year, :descr)";
            $stmt = $pdo->prepare($sql_2);
            $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':descr' => $desc)
                );  
            $rank++;
        }
        
        $_SESSION['success'] = "Record added";
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        
        $res = unset_session(); 
        header( 'Location: index.php' );
        return;
        ////////////////////////////////////////////////////////////////////////////////////////////////////
               
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

<!-- head.php -->

<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>

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
if ( isset($_SESSION['error']) ) {
        echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
        unset($_SESSION['error']);
    }

?>

<form method="post" id="form_1" action="add.php">
<p>First Name: 
<input type="text" name="first_name" id="first_name" size="60" value="<?= htmlentities($_SESSION['first_name']) ?>"><p/>
<p>Last Name:
<input type="text" name="last_name" id="last_name" size="60" value="<?= htmlentities($_SESSION['last_name']) ?>"><p/>
<p>Email
<input type="text" name="email" id="email" size="30" value="<?= htmlentities($_SESSION['email']) ?>"><p/>
<p>Headline<br/>
<input type="text" name="headline" id="headline" size="80" value="<?= htmlentities($_SESSION['headline']) ?>"><p/>
<p>Summary<br/>
<textarea name="summary" id="summary" rows="8" cols="80"><?= htmlentities($_SESSION['summary']) ?></textarea></p>
</p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<p>
<input type="submit" name="Add" id="Add" value="Add">
<input type="submit" name="Cancel" value="Cancel">
</p>
</form>

<script>
countPos = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript

$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        window.console && console.log('addPos click called');
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of Nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" id="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <p>Description<br/><textarea name="desc'+countPos+'" id="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

     $('#Add').click(function(){
        $('#form_1').submit(function (data) {
            //window.console && console.log(data);
        });
        //window.console && console.log("Form Submitted");
     });
});



</script>


</div>
</body>
</html>