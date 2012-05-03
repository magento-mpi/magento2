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
class Enterprise_Mage_CheckoutMultipleAddresses_Helper extends Mage_Selenium_TestCase
{
    /**
     * @param array $giftOptions
     */
    public function addGiftOptions(array $giftOptions)
    {
        $this->fillForm($giftOptions);
        if (isset($giftOptions['individual_items'])) {
            $this->fillForm(array('add_gift_options'     => 'Yes',
                                  'gift_option_for_item' => 'Yes'));
            foreach ($giftOptions['individual_items'] as $key => $data) {
                $this->addParameter('productName', $key);
                $this->fillForm($data);
                if (isset($data['gift_message'])) {
                    $this->clickControl('link', 'gift_message_for_item', false);
                    $this->fillForm($data['gift_message']);
                }
            }
        }
        if (isset($giftOptions['entire_order'])) {
            $this->fillForm(array('add_gift_options'      => 'Yes',
                                  'gift_option_for_order' => 'Yes'));
            $this->fillForm($giftOptions['entire_order']);
            if (isset($giftOptions['entire_order']['gift_message'])) {
                $this->clickControl('link', 'gift_message_for_order', false);
                $this->fillForm($giftOptions['entire_order']['gift_message']);
            }
        }
    }
}