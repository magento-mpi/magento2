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
     * Perform Checkout as a Guest from FrontEnd
     * @param - array wirh expecteded values:
     *       productUrl
     *       qty"firstName
     *       lastName
     *       company
     *       email
     *       street1
     *       street2
     *       city
     *       "country
     *       region
     *       postcode
     *       telephone
     *       fax
     */
    public function guestCheckout($params) {

        //Open ProductPage, place one to ShoppingCart, Press "Proceed to Checkout"
        $this->startCheckout($params);

        //Select "...as Guest"
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/inputs/asGuest"));
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/buttons/continue"));

        // Fill billing address tab
        $this->fillBillingTab($params);

        //Press Continue
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/buttons/continue"));
        $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/elements/pleaseWait"));
         
        //Perform rest of Checkout steps
        $this->shippingMethodPaymentPlaceOrderSteps($params);
    }

    /**
     * Perform Checkout witg Sign In from FrontEnd
     * @param - array wirh expecteded values:
     *       password
     *       productUrl
     *       qty"firstName
     *       lastName
     *       company
     *       email
     *       street1
     *       street2
     *       city
     *       "country
     *       region
     *       postcode
     *       telephone
     *       fax
     */
    public function signInCheckout($params) {

        //Open ProductPage, place one to ShoppingCart, Press "Proceed to Checkout"
        $this->startCheckout($params);

        //Select "...as Guest"
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/inputs/register"));
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/buttons/continue"));

        // Fill billing address tab
        $this->fillBillingTab($params);
        //Specify password with confirmation
        $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/password"),$params["password"]);
        $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/confirm"),$params["password"]);

        //Press Continue
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/buttons/continue"));
        $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/elements/pleaseWait"));
        $alert ='';


        if ($this->isAlertPresent()) {
                $this->storeAlert($alert);
                $this->setVerificationErrors("Check 2: BillingInfo tab could not be saved. Customer email already exists ?");
        } else {
            //Perform rest of Checkout steps
            $this->shippingMethodPaymentPlaceOrderSteps($params);
        }
    }

    /**
     * Perform Checkout with login from FrontEnd
     * @param - array wirh expecteded values:
     *       password
     *       email
     */
    public function loginCheckout($params) {

        //Open ProductPage, place one to ShoppingCart, Press "Proceed to Checkout"
        $this->startCheckout($params);

        //Select "...as login"
        $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/inputs/loginEmail"),$params["email"]);
        $this->type($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/inputs/password"),$params["password"]);
        $this->clickAndWait($this->getUiElement("frontend/pages/onePageCheckout/tabs/checkoutMethod/buttons/login"));
        $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/elements/pleaseWait"));

        // Fill billing address tab
        if ($this->waitForElement($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/elements/tabLoaded"), 5)) {
            $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/inputs/use_for_shipping"));
        };

        //Press Continue
        $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/billingAddress/buttons/continue"));

        //Perform rest of Checkout steps
        $this->shippingMethodPaymentPlaceOrderSteps($params);
    }

    /* Test-specific utilitary function
     *
     */

    /*
     * Open product page, place one to ShoppingCart, Proceed to Checkout
     * @params - array with expected values of:
     * productUrl
     * qty
     */
    function startCheckout($params)
    {
        //Open product page
        $this->open($params["productUrl"]);

        // Place product to the cart
        $this->type($this->getUiElement("frontend/pages/product/inputs/qty"),$params["qty"]);
        $this->clickAndWait($this->getUiElement("frontend/pages/product/buttons/addToCart"));

        //Proceed to checkout
        $this->clickAndWait($this->getUiElement("frontend/pages/shoppingCart/buttons/proceedToCheckout"));
    }


    /*
     * Sequentally fill all fields in the BillingInformation Checkout Step
     * @params - array with expected values of:
     * firstName
     * lastName
     * company
     * email
     * street1
     * street2
     * city
     * postcode
     * telephone
     * fax
     * country
     * region
     */
    function fillBillingTab($params)
    {
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
    }

    /*
     *  Sequentally fill all fields in the ShippingMethod, PaymentInfo, OrderReview Checkout Steps
     *  used free shipping and check/money order options
     */
    function shippingMethodPaymentPlaceOrderSteps($params)
    {
         //Fill Shipping Method Tab
         if (!$this->waitForElement($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/inputs/freeShipping"),10)) {
            $this->setVerificationErrors("Check 3: no Free shipping method available.");
            return false;
         }
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/inputs/freeShipping"));
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/buttons/continue"));
         $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/shippingMethod/elements/pleaseWait"));

         //Fill Payment Information Tab
         if (!$this->waitForElement($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/inputs/check"),10)) {
            $this->setVerificationErrors("Check 4: 'Check / MoneyOrder' payment method is not available.");
            return false;
         }
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/inputs/check"));
         $this->click($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/buttons/continue"));
         $this->pleaseWaitStep($this->getUiElement("frontend/pages/onePageCheckout/tabs/paymentInfo/elements/pleaseWait"));

         //Place Order
         $this->clickAndWait($this->getUiElement("frontend/pages/onePageCheckout/tabs/orderReview/buttons/placeOrder"));

         // Check for success message
         if (!$this->waitForElement($this->getUiElement("frontend/pages/onePageCheckout/messages/orderPlaced"),10)) {
            $this->setVerificationErrors('Check 1: no "Order Placed"  message');
            return false;
         }
         return true;
    }

    /*
     * wait for appearance and disappearence of "Loading Next step..." block during frontend checkout
     * Since all steps has unique block id, its should be passed as parameter
     * @param - ID of "$element-please-wait" block
     */
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

