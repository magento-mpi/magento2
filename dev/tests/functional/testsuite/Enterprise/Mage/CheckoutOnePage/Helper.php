<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Adding gift message(gift wrapping) for entire order of each item
     *
     * @param array $giftOptions
     *
     */
    public function frontAddGiftMessage(array $giftOptions)
    {
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
            $this->fillCheckbox('gift_option_for_item', 'Yes');
            $giftWrapping = (isset($data['item_gift_wrapping_design'])) ? $data['item_gift_wrapping_design'] : '';
            $giftMessage = (isset($data['gift_message'])) ? $data['gift_message'] : array();
            if ($giftWrapping) {
                $this->fillDropdown('item_gift_wrapping_design', $giftWrapping);
            }
            if ($giftMessage) {
                $this->clickControl('link', 'item_gift_message', false);
                $this->fillFieldset($giftMessage, 'shipping_method');
            }
        }
        if ($forOrder) {
            $this->fillCheckbox('gift_option_for_order', 'Yes');
            $giftWrapping =
                (isset($forOrder['order_gift_wrapping_design'])) ? $forOrder['order_gift_wrapping_design'] : '';
            $giftMessage = (isset($forOrder['gift_message'])) ? $forOrder['gift_message'] : array();
            if ($giftWrapping) {
                $this->fillDropdown('order_gift_wrapping_design', $giftWrapping);
            }
            if ($giftMessage) {
                $this->clickControl('link', 'order_gift_message', false);
                $this->fillFieldset($giftMessage, 'shipping_method');
            }
        }
    }

    /**
     * @param array $checkoutData
     */
    public function frontOrderReview(array $checkoutData)
    {
        parent::frontOrderReview($checkoutData);
        $itemsGiftWrapping = (isset($checkoutData['shipping_data']['add_gift_options']['individual_items'])) ?
            $checkoutData['shipping_data']['add_gift_options']['individual_items'] : array();

        if ($itemsGiftWrapping) {
            foreach($itemsGiftWrapping as $productData) {
                if (isset($productData['product_name']) && isset($productData['item_gift_wrapping_design'])) {
                    $this->addParameter('productName', $productData['product_name']);
                    $this->addParameter('giftWrapping', $productData['item_gift_wrapping_design']);
                    if (!$this->controlIsPresent('pageelement', 'product_gift_wrapping')) {
                        $this->addVerificationMessage('Gift Wrapping ' . $productData['item_gift_wrapping_design'] .
                                                      ' is absent for '. $productData['product_name']);
                    }
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}