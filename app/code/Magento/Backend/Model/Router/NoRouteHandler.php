<?php
/**
 * Backend no route handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Router;

class NoRouteHandler implements \Magento\Core\Model\Router\NoRouteHandlerInterface
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Backend\Helper\Data $helper
     */
    public function __construct(\Magento\Backend\Helper\Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Check and process no route request
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @return bool
     */
    public function process(\Magento\Core\Controller\Request\Http $request)
    {
        $requestPathParams = explode('/', trim($request->getPathInfo(), '/'));
        $areaFrontName = array_shift($requestPathParams);

        if ($areaFrontName == $this->_helper->getAreaFrontName()) {

            $moduleName     = 'core';
            $controllerName = 'index';
            $actionName     = 'noRoute';

            $request->setModuleName($moduleName)
                ->setControllerName($controllerName)
                ->setActionName($actionName);

            return true;
        }

        return false;
    }
}
