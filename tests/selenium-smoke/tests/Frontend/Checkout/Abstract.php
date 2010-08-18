<?php
/**
 * Abstract test class for Frontend module
 *
 * @author Magento Inc.
 */
abstract class Test_Frontend_Checkout_Abstract extends Test_Frontend_Abstract
{
    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    /**
     * Initialize the environment
     */
    public function  setUp() {
        parent::setUp();

        // Get test parameters
        $this->_baseurl = Core::getEnvConfig('frontend/baseUrl');
        $this->_email = Core::getEnvConfig('frontend/auth/email');
        $this->_password = Core::getEnvConfig('frontend/auth/password');
    }

    /**
     * Login to the FrontEnd
     *
     */
    public function guestCheckout($params) {
        //Open product page
        $this->open($params["productUrl"]);

        // Place product to the cart
        $this->type($this->getUiElement("frontend/pages/product/inputs/qty"),$params["qty"]);
        $this->clickAndWait($this->getUiElement("frontend/pages/product/buttons/addToCart"));
        
        //Proceed to checkout
        $this->clickAndWait($this->getUiElement("frontend/pages/shoppingCart/buttons/proceedToCheckout"));
        //Select "...as Guest"
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/inputs/asGuest"));
        $this->clickAndWait($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/buttons/continue"));

        //$this->clickAndWait($this->getUiElement("frontend/pages/home/links/myAccount"));
    }


}

