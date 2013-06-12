<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutMultipleAddresses
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
        $this->_addGiftOptionsForItems($forItems);
        $this->_addGiftOptionsForOrder($forOrder);
    }

    /**
     * Add gift options for items
     *
     * @param $forItems
     */
    protected function _addGiftOptionsForItems($forItems)
    {
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
                if (!$this->controlIsEditable('field', 'item_gift_message_from')) {
                    $this->clickControl('link', 'add_item_gift_message', false);
                }
                $this->fillFieldset($giftMessage, 'shipping_method_form');
            }
        }
    }

    /**
     * Add gift options for order
     * @param $forOrder
     */
    protected function _addGiftOptionsForOrder($forOrder)
    {
        if ($forOrder) {
            $this->fillCheckbox('gift_option_for_order', 'Yes');
            $giftWrapping = (isset($forOrder['order_gift_wrapping_design'])) ? $forOrder['order_gift_wrapping_design'] : '';
            $giftMessage = (isset($forOrder['gift_message'])) ? $forOrder['gift_message'] : array();
            if ($giftWrapping) {
                $this->fillDropdown('order_gift_wrapping_design', $giftWrapping);
            }
            if ($giftMessage) {
                if (!$this->controlIsEditable('field', 'order_gift_message_from')) {
                    $this->clickControl('link', 'add_order_gift_message', false);
                }
                $this->fillFieldset($giftMessage, 'shipping_method_form');
            }
        }
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
        $forOrderWrapping = (isset($giftOptions['entire_order']['order_gift_wrapping_design'])) ? true : false;
        $forOrderMessage = (isset($giftOptions['entire_order']['gift_message'])) ? true : false;
        $forItems = (isset($giftOptions['individual_items'])) ? true : false;
        $giftReceipt = (isset($giftOptions['send_gift_receipt'])) ? true : false;
        $printedCard = (isset($giftOptions['add_printed_card'])) ? true : false;
        //Verifying
        $this->verifyControlAvailability('checkbox', 'send_gift_receipt', $giftReceipt, 'send Gift Receipt');
        $this->verifyControlAvailability('checkbox', 'add_printed_card', $printedCard, 'add Printed Card to Order');
        //For Entire Order
        $this->verifyControlAvailability('checkbox', 'gift_option_for_order', $forOrder,
            'add gift options to Entire Order');
        $this->verifyControlAvailability('dropdown', 'order_gift_wrapping_design', $forOrderWrapping,
            'add gift wrapping to Entire Order');
        $this->verifyControlAvailability('link', 'add_order_gift_message', $forOrderMessage,
            'add gift message to Entire Order');
        //For Individual Items
        $this->verifyControlAvailability('checkbox', 'gift_option_for_item', $forItems,
            'add gift options to Individual Items');
        $this->_verifyGiftOptionsForItems($forItemsData);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Verifies gift options for items
     * @param $forItemsData
     */
    protected function _verifyGiftOptionsForItems($forItemsData)
    {
        foreach ($forItemsData as $data) {
            $productName = (isset($data['product_name'])) ? $data['product_name'] : '';
            $this->addParameter('productName', $productName);
            $forItemWrapping = (isset($data['item_gift_wrapping_design'])) ? true : false;
            $forItemMessage = (isset($data['gift_message'])) ? true : false;
            $this->verifyControlAvailability('link', 'add_item_gift_message', $forItemMessage,
                'add gift message to ' . $productName);
            $this->verifyControlAvailability('dropdown', 'item_gift_wrapping_design', $forItemWrapping,
                'add gift wrapping to ' . $productName);
        }
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

    /**
     * @return array
     */
    public function getOrderDataForAddress()
    {
        $addressData = array();
        if ($this->getParameter('addressHeader') != 'Other items in your order') {
            //Get order shipping method data
            $shipping = $this->getControlAttribute('pageelement', 'shipping_method', 'text');
            list($serviceAndMethod, $price) = explode(')', $shipping);
            list($service, $method) = explode('(', $serviceAndMethod);
            $addressData['shipping']['shipping_service'] = trim($service);
            $addressData['shipping']['shipping_method'] = trim($method);
            $addressData['shipping']['price'] = trim($price);
        }
        //Get order products data
        $products = $this->shoppingCartHelper()->getProductInfoInTable();
        foreach ($products as &$product) {
            $temp = explode('Gift Wrapping Design :', $product['product_name']);
            if (count($temp) > 1) {
                $temp = array_map('trim', $temp);
                list($product['product_name'], $product['gift_wrapping']) = $temp;
            }
            $product['product_qty'] = $product['qty'];
            unset($product['qty']);
        }
        $addressData['products'] = $products;
        //Get order total data
        $addressData['total'] = $this->shoppingCartHelper()->getOrderPriceData();
        return $addressData;
    }
}