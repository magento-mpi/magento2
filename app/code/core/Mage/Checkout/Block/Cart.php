<?php

class Mage_Checkout_Block_Cart extends Mage_Checkout_Block_Cart_Abstract 
{
    protected $_totals;
    
    public function chooseTemplate()
    {
        if ($this->getQuote()->hasItems()) {
            $this->setTemplate($this->getCartTemplate());
        } else {
            $this->setTemplate($this->getEmptyTemplate());
        }
    }
    
    public function getItems()
    {
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter($this->_qtyFilter, 'qty');
        $itemsFilter->addFilter($this->_priceFilter, 'price');
        $itemsFilter->addFilter($this->_priceFilter, 'row_total');
        $items = $this->getQuote()->getAllItems();
        $this->_addProductToItems($items);
        return $itemsFilter->filter($items);
    }
    
    protected function _addProductToItems($items)
    {
        /**
         * !!! Now Product adding in Mage_Sales_Model_Quote_Address_Total_Subtotal
         */
        /*$productIds = array();
        foreach ($items as $item) {
        	$productIds[$item->getProductId()] = $item;
        }
        
        if (!empty($productIds)) {
            $productCollection = Mage::getResourceSingleton('catalog/product_collection')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect('description')
                ->addIdFilter(array_keys($productIds))
                ->load();
            foreach ($productCollection as $product) {
            	$productIds[$product->getId()]->setProduct($product);
            }
        }*/
        
        return $this;
    }
    
    public function getTotals()
    {
        $totalsFilter = new Varien_Filter_Object_Grid();
        $totalsFilter->addFilter($this->_priceFilter, 'value');
        return $totalsFilter->filter($this->getTotalsCache());
    }
    
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $this->_totals = $this->getQuote()->getTotals();
        }
        return $this->_totals;
    }
    
    public function getGiftcertCode()
    {
        return $this->getQuote()->getGiftcertCode();
    }
    
    public function isWishlistActive()
    {
        return $this->_isWishlistActive;
    }
    
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }
    
    public function getMultiShippingUrl()
    {
        return $this->getUrl('checkout/multishipping', array('_secure'=>true));
    }
    
    public function getPaypalUrl()
    {
        return $this->getUrl('checkout/paypal');
    }
    
    public function getGoogleUrl()
    {
        return $this->getUrl('checkout/google');
    }
    
    public function getItemDeleteUrl(Mage_Sales_Model_Quote_Item $item)
    {
    	return $this->getUrl('checkout/cart/delete', array('id'=>$item->getId()));
    }
}