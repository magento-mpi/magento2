  <?php

class Frontend_Checkout_Login_Downlodalbe extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->modelProduct = $this->getModel('frontend/product');
        $this->modelShoppingCart = $this->getModel('frontend/shoppingcart');
        $this->modelCheckout = $this->getModel('frontend/checkout');
        $this->setUiNamespace();
    }

    /**
     * Test frontend guest checkout
     * MAGE-37:Performs Guest checkout of Simple product
     */
    function testGuestDownlodalbePositive()
    {
        // Test Dara
        $paramArray = array (
            //product data
            'baseUrl' => 'http://kq.varien.com/builds/ee-nightly/current/websites/smoke',
            //'categoryName' => 'st-subcat',
            'categoryName' => 'SL-Category/Base',
            'productName' => 'Downlodable Product - Base',
            'downloadOptions' => array (
                                    '1' => 'file sample',
                                    '2' => 'linko sample',
                                    ),
            'qty' => 1,
            //checkout data
            'checkoutMethod' => 'Login',
            'shippingMethod' => 'Flat',
            'paymentMethod' => 'Check / Money order',
            //customer data
            'firstName' => 'Guest',
            'lastName' => 'User',
            'email' => 'stu1@varien.com',
            'password' => '123123',
            'company' => 'AT Company',
            'street1' => 'street1',
            'street2' => 'street2',
            'city' => 'AT City',
            'country' => 'United States',
            'region' => 'Texas',
            'postcode' => '900034',
            'telephone' => '5555555',
            'fax' => '5555556'
        );

        //Test Flow
        if ($this->modelProduct->doOpen($paramArray)) {
            if ($this->modelProduct->placeToCart($paramArray)) {
                $this->modelShoppingCart->proceedCheckout();
                $this->modelCheckout->doCheckout($paramArray);
            } else {
                $this->setVerificationErrors('Product was not placed to cart');
            }
        } else {
            $this->setVerificationErrors('Product could not be opened');
        }
    }
}
