<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutOnePage
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class Enterprise2_Mage_for OnePageCheckout
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_CheckoutOnePage_Helper extends Core_Mage_CheckoutOnePage_Helper
{
    /**
     * @return string
     */
    public function submitOnePageCheckoutOrder()
    {
        $errorMessageXpath = $this->getBasicXpathMessagesExcludeCurrent('error');
        $waitConditions = array($this->_getMessageXpath('success_checkout'), $errorMessageXpath,
                                $this->_getMessageXpath('general_validation'));
        $this->clickButton('place_order', false);
        $this->waitForElementOrAlert($waitConditions);
        $this->verifyNotPresetAlert();
        //@TODO
        //Remove workaround for getting fails,
        //not skipping tests if payment methods are inaccessible
        $this->paypalHelper()->verifyMagentoPayPalErrors();
        $this->validatePage('onepage_checkout_success');
        $xpath = $this->_getControlXpath('link', 'order_number');
        if ($this->isElementPresent($xpath)) {
            return $this->getText($xpath);
        }

        return preg_replace('/[^0-9]/', '', $this->getText("//p[contains(text(),'Your order')]"));
    }
}