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

    $shoppingListId = htmlspecialchars($_GET["shoppingListId"]);
    $shoppingList = DataManager::getShoppingListById($shoppingListId);
    
    if (!$shoppingList) {
        // shopping list not found, redirect to errors page
        SharedShopping\Util::redirect('index.php?view=error&type=invalid_shopping_list');
    }

    // provide all the necessary data
    $listTitle = $shoppingList->getTitle();
    $listDateUntil = $shoppingList->getDateUntil();
    $totalPrice = $shoppingList->getTotalPrice();
    $shoppingListStatus = $shoppingList->getStatus();
    $userType = AuthenticationManager::getAuthenticatedUserType();

    // determine which detail view to load
    if ($userType == UserType::HELPSEEKER && $shoppingList->getCreatorId() == $_SESSION['user']) {
        require('views/partials/helpseeker/detailView.php');
    } elseif ($userType == UserType::VOLUNTEER){
        if (($shoppingListStatus != SharedShopping\ShoppingListStatus::OPEN && $shoppingList->getVolunteerId() == $_SESSION['user']) || $shoppingListStatus == SharedShopping\ShoppingListStatus::OPEN) {
            require('views/partials/volunteer/detailView.php');
        } else {
            SharedShopping\Util::redirect('index.php?view=error&type=invalid_authorization');
        }
    } else {
        // no user logged in, redirect to errors page
        SharedShopping\Util::redirect('index.php?view=error&type=invalid_authorization');
    }
?>

<?php require_once('views/partials/footer.php'); ?>