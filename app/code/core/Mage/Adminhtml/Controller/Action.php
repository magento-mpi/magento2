<?php

class Mage_Adminhtml_Controller_Action extends Mage_Core_Controller_Varien_Action
{
	protected function _construct()
	{
		parent::_construct();
		
		$appDir = Mage::getbaseDir('app');
		
		Mage::getConfig()->setNode(
			'stores/base/system/filesystem/layout', 
			$appDir.'/design/adminhtml/default/layout/default');
		Mage::getConfig()->setNode(
			'stores/base/system/filesystem/template', 
			$appDir.'/design/adminhtml/default/template/default');
		Mage::getConfig()->setNode(
			'stores/base/system/filesystem/translate', 
			$appDir.'/design/adminhtml/default/translate');
			
	}
	
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