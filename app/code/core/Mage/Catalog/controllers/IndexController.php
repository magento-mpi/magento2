<?php



class Mage_Catalog_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Index action
     *
     * Display categories home page
     *
     */
    function indexAction() 
    {
        $this->loadLayout();

				$homeBlock = $this->getLayout()->createBlock('tpl', 'homecontent')->setTemplate('catalog/home.phtml');
				$this->getLayout()->getBlock('content')->append($homeBlock);
        
        $this->renderLayout();
    }

    function testAction() 
    {
        echo __METHOD__;
    }
}

