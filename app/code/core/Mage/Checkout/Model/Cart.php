<?php
/**
 * Shoping cart model
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
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
    
    /**
     * Retrieve current quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckoutSession()->getQuote();
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
            
            $qty = (int) $qty;
            $qty = $qty>0 ? $qty : 1;
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
            Mage::throwException('Specify product option, please');
        }
        
        foreach($product->getSuperGroupProductsLoaded() as $productLink) {
            if(isset($groupedProducts[$productLink->getLinkedProductId()])) {
                $qty =  $groupedProducts[$productLink->getLinkedProductId()];
                if (!empty($qty)) {
                    $qty = (int) $qty;
                    $qty = $qty>0 ? $qty : 1;
                    
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
            Mage::throwException('Specify product option, please');
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
    
    
    public function removeItem($itemId)
    {
        $this->getQuote()->removeItem($itemId);
        return $this;
    }
    
    public function save()
    {
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->save();
        $this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
        return $this;
    }
}