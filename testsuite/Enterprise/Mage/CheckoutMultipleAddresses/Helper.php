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
class Enterprise_Mage_CheckoutMultipleAddresses_Helper extends Core_Mage_CheckoutMultipleAddresses_Helper
{
    /**
     * @param array $giftOptions
     * @param string $header
     */
    public function addGiftOptions(array $giftOptions, $header)
    {
        $this->addParameter('addressHeader', $header);
        $this->verifyGiftOptionsAvailability($giftOptions);
        $this->fillCheckbox('add_gift_options', 'Yes');
        $forItems = (isset($giftOptions['individual_items'])) ? $giftOptions['individual_items'] : array();
        $forOrder = (isset($giftOptions['entire_order'])) ? $giftOptions['entire_order'] : array();
        if (isset($giftOptions['send_gift_receipt'])) {
            $this->fillCheckbox('send_gift_receipt', $giftOptions['send_gift_receipt']);
        }
        if (isset($giftOptions['add_printed_card'])) {
            $this->fillCheckbox('add_printed_card', $giftOptions['add_printed_card']);
        }
        foreach ($forItems as $data) {
            $productName = (isset($data['product_name'])) ? $data['product_name'] : '';
            $this->addParameter('productName', $productName);
            $this->fillCheckbox('gift_option_for_individual_items', 'Yes');
            $giftWrapping = (isset($data['gift_wrapping_for_item'])) ? $data['gift_wrapping_for_item'] : '';
            $giftMessage = (isset($data['gift_message'])) ? $data['gift_message'] : array();
            if ($giftWrapping) {
                $this->fillDropdown('gift_wrapping_for_item', $giftWrapping);
            }
            if ($giftMessage) {
                $this->clickControl('link', 'gift_message_for_item', false);
                $this->fillFieldset($giftMessage, 'shipping_method_form');
            }
        }
        if ($forOrder) {
            $this->fillCheckbox('gift_option_for_the_entire_order', 'Yes');
            $giftWrapping = (isset($forOrder['gift_wrapping_for_order'])) ? $forOrder['gift_wrapping_for_order'] : '';
            $giftMessage = (isset($forOrder['gift_message'])) ? $forOrder['gift_message'] : array();
            if ($giftWrapping) {
                $this->fillDropdown('gift_wrapping_for_order', $giftWrapping);
            }
            if ($giftMessage) {
                $this->clickControl('link', 'gift_message_for_order', false);
                $this->fillFieldset($giftMessage, 'shipping_method_form');
            }
        }
    }

    /**
     * @param array $shippingData
     */
    public function verifyGiftOptions(array $shippingData)
    {
    }

    /**
     * @param array $giftOptions
     */
    public function verifyGiftOptionsAvailability(array $giftOptions)
    {
        $this->assertTrue($this->controlIsPresent('checkbox', 'add_gift_options'),
            'It\'s impossible to add gift options to order');
        //Data
        $forItemsData = (isset($giftOptions['individual_items'])) ? $giftOptions['individual_items'] : array();
        $forOrder = (isset($giftOptions['entire_order'])) ? true : false;
        $forOrderWrapping = (isset($giftOptions['entire_order']['gift_wrapping_for_order'])) ? true : false;
        $forOrderMessage = (isset($giftOptions['entire_order']['gift_message'])) ? true : false;
        $forItems = (isset($giftOptions['individual_items'])) ? true : false;
        $giftReceipt = (isset($giftOptions['send_gift_receipt'])) ? true : false;
        $printedCard = (isset($giftOptions['add_printed_card'])) ? true : false;
        //Verifying
        $this->verifyControlAvailability('checkbox', 'send_gift_receipt', $giftReceipt,
            'send Gift Receipt');
        $this->verifyControlAvailability('checkbox', 'add_printed_card', $printedCard,
            'add Printed Card to Order');
        //For Entire Order
        $this->verifyControlAvailability('checkbox', 'gift_option_for_the_entire_order', $forOrder,
            'add gift options to Entire Order');
        $this->verifyControlAvailability('dropdown', 'gift_wrapping_for_order', $forOrderWrapping,
            'add gift wrapping to Entire Order');
        $this->verifyControlAvailability('link', 'gift_message_for_order', $forOrderMessage,
            'add gift message to Entire Order');
        //For Individual Items
        $this->verifyControlAvailability('checkbox', 'gift_option_for_individual_items', $forItems,
            'add gift options to Individual Items');
        foreach ($forItemsData as $data) {
            $productName = (isset($data['product_name'])) ? $data['product_name'] : '';
            $this->addParameter('productName', $productName);
            $forItemWrapping = (isset($data['gift_wrapping_for_item'])) ? true : false;
            $forItemMessage = (isset($data['gift_message'])) ? true : false;
            $this->verifyControlAvailability('link', 'gift_message_for_item', $forItemMessage,
                'add gift message to ' . $productName);
            $this->verifyControlAvailability('dropdown', 'gift_wrapping_for_item', $forItemWrapping,
                'add gift wrapping to ' . $productName);
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * @param string $controlType
     * @param string $controlName
     * @param bool $availability
     * @param string $message
     */
    public function verifyControlAvailability($controlType, $controlName, $availability, $message)
    {
        $isAvailable = $this->controlIsPresent($controlType, $controlName);
        if ($availability && !$isAvailable) {
            $this->addVerificationMessage('It\'s impossible to ' . $message);
        } elseif (!$availability && $isAvailable) {
            $this->addVerificationMessage('It\'s possible to ' . $message);
        }
    }
}