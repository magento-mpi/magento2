<?php

class Mage_Cart_Cart
{
    protected $_cartId = null;
    protected $_cartModel = null;
    protected $_itemsCache = null;
    protected $_totals = null;

    public function __construct($cartId=null)
    {
        $this->_cartModel = Mage::getResourceModel('cart', 'cart');
        
        if (is_null($cartId)) {
            $cartId = $this->_cartModel->getCustomerCartId();
        }
        $this->_cartId = $cartId;
    }
    
    public function reset()
    {
        $this->_itemsCache = null;
        $this->_totals->reset();
    }

    public function getCart()
    {
        return $this->_cartModel->getCart($this->_cartId);
    }
    
    public function getItems()
    {
        if (empty($this->_itemsCache)) {
            $this->_itemsCache = $this->_cartModel->getItems($this->_cartId);
        }
        return $this->_itemsCache;
    }
    
    public function getTotals()
    {
        if (is_null($this->_totals)) {
            $this->_totals = new Mage_Cart_Total_Collection($this);
            $this->_totals->collect();
        }
        return $this->_totals;
    }
    
    public function addProduct($productId, $qty=1)
    {
        $this->_cartModel->addProduct($productId, $qty);
    }
    
    public function update($cartData)
    {
        $this->_cartModel->update($cartData, $this->_cartId);
    }
}