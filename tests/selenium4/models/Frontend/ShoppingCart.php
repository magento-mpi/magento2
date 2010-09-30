<?php
/**
 * Frontend_shopping_cart model
 *
 * @author Magento Inc.
 */
class Model_Frontend_ShoppingCart extends Model_Frontend
{
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();
    }

    /*
     * Start checkout process
     * @params isMultiple - if set, start multiShippingCheckout instead of ordinal one
     * 
     */
    public function  proceedCheckout($isMultiple = false)
    {
        $this->printDebug('proceedCheckout started...');
       //Proceed to checkout
        if ($isMultiple) {
            $this->printDebug('Starting multipleShipping checkout');
            $this->clickAndWait($this->getUiElement("/frontend/pages/shopping_cart/links/multipleShippingCheckout"));
        } else {
            $this->printDebug('Starting ordinal checkout');
            $this->clickAndWait($this->getUiElement("/frontend/pages/shopping_cart/buttons/proceedToCheckout"));
        }
        $this->printDebug('proceedCheckout finished');
    }


}
