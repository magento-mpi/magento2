<?php

class Frontend_ShoppingCart_AddGroupedNegative extends TestCaseAbstract
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
     * Test Grouped product placing to the Shopping Cart (Negative)
     * MAGE-29:Placing Grouped product to Shopping Cart. Neg
     */
    function testAddGroupedNegative()
    {
        // Test Dara
        $paramArray = array (
            'baseUrl' => 'http://kq.varien.com/builds/ee-nightly/current/websites/smoke',
            'categoryName' => 'SL-Category/Base',
            'productName' => 'Grouped Product - Base',
            'associatedProducts' => array (
                                    'A Product - A' => '1',
                                    'A Product - B' => '0',
                                    )
        );

        //Test Flow
        if ($this->modelProduct->doOpen($paramArray)) {
            if (!$this->modelProduct->placeToCart($paramArray)) {
               // Not placed. Remove errors from stack...
               $errorsStackWasChanged = true;
               while ($errorsStackWasChanged) :
               {
                    $error = $this->getLastVerificationError();
                    $errorsStackWasChanged = false;
                    $this->printDebug($error);
                    if (strpos($error, "doesn't appeared in the shopping cart list")) {
                        $this->popVerificationErrors();
                        $errorsStackWasChanged = true;
                    }
                    if (strpos($error, "No success message")) {
                        $this->popVerificationErrors();
                        $errorsStackWasChanged = true;
                    }
               };
               endwhile;
            } else {
              //Product placed
              $this->setVerificationErrors("Check 3 : Product " . $paramArray['productName'] . " was placed to the Shopping Cart");
            }
        } else {
            $this->setVerificationErrors("Check 2 : Product " . $paramArray['productName'] . " could not be opened");
        }
    }
}
