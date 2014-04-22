<?php
/**
 * Backend no route handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Router;

class NoRouteHandler implements \Magento\Framework\App\Router\NoRouteHandlerInterface
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\Route\ConfigInterface
     */
    protected $_routeConfig;

    /**
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Framework\App\Route\ConfigInterface $routeConfig
     */
    public function __construct(
        \Magento\Backend\Helper\Data $helper,
        \Magento\Framework\App\Route\ConfigInterface $routeConfig
    ) {
        $this->_helper = $helper;
        $this->_routeConfig = $routeConfig;
    }

    /**
     * Check and process no route request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function process(\Magento\Framework\App\RequestInterface $request)
    {
        $requestPathParams = explode('/', trim($request->getPathInfo(), '/'));
        $areaFrontName = array_shift($requestPathParams);

        if ($areaFrontName == $this->_helper->getAreaFrontName()) {

            $moduleName = $this->_routeConfig->getRouteFrontName('adminhtml');
            $controllerName = 'noroute';
            $actionName = 'index';

            $request->setModuleName($moduleName)->setControllerName($controllerName)->setActionName($actionName);

            return true;
        }

        return false;
    }
}
