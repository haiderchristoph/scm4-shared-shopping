<?php use SharedShopping\Util, Data\DataManager, SharedShopping\ShoppingListStatus;
    $articleTitle = '';
    $articleAmount = '';
    $articlePrice = '';
    $articleList = DataManager::getArticlesByShoppingListId($shoppingListId);
?>
<table class="table">
    <thead>
    <tr>
        <th>
            Article Name
        </th>
        <th>
            Amount
        </th>
        <th>
            Price Limit
        </th>
        <th>
            Action
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($articleList as $item):
        $articleId = $item->getId();
        $articleTitle = $item->getTitle();
        $articleAmount = $item->getAmount();
        $articlePrice = $item->getPriceLimit();
        ?>
        <form method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_ARTICLE_MODIFY, array('articleId' => Util::escape($item->getId()), 'shoppingListId' => $shoppingListId)); ?>">
        <tr>
            <td>
                <?php require('views/partials/article/articleTitle.php'); ?>
            </td>
            <td>
                <?php require('views/partials/article/articleAmount.php'); ?>
            </td>
            <td>
                <?php require('views/partials/article/articlePrice.php'); ?>
            </td>
            <td>
            <?php if (SharedShopping\PermissionManager::canManipulateDataInView($shoppingListId)) : ?>
                <button class="btn btn-warning" value="<?php echo SharedShopping\Controller::ACTION_ARTICLE_UPDATE ?>" name="<?php echo SharedShopping\Controller::ACTION_ARTICLE_UPDATE ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                <button class="btn btn-danger" value="<?php echo SharedShopping\Controller::ACTION_ARTICLE_DELETE ?>" name="<?php echo SharedShopping\Controller::ACTION_ARTICLE_DELETE ?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
            <?php endif; ?>
            <?php if (SharedShopping\PermissionManager::canSetArticlesToDone($shoppingListId)) : ?>
                <?php if ($item->getIsDone() == false) : ?>
                    <button class="btn btn-success" value="<?php echo SharedShopping\Controller::ACTION_ARTICLE_DONE ?>" name="<?php echo SharedShopping\Controller::ACTION_ARTICLE_DONE ?>"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                <?php endif; ?>
                <?php if ($item->getIsDone() == true) : ?>
                    <button class="btn btn-danger" value="<?php echo SharedShopping\Controller::ACTION_ARTICLE_NOT_DONE ?>" name="<?php echo SharedShopping\Controller::ACTION_ARTICLE_NOT_DONE ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                <?php endif; ?>
            <?php endif; ?>
            </td>
        </tr>
        </form>
    <?php endforeach;
        $articleId = '';
        $articleTitle = '';
        $articleAmount = '';
        $articlePrice = '';
    ?>
    <?php if ($userType == SharedShopping\UserType::HELPSEEKER && $shoppingListStatus == ShoppingListStatus::DRAFT) : ?>
    <form class="form-horizontal" method="post" action="<?php echo Util::action(SharedShopping\Controller::ACTION_ARTICLE_CREATE, array('shoppingListId' => $shoppingListId)); ?>">
    <tr>
    <td>
        <?php require('views/partials/article/articleTitle.php'); ?>
    </td>
    <td>
        <?php require('views/partials/article/articleAmount.php'); ?>
    </td>
    <td>
        <?php require('views/partials/article/articlePrice.php'); ?>
    </td>
    <td>
        <button class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></button>
    </td>
    </tr>
    </form>
    <?php endif; ?>
    </tbody>
</table>