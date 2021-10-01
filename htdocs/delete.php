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

//If there is no parameter for memberID
if (!isset($_GET['MemberID']))
{
  $_SESSION['error']= "No member selected";
  header("Location: index.php");
  return;
}


//Check if member is in database and qualifies for promotion.
$stmt = $pdo->query("SELECT Members.IGN FROM Members
  WHERE Members.MemberID = ".$_GET['MemberID']."");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($rows === false)
{
  $_SESSION['error'] = "Member not found";
  header("Location: index.php");
  return;
}
foreach($rows as $member)
{
    //If user confirms promotion,
    if (isset($_POST['confirm']))
    {
      $sql = "DELETE FROM Members WHERE Members.MemberID = ".$_GET['MemberID']."";
      $stmt = $pdo->query($sql);//$stmt doesn't blow up, its just there to catch the output (ie. 'false')
      $_SESSION['success'] = "You've passed the point of no return.";
      header("Location: index.php");
      return;
    }
    //If user clicks cancel on form
    if (isset ($_POST['cancel']))
    {
      header ("Location: index.php");
      return;
    }
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
<p> <strong>Confirm <strong style="color:red;"> deletion </strong> of </strong> </strong>
  <?php
  foreach($rows as $member)
  {
      $name = htmlentities($member['IGN']);
  }
  echo ("$name"."?<br>");
   ?>
</p>
<p>
  <form method = "POST"> <input type = "submit" name = "confirm" value = "Confirm"> &nbsp; &nbsp;
 <input type = "submit" name = "cancel" value = "Cancel"></form>
</p> <hr>
</body>
</html>
