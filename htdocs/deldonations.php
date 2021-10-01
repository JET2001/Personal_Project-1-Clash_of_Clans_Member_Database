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
if (!isset($_GET['DonationID']))
{
  $_SESSION['error']= "No entry selected";
  header("Location: viewdonations.php");
  return;
}


//Check if donationID is in database
$stmt = $pdo->prepare("SELECT Members.IGN, Donations.Donated, Donations.Received,
  Donations.Net, Dates.Date, Dates.DateID
FROM Members JOIN Donations JOIN Dates
ON Members.MemberID = Donations.MemberID
and Donations.DateID = Dates.DateID
WHERE Donations.DonationID = :DoID");
$stmt->execute(array(':DoID' => $_GET['DonationID']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
//Check if ID is in the Database
if($rows === false)
{
  $_SESSION['error'] = "Entry not found";
  header("Location: viewdonations.php");
  return;
}
//If user confirms deletion,
if (isset($_POST['confirm']))
  {
    $sql = "DELETE FROM Donations WHERE Donations.DonationID = ".$_GET['DonationID']."";
    $stmt = $pdo->query($sql);//$stmt doesn't blow up, its just there to catch the output (ie. 'false')
    $_SESSION['success'] = "Entry deleted.";
    header("Location: viewdonations.php");
    return;
  }
//If user clicks cancel on form
if (isset ($_POST['cancel']))
  {
    header ("Location: viewdonations.php");
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
      $do = htmlentities($entry['Donated']);
      $re = htmlentities($entry['Received']);
      $net = htmlentities($entry['Net']);
      $date = htmlentities($entry['Date']);
  }
  echo ("Name:".$name."<br> Troops Donated: ".$do." <br>
  Troops Received: ".$re." <br> Net Donations: ".$net."<br> Season: ".$date."
  ");
   ?>
</p>
<p>
  <form method = "POST"> <input type = "submit" name = "confirm" value = "Confirm"> &nbsp; &nbsp;
 <input type = "submit" name = "cancel" value = "Cancel"></form>
</p> <hr>
</body>
</html>
