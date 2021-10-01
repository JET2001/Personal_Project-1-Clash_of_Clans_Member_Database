<?php
require_once "pdo.php";
function errorMsgs()
{
  if (isset($_SESSION['success']))
  {
    echo("<p style = 'color:green;'>".htmlentities($_SESSION['success'])."</p>");
    unset($_SESSION['success']);
  }
  if (isset($_SESSION['error']))
  {
    echo('<p style = "color: red;">'.htmlentities($_SESSION['error']).'</p>');
    unset($_SESSION['error']);
  }
}
?>
<!----------------------------------------------------------------->
<script>
//Used for Add member validation
function validateTH()
{
  window.console && console.log("Validating th!");
  var th = document.getElementById("th");
  var error = document.getElementById("errorMsg_th");
  var submit = document.getElementById("submitMember");
  window.console && console.log(th.value);
  if (th.value < 1 || th.value > 13)
  {
    window.console && console.log("Buggy input!");
    error.innerHTML = "Town Hall level should be between 1 and 13.";
    error.style.color = "Red";
    error.style.display = "block";
    submit.disabled = true;
  }
  else
  {
    if(submit.disabled == true)
    {
      window.console && console.log("No longer buggy input!");
      error.innerHTML = "";
      error.style.display = "none";
      submit.disabled = false;
    }
    else{
      window.console && console.log("Ok input!");
    }
  }
}
function validateTele()
{
  var str = document.getElementById("tele").value;
  var error = document.getElementById("errorMsg_tele");
  var submit = document.getElementById("submitMember");
  if (str.length > 0 && str.startsWith("@")==false)
  {
    error.style.display = "block";
    error.style.color = "red";
    error.innerHTML = "Username must begin with '@'.";
    submit.disabled = true;
  }
  else
  {
    if (submit.disabled == true)
    {
      error.style.display = "none";
      error.innerHTML = "";
      submit.disabled = false;
    }

  }
}
</script>
