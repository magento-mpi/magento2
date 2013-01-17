<?php
/**
 * Http entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_EntryPoint_Http extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * Run http application
     */
    public function processRequest()
    {
        try {
            $request = $this->_objectManager->get('Mage_Core_Controller_Request_Http');
            $response = $this->_objectManager->get('Mage_Core_Controller_Response_Http');
            $handler = $this->_objectManager->get('Magento_Http_Handler_Composite');
            $handler->handle($request, $response);
        } catch (Exception $e) {
            Mage::printException($e);
        }
    }
}
