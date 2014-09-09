<head>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/login.css" rel="stylesheet">
    <link href="/css/nav.css" rel="stylesheet">
    <link href="navbar-fixed-top.css" rel="stylesheet">

    <script src="/assets/js/ie-emulation-modes-warning.js"></script>
    <script src="/assets/js/ie10-viewport-bug-workaround.js"></script>

    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="/">Sauron</a>
        </div>
        <div class="navbar-collapse collapse">

        </div>
      </div>
    </div>
</head>
<?php
session_start();  

$username = $_POST['username'];
$password = $_POST['password'];// yes plaintext at the moment will fix this ASAP but encrypted in DB :) 

include('lib/config.php');
$username = mysql_real_escape_string($username); 
$query = "SELECT salt FROM users WHERE username= '$username' LIMIT 1"; 
$result = mysql_query($query) or die(mysql_error());

while($row = mysql_fetch_array($result)){
  $salt = $row['salt']; 
}


$app_key = "DemoSaltGoesHere"; // application base salt this will be changed to be dynamic per instance ASAP. 


// MOAR SALT 
$salt .= $password; // The password is now: oijahsfdapsf80efdjnsdjp_PLUS_THE_USERS_PASSWORD
$app_key .=$salt; // The password is now: KAUsgh8723bha782IkasuaKJAGH_oijahsfdapsf80efdjnsdjp_PLUS_THE_USERS_PASSWORD
$password = $app_key; // Change the password var to contain our new salted pass.

// MD5 All the things
$password = md5($password);

?>

<?php

include('lib/config.php');
$query = "SELECT * FROM users WHERE username= '$username' and password = '$password' LIMIT 1"; 

$username = mysql_real_escape_string($username);

$result = mysql_query($query) or die(mysql_error());

while($row = mysql_fetch_array($result)){
  $resusername = $row['username']; 
  $respassword = $row['password']; 
  $resemail = $row['email']; 
  $rerole = $row['role'];

}


if ($respassword == $password) {
  $_SESSION['loggedin'] = "1";
  $_SESSION['email'] = $resemail;
  $_SESSION['username'] = $resusername;
  $_SESSION['timeout'] = time();
  $_SESSION['role'] = $rerole;
}else{
// NO SOUP FOR YOU COME BACK ONE YEAR
  $_SESSION['loggedin'] = "0";
  
}

?>
<?php

if ($_SESSION['timeout'] + 10 * 60 < time()) {
  } else {

  }
$loggedin = $_SESSION['loggedin'];
$role = $_SESSION['role'];
if ($loggedin != "1") {
  print "<body>";
  print "</br>";
  print "</br>";
  print "</br>";
  print "<div class=\"container\">";
  print "<form class=\"form-signin\" role=\"form\" id=\"Login\" name=\"login\" method=\"post\" action=\"login.php\">";
  print "<center><h1 class=\"form-signin-heading\">Login</h1></center>";
  print "<input type=\"text\" class=\"form-control\" placeholder=\"Username\" name=\"username\" id=\"username\" required autofocus>";
  print "<input type=\"password\" class=\"form-control\" placeholder=\"Password\" name=\"password\" id=\"password\" required>";
  print "<button class=\"btn btn-lg btn-primary btn-block\" type=\"submit\" value=\"Login\" >Sign in</button>";
  print "</form>";
  print "</div> ";
  print "</body>";
}
if ($loggedin != "0") {
header("Location: /index.php");
die();
}

?>

<?php include('lib/footer.php'); ?>



