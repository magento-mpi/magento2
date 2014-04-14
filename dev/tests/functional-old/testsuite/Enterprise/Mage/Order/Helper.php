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
     * @param string $wrappingName
     * @param string $type
     */
    public function verifyGiftWrapping($wrappingName, $type = 'order')
    {
        if (!$this->controlIsVisible('dropdown', $type . '_gift_wrapping_design')) {
            $actual = $this->getControlAttribute('pageelement', $type . '_gift_wrapping', 'text');
        } else {
            $actual = $this->getControlAttribute('dropdown', $type . '_gift_wrapping_design', 'selectedLabel');
        }
        if ($actual !== $wrappingName) {
            $this->addVerificationMessage(
                sprintf(
                    "Gift Wrapping for the %s is wrong: ('%s' != '%s')",
                    ($type == 'order' ? 'Entire Order' : 'Order Item'),
                    $wrappingName,
                    $actual
                )
            );
        }
    }

    /**
     * Verify gift Options
     *
     * @param array $giftMessages
     */
    public function verifyGiftOptions(array $giftMessages)
    {
        if (isset($giftMessages['entire_order']['order_gift_wrapping'])) {
            $this->verifyGiftWrapping($giftMessages['entire_order']['order_gift_wrapping']);
        }
        if (isset($giftMessages['entire_order'])) {
            $this->verifyForm($giftMessages['entire_order'], null, array('order_gift_wrapping_design'));
        }
        if (isset($giftMessages['individual'])) {
            foreach ($giftMessages['individual'] as $options) {
                $this->addParameter('sku', $options['sku_product']);
                $this->clickControl('link', 'gift_options', false);
                $this->waitForControlVisible('fieldset', 'gift_options');
                if (isset($options['item_gift_wrapping'])) {
                    $this->verifyGiftWrapping($options['item_gift_wrapping'], 'item');
                }
                $this->verifyForm($options, null, array('sku_product', 'item_gift_wrapping_design'));
                $this->clickButton('ok', false);
                $this->pleaseWait();
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