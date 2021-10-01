<?php
session_start();
require_once "pdo.php";
require_once "util.php";

//If user is not logged in,
if (!isset($_SESSION['name']))//Guardian
{
  $_SESSION['error']= "Access Denied";
  header("Location: index.php");
  return;
}

//If there is no parameter for CwID
if (!isset($_GET['CwID']))
{
  $_SESSION['error']= "No entry selected";
  header("Location: viewclanwars.php");
  return;
}


//Check if donationID is in database
$stmt = $pdo->prepare("SELECT Members.IGN, ClanWars.Count, Dates.Date, Dates.DateID
FROM Members JOIN ClanWars JOIN Dates
ON Members.MemberID = ClanWars.MemberID
and ClanWars.DateID = Dates.DateID
WHERE ClanWars.CwID = :CwID");
$stmt->execute(array(':CwID' => $_GET['CwID']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
//Check if ID is in the Database
if($rows === false)
{
  $_SESSION['error'] = "Entry not found";
  header("Location: viewclanwars.php");
  return;
}
//If user confirms deletion,
if (isset($_POST['confirm']))
  {
    $sql = "DELETE FROM ClanWars WHERE ClanWars.CwID = ".$_GET['CwID']."";
    $stmt = $pdo->query($sql);//$stmt doesn't blow up, its just there to catch the output (ie. 'false')
    $_SESSION['success'] = "Entry deleted.";
    header("Location: viewclanwars.php");
    return;
  }
//If user clicks cancel on form
if (isset ($_POST['cancel']))
  {
    header ("Location: viewclanwars.php");
    return;
  }
?>
<!DOCTYPE html>
<html lang = 'en'>
<head>
  <meta charset = "utf-8">
  <title> COC Ascension Database </title>
  <?php require_once "bootstrap.php" ?>
</head>
<body class = "starter-template">
<p> <strong>Confirm <strong style="color:red;"> deletion </strong> of the following entry:</strong> </p>
  <?php
  foreach($rows as $entry)
  {
      $name = htmlentities($entry['IGN']);
      $count = htmlentities($entry['Count']);
      $date = htmlentities($entry['Date']);
  }
  echo ("Name:".$name."<br> War Count: ".$count." <br> Date: ".$date."
  ");
   ?>
</p>
<p>
  <form method = "POST"> <input type = "submit" name = "confirm" value = "Confirm"> &nbsp; &nbsp;
 <input type = "submit" name = "cancel" value = "Cancel"></form>
</p> <hr>
</body>
</html>
