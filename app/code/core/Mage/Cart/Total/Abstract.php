<?php

abstract class Mage_Cart_Total_Abstract
{
    protected $_cart = null;
    
    public function __construct($cart)
    {
        $this->_cart = $cart;
    }
}