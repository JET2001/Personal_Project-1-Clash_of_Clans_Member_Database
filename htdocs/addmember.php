<?php
session_start();
require_once "pdo.php";
require_once "util.php";
//If user does not log in:
if(!isset($_SESSION['name']))
{
  header("Location: index.php");
  return;
}
//If user clicks cancel, redirect to index.php
//When user submits form, proceed to validate input:
if (isset($_POST['submit']))
{
  //Do client side validation for the input
  //Input passes data validation. We can execute the SQL.
  //Prepare the insert statement
  $sql = "INSERT INTO Members(IGN, PositionID, Thlvl, TeleName, Bio, Strategies) VALUES (:name, :posID, :th, :te, :bi, :st)";
  $stmt = $pdo->prepare($sql);
    //Execute the statement by inserting it into database
  $stmt->execute(array(
    ':name'=> $_POST['IGN'],
    ':posID'=> '3',
    ':th'=>$_POST['th'],
    ':te'=>$_POST['te'],
    ':bi'=>$_POST['bi'],
    ':st'=>$_POST['st']));
    $_SESSION['success'] = "New member added";
    header("Location: index.php");
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
  <h1> Add A New User </h1>
  <form method = "POST">
    <p> <label for = "IGN"> IGN:&nbsp; </label><input type = "text" name = "IGN" value = "" required></p>
    <p><label for = "th"> Town Hall Level: &nbsp;</label><input id = "th" type = "number" name = "th" onchange = "validateTH()" value = "" required></p>
    <p id= "errorMsg_th" style = "display: none;"></p>
    <p><label for = "te"> Telegram Username: &nbsp;</label><input id = "tele" type = "text" name = "te" onchange = "validateTele()" value = ""></p>
    <p id= "errorMsg_tele" style = "display: none;"></p>
    <p><label for = "bi"> Player Background:</label><br>
    <p><textarea name = "bi" rows = "4" cols = "50"
    placeholder = "Enter what you know about a player's personal life here"></textarea></p>
    <p><label for = "st"> Strategies:</label> <br>
    <textarea name = "st" rows = "4" cols = "50"
    placeholder = "Enter the strategies that a player is proficient in here"></textarea></p>
    <p><input id = "submitMember" type = "submit" name = "submit" value = "Submit"> &nbsp; &nbsp;
      <a href = "index.php">Cancel </a> </p>
  </form>
  <hr>
</body></html>
