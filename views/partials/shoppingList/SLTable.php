<?php use SharedShopping\Util;?>
<?php
    if (sizeof($lists) <= 0) :
        ?>
        <div class="alert alert-warning" role="alert">No shopping lists found for this status</div>
        <?php
    else :
        ?>
<table class="table">
    <thead>
    <tr>
        <th>
            Title
        </th>
        <th>
            Date Until
        </th>
        <th>
            Status
        </th>
        <th>
            Action
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($lists as $item):
        ?>
        <tr>
            <td>
                <?php echo Util::escape($item->getTitle()); ?>
            </td>
            <td>
                <?php echo Util::escape($item->getDateUntil()); ?>
            </td>
            <td>
                <?php echo Util::escape($item->getStatus()); ?>
            </td>
            <td>
                <a href="index.php?view=detail&shoppingListId=<?php echo Util::escape($item->getId()); ?>">Modify <span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>