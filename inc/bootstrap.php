<?php
declare(strict_types=1);
error_reporting(E_ALL); 
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

spl_autoload_register(function ($class) {
   $filename = __DIR__ . '/../lib/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($filename)) {
        include($filename);
    }
});

require_once(__DIR__ .'/../lib/Data/DataManager.php');

// create session context
SharedShopping\SessionContext::create();
