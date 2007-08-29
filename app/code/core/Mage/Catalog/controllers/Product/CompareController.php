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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog comapare controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Catalog_Product_CompareController extends Mage_Core_Controller_Front_Action
 {
	public function indexAction()
	{
	    $items = $this->getRequest()->getParam('items');
	    if ($items) {
	        $items = explode(',', $items);
	        $list = Mage::getSingleton('catalog/product_compare_list');
            $list->addProducts($items);
	    }

		$this->loadLayout(array('default', 'catalog_compare'), 'catalog_compare');
		$this->renderLayout();
	}

	public function addAction()
	{
		$productId = (int)$this->getRequest()->getParam('product');

		$product = Mage::getModel('catalog/product')
			->load($productId);

		if($product->getId()) {
		    Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
		}

		$this->_redirectToReferer();
	}

	public function removeAction()
	{
		$productId = (int)$this->getRequest()->getParam('product');

		$product = Mage::getModel('catalog/product')
			->load($productId);

		if($product->getId()) {
			$item = Mage::getModel('catalog/product_compare_item');
			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				$item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
			} else {
				$item->addVisitorId(Mage::getSingleton('core/session')->getLogVisitorId());
			}

			$item->loadByProduct($product);

			if($item->getId()) {
				$item->delete();
			}
		}

		$referer = $this->getRequest()->getServer('HTTP_REFERER');
		if (preg_match('#/items/([0-9,]+)/?#', $referer, $m)) {
		    $items = explode(',', $m[1]);
		    unset($items[array_search($productId, $items)]);
		    $newLink = str_replace('/items/'.$m[1], '/items/'.join(',', $items), $referer);
    		$this->getResponse()->setRedirect($newLink);
		}
		else {
		    $this->_redirectToReferer();
		}
	}

	public function clearAction()
	{
		$items = Mage::getResourceModel('catalog/product_compare_item_collection')
				->setStoreId(Mage::getSingleton('core/store')->getId());

        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
		} else {
			$items->setVisitorId(Mage::getSingleton('core/session')->getLogVisitorId());
		}

		$items->load();

		$items->walk('delete');

        $this->_redirectToReferer();
	}
 } // Class Mage_Catalog_CompareController end