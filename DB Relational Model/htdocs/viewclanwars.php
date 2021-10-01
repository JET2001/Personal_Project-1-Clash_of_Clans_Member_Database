<?php
session_start();
require_once "pdo.php";
require_once "util.php";
//If user doesn't log in or if the user clicks back on the form
if(!(isset($_SESSION['name'])))
{
  header("Location: index.php");
  return;
}
//Querying the database for data
$sql = "SELECT Members.IGN, ClanWars.CwID, ClanWars.Count, Dates.Date, Dates.DateID
FROM Members JOIN ClanWars JOIN Dates
ON Members.MemberID = ClanWars.MemberID
and ClanWars.DateID = Dates.DateID
ORDER BY Dates.DateID DESC, ClanWars.Count DESC"; //Data in this case is ordered primarily by date, then by points
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang = 'en'>
<head>
  <meta charset = "utf-8">
  <title> COC Ascension Database </title>
  <?php require_once "bootstrap.php" ?>
</head>
<body class = "starter-template">
<h1> Clan War Stats </h1>
<?php errorMsgs() ?>
<p> <strong> Actions: </strong> <br>
  <a href = "addclanwars.php">Add Clan War Entries </a> &nbsp; &nbsp;
<a href = "addmultiplecw.php">Add Entries For War League </a> </p>
<?php
echo ("<table class = 'starter_template'>
<tr><th>Member</th><th>Clan War Count</th><th> Season </th><th> Actions </th> </tr> ");
foreach ($rows as $entry)
{
  $name = htmlentities($entry['IGN']);
  $co = htmlentities($entry['Count']);
  $date = htmlentities($entry['Date']);
  $CwID = htmlentities($entry['CwID']);
  echo ("
  <tr><td>".$name."</td><td>".$co."</td><td>".$date."</td>
  <td><a href = 'delclanwars.php?CwID=".$CwID."'>Delete Entry</a></td></tr>
  ");
}
echo"</table>";
?>
<br>
<p> <strong> Other Actions: </strong></p>
<p> <a href = 'index.php'> Back To Main Page </a> &nbsp;| &nbsp;
<a href = 'viewdonations.php'> View Donations Stats </a>&nbsp;| &nbsp;
<a href = 'viewclangames.php'> View Clan Games Stats </a></p>
<hr>
</body></html>
