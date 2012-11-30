<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Controller_Varien_Router_Default extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Modify request and set to no-route action
     * If store is admin and specified different admin front name,
     * change store to default (Possible when enabled Store Code in URL)
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return boolean
     */
    public function match(Mage_Core_Controller_Request_Http $request)
    {
        $noRoute        = explode('/', Mage::app()->getStore()->getConfig('web/default/no_route'));
        $moduleName     = isset($noRoute[0]) ? $noRoute[0] : 'core';
        $controllerName = isset($noRoute[1]) ? $noRoute[1] : 'index';
        $actionName     = isset($noRoute[2]) ? $noRoute[2] : 'index';

        if (Mage::app()->getStore()->isAdmin()) {
            $adminFrontName = (string)Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
            if ($adminFrontName != $moduleName) {
                $moduleName     = 'core';
                $controllerName = 'index';
                $actionName     = 'noRoute';
                Mage::app()->setCurrentStore(Mage::app()->getDefaultStoreView());
            }
        }

        $request->setModuleName($moduleName)
            ->setControllerName($controllerName)
            ->setActionName($actionName);

        return $this->_controllerFactory->createController('Mage_Core_Controller_Varien_Action_Forward',
            array('request' => $request)
        );
    }
}
