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

//If there is no parameter for CgID
if (!isset($_GET['CgID']))
{
  $_SESSION['error']= "No entry selected";
  header("Location: viewclanwars.php");
  return;
}


//Check if donationID is in database
$stmt = $pdo->prepare("SELECT Members.IGN, ClanGames.Points, Dates.Date, Dates.DateID
FROM Members JOIN ClanGames JOIN Dates
ON Members.MemberID = ClanGames.MemberID
and ClanGames.DateID = Dates.DateID
WHERE ClanGames.CgID = :CgID");
$stmt->execute(array(':CgID' => $_GET['CgID']));
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
    $sql = "DELETE FROM ClanGames WHERE ClanGames.CgID = ".$_GET['CgID']."";
    $stmt = $pdo->query($sql);//$stmt doesn't blow up, its just there to catch the output (ie. 'false')
    $_SESSION['success'] = "Entry deleted.";
    header("Location: viewclangames.php");
    return;
  }
//If user clicks cancel on form
if (isset ($_POST['cancel']))
  {
    header ("Location: viewclangames.php");
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
      $po = htmlentities($entry['Points']);
      $date = htmlentities($entry['Date']);
  }
  echo ("<p>Name: ".$name."<br> Clan Game Points: ".$po." <br> Date: ".$date."</p>
  ");
   ?>
</p>
<p>
  <form method = "POST"> <input type = "submit" name = "confirm" value = "Confirm"> &nbsp; &nbsp;
 <input type = "submit" name = "cancel" value = "Cancel"></form>
</p> <hr>
</body>
</html>
