<?php
session_start();
require_once "pdo.php";
//require_once "util.php"; - why can't I not include this?
//For the autocomplete function using JSON
  $term = $_GET ['term'];
  $sql = "SELECT Dates.Date FROM Dates WHERE Dates.Date LIKE :prefix";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prefix'=> $term."%"));
  $dates = array();
  while($row = $stmt->fetch(PDO::FETCH_ASSOC))//fetch is different from fetchAll
  {
    $dates[] = htmlentities($row['Date']);
  }//Pulls out names of members from database
  echo (json_encode($dates, JSON_PRETTY_PRINT));
