<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
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
class Enterprise_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Choose custom options and additional products
     *
     * @param array $dataForBuy
     */
    public function frontAddProductToCart($dataForBuy = null)
    {
        $customize = $this->controlIsPresent('button', 'customize_and_add_to_cart');
        $customizeFieldset = $this->_getControlXpath('fieldset', 'customize_product_info');
        if ($customize) {
            $productInfoFieldset = $this->_getControlXpath('fieldset', 'product_info');
            $this->clickButton('customize_and_add_to_cart', false);
            $this->waitForElementVisible($customizeFieldset);
            $this->waitForElement($productInfoFieldset . "/parent::*[@style='display: none;']");
        }
        parent::frontAddProductToCart($dataForBuy);
    }

    /**
     * fillProductInfo
     *
     * @param array $productData
     * @param string $productType
     */
    public function fillProductInfo(array $productData, $productType = 'simple')
    {
        parent::fillProductInfo($productData, $productType);
        if ($productType == 'giftcard') {
            $arrayKey = 'prices_gift_card_amounts';
            if (array_key_exists($arrayKey, $productData) && is_array($productData[$arrayKey])) {
                $this->openTab('prices');
                foreach ($productData[$arrayKey] as $value) {
                    $this->addGiftCardAmount($value);
                }
            }
            $this->fillProductTab($productData, 'giftcardinfo');
        }
    }

    /**
     * Add Gift Card Amount
     *
     * @param array $giftCardData
     */
    public function addGiftCardAmount(array $giftCardData)
    {
        $rowNumber = count($this->getControlElements('fieldset', 'prices_gift_card_amounts', null, false));
        $this->addParameter('giftCardId', $rowNumber);
        $this->clickButton('add_gift_card_amount', false);
        $this->waitForAjax();
        $this->fillForm($giftCardData, 'prices');
    }

    /**
     * Verify product info
     *
     * @param array $productData
     * @param array $skipElements
     */
    public function verifyProductInfo(array $productData, $skipElements = array())
    {
        parent::verifyProductInfo($productData, $skipElements);
        // Verify gift cards amounts
        if (array_key_exists('prices_gift_card_amounts', $productData)) {
            $this->openTab('prices');
            $this->verifyGiftCardAmounts($productData['prices_gift_card_amounts']);
        }
    }

    /**
     * Verify GiftCardAmounts
     *
     * @param array $giftCardData
     *
     * @return boolean
     */
    public function verifyGiftCardAmounts(array $giftCardData)
    {
        $rowQty = count($this->getControlElements('fieldset', 'prices_gift_card_amounts', null, false));
        $needCount = count($giftCardData);
        if ($needCount != $rowQty) {
            $this->addVerificationMessage(
                'Product must contain ' . $needCount . ' gift card amount(s), but contains ' . $rowQty);
            return false;
        }
        $index = $rowQty - 1;
        foreach ($giftCardData as $value) {
            $this->addParameter('giftCardId', $index);
            $this->verifyForm($value, 'prices');
            --$index;
        }
        return true;
    }

    /**
     * Verify Gift Card info on frontend
     *
     * @param array $productData
     */
    public function frontVerifyGiftCardInfo(array $productData)
    {
        $this->frontOpenProduct($productData['general_name']);
        $xpathArray = $this->getCustomOptionsXpathesGiftCards($productData);
        foreach ($xpathArray as $fieldName => $data) {
            if (is_string($data)) {
                if (!$this->elementIsPresent($data)) {
                    $this->addVerificationMessage('Could not find element ' . $fieldName);
                }
            } else {
                foreach ($data as $optionData) {
                    if (is_string($optionData)) {
                        $this->_verifySingleElement($optionData);
                    } else {
                        foreach ($optionData as $x => $y) {
                            if (!preg_match('/xpath/', $x)) {
                                continue;
                            }
                            if (!$this->elementIsPresent($y)) {
                                $this->addVerificationMessage(
                                    'Could not find element type "' . $optionData['type'] . '" and title "'
                                    . $optionData['title'] . '"');
                            }
                        }
                    }
                }
            }
        }
        $this->_verifyGiftCardForProductInStock($productData);
        $this->assertEmptyVerificationErrors();
    }

    protected function _verifySingleElement($option)
    {
        if (!$this->elementIsPresent($option)) {
            $this->addVerificationMessage(
                'Could not find gift card amount ' . $this->getElement($option)->value() . '$');
        }
    }

    /**
     * Verifies gift card for products in stock
     *
     * @param $productData
     */
    protected function _verifyGiftCardForProductInStock($productData)
    {
        if ($productData['inventory_stock_availability'] == 'In Stock') {
            if ($productData['prices_gift_card_allow_open_amount'] == 'Yes') {
                $this->assertTrue($this->controlIsPresent('field', 'gift_card_open_amount'),
                    'There is no open amount field on the ' . $productData['general_name'] . 'page');
            }
            $this->assertTrue($this->controlIsPresent('field', 'gift_card_sender_name'),
                'There is no sender name field on the ' . $productData['general_name'] . 'page');
            $this->assertTrue($this->controlIsPresent('field', 'gift_card_sender_email'),
                'There is no sender email field on the ' . $productData['general_name'] . 'page');
            $this->assertTrue($this->controlIsPresent('field', 'gift_card_recipient_name'),
                'There is no recipient name field on the ' . $productData['general_name'] . 'page');
            $this->assertTrue($this->controlIsPresent('field', 'gift_card_recipient_email'),
                'There is no recipient email field on the ' . $productData['general_name'] . 'page');
            $this->assertTrue($this->controlIsPresent('field', 'gift_card_message'),
                'There is no message field on the ' . $productData['general_name'] . 'page');
        }
    }

    /**
     * Gets the xpathes for validation on frontend
     *
     * @param array $productData
     *
     * @return array
     */
    public function getCustomOptionsXpathesGiftCards(array $productData)
    {
        $xpathArray = array();
        $priceToCalc = '0';

        if (array_key_exists('prices_gift_card_amounts', $productData)
            && $productData['inventory_stock_availability'] == 'In Stock'
        ) {
            foreach ($productData['prices_gift_card_amounts'] as $key => $value) {
                $this->addParameter('giftCardAmount', $value['prices_gift_card_amount']);
                $xpathArray['prices_gift_card_amounts'][$key] = $this->_getControlXpath('dropdown', 'gift_card_amount');
            }
        }

        $avail =
            (isset($productData['inventory_stock_availability'])) ? $productData['inventory_stock_availability'] : null;
        $allowedQty =
            (isset($productData['inventory_min_allowed_qty'])) ? $productData['inventory_min_allowed_qty'] : null;
        $longDescription = (isset($productData['general_description'])) ? $productData['general_description'] : null;
        if ($longDescription) {
            $this->addParameter('longDescription', $longDescription);
            $xpathArray['Description'] = $this->_getControlXpath('pageelement', 'description');
        }
        $this->_defineAvailability($xpathArray, $avail);
        $xpathArray = $this->_formXpathForCustomOptions($productData, $allowedQty, $priceToCalc);
        return $xpathArray;
    }

    protected function _defineAvailability($xpathArray, $avail)
    {
        $avail = ($avail == 'In Stock') ? 'In stock' : 'Out of stock';
        if ($avail == 'Out of stock') {
            $this->addParameter('avail', $avail);
            $xpathArray['Availability'] = $this->_getControlXpath('pageelement', 'availability_param');
            return $xpathArray;
        }
    }

    /**
     * Form xpath array for custom options with gift cards
     *
     * @param $productData
     * @param $allowedQty
     * @param $priceToCalc
     * @return array
     */
    protected function _formXpathForCustomOptions($productData, $allowedQty, $priceToCalc)
    {
        $allowedQty = ($allowedQty == null) ? '1' : $allowedQty;
        $this->addParameter('price', $allowedQty);
        $xpathArray['Quantity'] = $this->_getControlXpath('pageelement', 'qty');
        $index = 0;
        foreach ($productData['custom_options_data'] as $value) {
            $title = $value['custom_options_general_title'];
            $optionType = $value['custom_options_general_input_type'];
            $xpathArray['custom_options']['option_' . $index]['title'] = $title;
            $xpathArray['custom_options']['option_' . $index]['type'] = $optionType;
            $this->addParameter('title', $title);
            if ($value['custom_options_general_input_type'] == 'Drop-down'
                || $value['custom_options_general_input_type'] == 'Multiple Select'
            ) {
                $someArr = $this->_formXpathForCustomOptionsRows($value, $priceToCalc, $index, 'custom_option_select');
                $xpathArray = array_merge_recursive($xpathArray, $someArr);
            } elseif ($value['custom_options_general_input_type'] == 'Radio Buttons'
                || $value['custom_options_general_input_type'] == 'Checkbox'
            ) {
                $someArr = $this->_formXpathForCustomOptionsRows($value, $priceToCalc, $index, 'custom_option_check');
                $xpathArray = array_merge_recursive($xpathArray, $someArr);
            } else {
                $someArr = $this->_formXpathesForFieldsArray($value, $index, $priceToCalc);
                $xpathArray = array_merge_recursive($xpathArray, $someArr);
            }
            $index++;
        }
        return $xpathArray;
    }
}