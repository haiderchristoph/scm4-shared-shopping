<div class="form-group">
    <div class="col-sm-6">
        <?php require('views/partials/shoppingList/SLName.php');?>
    </div>
    <div class="col-sm-2">
        <? require('views/partials/shoppingList/SLDatePicker.php');?>
    </div>
    <?php if($userType == SharedShopping\UserType::HELPSEEKER) : ?>
    <div class="col-sm-2">
        <?php
        $actionButtonClass = "btn-warning";
        $actionButtonText = "Save Changes";
        if ($actionButtonType == 'create') {
            $actionButtonClass = "btn-success";
            $actionButtonText = "Create";
        }
        ?>
        <button class="btn <?php echo $actionButtonClass; ?>"><?php echo $actionButtonText; ?></button>
    </div>
    <?php endif; ?>
</div>