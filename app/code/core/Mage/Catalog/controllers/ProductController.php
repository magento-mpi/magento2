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
 * Product controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @module     Catalog
 */
class Mage_Catalog_ProductController extends Mage_Core_Controller_Front_Action
{
	protected function _initProduct()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');

        $product = Mage::getModel('catalog/product')
            ->load($productId);

        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            Mage::register('current_category', $category);
        }
        Mage::register('current_product', $product);
        Mage::register('product', $product); // this need remove after all replace
    }

	public function viewAction()
    {
        $this->_initProduct();
        $product = Mage::registry('product');
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->_forward('noRoute');
            return;
        }

        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();

        $update->addHandle('PRODUCT_'.$product->getId());

        $this->loadLayoutUpdates();

        $update->addUpdate($product->getCustomLayoutUpdate());

        $this->generateLayoutXml()->generateLayoutBlocks();

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('tag/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function galleryAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function sendAction(){
    	
    	$this->_initProduct();
        $product = Mage::registry('product');
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->_forward('noRoute');
            return;
        }

        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();

        $update->addHandle('PRODUCT_'.$product->getId());

        $this->loadLayoutUpdates();

        $update->addUpdate($product->getCustomLayoutUpdate());

        $this->generateLayoutXml()->generateLayoutBlocks();

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('tag/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
        
    }
}
