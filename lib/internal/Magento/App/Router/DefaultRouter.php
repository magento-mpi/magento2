<?php
/**
 * Default application router
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Router;

use \Magento\App\ActionFactory,
    \Magento\App\RequestInterface;

class DefaultRouter extends AbstractRouter
{
    /**
     * @var \Magento\App\Router\NoRouteHandlerList
     */
    protected $_noRouteHandlerList;

    /**
     * @param ActionFactory $actionFactory
     * @param \Magento\App\Router\NoRouteHandlerList $noRouteHandlerList
     */
    public function __construct(
        ActionFactory $actionFactory,
        \Magento\App\Router\NoRouteHandlerList $noRouteHandlerList
    ) {
        parent::__construct($actionFactory);
        $this->_noRouteHandlerList = $noRouteHandlerList;
    }

    /**
     * Modify request and set to no-route action
     *
     * @param RequestInterface $request
     * @return boolean
     */
    public function match(RequestInterface $request)
    {
        foreach ($this->_noRouteHandlerList->getHandlers() as $noRouteHandler) {
            if ($noRouteHandler->process($request)) {
                break;
            }
        }

        return $this->_actionFactory->createController('Magento\App\Action\Forward',
            array('request' => $request)
        );
    }
}
