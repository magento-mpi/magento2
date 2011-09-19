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
     * Generates array of strings for filling customer's billing/shipping form
     * @param string $charsType :alnum:, :alpha:, :digit:, :lower:, :upper:, :punct:
     * @param string $addrType Gets two values: 'billing' and 'shipping'.
     *                         Default is 'billing'
     * @param int $symNum min = 5, default value = 32
     * @return array
     * @uses DataGenerator::generate()
     * @see DataGenerator::generate()
     */
    public function frontCustomerAddressGenerator($charsType, $addrType = 'billing', $symNum = 32, $required = FALSE)
    {
        $type = array(':alnum:', ':alpha:', ':digit:', ':lower:', ':upper:', ':punct:');
        if (array_search($charsType, $type) === FALSE
                || ($addrType != 'billing' && $addrType != 'shipping')
                || $symNum < 5 || !is_int($symNum)) {
            throw new Exception('Incorrect parameters');
        } else {
            if ($required == TRUE) {
                return array(
                    $addrType . '_address_select'          => 'New Address',
                    $addrType . '_prefix'                  => '%noValue%',
                    $addrType . '_first_name'              => $this->generate('string', $symNum, $charsType),
                    $addrType . '_middle_name'             => '%noValue%',
                    $addrType . '_last_name'               => $this->generate('string', $symNum, $charsType),
                    $addrType . '_suffix'                  => '%noValue%',
                    $addrType . '_company'                 => '%noValue%',
                    $addrType . '_street_address_1'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_2'        => '%noValue%',
                    $addrType . '_region'                  => '%noValue%',
                    $addrType . '_city'                    => $this->generate('string', $symNum, $charsType),
                    $addrType . '_zip_code'                => $this->generate('string', $symNum, $charsType),
                    $addrType . '_telephone'               => $this->generate('string', $symNum, $charsType),
                    $addrType . '_fax'                     => '%noValue%'
                );
            } else {
                return array(
                    $addrType . '_address_select'          => 'New Address',
                    $addrType . '_prefix'                  => $this->generate('string', $symNum, $charsType),
                    $addrType . '_first_name'              => $this->generate('string', $symNum, $charsType),
                    $addrType . '_middle_name'             => $this->generate('string', $symNum, $charsType),
                    $addrType . '_last_name'               => $this->generate('string', $symNum, $charsType),
                    $addrType . '_suffix'                  => $this->generate('string', $symNum, $charsType),
                    $addrType . '_company'                 => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_1'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_2'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_region'                  => $this->generate('string', $symNum, $charsType),
                    $addrType . '_city'                    => $this->generate('string', $symNum, $charsType),
                    $addrType . '_zip_code'                => $this->generate('string', $symNum, $charsType),
                    $addrType . '_telephone'               => $this->generate('string', $symNum, $charsType),
                    $addrType . '_fax'                     => $this->generate('string', $symNum, $charsType)
                );
            }
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
                if (preg_match('/product_name/', $clue)) {
                    $this->addParameter('productName', $dataset);
                } else {
                    $this->fillForm($dataset);
                }
            }
        }
    }














}
?>
