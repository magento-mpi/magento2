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
     * Check and process no route request
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function process(Mage_Core_Controller_Request_Http $request)
    {
        if (Mage::app()->getStore()->isAdmin()) {
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
