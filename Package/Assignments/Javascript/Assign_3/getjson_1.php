<?php
require_once "pdo.php";
session_start();
header('Content-Type: application/json; charset=utf-8');


// Demand a SESSION parameter
if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1) {
    die('Not logged in');
}

if ( ! isset($_SESSION['profile_id']) )  {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

try {
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_SESSION['profile_id']));
$rows = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  		$rows[] = $row;
		}
echo json_encode($rows, JSON_PRETTY_PRINT);
}catch (Exception $ex ) { 
        echo("Internal error, please contact support");
        error_log("error4.php, SQL error=".$ex->getMessage());
        return;
        }

?>






