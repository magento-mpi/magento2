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
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/buttons/continue"));
         //Fill billing address Tab
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/firstName"),$params["firstName"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/lastName"),$params["lastName"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/company"),$params["company"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/email"),$params["email"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/street1"),$params["street1"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/street2"),$params["street2"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/city"),$params["city"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/postcode"),$params["postcode"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/telephone"),$params["telephone"]);
         $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/fax"),$params["fax"]);
         //Country and Region
         $this->selectCountry($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/selectors/country"),$params["country"]);
         $this->selectRegion($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/selectors/region"),$params["region"]);
         //Use billing address for shipping
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/use_for_shipping"));
         //Press Continue
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/buttons/continue"));
         $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/elements/pleaseWait"));
         //Fill Shipping Method Tab
         $this->waitForElement($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/inputs/freeShipping"));
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/inputs/freeShipping"));
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/buttons/continue"));
         $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/elements/pleaseWait"));
         //Fill Payment Information Tab
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/inputs/check"));
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/buttons/continue"));
         $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/elements/pleaseWait"));
         //Place Order
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/orderReview/buttons/placeOrder"));
         $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/orderReview/elements/pleaseWait"));
        //$this->clickAndWait($this->getUiElement("frontend/pages/home/links/myAccount"));
    }

    public function pleaseWaitStep($element)
    {
        Core::debug("pleaseWaitStep started :" . $element);
        //*[@id='billing-please-wait' and contains(@style,'display: none')]
            // await for appear and disappear "Please wait" animated gif...
            for ($second = 0; ; $second++) {
                if ($second >= 2)  {
                    break;
                }
                try {
                    if (!$this->isElementPresent("//*[@id='" . $element . "' and contains(@style,'display: none')]")) {
                        break;
                    }
                } catch (Exception $e) {

                }
                sleep(1);
            }

            for ($second = 0; ; $second++) {
                if ($second >= 20)break;
                try {
                    if ($this->isElementPresent("//*[@id='" . $element . "' and contains(@style,'display: none')]")) {
                        break;
                    }
                } catch (Exception $e) {

                }
                sleep(1);
            }
            sleep(1);
        Core::debug("pleaseWaitStep finished :" . $element);
    }


}

