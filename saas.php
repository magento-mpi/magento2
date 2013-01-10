<?php
/**
 * SaaS application "entry point", requires "SaaS access point" to delegate execution to it
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Run application based on invariant configuration string
 *
 * Both "SaaS access point" and this "entry point" have a convention: API consists of one and only one string argument
 * Underlying implementation may differ, in future versions of the entry point, but API should remain the same
 *
 * @param string $appConfigString
 */
return function ($appConfigString) {
    try {
        $params = array_merge($_SERVER, unserialize($appConfigString));
        require __DIR__ . '/app/bootstrap.php';
        $objectManager = new Mage_Core_Model_ObjectManager_Http(
            BP, $params['MAGE_RUN_CODE'], $params['MAGE_RUN_TYPE'], $params['CUSTOM_LOCAL_XML']
        );
        $request = $objectManager->get('Mage_Core_Controller_Request_Http');
        $response = $objectManager->get('Mage_Core_Controller_Response_Http');
        $handler = $objectManager->get('Magento_Http_Handler_Composite');
        $handler->handle($request, $response);
        $response->send();
    } catch (Exception $e) {
        Mage::printException($e);
    }
};