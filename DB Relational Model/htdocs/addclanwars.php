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
  header("Location: viewclanwars.php");
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
    header("Location: addclanwars.php");
    return;
  }
  else
  {
    $dateID = $rows[0]['DateID'];
    $_SESSION['success'] = "You are recording entries for ".$_POST['date']."";
    header("Location: addclanwars.php?DateID=".$dateID."");
    return;
  }
}

//Insert all clan game entries
if(isset($_POST['submit']))
{
  $wrong_entry = array();
  $count = 0;
  for($i = 1; $i <= 20; $i++)
  {
    if (!isset($_POST['Mem_IGN'.$i])) continue;
    //If both ign is filled in,
    $name = $_POST['Mem_IGN'.$i];
    $mth = $_GET['DateID'];

    //Retrieve MemberID
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
      $MemID = htmlentities($rows[0]['MemberID']);
      //See if member has warred within the month.
      $sql = "SELECT ClanWars.Count FROM ClanWars WHERE
      ClanWars.MemberID = :MemID AND ClanWars.DateID = :DateID";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':MemID'=>$MemID, ':DateID'=>$mth));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if(count($rows) == 0)
      {//player has not warred within the month, insert new entry
        $stmt = $pdo->prepare("INSERT INTO ClanWars (MemberID, DateID, Count)
        VALUES (:MemID, :DateID, '1')");
        $stmt->execute(array(':MemID'=>$MemID, ':DateID'=>$mth));
      }
      else
      {//Player has warred within the month, update existing war count.
        $stmt = $pdo->prepare("UPDATE ClanWars SET Count = Count + 1 WHERE
        ClanWars.MemberID = :MemID AND ClanWars.DateID = :DateID");
        $stmt->execute(array(':MemID'=>$MemID, ':DateID'=>$mth));
      }
    }
  }//endfor
  $_SESSION['success'] = "".$count." new entry/entries added.";
  if (count($wrong_entry) != 0)
  {
    $_SESSION['error'] ="\n".count($wrong_entry)." entry /
    entries rejected: ";
    foreach($wrong_entry as $entry)
    {
      $_SESSION['error'].=$entry.", ";
    }
    $_SESSION['error'].=" have to be re-entered.";
  }
  header("Location: viewclanwars.php");
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
  <h1> Add Clan War Entries </h1>
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
    <label for = "add_entry" name = "adddo"> Click the "+" to add a member who has participated in a war: </label>
    <input type = "submit" name = "addcw" id = "addcw" value = "+"></input>
    <p><em> Avoid wrong entries by using the autocomplete function when keying in the member name.</em></p>
    <div id = "cw_fields"></div>
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
    $('#addcw').click(function(event)
    {
    event.preventDefault();
    if (countMems >= 20)
    {
      alert("Maximum of 20 entries exceeded.");
      return;
    }
    countMems++;
    window.console && console.log("Adding Member Entry"+countMems+"");
    //+ above represent concatenation
    //Grab some HTML with hotspots and insert it into the DOM.
    var source = $("#cw_template").html(); //local variable source
    $("#cw_fields").append(source.replace(/@COUNT@/g,countMems));
    //Autocomplete for member field
    $(".ign").autocomplete({
        source: "member.php", // echoed json_encode so the data is in member.php
        minLength: 1
      });//end autocomplete
  }//end function onclick
  )//end event onclick
});//end ready
</script>
<script id = "cw_template" type = "text">
  <div id = "Mem_Entry@COUNT@">
    <p> Member IGN: <input type = "text" name = "Mem_IGN@COUNT@" class = "ign"
    value = "" required /> &nbsp; &nbsp;
     <input type = "button" value = "-"
     onclick = $("#Mem_Entry@COUNT@").remove(); return false;> </p>
  </div>
</script>
</body></html>
