<?php
require_once('inc/bootstrap.php');

$default_view = 'welcome';
$view = $default_view;

if (isset($_REQUEST['view']) && 
    file_exists(__DIR__ . '/views/' . $_REQUEST['view'] . '.php')) {
        $view = $_REQUEST['view'];
    }

/* if we have a form post, invoke the controller */
$postAction = $_REQUEST[SharedShopping\Controller::ACTION] ?? null;
if ($postAction != null) {
    SharedShopping\Controller::getInstance()->invokePostAction();
}


require_once('views/' . $view . '.php');
?>
