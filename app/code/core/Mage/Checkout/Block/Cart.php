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
        $itemsFilter->addFilter($this->_priceFilter, 'price');
        $itemsFilter->addFilter($this->_priceFilter, 'row_total');
        $items = $this->getQuote()->getAllItems();
        return $itemsFilter->filter($items);
    }
    
    public function getItemsCount()
    {
        $count = $this->getData('items_count');
        if (is_null($count)) {
            $count = count($this->getQuote()->getAllItems());
            $this->setData('items_count', $count);
        }
        return $count;
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
    
    public function getItemUrl($item)
    {
        if ($superProduct = $item->getSuperProduct()) {
            return $superProduct->getProductUrl();
        }
        
        if ($product = $item->getProduct()) {
            return $product->getProductUrl();
        }
        return '';
    }
    
    public function getItemImageUrl($item)
    {
        if ($superProduct = $item->getSuperProduct()) {
            return $superProduct->getThumbnailUrl();
        }
        
        if ($product = $item->getProduct()) {
            return $product->getThumbnailUrl();
        }
        return '';
    }
    
    public function getItemName($item)
    {
        $superProduct = $item->getSuperProduct();
        if ($superProduct && $superProduct->isConfigurable()) {
            return $superProduct->getName();
        }
        
        if ($product = $item->getProduct()) {
            return $product->getName();
        }
        return $item->getName();
    }
    
    public function getItemDescription($item)
    {
        if ($superProduct = $item->getSuperProduct()) {
            if ($superProduct->isConfigurable()) {
                return $this->getLayout()->createBlock('checkout/cart_item_super')->setProduct($item->getProduct())->toHtml();
            }
        }
        return '';
    }
    
    public function getItemQty($item)
    {
        $qty = $item->getQty();
        if ($product = $item->getProduct()) {
            if ($product->getQtyIsDecimal()) {
                return number_format($qty, 2, null, '');
            }
        }
        return number_format($qty, 0, null, '');
    }
    
    public function getItemIsInStock($item)
    {
        if ($item->getProduct()->isSaleable()) {
            if ($item->getProduct()->getQty()>=$item->getQty()) {
                return true;
            }
        }
        return false;
    }
}