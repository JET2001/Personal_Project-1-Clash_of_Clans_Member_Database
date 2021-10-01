<?php
session_start();
require_once "pdo.php";
require_once "util.php";
//If user does not log in:
if(!isset($_SESSION['name'])|| isset($_POST['cancel']))
{
  header("Location: index.php");
  return;
}

//When user submits form, proceed to validate input:
if (isset($_POST['save']))
{
  //Do client side validation for the input
  //Input passes data validation. We can execute the SQL.
  //Prepare the insert statement
  $sql = "UPDATE Members SET IGN = :ign, Thlvl = :th, TeleName = :te, Bio = :bi,
  Strategies = :st
  WHERE Members.MemberID = :memID";
  $stmt = $pdo->prepare($sql);
    //Execute the statement by inserting it into database
  $stmt->execute(array(
    ':ign'=> $_POST['IGN'],
    ':th'=>$_POST['th'],
    ':te'=>$_POST['te'],
    ':bi'=>$_POST['bi'],
    ':st'=>$_POST['st'],
    ':memID'=>$_GET['MemberID']
  ));
    $_SESSION['success'] = "Member Updated";
    header("Location: index.php");
    return;
}
//Load the profile via query from the Database
$stmt = $pdo->prepare("SELECT Members.IGN, Members.Thlvl, Members.TeleName, Members.Bio, Members.Strategies
FROM Members WHERE Members.MemberID = :memID");
$stmt->execute(array(':memID'=> $_GET['MemberID']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$ign = htmlentities($rows[0]['IGN']);
$th = htmlentities($rows[0]['Thlvl']);
$te = htmlentities($rows[0]['TeleName']);
$bi = htmlentities($rows[0]['Bio']);
$st = htmlentities($rows[0]['Strategies']);
//If user clicks cancel, redirect to index.php
?>
<!DOCTYPE html>
<html lang = 'en'>
<head>
  <meta charset = "utf-8">
  <title> COC Ascension Database </title>
  <?php require_once "bootstrap.php" ?>
</head>
<body class = "starter-template">
  <h1> Edit Member Profile
  </h1>
  <form method = "POST">
    <p> <label for = "IGN"> IGN:&nbsp; </label><input type = "text" name = "IGN" value = "<?= $ign ?>" required></p>
    <p><label for = "th"> Town Hall Level: &nbsp;</label><input id = "th" type = "number" name = "th" onchange = "validateTH()" value = "<?= $th ?>" required></p>
    <p id= "errorMsg_th" style = "display: none;"></p>
    <p><label for = "te"> Telegram Username: &nbsp;</label><input id = "tele" type = "text" name = "te" onchange = "validateTele()" value = "<?= $te ?>"></p>
    <p id= "errorMsg_tele" style = "display: none;"></p>
    <p><label for = "bi"> Player Background:</label><br>
    <p><textarea name = "bi" rows = "4" cols = "50"
    placeholder = "Enter what you know about a player's personal life here"><?= $bi ?></textarea></p>
    <p><label for = "st"> Strategies:</label> <br>
    <textarea name = "st" rows = "4" cols = "50"
    placeholder = "Enter the strategies that a player is proficient in here"><?= $st ?></textarea></p>
    <p><input id = "submitMember" type = "submit" name = "save" value = "Save"> &nbsp; &nbsp;
      <a href = "index.php">Cancel </a> </p>
  </form>
  <hr>
</body></html>
