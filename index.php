<?php
/**
 * Application entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

 /**
 * Example - run a particular store or website:
 *
 * $params = $_SERVER;
 * $params['MAGE_RUN_CODE'] = 'website2';
 * $params['MAGE_RUN_TYPE'] = 'website';
 * ...
 * $app->run($params)
 */
try {
    require __DIR__ . '/app/bootstrap.php';
    $objectManager = new Mage_Core_Model_ObjectManager_Http(
        BP, $_SERVER['MAGE_RUN_CODE'], $_SERVER['MAGE_RUN_TYPE'], $_SERVER['CUSTOM_LOCAL_XML']
    );
    $request = $objectManager->get('Mage_Core_Controller_Request_Http');
    $response = $objectManager->get('Mage_Core_Controller_Response_Http');
    $handler = $objectManager->get('Magento_Http_Handler_Composite');
    $handler->handle($request, $response);
    $response->send();
} catch (Exception $e) {
    Mage::printException($e);
}
