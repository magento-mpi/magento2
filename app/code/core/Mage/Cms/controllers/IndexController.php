<?php

class Mage_Cms_IndexController extends Mage_Core_Controller_Front_Action
{
    public function noRouteAction($coreRoute = null)
    {
        $page = Mage::getSingleton('cms/page')->load();
        if( !$page->getPage() || !is_null($coreRoute) ) {
            #$redirectParams = $observer->getEvent()->getStatus();
            /*
            echo "<pre>";
            print_r($this->getRequest()->getParam('__status__'));
            echo "</pre>";
            */
            #$this->norouteAction(1);
        }
    }
}