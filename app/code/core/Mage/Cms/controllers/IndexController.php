<?php
class Mage_Cms_IndexController extends Mage_Core_Controller_Front_Action
{
    public function noRouteAction($coreRoute = null)
    {
    	Mage::getSingleton('cms/page')->load('no-route');
    	$this->_forward('view', 'page');
    }
}
