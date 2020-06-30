<?php use SharedShopping\ShoppingListStatus; ?>
<div class="badge badge-status 
<?php $status = $shoppingList->getStatus();
    switch($status) {
        case ShoppingListStatus::OPEN:
            echo htmlentities('btn-primary');
            break;
        case ShoppingListStatus::IN_PROGRESS:
            echo htmlentities('btn-info');
            break;
        case ShoppingListStatus::DONE:
            echo htmlentities('btn-success');
            break;
        default:
            echo htmlentities('btn-secondary');
            break;
    }
?>
"><?php echo htmlentities($shoppingList->getStatus());?></div>