<?php
/**
 * Application request handler. Launches front controller, request routing and dispatching process.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\App;

class Handler implements \Magento\HTTP\HandlerInterface
{
    /**
     * Application object
     *
     * @var \Magento\Core\Model\AppInterface
     */
    protected $_app;

    /**
     * @param \Magento\Core\Model\AppInterface $app
     */
    public function __construct(\Magento\Core\Model\AppInterface $app)
    {
        $this->_app = $app;
    }

    /**
     * Handle http request
     *
     * @param \Zend_Controller_Request_Http $request
     * @param \Zend_Controller_Response_Http $response
     */
    public function handle(\Zend_Controller_Request_Http $request, \Zend_Controller_Response_Http $response)
    {
        $response->headersSentThrowsException = \Mage::$headersSentThrowsException;
        $this->_app->setRequest($request)->setResponse($response)->run();
    }
}

