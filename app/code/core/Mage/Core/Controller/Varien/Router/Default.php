<?php
class Mage_Core_Controller_Varien_Router_Default extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function match(Zend_Controller_Request_Http $request)
    {
        // SEO article link parsing here
        // ...
        
        //default route (404)
        $d = explode('/', Mage::getStoreConfig('general/default/no_route'));
        
        $this->setModuleName(isset($d[0]) ? $d[0] : 'core')
            ->setControllerName(isset($d[1]) ? $d[1] : 'index')
            ->setActionName(isset($d[2]) ? $d[2] : 'index');
        
        return true;
    }
    
    public function getUrl($routeName, $params)
    {
        return 'ERROR (404)';
    }
}