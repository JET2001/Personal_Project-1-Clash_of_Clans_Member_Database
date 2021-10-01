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

//Query the database for the specific user
$stmt = $pdo->query("SELECT Members.IGN, Positions.Position, Members.Thlvl, Members.TeleName,
Members.Bio, Members.Strategies
FROM Members JOIN Positions
ON Members.PositionID = Positions.PositionID
WHERE MemberID = ".$_GET['MemberID']."
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($rows === false)
{//User is in the database
  $_SESSION['error'] = "Member not found.";
  header("Location: index.php");
  return;
}
else
{//User is in the database
  foreach($rows as $member)
  {
    $name = htmlentities($member["IGN"]);
    $pos = htmlentities($member["Position"]);
    $th = htmlentities($member["Thlvl"]);
    $tele = htmlentities($member["TeleName"]);
    if (strlen($tele) < 1){$tele = "NIL";}
    $bio = htmlentities($member["Bio"]);
    if (strlen($bio) < 1){$bio = "NIL";}
    $st = htmlentities($member["Strategies"]);
    if (strlen($st) < 1){$st = "NIL";}
  }
}
//Load member donations, member clan war count and member donation count by month
/*$sql = "SELECT Dates.Date, ClanWars.Count, Donations.Donated, Donations.Received, Donations.Net, ClanGames.Points
FROM Dates
JOIN ClanWars ON Dates.DateID = ClanWars.DateID AND ClanWars.MemberID = :memID
JOIN Donations ON ClanWars.DateID = Donations.DateID AND Donations.MemberID = :memID
JOIN ClanGames ON Donations.DateID = ClanGames.DateID AND ClanGames.MemberID = :memID
ORDER BY Dates.DateID ASC";*/
//Query Database for donations
$sqld= "SELECT Donations.Donated, Donations.Received, Donations.Net, Dates.Date
FROM Donations JOIN Dates ON Donations.DateID = Dates.DateID
WHERE Donations.MemberID = :memID ORDER BY Dates.DateID ASC";
$stmtd= $pdo->prepare($sqld);
$stmtd->execute(array(':memID'=>$_GET['MemberID']));
$rowsd = $stmtd->fetchAll(PDO::FETCH_ASSOC);

//Query Database for clan wars
$sqlw= "SELECT ClanWars.Count, Dates.Date
FROM ClanWars JOIN Dates ON ClanWars.DateID = Dates.DateID
WHERE ClanWars.MemberID = :memID ORDER BY Dates.DateID ASC";
$stmtw= $pdo->prepare($sqlw);
$stmtw->execute(array(':memID'=>$_GET['MemberID']));
$rowsw = $stmtw->fetchAll(PDO::FETCH_ASSOC);

//Query Database for clan games
$sqlg= "SELECT ClanGames.Points, Dates.Date
FROM ClanGames JOIN Dates ON ClanGames.DateID = Dates.DateID
WHERE ClanGames.MemberID = :memID ORDER BY Dates.DateID ASC";
$stmtg= $pdo->prepare($sqlg);
$stmtg->execute(array(':memID'=>$_GET['MemberID']));
$rowsg = $stmtg->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang = 'en'>
<head>
  <meta charset = "utf-8">
  <title> COC Ascension Database </title>
  <?php require_once "bootstrap.php" ?>
</head>
<body class = "starter-template">
<h1> Member Information for
  <?php
  echo ($name)
  ?>
</h1>
<?php
  echo ("
  <h4> Position:</h4><p>".$pos."</p>
  <h4> Town Hall:</h4><p>".$th."</p>
  <h4> Telegram Username: </h4><p>".$tele."</p>
  <h4> Player Background: </h4><p>".$bio."</p>
  <h4> Strategies: </h4><p>".$st."</p>
  <h4> Donations: </h4>"
);
//Print Donations
if(count($rowsg)== 0) {echo "<p> No records found </p>";}
else{
  echo("
  <table><tr><th>Season</th><th>Donated</th><th>Received</th><th>Net</th></tr>
  ");
  foreach($rowsd as $entry)
  {
    $date = htmlentities($entry["Date"]);
    $do = htmlentities($entry["Donated"]);
    $re = htmlentities($entry["Received"]);
    $net = htmlentities($entry["Net"]);
    echo("
    <tr><td>".$date."</td><td>".$do."</td><td>".$re."</td><td>".$net."</td></tr>
    ");
  }
  echo ("</table>");
}
echo ("<h4> Clan Wars</h4>");
//Clan War record
if(count($rowsw)== 0) {echo "<p> No records found </p>";}
else{
  echo("
  <table><tr><th>Season</th>
  ");
  foreach($rowsw as $entry)
  {
    $date = htmlentities($entry["Date"]);
    echo("
    <th>".$date."</th>
    ");
  }
  echo("</tr><tr><td>War count </td>");
  foreach($rowsw as $entry)
  {
    $co = htmlentities($entry["Count"]);
    echo("
    <td>".$co."</td>
    ");
  }
  echo ("</tr></table>");
}
echo ("<h4> Clan Games Stats</h4>");
//Clan Games record
if(count($rowsg)== 0) {echo "<p> No records found </p>";}
else{
  echo("
  <table><tr><th>Season</th>
  ");
  foreach($rowsg as $entry)
  {
    $date = htmlentities($entry["Date"]);
    echo("
    <th>".$date."</th>
    ");
  }
  echo("</tr><tr><td style = font-weight: bold;>Clan Games Score </td>");
  foreach($rowsg as $entry)
  {
    $po = htmlentities($entry["Points"]);
    echo("
    <td>".$po."</td>
    ");
  }
  echo ("</tr></table>");
}
?>
<br>
<form method = "POST"> <input type = "submit" name = "back" value = "Back"></form>
<hr>
</body></html>
