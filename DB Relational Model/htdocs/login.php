<?php
session_start();
require_once "pdo.php";
require_once "util.php";
//If user has already logged in.
if (isset($_SESSION['name']))
{
  $_SESSION['success'] = "Already logged in.";
  header("Location: index.php");
  return;
}
//If user clicks Cancel
if(isset ($_POST['Cancel']))
{
  header("Location: index.php");
  return;
}
//When user has clicked submit
if(isset($_POST['Submit']))
{
  //If either field is empty
  if (strlen($_POST['username']) <1 || strlen($_POST['password']) < 1)
  {
    $_SESSION['error'] = "Both username and password are required.";
    header("Location: login.php");
    return;
  }
  else
  {
    //Hashing the password
    $hash = md5($_POST['password']);
    $sql = "SELECT Leaders.password FROM Leaders
    WHERE Leaders.username = '".$_POST['username']."'";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    if($rows === false)
    {
      $_SESSION['error'] = "Wrong Username entered.";
      header("Location: login.php");
      return;
    }
    else if($rows['password'] !== $hash)
    {
      $_SESSION['error'] = "Wrong password entered.";
      header("Location: login.php");
      return;
    }
    else
    {//Passed validation!
      $_SESSION['success'] = "Login successful.";
      $_SESSION['name'] = $_POST['username'];
      header("Location: index.php");
      return;
    }
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
<body class = 'starter-template'>
  <h1> Please log in </h1>
  <?php
    errorMsgs();
  ?>
  <form method = "POST">
  <label for = "Username"> Username: </label>
  <input type = "text" name = "username"> <br>
  <label for = "Password"> Password: </label>
  <input type = "text" name = "password"><br>
  <input type = "submit" name = "Submit" value = "Submit">
  <input type = "submit" name = "Cancel" value = "Cancel">
</form>

</body>
</html>
