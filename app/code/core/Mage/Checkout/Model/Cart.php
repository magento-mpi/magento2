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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shoping cart model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Model_Cart extends Varien_Object
{
    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    public function getProductIds()
    {
        $products = $this->getData('product_ids');
        if (is_null($products)) {
            $products = array();
            foreach ($this->getQuote()->getAllItems() as $item) {
            	$products[$item->getProductId()] = $item->getProductId();
            }
            $this->setData('product_ids', $products);
        }
        return $products;
    }
    
    public function getCustomerWishlist()
    {
        $wishlist = $this->getData('customer_wishlist');
        if (is_null($wishlist)) {
            $wishlist = false;
            if ($customer = $this->getCustomerSession()->getCustomer()) {
                $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
            }
            $this->setData('customer_wishlist', $wishlist);
        }
        return $wishlist;
    }
    
    /**
     * Retrieve current quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckoutSession()->getQuote();
    }
    
    public function init()
    {
        /**
         * If user try do checkout, reset shipiing and payment data
         */
        if ($this->getCheckoutSession()->getCheckoutState() !== Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN) {
        	$this->getQuote()
        		->removeAllAddresses()
        		->removePayment();
            $this->getCheckoutSession()->resetCheckout();
        }
        
        if (!$this->getQuote()->hasItems()) {
        	$this->getQuote()->getShippingAddress()
        		->setCollectShippingRates(false)
        		->removeAllShippingRates();
        }
        
        return $this;
    }
    
    /**
     * Add products 
     *
     * @param   int $productId
     * @param   int $qty
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProduct($product, $qty=1)
    {
        if ($product->getId() && $product->isVisibleInCatalog()) {
            if (!$product->isInStock()) {
                $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
                Mage::throwException('This product is out of stock');
            }
            
            switch ($product->getTypeId()) {
                case Mage_Catalog_Model_Product::TYPE_SIMPLE:
                    $this->_addSimpleProduct($product, $qty);
                    break;
                case Mage_Catalog_Model_Product::TYPE_CONFIGURABLE_SUPER:
                    $this->_addConfigurableProduct($product, $qty);
                    break;
                case Mage_Catalog_Model_Product::TYPE_GROUPED_SUPER:
                    $this->_addGroupedProduct($product, $qty);
                    break;
                default:
                    Mage::throwException('Indefinite product type');
                    break;
            }
        }
        else {
            Mage::throwException('Product do not exist');
        }
        
        $this->getCheckoutSession()->setLastAddedProductId($product->getId());
        return $this;
    }
    
    /**
     * Adding simple product to shopping cart
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   int $qty
     * @return  Mage_Checkout_Model_Cart
     */
    protected function _addSimpleProduct(Mage_Catalog_Model_Product $product, $qty)
    {
        if (!$this->_setProductQuoteQty($product, $qty)) {
            $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
            Mage::throwException('Requested quantity is not available');
        }
        
        $this->getQuote()->addCatalogProduct($product);
        return $this;
    }
    
    /**
     * Adding grouped product to cart
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Checkout_Model_Cart
     */
    protected function _addGroupedProduct(Mage_Catalog_Model_Product $product)
    {
        $groupedProducts = $product->getGroupedProducts();

        if(!is_array($groupedProducts) || empty($groupedProducts)) {
            $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
            $this->getCheckoutSession()->setUseNotice(true);
            Mage::throwException('Please specify the product option(s)');
        }
        
        foreach($product->getSuperGroupProductsLoaded() as $productLink) {
            if(isset($groupedProducts[$productLink->getLinkedProductId()])) {
                $qty =  $groupedProducts[$productLink->getLinkedProductId()];
                if (!empty($qty)) {
                    $subProduct = Mage::getModel('catalog/product')
                        ->load($productLink->getLinkedProductId())
                        ->setSuperProduct($product);
                        
                    if (!$subProduct->isInStock() || !$this->_setProductQuoteQty($subProduct, $qty)) {
                        $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
                        Mage::throwException('Requested quantity is not available');
                    }
                    $this->getQuote()->addCatalogProduct($subProduct);
                }
            }
        }
        return $this;
    }
    
    /**
     * Adding configurable product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Checkout_Model_Cart
     */
    protected function _addConfigurableProduct(Mage_Catalog_Model_Product $product, $qty=1)
    {
        $subProductId = $product->getSuperLinkIdByOptions($product->getConfiguredAttributes());
        if($subProductId) {
            $subProduct = Mage::getModel('catalog/product')
                ->load($subProductId)
                ->setSuperProduct($product);
        
            if (!$subProduct->isInStock()) {
                $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
                Mage::throwException('This product is out of stock');
            }
            
            if (!$this->_setProductQuoteQty($subProduct, $qty)) {
                $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
                Mage::throwException('Requested quantity is not available');
            }
            
            $this->getQuote()->addCatalogProduct($subProduct);
        } 
        else {
            $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
            $this->getCheckoutSession()->setUseNotice(true);
            Mage::throwException('Please specify the product option(s)');
        }
        return $this;
    }
    
    /**
     * Initialize product qty
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   int $qty
     * @param   bool $replace if we use true - the $qty will be used as new value
     * @return  bool
     */
    protected function _setProductQuoteQty(Mage_Catalog_Model_Product $product, $qty, $replace = false)
    {
        $res = false;
        if ($product->getQtyIsDecimal()) {
            $qty = (float) $qty;
        }
        else {
            $qty = (int) $qty;
        }
        $qty = $qty>0 ? $qty : 1;
        
        if ($item = $this->getQuote()->getItemByProduct($product)) {
            $itemQty = $item->getQty();
            $newQty  = $replace ? $qty : $itemQty+$qty;
            
            if ($newQty <= $product->getQty()) {
                $product->setQuoteQty($newQty);
                $res = true;
            }
        }
        else {
            if ($qty <= $product->getQty()) {
                $product->setQuoteQty($qty);
                $res = true;
            }
        }
        return $res;
    }
    
    /**
     * Adding products to cart by ids
     *
     * @param   array $productIds
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProductsByIds($productIds)
    {
        $allAvailable = true;
        $allAdded     = true;
        
        foreach ($productIds as $productId) {
        	$product = Mage::getModel('catalog/product')
        	   ->load($productId);
            if ($product->getId() && $product->isVisibleInCatalog() && $product->isInStock()) {
                if ($this->_setProductQuoteQty($product, 1)) {
                    $this->getQuote()->addCatalogProduct($product);
                }
                else {
                    $allAdded = false;
                }
            }
            else {
                $allAvailable = false;
            }
        }
        if (!$allAvailable) {
            $this->getCheckoutSession()->addError(__('Some of the products you requested are not available'));
        }
        if (!$allAdded) {
            $this->getCheckoutSession()->addError(__('Some of the products you requested are not available in desired quantity'));
        }
        return $this;
    }
    
    /**
     * Update cart items
     *
     * @param   array $data
     * @return  Mage_Checkout_Model_Cart
     */
    public function updateItems($data)
    {
        foreach ($data as $itemId => $itemInfo) {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }
            
        	if (!empty($itemInfo['remove'])) {
        	    $this->removeItem($itemId);
        	    continue;
        	}
        	
        	if (!empty($itemInfo['wishlist'])) {
        	    $this->moveItemToWishlist($itemId);
        	    continue;
        	}
        	
        	if ($item->getProduct()->getQtyIsDecimal()) {
        	    $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
        	}
        	else {
        	    $qty = isset($itemInfo['qty']) ? (int) $itemInfo['qty'] : false;
        	}
        	
        	if ($qty > 0) {
        	    $item->setQty($qty);
        	}
        }
        return $this;
    }
    
    /**
     * Remove item from cart
     *
     * @param   int $itemId
     * @return  Mage_Checkout_Model_Cart
     */
    public function removeItem($itemId)
    {
        $this->getQuote()->removeItem($itemId);
        return $this;
    }
    
    /**
     * Move cart item to wishlist
     *
     * @param   int $itemId
     * @return  Mage_Checkout_Model_Cart
     */
    public function moveItemToWishlist($itemId)
    {
        if ($wishlist = $this->getCustomerWishlist()) {
            if ($item = $this->getQuote()->getItemById($itemId)) {
                $productId = $item->getProductId();
                if ($item->getSuperProductId()) {
                    $productId = $item->getSuperProductId();
                }
                $wishlist->addNewItem($productId)
                    ->save();
                $this->removeItem($itemId);
            }
        }
        return $this;
    }
    
    public function isValidItemsQty()
    {
        foreach ($this->getQuote()->getAllItems() as $item) {
        	if ($item->getQty() > $item->getProduct()->getQty()) {
        	    return false;
        	}
        }
        return true;
    }
    
    /**
     * Save cart
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function save()
    {
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->collectTotals()
            ->save();
        $this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
        return $this;
    }
}