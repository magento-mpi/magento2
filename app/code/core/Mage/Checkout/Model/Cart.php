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
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Model_Cart extends Varien_Object
{
    protected $_cacheKey;
    protected $_cacheData;

    protected function _getResource()
    {
        return Mage::getResourceSingleton('checkout/cart');
    }

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieve custome session model
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function getItems()
    {
        if (!$this->getQuote()->getId()) {
            return array();
        }
        return $this->getQuote()->getItemsCollection();
    }


    public function getItemsCount()
    {
        if (!$this->hasItemsCount()) {
            $this->setItemsCount($this->getQuote()->getItemsCollection()->getSize());
        }
        return $this->getData('items_count');
    }

    public function getItemsQty()
    {
        if (!$this->hasItemsQty()) {
            Varien_Profiler::start('TEST1: '.__METHOD__);
            $quote = $this->getQuote();
            Varien_Profiler::stop('TEST1: '.__METHOD__);
            Varien_Profiler::start('TEST2: '.__METHOD__);
            $items = $quote->getItemsCollection();
            Varien_Profiler::stop('TEST2: '.__METHOD__);
            $qty = 0;
            Varien_Profiler::start('TEST3: '.__METHOD__);
            foreach ($items as $item) {
                $qty += $item->getQty();
            }
            $this->setItemsQty($qty);
            Varien_Profiler::stop('TEST3: '.__METHOD__);
        }
        return $this->getData('items_qty');
    }

    /**
     * Retrieve array of cart product ids
     *
     * @return array
     */
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
        $this->getQuote()->setCheckoutMethod('');

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

        foreach ($this->getQuote()->getMessages() as $message) {
            if ($message) {
                $this->getCheckoutSession()->addMessage($message);
            }
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
            switch ($product->getTypeId()) {
                case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
                    $this->_addSimpleProduct($product, $qty);
                    break;
                case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                    $this->_addConfigurableProduct($product, $qty);
                    break;
                case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                    $this->_addGroupedProduct($product, $qty);
                    break;
                default:
                    Mage::throwException(Mage::helper('checkout')->__('Indefinite product type'));
                    break;
            }
        }
        else {
            Mage::throwException(Mage::helper('checkout')->__('Product does not exist'));
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
        $item = $this->getQuote()->addCatalogProduct($product, $qty);
        if ($item->getHasError()) {
            $this->setLastQuoteMessage($item->getQuoteMessage());
            Mage::throwException($item->getMessage());
        }
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
            Mage::throwException(Mage::helper('checkout')->__('Please specify the product option(s)'));
        }

        $added = false;
        foreach($product->getTypeInstance()->getAssociatedProducts() as $subProduct) {
            if(isset($groupedProducts[$subProduct->getId()])) {
                $qty =  $groupedProducts[$subProduct->getId()];
                if (!empty($qty)) {
                    $subProduct->setSuperProduct($product);
                    $this->getQuote()->addCatalogProduct($subProduct, $qty);
                    $added = true;
                }
            }
        }
        if (!$added) {
            Mage::throwException(Mage::helper('checkout')->__('Please specify the product(s) quantity'));
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
        if($product->getConfiguredAttributes()) {
            $subProduct = $product->getTypeInstance()->getProductByAttributes(
                $product->getConfiguredAttributes()
            );
        } else {
            $subProduct = false;
        }

        if($subProduct) {
            $subProduct->setSuperProduct($product);
            $item = $this->getQuote()->addCatalogProduct($subProduct, $qty);
            if ($item->getHasError()) {
                $this->setLastQuoteMessage($item->getQuoteMessage());
                Mage::throwException($item->getMessage());
            }
        }
        else {
            $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
            $this->getCheckoutSession()->setUseNotice(true);
            Mage::throwException(Mage::helper('checkout')->__('Please specify the- product option(s)'));
        }
        return $this;
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

        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $product = Mage::getModel('catalog/product')
                ->load($productId);
                if ($product->getId() && $product->isVisibleInCatalog()) {
                    try {
                        $this->getQuote()->addCatalogProduct($product);
                    }
                    catch (Exception $e){
                        $allAdded = false;
                    }
                }
                else {
                    $allAvailable = false;
                }
            }

            if (!$allAvailable) {
                $this->getCheckoutSession()->addError(
                Mage::helper('checkout')->__('Some of the products you requested are unavailable')
                );
            }
            if (!$allAdded) {
                $this->getCheckoutSession()->addError(
                Mage::helper('checkout')->__('Some of the products you requested are not available in the desired quantity')
                );
            }
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

        	if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty']=='0')) {
        	    $this->removeItem($itemId);
        	    continue;
        	}

        	if (!empty($itemInfo['wishlist'])) {
        	    $this->moveItemToWishlist($itemId);
        	    continue;
        	}

            $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
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

    /**
     * Save cart
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function save()
    {
        $address = $this->getQuote()->getShippingAddress();
        $total = $address->getGrandTotal();
        $address->setCollectShippingRates(true);
        $this->getQuote()->collectTotals();
        if ($total!=$address->getGrandTotal()) {
            $this->getQuote()->save();
        }
        $this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
        return $this;
    }

    public function truncate()
    {
        foreach ($this->getQuote()->getItemsCollection() as $item) {
            $item->isDeleted(true);
        }
    }
}