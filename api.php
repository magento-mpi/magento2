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

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

#ini_set('display_errors', 1);

Mage::$headersSentThrowsException = false;
Mage::init('admin');

$isNewApi = true;
if ($isNewApi) {
    /** @var $server Mage_Api2_Model_Server */
    $server = Mage::getModel('api2/server');    //new Mage_Api2_Model_Server;
} else {
    /** @var $server Mage_Api2_Model_OldServer */
    $server = Mage::getModel('api2/old_server');    //new Mage_Api2_Model_OldServer;
}

$server->run();
