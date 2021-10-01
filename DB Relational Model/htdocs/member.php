<?php
session_start();
require_once "pdo.php";
//require_once "util.php"; - why can't I not include this?
//For the autocomplete function using JSON
  $term = $_GET ['term'];
  $sql = "SELECT Members.IGN FROM Members WHERE Members.IGN LIKE :prefix";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prefix'=> $term."%"));
  $members = array();
  while($row = $stmt->fetch(PDO::FETCH_ASSOC))//fetch is different from fetchAll
  {
    $members[] = htmlentities($row['IGN']);
  }//Pulls out names of members from database
  echo (json_encode($members, JSON_PRETTY_PRINT));
