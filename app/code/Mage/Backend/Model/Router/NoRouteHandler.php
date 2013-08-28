<?php
/**
 * Backend no route handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Router_NoRouteHandler implements Mage_Core_Model_Router_NoRouteHandlerInterface
{
    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Backend_Helper_Data $helper
     */
    public function __construct(Mage_Backend_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Check and process no route request
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function process(Mage_Core_Controller_Request_Http $request)
    {
        $areaFrontName = array_shift(explode('/', trim($request->getPathInfo(), '/')));

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
