<?php
/**
 * Application request handler. Launches front controller, request routing and dispatching process.
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
     * @var Mage_Core_Model_AppInterface
     */
    protected $_app;

    /**
     * @param Mage_Core_Model_AppInterface $app
     */
    public function __construct(Mage_Core_Model_AppInterface $app)
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
        set_error_handler(Mage::DEFAULT_ERROR_HANDLER);
        date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        $this->_app->setRequest($request)->setResponse($response)->run();
    }
}

