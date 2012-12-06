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
        if (array_key_exists('individual', $giftOptions)) {
            foreach ($giftOptions['individual'] as $options) {
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
        if (array_key_exists('individual', $giftOptions['gift_messages'])) {
            foreach ($giftOptions['gift_messages']['individual'] as $options) {
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
                if (is_array($options) && isset($options['filter_sku'])) {
                    $this->addParameter('sku', $options['filter_sku']);
                    if ($this->controlIsPresent('link', 'gift_options')) {
                        $this->addVerificationMessage('Gift options link is available for product '
                                                      . $options['filter_sku']);
                    }
                }
            }
        }

        $fieldSetElements = $this->_findUimapElement('fieldset', 'gift_options_for_order')->getFieldsetElements();
        foreach($fieldSetElements as $controlType => $elements) {
            foreach($elements as $element => $elementXpath) {
                if ($this->controlIsPresent($controlType, $element)) {
                    $this->addVerificationMessage("Gift options($element) is available for the order");
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Verify gift wrapping(title and price) for entire order of each item
     *
     * @param array $giftOptions
     * @return boolean
     */
    public function verifyGiftWrapping(array $giftOptions)
    {
        if (array_key_exists('entire_order', $giftOptions) && is_array($giftOptions['entire_order'])) {
            foreach ($giftOptions['entire_order'] as $element => $value) {
                $this->verifyPageelement($element, $value);
            }
        }
        if (array_key_exists('individual', $giftOptions)) {
            foreach ($giftOptions['individual'] as $options) {
                if (is_array($options) && isset($options['sku_product'])) {
                    $this->addParameter('sku', $options['sku_product']);
                    $this->clickControl('link', 'gift_options', false);
                    $this->waitForAjax();
                    unset($options['sku_product']);
                    foreach ($options as $element => $value) {
                        $this->verifyPageelement($element, $value);
                    }
                    $this->clickButton('ok', false);
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function verifyPageelement($elementName, $expectedValue)
    {
        $resultFlag = true;
        $elementXpath = $this->_getControlXpath('pageelement',$elementName);
        if ($this->isElementPresent($elementXpath)) {
            $val = $this->getElementByXpath($elementXpath);
            if ($val != $expectedValue) {
                $this->addVerificationMessage(
                    $elementName . ": The stored value is not equal to specified: (" . $expectedValue
                    . "' != '" . $val . "')");
                $resultFlag = false;
            }
        } else {
            $this->addVerificationMessage('Can not find field (xpath:' . $elementXpath . ')');
            $resultFlag = false;
        }
        return $resultFlag;
    }
}