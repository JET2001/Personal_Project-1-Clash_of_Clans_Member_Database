<?php
session_start();
require_once "pdo.php";
require_once "util.php";
$stmt = $pdo->query("SELECT Members.IGN, Positions.Position,
  Members.Thlvl, Members.TeleName, Members.MemberID
  FROM Members JOIN Positions
  ON Members.PositionID = Positions.PositionID
  ORDER BY Members.PositionID ASC, Members.Thlvl DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_POST['logout']))
{
  header ('Location: logout.php');
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
  <h1> Welcome to the COC Ascension Database </h1>
  <?php
  errorMsgs();
  if (!isset($_SESSION['name']))
  {
    echo ("
    <p> <a href = 'login.php'> Log in to make changes. </a> </p>
    ");
  }
  else
  {
    echo ("<p><a href = 'logout.php'> Click here to logout </a></p> ");
  }
  if (isset ($_SESSION['name']))
  {
    echo (" <h3> Hello ".$_SESSION['name'].", </h3>
    <strong> What is your main focus for today? </strong> <br>");

    //TO DO: Change all these urls to actual urls
    echo ("<p> <a href = 'addmember.php'> Add a new Member </a> </p>");
    echo ("<p> <a href = 'viewdonations.php'> View Donations Stats </a></p>");
    echo ("<p> <a href = 'viewclangames.php'> View Clan Games Stats </a></p>");
    echo ("<p> <a href = 'viewclanwars.php'> View Clan Wars Stats </a></p>");

    //TO DO: Create table
    if (count($rows) == 0){echo("No members found.\n");}
    else{
      $count = 1;
      echo (" <table class = 'starter-template'>
      <tr> <th> No. </th> <th> IGN </th><th> Position </th><th> Town Hall Level </th>
      <th> Telegram Username </th><th> Other Actions</th></tr>");
      foreach($rows as $member)
      {

        $name = htmlentities($member['IGN']);
        $pos = htmlentities($member['Position']);
        $th = htmlentities($member['Thlvl']);
        $tele = htmlentities($member['TeleName']);
        $memID = htmlentities($member['MemberID']);
        echo ("
        <tr> <td>".$count."</td><td><a href = 'about.php?MemberID=".$memID."'>".$name."</a></td>
        <td>".$pos."</td>
        <td>".$th."</td><td>".$tele."</td><td>
        <a href = 'promote.php?MemberID=".$memID."'>Promote</a> &nbsp;| &nbsp;
        <a href = 'demote.php?MemberID=".$memID."'>Demote </a> &nbsp; | &nbsp;
        <a href = 'editmember.php?MemberID=".$memID."'>Edit</a> &nbsp; | &nbsp;
        <a href = 'delete.php?MemberID=".$memID."'>Delete</a></td></tr>
        ");
        $count++;
      }
      echo("</table>");
      echo("</p><hr>");
    }
  }
  ?>
</body>
</html>
