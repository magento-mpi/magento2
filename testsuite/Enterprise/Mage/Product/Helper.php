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
}