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

// Guardian: Make sure that profile_id is present
if(isset($_GET['profile_id']))
{
    $_SESSION['profile_id'] = $_GET['profile_id'];
}

if ( ! isset($_SESSION['profile_id']) )  {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

try {
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_SESSION['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
    }
}catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

$_SESSION['first_name'] = $row['first_name'];
$_SESSION['last_name'] = $row['last_name'];
$_SESSION['email'] = $row['email'];
$_SESSION['headline'] = $row['headline'];
$_SESSION['summary'] = $row['summary'];

// Session Initialize
for($i=1; $i<=9; $i++) {
    $_SESSION['year'.$i] = isset($_SESSION['year'.$i]) ? $_SESSION['year'.$i]:false;
    $_SESSION['desc'.$i] = isset($_SESSION['desc'.$i]) ? $_SESSION['desc'.$i]:false;
    $_SESSION['edu_year'.$i] = isset($_SESSION['edu_year'.$i]) ? $_SESSION['edu_year'.$i]:false;
    $_SESSION['edu_school'.$i] = isset($_SESSION['edu_school'.$i]) ? $_SESSION['edu_school'.$i]:false;
}


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

    for($i=1; $i<=9; $i++) 
    {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    
    $_SESSION['edu_year'.$i] = $_POST['edu_year'.$i];
    $_SESSION['edu_school'.$i] = $_POST['edu_school'.$i];
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
    unset($_SESSION['edu_year'.$i]);
    unset($_SESSION['edu_school'.$i]);
    }
                
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) 
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {


    $res = set_session();


    // Validations
    ////////////////////////////////////////////////////////////////////////////////////////////
    // Validation for Dynamic Position Form Fields
    for($i=1; $i<=9; $i++) 
    {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) 
        {
        $_SESSION['error'] = 'All fields are required';
        header("Location: edit.php");
        return;
        }

    if ( ! is_numeric($year) ) 
        {
        $_SESSION['error'] = 'Position year must be numeric';
        header("Location: edit.php");
        return;
        }
    }

    // Validation for Dynamic Education Form Fields
    for($i=1; $i<=9; $i++) 
    {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $edu_year = $_POST['edu_year'.$i];
    $edu_school = $_POST['edu_school'.$i];

    if ( strlen($edu_year) == 0 || strlen($edu_school) == 0 ) 
        {
        $_SESSION['error'] = 'All fields are required';
        header("Location: edit.php");
        return;
        }

    if ( ! is_numeric($edu_year) ) 
        {
        $_SESSION['error'] = 'Education year must be numeric';
        header("Location: edit.php");
        return;
        }
    }

    // Validation for Static Form Fields

    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 
            || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            $_SESSION['error'] = 'All fields are required';
            header("Location: edit.php");
            return;
            }

    if( strpos($_POST['email'],"@") === false){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: edit.php");
        return;
        }  
    
    ////////////////////////////////////////////////////////////////////////////////////////////

        // Database Operations
    ////////////////////////////////////////////////////////////////////////////////////////////
    try{

        $sql_1 = "UPDATE profile SET first_name=:fn, last_name=:ln, email=:email, headline=:hd, summary=:sm WHERE profile_id=:pid";
        $stmt_1 = $pdo->prepare($sql_1);
        $stmt_1->execute(array(
            ':fn' => $_POST['first_name'], 
            ':ln' => $_POST['last_name'], 
            ':email' => $_POST['email'],
            ':hd' => $_POST['headline'],
            ':sm' => $_POST['summary'],
            ':pid' => $_SESSION['profile_id']));


        // Clear out the old position entries
        $stmt = $pdo->prepare('DELETE FROM position
        WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_SESSION['profile_id']));

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
                ':pid' => $_SESSION['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':descr' => $desc)
                );  
            $rank++;
        }

        /////////////////////////////////
        // Clear out the old Education entries
        $stmt = $pdo->prepare('DELETE FROM education
        WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_SESSION['profile_id']));


        // Check the Institution entries
        $rank_2 = 1;
        for($i=1; $i<=9; $i++) 
        {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];


        $sql_3 = "SELECT * FROM institution WHERE name=:xyz";
        $stmt = $pdo->prepare($sql_3);
        $stmt->execute(array(":xyz" => $edu_school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row === false ) {
            // Inserting Institution entry
            $sql_4 = "INSERT INTO institution (name) VALUES ( :name)";
            $stmt = $pdo->prepare($sql_4);
            $stmt->execute(array(
                ':name' => $edu_school)
                );
            $inst_id = $pdo->lastInsertId();  
        }
        else
        {
            $inst_id = $row['institution_id'];
        }

        /////////////////////////////////////////
        // Inserting Education Entries

        $sql_5 = "INSERT INTO education (profile_id, institution_id, rank, years) VALUES ( :pid, :iid, :rank, :year)";
            $stmt = $pdo->prepare($sql_5);
            $stmt->execute(array(
                ':pid' => $_SESSION['profile_id'],
                ':iid' => $inst_id,
                ':rank' => $rank_2,
                ':year' => $edu_year)
                );  
            $rank_2++;
        }
        /////////////////////////////////
        
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
            header("Location: edit.php");
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
<title>Profile Edit</title>
<!-- head.php -->

<link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
    crossorigin="anonymous">

<link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" 
    integrity="sha384-xewr6kSkq3dBbEtB6Z/3oFZmknWn7nHqhLVLrYgzEFRbU/DHSxW7K3B44yWUN60D" 
    crossorigin="anonymous">

<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>

<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
  crossorigin="anonymous"></script>

</head>
<body>
<div class="container">

<h1>Editing Profile for 
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

<form method="post" id="form_1" action="edit.php">
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
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
<?php

        try {
    $stmt = $pdo->prepare("SELECT institution.name AS Institute_Name, education.years AS Year , education.rank AS Rank FROM institution JOIN education where  education.institution_id=institution.institution_id AND education.profile_id=:xyz ORDER BY education.rank");
    $stmt->execute(array(":xyz" => $_SESSION['profile_id']));
    
    $count_edu = $stmt->rowCount();

    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
        {
        $rank = $row['Rank'];

        echo('<div id="edu'.$rank.'">'); 
        echo('<p>Year: <input type="text" name="edu_year'.$rank.'" value="'.$row['Year'].'" />');
        echo('<input type="button" value="-" onclick="$(\'#edu'.$rank.'\').remove();return false;"><br>');
        echo('<p>School: <input type="text" size="80" name="edu_school'.$rank.'" class="school" value="'.$row['Institute_Name'].'" />');
        echo('</p></div>');

        }
    }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }


?>
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">

<?php

        try {
    $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :xyz ORDER BY rank");
    $stmt->execute(array(":xyz" => $_SESSION['profile_id']));
    
    $count_pos = $stmt->rowCount();

    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
        {
        $rank = $row['rank'];

        echo('<div id="position'.$rank.'">');
        echo('<p>Year: <input type="text" name="year'.$rank.'" value="'.$row['years'].'" />');
        echo('<input type="button" value="-" onclick="$(\'#position'.$rank.'\').remove();return false;"><br>');
        echo('<p>Description<br/><textarea name="desc'.$rank.'" id="desc'.$rank.'" rows="8" cols="80">'.$row['description'].'</textarea>');
        echo('</div>');

        }
    }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

?>

</div>
</p>
<p>
<input type="submit" value="Save">
<input type="submit" name="Cancel" value="Cancel">
</p>
</form>
<script>
countPos = <?php echo($count_pos); ?>;
countEdu = <?php echo($count_edu); ?>;


// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');

    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"><br>\
            <p>Description<br/><textarea name="desc'+countPos+'" id="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );

        $('.school').autocomplete({
            source: "school.php"
        });

    });

});

</script>
</div>
</body>
</html>
