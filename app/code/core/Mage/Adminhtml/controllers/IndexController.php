<?php

class Mage_Adminhtml_IndexController extends Mage_Core_Controller_Front_Action 
{
    protected function _outTemplate($tplName)
    {
        $block = $this->getLayout()->createBlock('core/template')->setTemplate("adminhtml/$tplName.phtml");
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function indexAction()
    {
        $this->_outTemplate('frameset');
    }
    
    public function jsFrameAction()
    {
        $this->_outTemplate('js');
    }
    
    public function waitFrameAction()
    {
        $this->_outTemplate('wait');
    }
    
    public function layoutFrameAction()
    {
        $this->_outTemplate('layout');
    }
    
    public function exampleAction()
    {
        $this->_outTemplate('example');
    }
}