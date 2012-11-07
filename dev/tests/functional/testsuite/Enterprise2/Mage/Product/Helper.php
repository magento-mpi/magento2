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
 * @method Community2_Mage_Product_Helper helper(string $className)
 */
class Enterprise2_Mage_Product_Helper extends Enterprise_Mage_Product_Helper
{
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
     * @param array $GiftCardData
     */
    public function addGiftCardAmount(array $GiftCardData)
    {
        $rowNumber = count($this->getControlElements('fieldset', 'prices_gift_card_amounts', null, false));
        $this->addParameter('giftCardId', $rowNumber);
        $this->clickButton('add_gift_card_amount', false);
        $this->waitForAjax();
        $this->fillForm($GiftCardData, 'prices');
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
        $i = $rowQty - 1;
        foreach ($giftCardData as $value) {
            $this->addParameter('giftCardId', $i);
            $this->verifyForm($value, 'prices');
            --$i;
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
                        if (!$this->elementIsPresent($optionData)) {
                            $this->addVerificationMessage(
                                'Could not find gift card amount ' . $this->getElement($optionData)->value() . '$');
                        }
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
        $this->assertEmptyVerificationErrors();
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
        $avail = ($avail == 'In Stock') ? 'In stock' : 'Out of stock';
        if ($avail == 'Out of stock') {
            $this->addParameter('avail', $avail);
            $xpathArray['Availability'] = $this->_getControlXpath('pageelement', 'availability_param');
            return $xpathArray;
        }
        $allowedQty = ($allowedQty == null) ? '1' : $allowedQty;
        $this->addParameter('price', $allowedQty);
        $xpathArray['Quantity'] = $this->_getControlXpath('pageelement', 'qty');
        $i = 0;
        foreach ($productData['custom_options_data'] as $value) {
            $title = $value['custom_options_general_title'];
            $optionType = $value['custom_options_general_input_type'];
            $xpathArray['custom_options']['option_' . $i]['title'] = $title;
            $xpathArray['custom_options']['option_' . $i]['type'] = $optionType;
            $this->addParameter('title', $title);
            if ($value['custom_options_general_input_type'] == 'Drop-down'
                || $value['custom_options_general_input_type'] == 'Multiple Select'
            ) {
                $someArr = $this->_formXpathForCustomOptionsRows($value, $priceToCalc, $i, 'custom_option_select');
                $xpathArray = array_merge_recursive($xpathArray, $someArr);
            } elseif ($value['custom_options_general_input_type'] == 'Radio Buttons'
                      || $value['custom_options_general_input_type'] == 'Checkbox'
            ) {
                $someArr = $this->_formXpathForCustomOptionsRows($value, $priceToCalc, $i, 'custom_option_check');
                $xpathArray = array_merge_recursive($xpathArray, $someArr);
            } else {
                $someArr = $this->_formXpathesForFieldsArray($value, $i, $priceToCalc);
                $xpathArray = array_merge_recursive($xpathArray, $someArr);
            }
            $i++;
        }
        return $xpathArray;
    }

    /**
     * Change attribute set
     *
     * @param array $newAttributeSet
     */
    public function changeAttributeSet($newAttributeSet)
    {
        $this->helper('Community2/Mage/Product/Helper')->changeAttributeSet($newAttributeSet);
    }

    /**
     * Import custom options from existent product
     *
     * @param array $productData
     */
    public function importCustomOptions(array $productData)
    {
        $this->helper('Community2/Mage/Product/Helper')->importCustomOptions($productData);
    }

    /**
     * Delete all custom options
     */
    public function deleteAllCustomOptions()
    {
        $this->helper('Community2/Mage/Product/Helper')->deleteAllCustomOptions();
    }

    /**
     * Verify Custom Options
     *
     * @param array $customOptionData
     *
     * @return boolean
     */
    public function verifyCustomOption(array $customOptionData)
    {
        return $this->helper('Community2/Mage/Product/Helper')->verifyCustomOption($customOptionData);
    }

    /**
     * Get option id for selected row
     *
     * @param int $rowNum
     *
     * @return int
     */
    public function getOptionId($rowNum)
    {
        return $this->helper('Community2/Mage/Product/Helper')->getOptionId($rowNum);
    }

    /**
     * Create Product method using "Add Product" split button
     *
     * @param array $productData
     * @param string $productType
     * @param bool $isSave
     */
    public function createProduct(array $productData, $productType = 'simple', $isSave = true)
    {
        $this->helper('Community2/Mage/Product/Helper')->createProduct($productData, $productType, $isSave);
    }

    /**
     * Select product type
     *
     * @param array $productData
     * @param string $productType
     */
    public function selectTypeProduct(array $productData, $productType)
    {
        $this->helper('Community2/Mage/Product/Helper')->selectTypeProduct($productData, $productType);
    }

    /**
     * Get auto-incremented SKU
     *
     * @param string $productSku
     *
     * @return string
     */
    public function getGeneratedSku($productSku)
    {
        return $this->helper('Community2/Mage/Product/Helper')->getGeneratedSku($productSku);
    }

    /**
     * Creating product using fields autogeneration with variables on General tab
     *
     * @param array $productData
     * @param bool $isSave
     * @param array $skipFieldFillIn
     * @param string $productType
     */
    public function createProductWithAutoGeneration(array $productData, $isSave = false, $skipFieldFillIn = array(), $productType = 'simple')
    {
        $this->helper('Community2/Mage/Product/Helper')
            ->createProductWithAutoGeneration($productData, $isSave, $skipFieldFillIn, $productType);
    }

    /**
     * Form mask's value replacing variable in mask with variable field's value on General tab
     *
     * @param string $mask
     * @param array $placeholders
     *
     * @return string
     */
    public function formFieldValueFromMask($mask, array $placeholders)
    {
        return $this->helper('Community2/Mage/Product/Helper')->formFieldValueFromMask($mask, $placeholders);
    }

    /**
     * Delete all Custom Options
     *
     * @return void
     */
    public function deleteCustomOptions()
    {
        $this->helper('Community2/Mage/Product/Helper')->deleteCustomOptions();
    }

    /**
     * Get Custom Option Id By Title
     *
     * @param string
     *
     * @return integer
     */
    public function getCustomOptionId($optionTitle)
    {
        return $this->helper('Community2/Mage/Product/Helper')->getCustomOptionId($optionTitle);
    }

    /**
     * Check if product is present in products grid
     *
     * @param array $productData
     *
     * @return bool
     */
    public function isProductPresentInGrid($productData)
    {
        return $this->helper('Community2/Mage/Product/Helper')->isProductPresentInGrid($productData);
    }

    /**
     * Fill in Product Settings tab
     *
     * @param array $dataForAttributesTab
     * @param array $dataForInventoryTab
     * @param array $dataForWebsitesTab
     */
    public function updateThroughMassAction($dataForAttributesTab, $dataForInventoryTab, $dataForWebsitesTab)
    {
        $this->helper('Community2/Mage/Product/Helper')
            ->updateThroughMassAction($dataForAttributesTab, $dataForInventoryTab, $dataForWebsitesTab);
    }
}
