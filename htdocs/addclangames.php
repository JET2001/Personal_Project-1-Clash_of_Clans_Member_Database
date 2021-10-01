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
  header("Location: viewclangames.php");
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
    header("Location: addclangames.php");
    return;
  }
  else
  {
    $dateID = $rows[0]['DateID'];
    $_SESSION['success'] = "You are recording entries for ".$_POST['date']."";
    header("Location: addclangames.php?DateID=".$dateID."");
    return;
  }
}

//Insert all clan game entries
if(isset($_POST['submit']))
{
  $wrong_entry = array();
  $count = 0;
  for($i = 1; $i <= 50; $i++)
  {
    if (!isset($_POST['Mem_IGN'.$i])) continue;
    if (!isset($_POST['Points'.$i])) continue;
    //If both ign and points are filled in,
    $name = $_POST['Mem_IGN'.$i];
    $po = $_POST['Points'.$i];
    $mth = $_GET['DateID'];
    //Look up member if it is there
    //First use the server to validate database input
    $stmt = $pdo->prepare("SELECT Members.MemberID FROM Members WHERE Members.IGN = :name");
    $stmt->execute(array(':name' => $name));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($rows)==0)
    {
      array_push($wrong_entry, $name);
    }
    else
    {
      $count = $count+1;
      $MemID = $rows[0]['MemberID'];
      $stmt = $pdo->prepare("INSERT INTO ClanGames (MemberID, Points, DateID) VALUES
      (:MemID, :points, :DateID)");
      $stmt->execute(array(':MemID'=>$MemID, ':points'=>$po, ':DateID'=>$mth));
    }
  }//endfor
  $_SESSION['success'] = "".$count." new entry/entries added.";
  if (count($wrong_entry) != 0)
  {

    $_SESSION['error'] = count($wrong_entry)." entry /
    entries rejected: ";
    foreach($wrong_entry as $entry)
    {
      $_SESSION['error'].=$entry.", ";
    }
    $_SESSION['error'].=" have to be re-entered.";
  }
  header("Location: viewclangames.php");
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
  <h1> Add Clan Game Entries </h1>
  <?php errorMsgs() ?>
  <form method = "POST">
    <label for = "date"> Enter a season in the format Mth/Year (eg. 'Jan/2021'): &nbsp; </label>
    <input type = "text" id = "date_field" name = "date" value = ""></input>
    <input type = "submit" name = "submitdate" value = "Submit"></input></form>
  <?php
  if (isset($_GET['DateID']))
  {//After user sets date, then load the donation form.
    echo('
    <form method = "POST">
    <label for = "add_entry" name = "addcg"> Click the "+" to add an entry: </label>
    <input type = "submit" name = "addcg" id = "addcg" value = "+"></input><br>
    <p><em> Avoid wrong entries by using the autocomplete function when keying in the member name.</em></p>
    <div id = "cg_fields"></div>
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
  window.console && console.log("Document ready called!");
  //When the document is ready,
  //autocomplete for date_field
  $(document).ready(function()
  {
    $('#date_field').change(function(event){
      $('#date_field').autocomplete({
        source: "date.php"
      });
    });
    //Create a Select menu for the Season


    //Function to add clan games
    $('#addcg').click(function(event)
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
    var source = $("#cg_template").html(); //local variable source
    $("#cg_fields").append(source.replace(/@COUNT@/g,countMems));
    //Autocomplete for member field
    $(".ign").autocomplete({
        source: "member.php", // echoed json_encode so the data is in member.php
        minLength: 1
      });//end autocomplete
  }//end function onclick
);//end event click
});//end ready
</script>
<!--HTML with hotspots -->
<script id = "cg_template" type = "text">
<div id = "Mem_Entry@COUNT@">
  <p> Member IGN: <input type = "text" id = "Mem_IGN@COUNT@" name = "Mem_IGN@COUNT@"
  class = "ign" value = "" required /> &nbsp; &nbsp;
  Clan Game Points: <input type = "number" name = "Points@COUNT@" required max = "4000" min = "0"/>
  &nbsp; <input type = "button" value = "-"
  onclick = $("Mem_Entry@COUNT@").remove(); return false;></p>
</script>
</body></html>
