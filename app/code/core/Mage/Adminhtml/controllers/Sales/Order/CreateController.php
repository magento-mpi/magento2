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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders creation process controller
 *
 * @author     Michael Bessolov <michael@varien.com>
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }
    
    /**
     * Retrieve order create model
     * 
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Initialize order creation session data
     *
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _initSession()
    {
        /**
         * Identify customer
         */
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int) $customerId);
        }
        
        /**
         * Identify store
         */
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int) $storeId);
        }
        
        /**
         * Identify currency
         */
        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string) $currencyId);
        }
        return $this;
    }
    
    /**
     * Processing request data
     *
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _processData()
    {
        /**
         * Saving order data
         */
        if ($data = $this->getRequest()->getPost('order')) {
            $this->_getOrderCreateModel()->importPostData($data);
        }
        
        /**
         * Change shipping address flag
         */
        if ($this->getRequest()->getPost('setShipping')) {
            $this->_getOrderCreateModel()->setRecollect(true);
        }
        
        /**
         * Flag for using billing address for shipping
         */
        $syncFlag = $this->getRequest()->getPost('shippingAsBilling');
        if (!is_null($syncFlag)) {
            $this->_getOrderCreateModel()->setShippingAsBilling((int)$syncFlag);
        }
        
        /**
         * Adding product to quote from shoping cart, wishlist etc.
         */
        if ($productId = (int) $this->getRequest()->getPost('addProduct')) {
            $this->_getOrderCreateModel()->addProduct($productId);
        }
        
        /**
         * Adding products to quote from special grid
         */
        if ($data = $this->getRequest()->getPost('addProducts')) {
            $this->_getOrderCreateModel()->addProducts(Zend_Json::decode($data));
        }
        
        /**
         * Update quote items
         */
        if ($items = $this->getRequest()->getPost('updateItems')) {
            $this->_getOrderCreateModel()->updateQuoteItems(Zend_Json::decode($items));
        }
        
        /**
         * Remove quote item
         */
        if ($itemId = (int) $this->getRequest()->getPost('removeItem')) {
            $this->_getOrderCreateModel($itemId)->removeQuoteItem($itemId);
        }
        
        /**
         * Moove quote item
         */
        if ( ($itemId = (int) $this->getRequest()->getPost('moveItem')) 
            && ($moveTo = (string) $this->getRequest()->getPost('moveTo')) ) {
            $this->_getOrderCreateModel()->moveQuoteItem($itemId, $moveTo);
        }
        
        $this->_getOrderCreateModel()->saveQuote();
        return $this;
    }
    
    /**
     * Index page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('left')->setIsCollapsed(true);
        
        $sidebar = $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar')
            ->setShowContainer(true);
        $this->_initSession()
            ->_setActiveMenu('sales/order')
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order_create'))
            ->_addLeft($sidebar)
            ->_addJs($this->getLayout()->createBlock('core/template')->setTemplate(
                'sales/order/create/js.phtml'
            ))
            ->renderLayout();
    }
    
    /**
     * Loading page block
     */
    public function loadBlockAction()
    {
        $this->_initSession()
            ->_processData();
            
        $asJson= $this->getRequest()->getParam('json');
        $block = $this->getRequest()->getParam('block');
        $res = array();
        
        if ($block) {
            $blocks = explode(',', $block);
            
            if ($asJson && !in_array('messages', $blocks)) {
                $blocks[] = 'messages';
            }
            
            foreach ($blocks as $block) {
                $blockName = 'adminhtml/sales_order_create_'.$block;
                try {
                    $blockObject = $this->getLayout()->createBlock($blockName);
                    $res[$block] = $blockObject->toHtml();
                }
                catch (Exception $e){
                    $res[$block] = __('Can not create block "%s"', $blockName);
                }
            }
        }
        
        if ($asJson) {
            $this->getResponse()->setBody(Zend_Json::encode($res));
        }
        else {
            $this->getResponse()->setBody(implode('', $res));
        }
    }
    
    /**
     * Start order create action
     */
    public function startAction()
    {
        $this->_getSession()->clear();
        $this->_redirect('*/*', array('customer_id' => $this->getRequest()->getParam('customer_id')));
    }
    
    /**
     * Cancel order create
     */
    public function cancelAction()
    {
        $this->_getSession()->clear();
        $this->_redirect('*/*');
    }
    
    /**
     * Saving quote and create order
     */
    public function saveAction()
    {
        try {
            $order = $this->_getOrderCreateModel()->createOrder();
            $this->_getSession()->clear();
            $url = $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
        catch (Exception $e){
            //echo $e;
            $url = $this->_redirect('*/*/');
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
//    public function editAction()
//    {
//        $this->getSession()->reset();
//        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
//        /* @var $order Mage_Sales_Model_Order */
//        $this->getSession()->setStoreId($order->getStoreId());
//        $this->getSession()->setCustomerId($order->getCustomerId());
//        $this->getQuote()->createFromOrder($order);
//        $this->getQuote()->collectTotals()->save();
//        $order->cancel()->save();
//        $this->_redirect('*/*');
//    }


    /*public function cartAction()
    {
        $intFilter = new Zend_Filter_Int();

        // customer's front-end quote
    	$customerQuote = $this->getSession()->getCustomerQuote();


        //remove items from admin quote
        $ids = Zend_Json::decode($this->getRequest()->getParam('move'));
        if (is_array($ids)) {
            foreach ($ids as $id) {
                if ($itemId = $intFilter->filter($id)) {
                    // add items to frontend quote
                    // TODO - if customer is not created yet
                    if ($customerQuote && ($item = $this->getQuote()->getItemById($itemId))) {
                        $newItem = clone $item;
                        $customerQuote->addItem($item);
                    }
                    $this->getQuote()->removeItem($itemId);
                }
            }
        }

        // update frontend quote
        if ($customerQuote) {
            $customerQuote->getShippingAddress()->collectTotals(); // ?
            $customerQuote->save();
        }

        // update admin quote
    	$this->getQuote()->getShippingAddress()->collectTotals();
    	$this->getQuote()->save();

        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_cart')->toHtml());
    }*/

    public function itemsAction()
    {

        $intFilter = new Zend_Filter_Int();

        // add products
        $ids = Zend_Json::decode($this->getRequest()->getParam('products'));
        if (is_array($ids)) {
            foreach ($ids as $id => $ar) {
                $this->_addItem($id, $ar['qty']);
            }
        } elseif (! empty($ids)) {
            $this->_addItem($ids);
        }

        // update items
        $ids = Zend_Json::decode($this->getRequest()->getParam('update'));
        if (is_array($ids)) {
            foreach ($ids as $id) {
                foreach ($id as $key => $qty) {
                    if ($qty>0) {
                        if ($item = $this->getQuote()->getItemById($key)) {
                            $item->setQty($qty);
                        }
                    } else {
                        $this->getQuote()->removeItem($key);
                    }
                }
            }
        }

        //remove items
        $ids = $intFilter->filter($this->getRequest()->getParam('remove'));
        if (!empty($ids)) {
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $this->getQuote()->removeItem($id);
                }
            } else {
                $this->getQuote()->removeItem($ids);
            }
        }

    	$this->getQuote()->getShippingAddress()->collectTotals();
    	$this->getQuote()->save();

        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_items')->toHtml());
    }

    protected function _addItem($productId, $qty=1)
    {

        $product = Mage::getModel('catalog/product')->load($productId);

        if ($product->getId()) {
//        	if($product->isSuperConfig()) {
//        		$productId = $product->getSuperLinkIdByOptions($this->getRequest()->getParam('super_attribute'));
//        		if($productId) {
//        			$superProduct = Mage::getModel('catalog/product')
//        				->load($productId)
//        				->setParentProduct($product);
//        			if($superProduct->getId()) {
//        				$item = $this->getQuote()->addCatalogProduct($superProduct->setQty($qty));
//        				$item->setDescription(
//		            		$this->getLayout()->createBlock('checkout/cart_item_super')->setSuperProduct($superProduct)->toHtml()
//		            	);
//		            	$item->setName($product->getName());
//		            	$this->getQuote()->getShippingAddress()->collectTotals();
//		            	$this->getQuote()->save();
//
//        			}
//        		} else {
//        			$this->_backToProduct($product->getId());
//        			return;
//        		}
//
//        	} else if($product->isSuperGroup()) {
//        		$superGroupProducts = $this->getRequest()->getParam('super_group', array());
//        		if(!is_array($superGroupProducts)) {
//        			$superGroupProducts = array();
//        		}
//
//        		if(sizeof($superGroupProducts)==0) {
//        			$this->_backToProduct($product->getId());
//        			return;
//        		}
//        		foreach($product->getSuperGroupProductsLoaded() as $superProductLink) {
//
//        			if(isset($superGroupProducts[$superProductLink->getLinkedProductId()]) && $qty =  $intFilter->filter($superGroupProducts[$superProductLink->getLinkedProductId()])) {
//      				   $superProduct = Mage::getModel('catalog/product')
//	        				->load($superProductLink->getLinkedProductId())
//	        				->setParentProduct($product);
//	        			if($superProduct->getId()) {
//	        				$this->getQuote()->addCatalogProduct($superProduct->setQty($qty));
//			            	$this->getQuote()->getShippingAddress()->collectTotals();
//			            	$this->getQuote()->save();
//	        			}
//        			}
//        		}
//        	} else {
        	   	$this->getQuote()->addCatalogProduct($product->setQty($qty));
//        	}
        }
//        Mage::getSingleton('checkout/session')->setQuoteId($this->getQuote()->getId());
    }
}
