<?php
require_once "pdo.php";
session_start();


// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

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
    echo "<tr>";
    echo "<td>".htmlentities($first_name)."</td>";
    echo "<td>".htmlentities($last_name)."</td>";
    echo "<td>".htmlentities($email)."</td>";
    echo "<td>".htmlentities($headline)."</td>";
    echo "<td>".htmlentities($summary)."</td>";
    echo "</tr>";
?>
</table>

<br/>
<p> 
<a href="index.php">Back</a>
</p>


</div>
</body>
</html>