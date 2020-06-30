<?php
    use Data\DataManager;
    use SharedShopping\Util;
    use SharedShopping\ShoppingListStatus;
    
    // get all lists by volunteer and filter them by status
    $allVolunteerLists = DataManager::getShoppingListsByVolunteerId($_SESSION['user']);
    $openLists = DataManager::getOpenShoppingLists();
    $doneLists = array_filter($allVolunteerLists, function($e){
        return $e->getStatus() == ShoppingListStatus::DONE;
    });
    $inProgressLists = array_filter($allVolunteerLists, function($e){
        return $e->getStatus() == ShoppingListStatus::IN_PROGRESS;
    });
?>
<div class="page-header">
    <h2>Dashboard</h2>
</div>

<h3>Your Volunteer Shopping Lists</h3>
<?php if (!isset($allVolunteerLists)) : ?>
    <div class="alert alert-warning" role="alert">No shopping lists for this user</div>
<?php endif; ?>
<h4>Open</h4>
<?php $lists = $openLists; require('views/partials/shoppingList/SLTable.php'); ?>
<h4>In Progress</h4>
<?php $lists = $inProgressLists; require('views/partials/shoppingList/SLTable.php'); ?>
<h4>Done</h4>
<?php $lists = $doneLists; require('views/partials/shoppingList/SLTable.php'); ?>