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
 * Helper class Enterprise_Mage for OnePageCheckout
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CheckoutOnePage_Helper extends Core_Mage_CheckoutOnePage_Helper
{
    /**
     * Adding gift message(gift wrapping) for entire order of each item
     *
     * @param array $giftOptions
     */
    public function frontAddGiftOptions(array $giftOptions)
    {
        parent::frontAddGiftOptions($giftOptions);
        if (isset($giftOptions['send_gift_receipt'])) {
            $this->fillCheckbox('send_gift_receipt', $giftOptions['send_gift_receipt']);
        }
        if (isset($giftOptions['add_printed_card'])) {
            $this->fillCheckbox('add_printed_card', $giftOptions['add_printed_card']);
        }
    }

    /**
     * Add gift options for one product
     *
     * @param array $oneItemData
     */
    protected function _addGiftOptionsForItem(array $oneItemData)
    {
        parent::_addGiftOptionsForItem($oneItemData);
        $this->addParameter('productName', $oneItemData['product_name']);
        if (isset($oneItemData['item_gift_wrapping_design'])) {
            $this->fillCheckbox('gift_option_for_item', 'Yes');
            $this->fillDropdown('item_gift_wrapping_design', $oneItemData['item_gift_wrapping_design']);
        }
    }

    /**
     * Add gift options for order
     * @param array $forOrder
     */
    protected function _addGiftOptionsForOrder(array $forOrder)
    {
        parent::_addGiftOptionsForOrder($forOrder);
        if (isset($forOrder['order_gift_wrapping_design'])) {
            $this->fillCheckbox('gift_option_for_order', 'Yes');
            $this->fillDropdown('order_gift_wrapping_design', $forOrder['order_gift_wrapping_design']);
        }
    }
}