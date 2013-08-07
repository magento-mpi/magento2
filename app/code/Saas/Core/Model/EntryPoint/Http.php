<?php
/**
 * Http entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Core_Model_EntryPoint_Http extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager $objectManager
     * @throws Magento_BootstrapException
     */
    public function __construct(Mage_Core_Model_Config_Primary $config, Magento_ObjectManager $objectManager = null)
    {
        try {
            parent::__construct($config, $objectManager);
        } catch (Magento_BootstrapException $e) {
            header('Content-Type: text/plain', true, 503);
            echo $e->getMessage();
            throw $e;
        }
    }

    /**
     * Run http application
     */
    protected function _processRequest()
    {
        try {
            $request = $this->_objectManager->get('Mage_Core_Controller_Request_Http');
            $response = $this->_objectManager->get('Mage_Core_Controller_Response_Http');
            $handler = $this->_objectManager->get('Magento_HTTP_Handler_Composite');
            $handler->handle($request, $response);
        } catch (Mage_Core_Model_Session_Exception $e) {
            header('Location: ' . Mage::getBaseUrl());
        } catch (Mage_Core_Model_Store_Exception $e) {
            require Mage::getBaseDir(Mage_Core_Model_Dir::PUB) . DS . 'errors' . DS . '404.php';
        } catch (Saas_Core_Model_Config_Exception $e) {
            require Mage::getBaseDir(Mage_Core_Model_Dir::PUB) . DS . 'errors' . DS. 'noCache.php';
        } catch (Magento_BootstrapException $e) {
            header('Content-Type: text/plain', true, 503);
            echo $e->getMessage();
        } catch (Exception $e) {
            Mage::printException($e);
        }
    }
}
