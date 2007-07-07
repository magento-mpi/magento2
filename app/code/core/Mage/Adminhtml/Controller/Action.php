<?php

class Mage_Adminhtml_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
    }
    
    protected function _addBreadcrumb($label, $title, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
    }
    
    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('content')->append($block);
    }
}