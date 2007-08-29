<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


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