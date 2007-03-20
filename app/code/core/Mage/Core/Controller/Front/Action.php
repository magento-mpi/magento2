<?php

class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Zend_Action 
{
    function preDispatch()
    {
        parent::preDispatch();
        
        $layout = $this->getLayout();
        
        if (!$this->getFlag('', 'no-defaultLayout')) {
            $action = $this->getFullActionName();
            $layout->init($action);
            if (!$layout->isCacheLoaded()) {
                $layout->loadUpdatesFromConfig('front', 'default');
                $layout->loadUpdatesFromConfig('front', $action);
                $layout->saveCache();
            }
            $layout->createBlocks();
        }
    }
    
    function postDispatch()
    {
        parent::postDispatch();
        
        $this->renderLayout();
    }
}