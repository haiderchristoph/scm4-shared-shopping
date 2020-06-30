<?php require_once('views/partials/header.php');
    $errType = htmlspecialchars($_GET["type"])
    
?>
<?php if ($errType == "invalid_shopping_list") : ?>
    <h1> 404 - Shopping List not found</h1>
<?php endif; ?>

<?php if ($errType == "invalid_authorization") : ?>
    <h1> 401 - Unauthorized</h1>
    <p>Please login</p>
<?php endif; ?>

<?php require_once('views/partials/footer.php'); ?>