<?php

class Mage_Adminhtml_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        return $this;
    }

    protected function _addBreadcrumb($label, $title, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
        return $this;
    }

    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('content')->append($block);
        return $this;
    }

    protected function _addLeft(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('left')->append($block);
        return $this;
    }
    
    function loadLayout($ids=null, $key='', $generateBlocks=true)
    {
        parent::loadLayout($ids, $key, $generateBlocks);
        $this->_initLayoutMessages('adminhtml/session');
        return $this;
    }
}