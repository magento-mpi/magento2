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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Add product to shopping cart
     *
     * @param array|null $dataForBuy
     */
    public function frontAddProductToCart($dataForBuy = null)
    {
        if ($dataForBuy) {
            $this->frontFillBuyInfo($dataForBuy);
        }
        $openedProductName = trim($this->getText($this->_getControlXpath('pageelement', 'product_name')));
        $this->addParameter('productName', $openedProductName);
        $xpathCustomize = $this->_getControlXpath('fieldset', 'customize_product_info');
        if ($this->isElementPresent($xpathCustomize)) {
            if ($this->isVisible($xpathCustomize)) {
                $xpathInfo = $this->_getControlXpath('fieldset', 'product_info');
                $button = $this->_getControlXpath('button', 'add_to_cart');
                $button = str_replace($xpathInfo, $xpathCustomize, $button);
                $this->clickAndWait($button);
                $this->validatePage('shopping_cart');
            } else {
                throw new RuntimeException('Additional data for "' . $openedProductName . '" product is not filled in');
            }
        } else {
            $this->clickButton('add_to_cart');
        }
    }

    /**
     * Choose custom options and additional products
     *
     * @param array $dataForBuy
     */
    public function frontFillBuyInfo($dataForBuy)
    {
        $customize = $this->controlIsPresent('button', 'customize_and_add_to_cart');
        $customizeFieldset = $this->_getControlXpath('fieldset', 'customize_product_info');
        if ($customize) {
            $productInfoFieldset = $this->_getControlXpath('fieldset', 'product_info');
            $this->clickButton('customize_and_add_to_cart', false);
            $this->waitForElementVisible($customizeFieldset);
            $this->waitForElementPresent($productInfoFieldset . "/parent::*[@style='display: none;']");
        }
        parent::frontFillBuyInfo($dataForBuy);
    }
}