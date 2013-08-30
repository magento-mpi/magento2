<?php
/**
 * Backend no route handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Router_NoRouteHandler implements Magento_Core_Model_Router_NoRouteHandlerInterface
{
    /**
     * @var Magento_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Backend_Helper_Data $helper
     */
    public function __construct(Magento_Backend_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Check and process no route request
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return bool
     */
    public function process(Magento_Core_Controller_Request_Http $request)
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
