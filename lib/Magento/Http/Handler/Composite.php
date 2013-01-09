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
     * @param Magento_Http_HandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->_children = $handlers;
    }

    /**
     * Handle http request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     */
    public function handle(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        foreach ($this->_children as $handler) {
            $handler->handle($request, $response);
            if ($request->isDispatched()) {
                break;
            }
        }
    }
}

