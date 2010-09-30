<?php

class Frontend_ShoppingCart_AddVirtualPositive extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->modelProduct = $this->getModel('frontend/product');
        $this->modelShoppingCart = $this->getModel('frontend/shoppingcart');
        $this->setUiNamespace();
    }

    /**
     * Test frontend simple product placing to Shopping Cart
     * MAGE-26:Placing Simple product to Shopping Cart
     */
    function testAddGroupedPositive()
    {
        // Test Dara
        $paramArray = array (
            'baseUrl' => 'http://kq.varien.com/builds/ee-nightly/current/websites/smoke',
            'categoryName' => 'SL-Category/Base',
            'productName' => 'Virtual Product - Base',
            'qty' => 1,
        );

        //Test Flow
        if ($this->modelProduct->doOpen($paramArray)) {
            $this->modelProduct->placeToCart($paramArray);
        } else {
            $this->setVerificationErrors("Check1 : Product ${paramArray['productName']} could not be opened");
        }
    }
}
