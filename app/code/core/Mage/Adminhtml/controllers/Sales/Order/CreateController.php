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
     * Initialize layout and data
     *
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _initAction()
    {
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int) $customerId);
        }
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int) $storeId);
        }
        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string) $currencyId);
        }
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
        
        $this->_initAction()
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
        $this->_initAction();
        $block = $this->getRequest()->getParam('block');
        if ($block) {
            $blockName = 'adminhtml/sales_order_create_'.$block;
            try {
                $block = $this->getLayout()->createBlock($blockName);
                $this->getResponse()->setBody($block->toHtml());
            }
            catch (Exception $e){
                $this->getResponse()->setBody(__('Can not create block "%s"', $blockName));
            }
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

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function editAction()
    {
        $this->getSession()->reset();
        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
        /* @var $order Mage_Sales_Model_Order */
        $this->getSession()->setStoreId($order->getStoreId());
        $this->getSession()->setCustomerId($order->getCustomerId());
        $this->getQuote()->createFromOrder($order);
        $this->getQuote()->collectTotals()->save();
        $order->cancel()->save();
        $this->_redirect('*/*');
    }

    public function customerGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_customer_grid')->toHtml());
    }

    /*public function storeAction()
    {
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->getSession()->setStoreId($storeId);
            $this->getResponse()->setBody('<script type="text/javascript">$("sc_store_name").innerHTML="' . __('in') . ' ' . $this->getSession()->getQuote()->getStore()->getName() . '"; $("sc_store_name").show();</script>');
        } else {
            $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_store')->toHtml());
        }
    }*/

    /*public function sidebarAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar')->toHtml());
    }*/

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

    public function wishlistAction()
    {

        $wishlist = null;

        try {
            $wishlist = Mage::getModel('wishlist/wishlist');
            /* @var $wishlist Mage_Wishlist_Model_Wishlist */
            $wishlist->setStore($this->getSession()->getQuote()->getStore());
            $wishlist->loadByCustomer($this->getSession()->getCustomer(), true);
            $wishlist->setStore($this->getSession()->getQuote()->getStore());
        } catch (Exception $e) {
            // TODO - if customer is not created yet
        }

        //remove items from admin quote
        $ids = Zend_Json::decode($this->getRequest()->getParam('move'));
        if (is_array($ids)) {
            foreach ($ids as $id) {
                if ($itemId = $intFilter->filter($id)) {
                    // add items to customer's wishlist
                    // TODO - if customer is not created yet
                    if ($wishlist && ($item = $this->getQuote()->getItemById($itemId))) {
                        $wishlist->addNewItem($item->getProductId());
                    }


                    if ($customerQuote && ($item = $this->getQuote()->getItemById($itemId))) {
                        $newItem = clone $item;
                        $customerQuote->addItem($item);
                    }
                    $this->getQuote()->removeItem($itemId);
                }
            }
        }



        $intFilter = new Zend_Filter_Int();

        //remove items
        $ids = $intFilter->filter($this->getRequest()->getParam('move'));
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

        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_wishlist')->toHtml());
    }

    /*public function viewedAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_viewed')->toHtml());
    }*/

    /*public function comparedAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_compared')->toHtml());
    }*/

    public function shippingAddressAction()
    {
        if (! is_null($same = $this->getRequest()->getParam('same_as_billing'))) {
            $this->getSession()->setSameAsBilling($same);
        } elseif (! is_null($addressId = $this->getRequest()->getParam('address_id'))) {
            $this->getSession()->setShippingAddressId($addressId);
        } elseif ($address = $this->getRequest()->getParam('address')) {
            $this->getSession()->setShippingAddressId(null);
            $addressData = Zend_Json::decode($address);
            if (is_array($addressData)) {
                $this->getQuote()->getShippingAddress()->addData($addressData);
                $this->getQuote()->getShippingAddress()->collectTotals();
                $this->getQuote()->save();
            }
        }

        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_shipping_address')->toHtml());
    }

    public function billingAddressAction()
    {
        if (! is_null($addressId = $this->getRequest()->getParam('address_id'))) {
            $this->getSession()->setBillingAddressId($addressId);
        } elseif ($address = $this->getRequest()->getParam('address')) {
            $this->getSession()->setBillingAddressId(null);
            $addressData = Zend_Json::decode($address);
            if (is_array($addressData)) {
                $this->getQuote()->getBillingAddress()->addData($addressData);
                $this->getQuote()->save();
            }
        }
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_billing_address')->toHtml());
    }

    public function shippingMethodAction()
    {
        if ($shippingMethod = $this->getRequest()->getParam('shipping_method')) {
            $this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod)->collectTotals()->save();
        }
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_shipping_method')->toHtml());
    }

    /*public function billingMethodAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_billing_method')->toHtml());
    }*/

    /*public function couponsAction()
    {
        if (! is_null($couponCode = $this->getRequest()->getParam('coupon_code'))) {
            $this->getQuote()->setCouponCode($couponCode);
        }
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_coupons')->toHtml());
    }*/

    /*public function newsletterAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_newsletter')->toHtml());
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

    /*public function searchAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_search')->toHtml());
    }

    public function searchGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_search_grid')->toHtml());
    }

    public function totalsAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/sales_order_create_totals')->toHtml());
    }*/

    public function saveAction()
    {
        $order = Mage::getModel('sales/order');
        /* @var $order Mage_Sales_Model_Order */
        $order->createFromQuoteAddress($this->getQuote()->getShippingAddress());
        $order->setStoreId($this->getQuote()->getStore()->getId());
        $order->setOrderCurrencyCode($this->getQuote()->getStore()->getCurrentCurrencyCode());
        $order->setInitialStatus();
        $order->validate();
        if ($order->getErrors()) {
            //TODO: handle errors (exception?)
        }
        $order->save();
        $this->getQuote()->setIsActive(false);
        $this->getQuote()->save();
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }

}
