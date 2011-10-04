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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ShoppingCart_Helper extends Mage_Selenium_TestCase
{
    const QTY = 'Qty';
    const UNITPRICE = 'Unit Price';
    const SUBTOTAL = 'Subtotal';
    const EXCLTAX = '(Excl. Tax)';
    const INCLTAX = '(Incl. Tax)';

    /**
     * Get all Products info in Shopping Cart
     *
     * @return array
     */
    public function frontGetProductInfoInShoppingCart()
    {
        $xpath = $this->_getControlXpath('pageelement', 'table_head') . '/th';
        $productLine = $this->_getControlXpath('pageelement', 'product_line');

        $tableRowNames = $productValues = $returnData = array();

        $rowCount = $this->getXpathCount($xpath);
        for ($i = 1; $i <= $rowCount; $i++) {
            $text = trim($this->getText($xpath . "[$i]"));
            if ($text == self::UNITPRICE) {
                if ($this->getAttribute($xpath . "[$i]/@colspan") == 2) {
                    $tableRowNames[$text . self::EXCLTAX] = $i;
                    $tableRowNames[$text . self::INCLTAX] = $i + 1;
                } else {
                    $tableRowNames[$text] = $i;
                }
            } elseif ($text == self::SUBTOTAL) {
                if ($this->getAttribute($xpath . "[$i]/@colspan") == 2) {
                    $tableRowNames[$text . self::EXCLTAX] = $i + 1;
                    $tableRowNames[$text . self::INCLTAX] = $i + 2;
                } else {
                    $tableRowNames[$text] = $i;
                }
            } else {
                $tableRowNames[$text] = $i;
            }
        }
        if (array_key_exists(self::UNITPRICE . self::EXCLTAX, $tableRowNames) && array_key_exists(self::QTY,
                        $tableRowNames)) {
            $tableRowNames[self::QTY] = $tableRowNames[self::QTY] + 1;
        }

        $productCount = $this->getXpathCount($productLine);
        for ($i = 1; $i <= $productCount; $i++) {
            foreach ($tableRowNames as $key => $value) {
                if ($key != '') {
                    if ($key == self::QTY) {
                        $productValues[$i - 1][$key] = $this->getAttribute($productLine .
                                "[$i]/td[$value]/input/@value");
                    } else {
                        $productValues[$i - 1][$key] = $this->getText($productLine . "[$i]/td[$value]");
                    }
                }
            }
        }

        foreach ($productValues as $key => &$productData) {
            $productData = array_diff($productData, array(''));
            foreach ($productData as $field_key => $field_value) {
                $field_key = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $field_key)), '_');
                $returnData[$key][$field_key] = $field_value;
            }
        }
        return $returnData;
    }

    /**
     * Get all order prices info in Shopping Cart
     *
     * @return type
     */
    public function frontGetOrderPriceDataInShoppingCard()
    {
        $setXpath = "//*[@id='shopping-cart-totals-table']/descendant::tr";
        $count = $this->getXpathCount($setXpath);
        $returnData = array();
        for ($i = $count; $i >= 1; $i--) {
            $fieldName = $this->getText($setXpath . "[$i]/*[@style][1]");
            if (!preg_match('/\(([\d]+\.[\d]+)|([\d]+)\%\)/', $fieldName)) {
                $fieldName = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $fieldName)), '_');
            }
            $fielValue = $this->getText($setXpath . "[$i]/*[@style][2]");
            $returnData[$fieldName] = $fielValue;
        }
        return $returnData;
    }

    /**
     * Verify Shopping Cart info
     *
     * @param string|array $productData
     * @param string|array $orderPriceData
     */
    public function frontVerifyShoppingCartData($productData, $orderPriceData)
    {
        if (is_string($productData)) {
            $productData = $this->loadData($productData);
        }
        if (is_string($orderPriceData)) {
            $orderPriceData = $this->loadData($orderPriceData);
        }
        //Get Products data and order prices data
        $actualProductData = $this->frontGetProductInfoInShoppingCart();
        $actualOrderPriceData = $this->frontGetOrderPriceDataInShoppingCard();
        //Verify Products data
        $actualProductQty = count($actualProductData);
        $expectedProductQty = count($productData);
        if ($actualProductQty != $expectedProductQty) {
            $this->messages['error'][] = "'" . $actualProductQty . "' product(s) added to Shopping cart but must be '"
                    . $expectedProductQty . "'";
        } else {
            for ($i = 0; $i < $actualProductQty; $i++) {
                $this->compareArrays($actualProductData[$i], $productData[$i], $productData[$i]['product_name']);
            }
        }
        //Verify order prices data
        $this->compareArrays($actualOrderPriceData, $orderPriceData);
        if (!empty($this->messages['error'])) {
            $this->fail(implode("\n", $this->messages['error']));
        }
    }

    /**
     *
     * @param array $actualArray
     * @param array $expectedArray
     * @param string $productName
     */
    public function compareArrays($actualArray, $expectedArray, $productName = '')
    {
        foreach ($actualArray as $key => $value) {
            if (array_key_exists($key, $expectedArray) && (strcmp($expectedArray[$key], $value) == 0)) {
                unset($expectedArray[$key]);
                unset($actualArray[$key]);
            }
        }

        if ($productName) {
            $productName = $productName . ': ';
        }

        if ($actualArray) {
            $actualErrors = $productName . "Data is displayed on the page: \n";
            foreach ($actualArray as $key => $value) {
                $actualErrors .= "Field '$key': value '$value'\n";
            }
        }
        if ($expectedArray) {
            $expectedErrors = $productName . "Data should appear on the page: \n";
            foreach ($expectedArray as $key => $value) {
                $expectedErrors .= "Field '$key': value '$value'\n";
            }
        }
        if (isset($actualErrors)) {
            $this->messages['error'][] = trim($actualErrors, "\x00..\x1F");
        }
        if (isset($expectedErrors)) {
            $this->messages['error'][] = trim($expectedErrors, "\x00..\x1F");
        }
    }

    /**
     *
     * @param string|array $shippingAddress
     * @param string|array $shippingMethod
     * @param boolean $validate
     */
    public function frontEstimateShipping($shippingAddress, $shippingMethod, $validate = true)
    {
        if (is_string($shippingAddress)) {
            $shippingAddress = $this->loadData($shippingAddress);
        }
        $shippingAddress = $this->arrayEmptyClear($shippingAddress);
        $this->messages['error'] = array();
        $this->fillForm($shippingAddress);
        $this->clickButton('get_quote');
        $this->chooseShipping($shippingMethod, $validate);
        $this->clickButton('update_total');
    }

    /**
     *
     * @param type $shippingMethod
     * @param type $validate
     */
    public function chooseShipping($shippingMethod, $validate)
    {
        if (is_string($shippingMethod)) {
            $shippingMethod = $this->loadData($shippingMethod);
        }
        $shipService = (isset($shippingMethod['shipping_service'])) ? $shippingMethod['shipping_service'] : NULL;
        $shipMethod = (isset($shippingMethod['shipping_method'])) ? $shippingMethod['shipping_method'] : NULL;
        if (!$shipService or !$shipMethod) {
            $this->messages['error'][] = 'Shipping Service(or Shipping Method) is not set';
        } else {
            $this->addParameter('shipService', $shipService);
            $this->addParameter('shipMethod', $shipMethod);
            if ($this->isElementPresent($this->_getControlXpath('field', 'ship_service_name'))) {
                $method = $this->_getControlXpath('radiobutton', 'ship_method');
                if ($this->isElementPresent($method)) {
                    $this->click($method);
                    $this->waitForAjax();
                } else {
                    $this->messages['error'][] = 'Shipping Method "' . $shipMethod . '" for "'
                            . $shipService . '" is currently unavailable.';
                }
            } else {
                $this->messages['error'][] = 'Shipping Service "' . $shipService . '" is currently unavailable.';
            }
        }
        if ($this->messages['error'] && $validate) {
            $this->fail(implode("\n", $this->messages['error']));
        }
    }

}

