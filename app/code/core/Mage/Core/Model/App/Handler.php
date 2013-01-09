<?php
/**
 * Abstraction of ACL Resource Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_App_Handler implements Magento_Http_HandlerInterface
{
    /**
     * Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @param Mage_Core_Model_App $app
     */
    public function __construct(Mage_Core_Model_App $app)
    {
        $this->_app = $app;
    }

    /**
     * Handle http request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     */
    public function handle(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        $response->headersSentThrowsException = Mage::$headersSentThrowsException;
        $response->setHeader("Content-Type", "text/html; charset=UTF-8");
        $this->_app->setRequest($request)->setResponse($response)->run();
    }
}

