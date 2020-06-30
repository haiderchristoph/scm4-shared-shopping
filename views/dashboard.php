
<?php require_once('views/partials/header.php'); ?>
<?php if (isset($_GET["errors"])) : ?>
    <div class="errors alert alert-danger">
      <ul>
        <?php foreach ($errors as $errMsg): ?>
          <li><?php echo(SharedShopping\Util::escape($errMsg)); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
<?php endif; ?>
<?php
    use Data\DataManager;
    use SharedShopping\AuthenticationManager;
    use SharedShopping\UserType;
    $userType = AuthenticationManager::getAuthenticatedUserType();
    // determine which dashboard view to load
    if ($userType == UserType::HELPSEEKER) {
        require('views/partials/helpseeker/dashboardView.php');
    } elseif ($userType == UserType::VOLUNTEER) {
        require('views/partials/volunteer/dashboardView.php');
    } else {
        // no user logged in, redirect to welcome page
        SharedShopping\Util::redirect('index.php?view=welcome');
    }
?>



<?php require_once('views/partials/footer.php'); ?>

