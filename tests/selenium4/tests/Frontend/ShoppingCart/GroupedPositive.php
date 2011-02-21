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
     * Test Grouped product placing to the Shopping Cart (Positive)
     * MAGE-27:Placing Grouped product to Shopping Cart
     */
    function testAddGroupedPositive()
    {
        // Test Dara
        $paramArray = array (
            'categoryName'          => 'st-subcat',
            'productName'           => 'Grouped Product 01.Required Fields',
            'associatedProducts'    => array (
                                        'Virtual Product 01.Required Fields'        => '3',
                                        'Downloadable Product 01.Required Fields'   => '2',
                                        'Simple Product 01.Required Fields'         => '5',
                                        )
        );

        //Test Flow
        if ($this->modelProduct->doOpen($paramArray)) {
            $this->modelProduct->placeToCart($paramArray);
        } else {
            $this->setVerificationErrors("Check1 : Product ${paramArray['productName']} could not be opened");
        }
    }
}
