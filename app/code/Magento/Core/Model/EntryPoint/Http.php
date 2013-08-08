<?php
/**
 * Http entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_EntryPoint_Http extends Magento_Core_Model_EntryPointAbstract
{
    /**
     * Process http request, output html page or proper information about an exception (if any)
     */
    public function processRequest()
    {
        try {
            parent::processRequest();
        } catch (Magento_Core_Model_Session_Exception $e) {
            header('Location: ' . Mage::getBaseUrl());
        } catch (Magento_Core_Model_Store_Exception $e) {
            require Mage::getBaseDir(Magento_Core_Model_Dir::PUB) . DS . 'errors' . DS . '404.php';
        } catch (Magento_BootstrapException $e) {
            header('Content-Type: text/plain', true, 503);
            echo $e->getMessage();
        } catch (Exception $e) {
            Mage::printException($e);
        }
    }

    /**
     * Run http application
     */
    protected function _processRequest()
    {
        $request = $this->_objectManager->get('Magento_Core_Controller_Request_Http');
        $response = $this->_objectManager->get('Magento_Core_Controller_Response_Http');
        $handler = $this->_objectManager->get('Magento_HTTP_Handler_Composite');
        $handler->handle($request, $response);
    }
}
