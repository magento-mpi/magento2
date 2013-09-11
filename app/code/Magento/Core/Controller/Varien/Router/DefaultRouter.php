<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Controller\Varien\Router;

class DefaultRouter extends \Magento\Core\Controller\Varien\Router\AbstractRouter
{
    /**
     * @var \Magento\Core\Model\NoRouteHandlerList
     */
    protected $_noRouteHandlerList;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Factory $controllerFactory
     * @param \Magento\Core\Model\NoRouteHandlerList $noRouteHandlerList
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Factory $controllerFactory,
        \Magento\Core\Model\NoRouteHandlerList $noRouteHandlerList
    ) {
        parent::__construct($controllerFactory);
        $this->_noRouteHandlerList = $noRouteHandlerList;
    }

    /**
     * Modify request and set to no-route action
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @return boolean
     */
    public function match(\Magento\Core\Controller\Request\Http $request)
    {
        foreach ($this->_noRouteHandlerList->getHandlers() as $noRouteHandler) {
            if ($noRouteHandler->process($request)) {
                break;
            }
        }

        return $this->_controllerFactory->createController('\Magento\Core\Controller\Varien\Action\Forward',
            array('request' => $request)
        );
    }
}
