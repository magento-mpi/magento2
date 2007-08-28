<?php

class Mage_Cms_PageController extends Mage_Core_Controller_Front_Action
{
	public function viewAction()
	{
		$page = Mage::getSingleton('cms/page');
		if (!$page) {
			$pageId = $this->getRequest()->getParam('page_id', false);
			if ($pageId) {
				$page = Mage::getModel('cms/page')->load($pageId);
			}
		}

		if (!$page) {
			$this->_forward('noRoute');
			return;
		}

		$this->loadLayout();

		$template = (string)Mage::getConfig()->getNode('global/cms/layouts/'.$page->getRootTemplate().'/template');
		$this->getLayout()->getBlock('root')->setTemplate($template);

		$block = $this->getLayout()->createBlock('cms/page')->setPage($page);
		$this->getLayout()->getBlock('content')->append($block);

		$this->renderLayout();
	}
}