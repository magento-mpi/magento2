<?php
/**
 * Composite http request handler. Used to apply multiple request handlers
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Http_Handler_Composite implements Magento_Http_HandlerInterface
{
    /**
     * Leaf request handlers
     *
     * @var Magento_Http_HandlerInterface[]
     */
    protected $_children;

    /**
     * Handler factory
     *
     * @var Magento_Http_HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @param Magento_Http_HandlerFactory $factory
     * @param array $handlers
     */
    public function __construct(Magento_Http_HandlerFactory $factory, array $handlers)
    {
        $this->_children = $handlers;
        $this->_handlerFactory = $factory;
    }

    /**
     * Handle http request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     */
    public function handle(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        foreach ($this->_children as $handlerName) {
            $this->_handlerFactory->create($handlerName)->handle($request, $response);
            if ($request->isDispatched()) {
                break;
            }
        }
    }
}

