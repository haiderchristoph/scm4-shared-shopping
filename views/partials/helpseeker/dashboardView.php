<?php
    use Data\DataManager;
    use SharedShopping\Util;
    use SharedShopping\ShoppingListStatus;
    
    // fetch all shopping lists by creator and filter it
    $allLists = DataManager::getShoppingListsByCreatorId($_SESSION['user']);
    $openLists = array_filter($allLists, function($e){
        return $e->getStatus() == ShoppingListStatus::OPEN;
    });
    $doneLists = array_filter($allLists, function($e){
        return $e->getStatus() == ShoppingListStatus::DONE;
    });
    $inProgressLists = array_filter($allLists, function($e){
        return $e->getStatus() == ShoppingListStatus::IN_PROGRESS;
    });
    $draftLists = array_filter($allLists, function($e){
        return $e->getStatus() == ShoppingListStatus::DRAFT;
    });
?>
<div class="page-header">
    <h2>Dashboard</h2>
</div>

<h4>Create New Shopping List</h4>
<?
    $listTitle = "";
    $listDateUntil = date("Y-m-d");
?>
<form class="form-horizontal" method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_SL_CREATE); ?>">
    <?php $actionButtonType = "create"; require('views/partials/shoppingList/SLInputGroup.php'); ?>
</form>

<h3>Your Shopping Lists</h3>
<?php if (!isset($allLists)) : ?>
    <div class="alert alert-warning" role="alert">No shopping lists for this user</div>
<?php endif; ?>
<h4>Drafts</h4>
<?php $lists = $draftLists; require('views/partials/shoppingList/SLTable.php'); ?>
<h4>Open</h4>
<?php $lists = $openLists; require('views/partials/shoppingList/SLTable.php'); ?>
<h4>In Progress</h4>
<?php $lists = $inProgressLists; require('views/partials/shoppingList/SLTable.php'); ?>
<h4>Done</h4>
<?php $lists = $doneLists; require('views/partials/shoppingList/SLTable.php'); ?>