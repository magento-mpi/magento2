<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Order
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Verify gift Options
     *
     * @param array $giftMessages
     */
    public function verifyGiftOptions(array $giftMessages)
    {
        if (array_key_exists('entire_order', $giftMessages)) {
            if (array_key_exists('order_gift_wrapping_design', $giftMessages['entire_order'])
                && !$this->controlIsVisible('dropdown', 'order_gift_wrapping_design')
            ) {
                $wrapping = $giftMessages['entire_order']['order_gift_wrapping_design'];
                $actual = $this->getControlAttribute('pageelement', 'order_gift_wrapping', 'text');
                if ($actual !== $wrapping) {
                    $this->addVerificationMessage("Gift Wrapping for the Entire Order is wrong: ('"
                        . $wrapping . "' != '" . $actual . "')");
                }
                unset($giftMessages['entire_order']['order_gift_wrapping_design']);
            }
            $this->verifyForm($giftMessages['entire_order']);
        }
        if (array_key_exists('individual', $giftMessages)) {
            foreach ($giftMessages['individual'] as $options) {
                if (is_array($options) && isset($options['sku_product'])) {
                    $this->addParameter('sku', $options['sku_product']);
                    unset($options['sku_product']);
                    $this->clickControl('link', 'gift_options', false);
                    $this->waitForControlVisible('fieldset', 'gift_options');
                    if (array_key_exists('item_gift_wrapping_design', $options)
                        && !$this->controlIsVisible('dropdown', 'item_gift_wrapping_design')
                    ) {
                        $actual = $this->getControlAttribute('pageelement', 'item_gift_wrapping', 'text');
                        if ($actual !== $options['item_gift_wrapping_design']) {
                            $this->addVerificationMessage("Gift Wrapping for the Entire Order is wrong: ('"
                                . $options['item_gift_wrapping_design'] . "' != '" . $actual . "')");
                        }
                        unset($options['item_gift_wrapping_design']);
                    }
                    $this->verifyForm($options, null, array('sku_product'));
                    $this->clickButton('ok', false);
                    $this->pleaseWait();
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Verifies that gift options link is not available for items and order.
     * Fails test if any of the gift options is available.
     *
     * @param array $orderData
     */
    public function verifyGiftOptionsDisabled($orderData)
    {
        if (array_key_exists('products_to_add', $orderData)) {
            foreach ($orderData['products_to_add'] as $options) {
                if (!isset($options['filter_sku'])) {
                    continue;
                }
                $this->addParameter('sku', $options['filter_sku']);
                if ($this->controlIsPresent('link', 'gift_options')) {
                    $this->addVerificationMessage('Gift options is available for product ' . $options['filter_sku']);
                }
            }
        }
        $fieldSetElements = $this->_findUimapElement('fieldset', 'gift_options_for_order')->getFieldsetElements();
        foreach ($fieldSetElements as $elements) {
            foreach ($elements as $element => $elementXpath) {
                if ($this->elementIsPresent($elementXpath)) {
                    $this->addVerificationMessage("Gift options($element) is available for the order");
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}