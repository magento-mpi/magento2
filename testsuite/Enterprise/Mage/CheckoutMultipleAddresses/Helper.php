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
     */
    public function addGiftOptions(array $giftOptions)
    {
        if (isset($giftOptions['individual_items'])) {
            $this->fillCheckbox('add_gift_options', 'Yes');
            $this->fillCheckbox('gift_option_for_individual_items', 'Yes');
            foreach ($giftOptions['individual_items'] as $data) {
                $productName = (isset($data['product_name'])) ? $data['product_name'] : '';
                $this->addParameter('productName', $productName);
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
        }
        if (isset($giftOptions['entire_order'])) {
            $data = $giftOptions['entire_order'];
            $this->fillCheckbox('add_gift_options', 'Yes');
            $this->fillCheckbox('gift_option_for_the_entire_order', 'Yes');
            $giftWrapping = (isset($data['gift_wrapping_for_order'])) ? $data['gift_wrapping_for_order'] : '';
            $giftMessage = (isset($data['gift_message'])) ? $data['gift_message'] : array();
            if ($giftWrapping) {
                $this->fillDropdown('gift_wrapping_for_order', $giftWrapping);
            }
            if ($giftMessage) {
                $this->clickControl('link', 'gift_message_for_order', false);
                $this->fillFieldset($giftMessage, 'shipping_method_form');
            }
        }
    }
}