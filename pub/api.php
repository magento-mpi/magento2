<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Api2
 */

require dirname(__DIR__) . '/app/bootstrap.php';

if (!Mage::isInstalled()) {
    echo 'Application is not installed yet, please complete install wizard first.';
    exit;
}

// emulate index.php entry point for correct URLs generation in API
Mage::register('custom_entry_point', true);
Mage::$headersSentThrowsException = false;
Mage::init('admin');
Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_EVENTS);
Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage_Core_Model_App_Area::PART_EVENTS);

// query parameter "type" is set by .htaccess rewrite rule
$apiAlias = Mage::app()->getRequest()->getParam('type');

// check request could be processed by API2
if (in_array($apiAlias, Mage_Api2_Model_Server::getApiTypes())) {
    /** @var $server Mage_Api2_Model_Server */
    $server = Mage::getSingleton('Mage_Api2_Model_Server');

    $server->run();
} else {
    /* @var $server Mage_Api_Model_Server */
    $server = Mage::getSingleton('Mage_Api_Model_Server');
    $adapterCode = $server->getAdapterCodeByAlias($apiAlias);

    // if no adapters found in aliases - find it by default, by code
    if (null === $adapterCode) {
        $adapterCode = $apiAlias;
    }
    try {
        $server->initialize($adapterCode);
        $server->run();

        Mage::app()->getResponse()->sendResponse();
    } catch (Exception $e) {
        Mage::logException($e);

        echo $e->getMessage();
        exit;
    }
}
