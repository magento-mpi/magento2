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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Checkout_Helper extends Mage_Selenium_TestCase
{

    /**
     * Select Checkout Method(Onepage Checkout)
     *
     * @param array $methodName guest|register|login
     */
    public function frontSelectCheckoutMethod($method = 'guest')
    {
        if (is_string($method)) {
            $method = $this->loadData($method);
        }
        $checkoutType = (isset($method['checkout_method'])) ? $method['checkout_method'] : null;
        $page = $this->getCurrentLocationUimapPage();
        $set = $page->findFieldset('checkout_method');
        $xpath = $set->getXpath();

        $this->waitForElement($xpath . "[contains(@class,'active')]");

        switch ($checkoutType) {
            case 'guest':
                $this->fillForm(array('checkout_as_guest' => 'Yes'));
                $this->click($set->findButton('continue'));
                break;
            case 'register':
                $this->fillForm(array('register' => 'Yes'));
                $this->click($set->findButton('continue'));
                break;
            case 'login':
                if (isset($method['additional_data'])) {
                    $this->fillForm($method['additional_data']);
                }
                $billingSetXpath = $page->findFieldset('billing_information')->getXpath();
                $this->click($set->findButton('login'));
                $this->waitForElement(array(self::xpathErrorMessage, self::xpathValidationMessage,
                    $billingSetXpath . "[contains(@class,'active')]"));
                break;
            default:
                $this->click($set->findButton('continue'));
                break;
        }
    }

    /**
     * The way to ship the order
     *
     * @param array|string $shippingMethod
     * @param bool         $validate
     *
     */
    public function frontSelectShippingMethod($shippingMethod, $validate = TRUE)
    {
        $setXpath = $this->_getControlXpath('fieldset', 'shipping_method') . "[contains(@class,'active')]";
        $this->waitForElement($setXpath);
        if (is_string($shippingMethod)) {
            $shippingMethod = $this->loadData($shippingMethod);
        }
        if (array_key_exists('shipping_service', $shippingMethod) &&
                array_key_exists('shipping_method', $shippingMethod)) {
            $this->addParameter('shipService', $shippingMethod['shipping_service']);
            $this->addParameter('shipMethod', $shippingMethod['shipping_method']);
            if ($this->errorMessage('ship_method_unavailable')) {
                if ($validate) {
                    $this->fail('This shipping method is currently unavailable.');
                }
            } else {
                $this->clickControl('radiobutton', 'ship_method', FALSE);
                if (array_key_exists('add_gift_options', $shippingMethod)) {
                    $this->frontAddGiftMessage($shippingMethod['add_gift_options']);
                }
                $this->clickButton('continue', FALSE);
                $this->pleaseWait();
            }
        }
    }

    /**
     * Adding gift message for entire order of each item
     *
     * @param array|string $giftOptions
     *
     */
    public function frontAddGiftMessage($giftOptions)
    {
        if (is_string($giftOptions)) {
            $giftOptions = $this->loadData($giftOptions);
        }
        if (array_key_exists('entire_order', $giftOptions)) {
            $this->fillForm($giftOptions['entire_order']);
        }
        if (array_key_exists('individual_items', $giftOptions)) {
            $this->fillForm(array('gift_option_for_individual_items' => 'Yes'));
            foreach ($giftOptions['individual_items'] as $clue => $dataset) {
                if (isset($dataset['product_name'])) {
                    $this->addParameter('productName', $dataset['product_name']);
                    $this->fillForm($dataset);
                }
            }
        }
    }

    /**
     * Selecting payment method
     *
     * @param array $paymentMethod
     * @param bool  $validate
     *
     */
    public function frontSelectPaymentMethod($paymentMethod, $validate = TRUE)
    {
        $setXpath = $this->_getControlXpath('fieldset', 'payment_method') . "[contains(@class,'active')]";
        $this->waitForElement($setXpath);
        if ($validate) {
            $this->assertFalse($this->errorMessage('no_payment'), 'No Payment Information Required');
        }
        if (is_string($paymentMethod)) {
            $paymentMethod = $this->loadData($paymentMethod);
        }
        $payment = (isset($paymentMethod['payment_method'])) ? $paymentMethod['payment_method'] : NULL;
        $card = (isset($paymentMethod['payment_info'])) ? $paymentMethod['payment_info'] : NULL;
        if ($payment) {
            $this->addParameter('paymentTitle', $payment);
            $xpath = $this->_getControlXpath('radiobutton', 'check_payment_method');
            $this->click($xpath);
            $this->pleaseWait();
            if ($card) {
                $paymentId = $this->getAttribute($xpath . '/@value');
                $this->addParameter('paymentId', $paymentId);
                $this->fillForm($card, 'order_payment_method');
                $this->clickButton('continue', FALSE);
                $this->frontValidate3dSecure();
            }
        }
    }

    /**
     * Enters code to centinel iframe in case it appears.
     */
    public function frontValidate3dSecure($password = '1234')
    {
        $xpath = $this->_getControlXpath('fieldset', 'payment_method') . "[contains(@class,'active')]";
        $this->waitForElementNotPresent($xpath);
        $xpath = $this->_getControlXpath('fieldset', '3d_secure_card_validation');
        if ($this->isElementPresent($xpath)) {
            $xpath = $this->_getControlXpath('field', '3d_password');
            $this->waitForElement($xpath);
            $this->type($xpath, $password);
            $this->clickButton('3d_submit', FALSE);
            $this->waitForElementNotPresent($xpath);
            $this->pleaseWait();
        }
    }

}

?>
