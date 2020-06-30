<?php
use SharedShopping\Util, SharedShopping\AuthenticationManager;
$user = AuthenticationManager::getAuthenticatedUser();

if (isset($_GET['errors'])) {
    $errors = unserialize(urldecode($_GET['errors']));
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Shared Shopping</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="assets/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="assets/main.css" rel="stylesheet">

</head>
<body>


<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Shared Shopping</a>
        </div>


        <div class="navbar-collapse collapse" id="bs-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li  <?php if ($view === 'welcome') { ?>class="active"<?php } ?>><a href="index.php">Home</a></li>
                <li <?php if ($view === 'dashboard') { ?>class="active"<?php } ?>><a href="index.php?view=dashboard">Dashboard</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right login">
                <li class="dropdown">
                    <?php  if ($user == null): ?>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            Not logged in!
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="index.php?view=login">Login now</a>
                            </li>
                        </ul>
                    <?php else: ?>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                  Logged in as  <span class="badge"><?php echo Util::escape($user->getUserName()); ?></span>
                  <b class="caret"></b>
                </a>
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li class="centered">
                    <form method="post" action="<?php  echo Util::action(SharedShopping\Controller::ACTION_LOGOUT); ?>">
                      <input class="btn btn-xs" role="button" type="submit" value="Logout" />
                    </form>
                    </li>
                  </ul>
              <?php endif;  ?>
                </li>
            </ul> <!-- /. login -->
        </div><!--/.nav-collapse -->
    </div>
</div>

<div class="container">

