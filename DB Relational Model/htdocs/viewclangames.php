<?php
session_start();
require_once "pdo.php";
require_once "util.php";
//If user doesn't log in or if the user clicks back on the form
if(!(isset($_SESSION['name'])) || isset($_POST['back']))
{
  header("Location: index.php");
  return;
}
//Querying the database for data
$sql = "SELECT Members.IGN, ClanGames.CgID, ClanGames.Points, Dates.Date, Dates.DateID
FROM Members JOIN ClanGames JOIN Dates
ON Members.MemberID = ClanGames.MemberID
and ClanGames.DateID = Dates.DateID
ORDER BY Dates.DateID DESC, ClanGames.Points DESC"; //Data in this case is ordered primarily by date, then by points
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
<h1> Clan Games Stats </h1>
<?php errorMsgs() ?>
<p> <strong> Actions: </strong> <br>
  <a href = "addclangames.php">Add Clan Game Entries </a> </p>
<?php
echo ("<table class = 'starter_template'>
<tr><th>Member</th><th>Clan Game Points</th><th> Season </th><th> Actions </th> </tr> ");
foreach ($rows as $entry)
{
  $name = htmlentities($entry['IGN']);
  $po = htmlentities($entry['Points']);
  $date = htmlentities($entry['Date']);
  $CgID = htmlentities($entry['CgID']);
  echo ("
  <tr><td>".$name."</td><td>".$po."</td><td>".$date."</td>
  <td><a href = 'delclangames.php?CgID=".$CgID."'>Delete Entry</a></td></tr>
  ");
}
echo"</table>";
?>
<br>
<p> <strong> Other Actions: </strong></p>
<p> <a href = 'index.php'> Back To Main Page </a> &nbsp;| &nbsp;
<a href = 'viewdonations.php'> View Donations Stats </a>&nbsp;| &nbsp;
<a href = 'viewclanwars.php'> View Clan Wars Stats </a></p>
<hr>
</body></html>
