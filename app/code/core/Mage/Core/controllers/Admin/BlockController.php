<?php

class BlockController extends Mage_Core_Controller_Admin_Action
{
    function indexAction()
    {
        
    }
    
    function loadPanelAction()
    {
        $this->renderLayout('layout', 'toJs');
    }
    
    function loadTreeAction() {
        $this->getResponse()->setBody($this->_view->render('core/block.tree.phtml'));
    }
    
}