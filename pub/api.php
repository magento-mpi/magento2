<?php
/**
 * Web services API entry point
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__DIR__) . '/app/bootstrap.php';

Mage::register('custom_entry_point', true);
Mage::$headersSentThrowsException = false;

try {
    Mage::init('admin');
    Mage::app()->requireInstalledInstance();
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
        $server->initialize($adapterCode);
        $server->run();

        Mage::app()->getResponse()->sendResponse();
    }
} catch (Exception $e) {
    Mage::logException($e);
    echo $e->getMessage();
}
