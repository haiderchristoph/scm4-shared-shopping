<?php use SharedShopping\Util, SharedShopping\Controller, Data\DataManager, SharedShopping\ShoppingListStatus,  SharedShopping\AuthenticationManager, SharedShopping\UserType;
?>
<div class="page-header">
    <h2><?php echo htmlentities($listTitle);?></h2>
    <?php require('views/partials/shoppingList/SLStatus.php'); ?>

    <?php if ($shoppingListStatus == ShoppingListStatus::OPEN) : ?>
    <form class="float-right" method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_SL_INPROGRESS, array('shoppingListId' => Util::escape($shoppingList->getId()))); ?>">
        <button class="btn btn-success">Take this list</button>
    </form>
    <?php endif; ?>
    <?php if (DataManager::allArticlesDone($shoppingListId)) : ?>
        <form class="float-right" method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_SL_DONE, array('shoppingListId' => Util::escape($shoppingList->getId()))); ?>">
            <div class="form-group">
                <div class="col-sm-6">
                    <input required pattern="^(\d+(?:\.?\d{2})?)" type="text" class="form-control" id="totalPrice" name="<?php print SharedShopping\Controller::SL_TOTAL_PRICE; ?>" placeholder="Total Price" value="<?php echo htmlentities($totalPrice); ?>" <?php if (!DataManager::allArticlesDone($shoppingListId) || $shoppingListStatus == ShoppingListStatus::DONE) : ?> disabled <?php endif; ?>>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-success" <?php if (!DataManager::allArticlesDone($shoppingListId) || $shoppingListStatus == ShoppingListStatus::DONE) : ?> disabled <?php endif; ?>>Finish this list</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<h4>Shopping List Data</h4>
<form class="form-horizontal" method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_SL_UPDATE, array('shoppingListId' => Util::escape($shoppingList->getId()))); ?>">
<?php $actionButtonType = "update"; require('views/partials/shoppingList/SLInputGroup.php'); ?>
</form>
<div class="page-header">
    <h4>Articles</h4>
</div>
<?php require('views/partials/article/articleList.php'); ?>