<?php

class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Zend_Action 
{
    function preDispatch()
    {
        parent::preDispatch();
        
        if (!$this->getFlag('', 'no-defaultLayout')) {
            $action = $this->getFullActionName();
            $this->loadLayout('front', array('default', $action), $action);
        }
    }
    
    function postDispatch()
    {
        parent::postDispatch();
        
        $this->renderLayout();
    }
}