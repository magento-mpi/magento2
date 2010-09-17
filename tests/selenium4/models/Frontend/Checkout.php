<?php
/**
 * Frontend_checkout model
 *
 * @author Magento Inc.
 */
class Model_Frontend_Checkout extends Model_Frontend {
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->userData = Core::getEnvConfig('backend/user');
    }


    /**
     * Perform Checkout as a Guest from FrontEnd
     * @param - array wirh expecteded values:
     *       productUrl
     *       qty'firstName
     *       lastName
     *       company
     *       email
     *       street1
     *       street2
     *       city
     *       'country
     *       region
     *       postcode
     *       telephone
     *       fax
     */
    public function guestCheckout($params)
    {
        $this->printDebug('guestCheckout started...');
        //Open ProductPage, place one to ShoppingCart, Press 'Proceed to Checkout'
        $this->startCheckout($params);

        $this->setUiNamespace('frontend/pages/onePageCheckout/tabs/');

        //Select '...as Guest'
        $this->click($this->getUiElement('checkoutMethod/inputs/asGuest'));
        $this->click($this->getUiElement('checkoutMethod/buttons/continue'));

        // Fill billing address tab
        $this->fillBillingTab($params);

        $this->setUiNamespace('frontend/pages/onePageCheckout/tabs/');
        
        //Press Continue
        $this->click($this->getUiElement('billingAddress/buttons/continue'));
        $this->pleaseWaitStep($this->getUiElement('billingAddress/elements/pleaseWait'));

        //Perform rest of Checkout steps
        $this->shippingMethodPaymentPlaceOrderSteps($params);
        $this->printDebug('guestCheckout finished');
    }

    /* Test-specific utilitary functions
     *
     */

    /*
     * Open product page, place one to ShoppingCart, Proceed to Checkout
     * @params - array with expected values of:
     * productUrl
     * qty
     * isMultiple - if set, start multiShippingCheckout instead of ordinal one
     */
    function startCheckout($params, $isMultiple = false)
    {
        $this->printDebug('startCheckout started...');
        //Open product page
        $this->open($params['productUrl']);

        $this->setUiNamespace('/frontend/pages/product/');

        // Place product to the cart
        $this->type($this->getUiElement('inputs/qty'),$params['qty']);
        $this->clickAndWait($this->getUiElement('buttons/addToCart'));
        $this->printInfo('Placing ' . $params['productUrl'] . ' to cart');

        //Proceed to checkout
        if ($isMultiple) {
            $this->printInfo('Starting multipleShipping checkout');
            $this->clickAndWait($this->getUiElement('/frontend/pages/shoppingCart/links/multipleShippingCheckout'));
        } else {
            $this->printInfo('Starting ordinal checkout');
            $this->clickAndWait($this->getUiElement('/frontend/pages/shoppingCart/buttons/proceedToCheckout'));
        }
        $this->printDebug('startCheckout finished');
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
        $this->printDebug('fillBillingTab() started...');

        $this->setUiNamespace('frontend/pages/onePageCheckout/tabs/billingAddress/');

        $this->type($this->getUiElement('inputs/firstName'),$params['firstName']);
        $this->type($this->getUiElement('inputs/lastName'),$params['lastName']);
        $this->type($this->getUiElement('inputs/company'),$params['company']);
        $this->type($this->getUiElement('inputs/email'),$params['email']);
        $this->type($this->getUiElement('inputs/street1'),$params['street1']);
        $this->type($this->getUiElement('inputs/street2'),$params['street2']);
        $this->type($this->getUiElement('inputs/city'),$params['city']);
        $this->type($this->getUiElement('inputs/postcode'),$params['postcode']);
        $this->type($this->getUiElement('inputs/telephone'),$params['telephone']);
        $this->type($this->getUiElement('inputs/fax'),$params['fax']);
        //Country and Region
        $this->selectCountry($this->getUiElement('selectors/country'),$params['country']);
        $this->selectRegion($this->getUiElement('selectors/region'),$params['region']);
        //Use billing address for shipping
        $this->click($this->getUiElement('inputs/use_for_shipping'));
        $this->printDebug('fillBillingTab finished...');
    }

    /*
     *  Sequentally fill all fields in the ShippingMethod, PaymentInfo, OrderReview Checkout Steps
     *  used free shipping and check/money order options
     */
    function shippingMethodPaymentPlaceOrderSteps($params)
    {
         $this->printDebug('shippingMethodPaymentPlaceOrderSteps started...');

         $this->setUiNamespace('/frontend/pages/onePageCheckout/tabs/');

         //Fill Shipping Method Tab
         if (!$this->waitForElement($this->getUiElement('shippingMethod/inputs/freeShipping'),10)) {
            $this->setVerificationErrors('Check 3: no Free shipping method available.');
            return false;
         }
         $this->printInfo('Using Free shipping');
         $this->click($this->getUiElement('shippingMethod/inputs/freeShipping'));
         $this->click($this->getUiElement('shippingMethod/buttons/continue'));
         $this->pleaseWaitStep($this->getUiElement('shippingMethod/elements/pleaseWait'));

         //Fill Payment Information Tab
         if (!$this->waitForElement($this->getUiElement('paymentInfo/inputs/check'),10)) {
            $this->setVerificationErrors("Check 4: 'Check / MoneyOrder' payment method is not available.");
            return false;
         }
         $this->printInfo('Using Check/Money Order method');
         $this->click($this->getUiElement('paymentInfo/inputs/check'));
         $this->click($this->getUiElement('paymentInfo/buttons/continue'));
         $this->pleaseWaitStep($this->getUiElement('paymentInfo/elements/pleaseWait'));

         //Place Order
         $this->clickAndWait($this->getUiElement('orderReview/buttons/placeOrder'));

         // Check for success message
         if (!$this->waitForElement($this->getUiElement('/frontend/pages/onePageCheckout/messages/orderPlaced'),10)) {
            $this->setVerificationErrors("Check 1: no 'Order Placed'  message");
            return false;
         }

         $this->printInfo('Placing order');
         $this->printDebug('shippingMethodPaymentPlaceOrderSteps finished');
         return true;
    }

    /*
     * wait for appearance and disappearence of 'Loading Next step...' block during frontend checkout
     * Since all steps has unique block id, its should be passed as parameter
     * @param - ID of '$element-please-wait' block
     */
    public function pleaseWaitStep($element)
    {
        $this->printDebug('pleaseWaitStep started :' . $element);
        //*[@id='billing-please-wait' and contains(@style,'display: none')]
        // await for appear and disappear 'Please wait' animated gif...
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
        $this->printDebug('pleaseWaitStep finished :');
    }
}
