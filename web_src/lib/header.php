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
          <a class="navbar-brand" href="/"><i class="glyphicon glyphicon-eye-open"></i> Sauron</a>
        </div>
        <div class="navbar-collapse collapse">
   
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="caret"></span><i class="glyphicon glyphicon-user"></i></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Account Settings</a></li>
                <li><a href="/lib/logout.php">Signout</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
<?php
                  session_start();
                  $loggedin = $_SESSION['loggedin']; 
                  $role = $_SESSION['role'];
                  if ($loggedin != "1") {
                    header("Location: /login.php");
                    }

                    ?>

</head>





