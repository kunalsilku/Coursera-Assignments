<?php
require_once "pdo.php";
session_start();

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) )  {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

?>


<!DOCTYPE html>
<html>
<head>
<title>Profile View</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Detail View</h1>

<table border="1" style="width:100%">
  <tr>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Email</th>
    <th>Headline</th>
    <th>Summary</th>

  </tr>
  
<?php

try {
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
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

    echo "<tr>";
    echo "<td>".htmlentities($row['first_name'])."</td>";
    echo "<td>".htmlentities($row['last_name'])."</td>";
    echo "<td>".htmlentities($row['email'])."</td>";
    echo "<td>".htmlentities($row['headline'])."</td>";
    echo "<td>".htmlentities($row['summary'])."</td>";
    echo "</tr>";
?>

</table>

<h3>Positions</h3>
<ul>

<?php 

try {
    $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    

    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
        {
        echo "<li>";
        echo(htmlentities($row['years']." | ".$row['description']));
        echo("</li>");
        }
    }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

?>

</ul>

<h3>Institutions</h3>
<ul>

<?php 

try {
    $stmt = $pdo->prepare("SELECT institution.name AS Institute_Name, education.years AS Year FROM institution JOIN education where  education.institution_id=institution.institution_id AND education.profile_id=:xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    

    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
        {
        echo "<li>";
        echo(htmlentities($row['Year']." | ".$row['Institute_Name']));
        echo("</li>");
        }
    }catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

?>

</ul>

<br/>
<p> 
<a href="index.php">Back</a>
</p>

</div>
</body>
</html>