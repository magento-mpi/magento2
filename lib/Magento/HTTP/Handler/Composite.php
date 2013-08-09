<?php
/**
 * Composite http request handler. Used to apply multiple request handlers
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_HTTP_Handler_Composite implements Magento_HTTP_HandlerInterface
{
    /**
     * Leaf request handlers
     *
     * @var Magento_HTTP_HandlerInterface[]
     */
    protected $_children;

    /**
     * Handler factory
     *
     * @var Magento_HTTP_HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @param Magento_HTTP_HandlerFactory $factory
     * @param array $handlers
     */
    public function __construct(Magento_HTTP_HandlerFactory $factory, array $handlers)
    {
        usort($handlers, array($this, '_cmp'));
        $this->_children = $handlers;
        $this->_handlerFactory = $factory;
    }

    /**
     * Sort handlers
     *
     * @param array $handlerA
     * @param array $handlerB
     * @return int
     */
    protected function _cmp($handlerA, $handlerB)
    {
        $sortOrderA = intval($handlerA['sortOrder']);
        $sortOrderB = intval($handlerB['sortOrder']);
        if ($sortOrderA == $sortOrderB) {
            return 0;
        }
        return ($sortOrderA < $sortOrderB) ? -1 : 1;
    }

    /**
     * Handle http request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     */
    public function handle(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        foreach ($this->_children as $handlerConfig) {
            $this->_handlerFactory->create($handlerConfig['class'])->handle($request, $response);
            if ($request->isDispatched()) {
                break;
            }
        }
    }
}

