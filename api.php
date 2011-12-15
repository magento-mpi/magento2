<?php

if (version_compare(phpversion(), '5.2.0', '<')) {
    echo 'It looks like you have an invalid PHP version. Magento supports PHP 5.2.0 or newer';
    exit;
}
error_reporting(E_ALL | E_STRICT);

require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo 'Application is not installed yet, please complete install wizard first.';
    exit;
}

//should be set to true, otherwise "Fatal error: Class '%s' not found" in Autoload will not generate exceptions in error handler mageCoreErrorHandler()
Mage::setIsDeveloperMode(true);
#ini_set('display_errors', 1);

Mage::$headersSentThrowsException = false;
Mage::init('admin');

$isNewApi = true;
if ($isNewApi) {
    $server = Mage::getModel('api2/server');    //new Mage_Api2_Model_Server;
} else {
    $server = Mage::getModel('api2/old_server');    //new Mage_Api2_Model_OldServer;
}

$server->run();
