<?php
class Mage_Core_Controller_Varien_Router_Default extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function match(Zend_Controller_Request_Http $request)
    {
        // SEO article link parsing here
        // ...
        
        //default route (404)
        $defaultRoute = (string)Mage::getSingleton('core/store')->getConfig('core/defaultRoute');
        list($module, $controller, $action) = explode('/', $defaultRoute);
        $request->setModuleName($module)->setControllerName($controller)->setActionName($action);
        
        return true;
    }
}