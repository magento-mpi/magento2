<?php
class Mage_Cms_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function match(Zend_Controller_Request_Http $request)
    {
    	$identifier = trim($request->getPathInfo(), '/');

        $page = Mage::getSingleton('cms/page')->load($identifier);
        if (!$page->getIsActive()) {
        	return false;
        }
        
        $request->setModuleName(isset($d[0]) ? $d[0] : 'cms')
            ->setControllerName(isset($d[1]) ? $d[1] : 'page')
            ->setActionName(isset($d[2]) ? $d[2] : 'view')
            ->setParam('page_id', $page->getId());
           
        return true;
    }
    
    public function getUrl($routeName, $params)
    {
        return '';
    }
}