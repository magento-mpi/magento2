<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class Core_Mage_for OnePageCheckout
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CheckoutOnePage_Helper extends Core_Mage_CheckoutOnePage_Helper
{
    /**
     * @return string
     */
    public function submitOnePageCheckoutOrder()
    {
        $waitConditions = array($this->_getMessageXpath('success_checkout'), $this->_getMessageXpath('general_error'),
                                $this->_getMessageXpath('general_validation'));
        $this->clickButton('place_order', false);
        $this->waitForElementOrAlert($waitConditions);
        $error = $this->errorMessage();
        $validation = $this->validationMessage();
        if (!$this->verifyNotPresetAlert() || $error['success'] || $validation['success']) {
            $message = self::messagesToString($this->getMessagesOnPage());
            //@TODO
            //Uncomment and remove workaround for getting fails,
            //not skipping tests if payment methods are inaccessible
            $this->skipTestWithScreenshot($message);
            //$this->fail($message);
        }
        $this->validatePage('onepage_checkout_success');
        $xpath = $this->_getControlXpath('link', 'order_number');
        if ($this->isElementPresent($xpath)) {
            return $this->getText($xpath);
        }

        return preg_replace('/[^0-9]/', '', $this->getText("//*[contains(text(),'Your order')]"));
    }
}