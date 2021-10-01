<?php
session_start();
require_once "pdo.php";
require_once "util.php";

//If user tries to enter without logging in.
if (!isset($_SESSION['name']))
{
  header ("Location: index.php");
  return;
}
//If user clicks back or cancel
if (isset($_POST['cancel']))
{
  header("Location: viewdonations.php");
  return;
}
//Validate date entry
if (isset($_POST['submitdate']))
{
  $stmt= $pdo->prepare("SELECT Dates.DateID FROM Dates WHERE Dates.Date = :date");
  $stmt->execute(array(':date'=> $_POST['date']));
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if (count($rows) == 0)
  {
    $_SESSION['error'] = "Invalid Season Entered.";
    header("Location: adddonations.php");
    return;
  }
  else
  {
    $dateID = $rows[0]['DateID'];
    $_SESSION['success'] = "You are recording entries for ".$_POST['date']."";
    header("Location: adddonations.php?DateID=".$dateID."");
    return;
  }
}

//Insert all clan game entries
if(isset($_POST['submit']))
{
  $count = 0;
  for($i = 1; $i <= 50; $i++)
  {
    if (!isset($_POST['Mem_IGN'.$i])) continue;
    if (!isset($_POST['Donated'.$i])) continue;
    if (!isset($_POST['Received'.$i])) continue;
    //If both ign and points are filled in,
    $name = $_POST['Mem_IGN'.$i];
    $do = $_POST['Donated'.$i];
    $re = $_POST['Received'.$i];
    $ne = $do - $re;
    $mth = $_GET['DateID'];
    //Look up member if it is there
    //First use the server to validate database input
    $stmt = $pdo->prepare("SELECT Members.MemberID FROM Members WHERE Members.IGN = :name");
    $stmt->execute(array(':name' => $name));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($rows)==0)
    {
      $_SESSION['error'] = "Member not found in Database.";
      header("Location: adddonations.php?DateID=".$mth."");
      return;
    }
    else
    {
      $count = $count+1;
      $MemID = $rows[0]['MemberID'];
      $stmt = $pdo->prepare("INSERT INTO Donations (MemberID, Donated, Received, Net, DateID) VALUES
      (:MemID, :do, :re, :ne, :DateID)");
      $stmt->execute(array(':MemID'=>$MemID, ':do'=>$do, ':re'=>$re, ':ne'=>$ne, ':DateID'=>$mth));
    }
  }//endfor
  $_SESSION['success'] = "".$count." new entry/entries added.";
  header("Location: viewdonations.php");
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
  <h1> Add Donation Entries </h1>
  <?php errorMsgs() ?>
  <form method = "POST">
    <label for = "date"> Enter a season in the format Mth/Year (eg. 'Jan/2021'): &nbsp; </label>
    <input type = "text" name = "date" value = ""></input>
    <input type = "submit" name = "submitdate" value = "Submit"></input></form>
  <?php
  if (isset($_GET['DateID']))
  {//After user sets date, then load the donation form.
    echo('
    <form method = "POST">
    <label for = "add_entry" name = "adddo"> Click the "+" to add an entry: </label>
    <input type = "submit" name = "adddo" id = "adddo" value = "+"></input>
    <p><em> Avoid wrong entries by using the autocomplete function when keying in the member name.</em></p>
    <div id = "do_fields"></div>
    <input type = "submit" name = "submit" value = "Submit"></input>
    </form>
    ');
  }
  ?>
  <br>
  <form method = "POST">
    <input type = "submit" name = "cancel" value = "Cancel"></input></form>
    <br><hr>
  <script>
  //Initialising global variables
  countMems = 0;
  //When the document is ready,
  <script>
  //Initialising global variables
  countMems = 0;
  //When the document is ready,
  $(document).ready(function()
  {
    window.console && console.log("Document ready called!");
    //Function use JSON to validate date
    $('#date_field').change(function(event){
      $('#date_field').autocomplete({
        source: "date.php"
      });
    });
    //Function addclangames
    $('#adddo').click(function(event)
    {
    event.preventDefault();
    if (countMems >= 50)
    {
      alert("Maximum of 50 entries exceeded.");
      return;
    }
    countMems++;
    window.console && console.log("Adding Member Entry"+countMems+"");
    //+ above represent concatenation
    //Grab some HTML with hotspots and insert it into the DOM.
    var source = $("#do_template").html(); //local variable source
    $("#do_fields").append(source.replace(/@COUNT@/g,countMems));
    //Autocomplete for member field
    $(".ign").autocomplete({
        source: "member.php", // echoed json_encode so the data is in member.php
        minLength: 1
      });//end autocomplete
  }//end function onclick
  )//end event onclick
});//end ready
</script>
<script id = "do_template" type = "text">
  <div id = "Mem_Entry@COUNT@">
  <p> Member IGN: <input type = "text" name = "Mem_IGN@COUNT@"
  value = "" class = "ign" required /> &nbsp; &nbsp;
   Donated: <input type = "number" name = "Donated@COUNT@" required min = "0"> &nbsp; &nbsp;
   Received: <input type = "number" name = "Received@COUNT@" required min = "0"> &nbsp; &nbsp;
   <input type = "button" value = "-"
   onclick = $("#Mem_Entry@COUNT@").remove(); return false;> </p>
</div>
</script>
</body></html>
