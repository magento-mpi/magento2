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
class Enterprise_Mage_Order_Helper extends Core_Mage_Order_Helper
{
    /**
     * Add gift options
     *
     * @param array $giftOptions
     */
    public function addGiftMessage(array $giftOptions)
    {
        if (array_key_exists('entire_order', $giftOptions)) {
            $this->fillFieldset($giftOptions['entire_order'], 'gift_options_for_order');
        }
        if (array_key_exists('individual_item', $giftOptions)) {
            foreach ($giftOptions['individual_item'] as $options) {
                    if (is_array($options) && isset($options['sku_product'])) {
                        $this->addParameter('sku', $options['sku_product']);
                        $this->clickControl('link', 'gift_options', FALSE);
                        $this->waitForAjax();
                        unset($options['sku_product']);
                        $this->fillFieldset($options, 'gift_options');
                        $this->clickButton('ok', FALSE);
                        $this->pleaseWait();
                    }
            }
        }
    }

    /**
     * Verifies gift options
     *
     * @param array $giftOptions
     */
    public function verifyGiftOptions($giftOptions)
    {
        if (array_key_exists('entire_order', $giftOptions['gift_messages'])) {
            $this->verifyForm($giftOptions['gift_messages']['entire_order']);
        }
        if (array_key_exists('individual_item', $giftOptions['gift_messages'])) {
            foreach ($giftOptions['gift_messages']['individual_item'] as $options) {
                    if (is_array($options) && isset($options['sku_product'])) {
                        $this->addParameter('sku', $options['sku_product']);
                        $this->clickControl('link', 'gift_options', FALSE);
                        $this->waitForAjax();
                        $this->verifyForm($options, null, array('sku_product'));
                        $this->clickButton('ok', FALSE);
                        $this->pleaseWait();
                    }
            }
        }
    }

    /**
     * @param array $orderData
     */
    public function verifyGiftOptionsDisabled($orderData)
    {
        if(array_key_exists('products_to_add', $orderData)) {
            foreach ($orderData['products_to_add'] as $options) {
                if (is_array($options) && isset($options['filter_sku'])) {
                    $this->addParameter('sku', $options['filter_sku']);
                    $this->assertFalse($this->controlIsPresent('link', 'gift_options'));
                }
            }
        }
        $this->assertFalse($this->controlIsPresent('fieldset', 'gift_options_for_order'));
    }
}