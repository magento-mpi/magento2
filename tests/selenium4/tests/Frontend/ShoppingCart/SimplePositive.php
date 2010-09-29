<?php

class Frontend_ShoppingCart_AddGroupedPositive extends TestCaseAbstract
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
     * Test frontend guest checkout
     */
    function testAddGroupedPositive()
    {
        // Test Dara
        $paramArray = array (
            'baseUrl' => 'http://kq.varien.com/builds/ee-nightly/current/websites/smoke',
            'categoryName' => 'SL-Category/Base',
            'productName' => 'Grouped Product - Base',
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
