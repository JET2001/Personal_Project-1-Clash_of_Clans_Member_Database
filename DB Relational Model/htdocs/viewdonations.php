<?php
session_start();
require_once "pdo.php";
require_once "util.php";
//If user doesn't log in or if the user clicks back on the form
if(!isset($_SESSION['name']))
{
  die(" The proverb 'Knock & The Door Will Open, Seek and You Shall Find, Ask And It Will Be Given To You' doesn't work in the realm of the web browser. Please Log in.");
}
//Querying the database for data
$sql = "SELECT Members.IGN, Donations.DonationID, Donations.Donated, Donations.Received, Donations.Net, Dates.Date, Dates.DateID
FROM Members JOIN Donations JOIN Dates
ON Members.MemberID = Donations.MemberID
and Donations.DateID = Dates.DateID"; //Data here is not ordered.
$stmt = $pdo->prepare($sql);
$stmt->execute();
//Sorting functions for data
function descdon($a, $b)
{
  if ($a['DateID'] == $b['DateID'])
  {
    if ($a['Donated'] == $b['Donated']) {return 0;}
    return ($a['Donated'] < $b ['Donated'])? 1 : -1;//DESC order of donations
  }
  return ($a['DateID'] < $b ['DateID'])? 1 : -1; //DESC order of month
}
function descrec($a, $b)
{
  if($a['DateID'] == $b['DateID'])
  {
    if ($a['Received'] == $b['Received']) {return 0;}
    return ($a['Received'] < $b ['Received'])? 1 : -1;
  }
  return ($a['DateID'] < $b['DateID'])? 1: -1;
}
function descnet($a, $b)
{
  if($a['DateID'] == $b['DateID'])
  {
    if ($a['Net'] == $b['Net']) {return 0;}
    return ($a['Net'] < $b ['Net'])? 1 : -1;
  }
  return ($a['DateID'] < $b['DateID'])? 1: -1;
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
<h1> Donation Stats </h1>
<?php errorMsgs() ?>
<strong> Actions: </strong> <br>
  <a href = "adddonations.php">Add donation entries here </a> </p>
<p><strong> Order results by: </strong></p>
<form method = "POST">
  <input type = "submit" name = "Donated" value = "Troops Donated"></input>
  <input type = "submit" name = "Received" value = "Troops Received"></input>
  <input type = "submit" name = "Net" value = "Net Donations"></input>
</form>
<?php

if (isset($_POST['Net']) || isset($_POST['Donated']) || isset($_POST['Received']))
{
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if (isset($_POST['Net']))
  {
    usort($rows, "descnet");// TO DO: FIND WHATS WRONG WITH THIS FUNCTION
  }
  else if (isset($_POST['Donated']))
  {
    usort($rows, "descdon");
  }
  else
  {
    usort($rows, "descrec");
  }
  echo ("<table class = 'starter_template'>
  <tr><th>Member</th><th>Troops Donated</th><th> Troops Received </th>
  <th> Net Donations </th><th> Season </th> <th> Actions </th> </tr> ");
  foreach ($rows as $entry)
  {
    $name = htmlentities($entry['IGN']);
    $do = htmlentities($entry['Donated']);
    $re = htmlentities($entry['Received']);
    $ne = htmlentities($entry['Net']);
    $date = htmlentities($entry['Date']);
    $doID = htmlentities($entry['DonationID']);
    echo ("
    <tr><td>".$name."</td><td>".$do."</td><td>".$re."</td><td>".$ne."</td>
    <td>".$date."</td><td><a href = 'deldonations.php?DonationID=".$doID."'>Delete Entry</a></td></tr>
    ");
  }
  echo"</table>";
}
?>
<br>
<p> <strong> Other Actions: </strong></p>
<p> <a href = 'index.php'> Back To Main Page </a> &nbsp;| &nbsp;
<a href = 'viewclangames.php'> View Clan Games Stats </a>&nbsp;| &nbsp;
<a href = 'viewclanwars.php'> View Clan Wars Stats </a></p>
<hr>
</body></html>
