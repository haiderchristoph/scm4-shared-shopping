<?php use SharedShopping\Util, SharedShopping\Controller, Data\DataManager, SharedShopping\ShoppingListStatus,  SharedShopping\AuthenticationManager, SharedShopping\UserType;
    if ($shoppingList->getVolunteerId()) {
        $volunteerUser = DataManager::getUserByid($shoppingList->getVolunteerId());
    }
?>
<div class="page-header">
    <h2><?php echo htmlentities($listTitle);?></h2>
    <?php if(isset($volunteerUser) && $volunteerUser) : ?>

    <h4>Volunteer: <?php echo htmlentities($volunteerUser->getUserName()); ?></h4>
    <?php endif; ?>
    <?php if ($shoppingListStatus == ShoppingListStatus::DONE) : ?>
        <h4>Total Price: <?php echo htmlentities($totalPrice); ?> €</h4>
    <?php endif; ?>
    
    <?php require('views/partials/shoppingList/SLStatus.php'); ?>
    
    <?php if ($shoppingList->getStatus() == ShoppingListStatus::DRAFT) : ?>
        <form class="float-right" method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_SL_PUBLISH, array('shoppingListId' => Util::escape($shoppingList->getId()))); ?>">
            <button class="btn btn-success">Publish Shopping List</button>
        </form>
    <?php endif; ?>
    <form class="float-right padding-right" method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_SL_DELETE, array('shoppingListId' => Util::escape($shoppingList->getId()))); ?>">
        <button class="btn btn-danger">Delete Shopping List</button>
    </form>
</div>

<h4>Shopping List Data</h4>
<form class="form-horizontal" method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_SL_UPDATE, array('shoppingListId' => Util::escape($shoppingList->getId()))); ?>">
    <?php $actionButtonType = "update"; require('views/partials/shoppingList/SLInputGroup.php'); ?>
</form>
<div class="page-header">
    <h4>Articles</h4>
</div>
<?php require('views/partials/article/articleList.php'); ?>