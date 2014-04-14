<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ShoppingCart
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
class Core_Mage_ShoppingCart_Helper extends Mage_Selenium_AbstractHelper
{
    const QTY = 'Qty';
    const EXCLTAX = '(Excl. Tax)';
    const INCLTAX = '(Incl. Tax)';

    /**
     * Get table column names and column numbers
     *
     * @param string $tableHeadName
     *
     * @return array
     */
    public function getColumnNamesAndNumbers($tableHeadName = 'product_table_head')
    {
        $headElement = $this->getControlElement('pageelement', $tableHeadName);
        $isExlAndInclInHead = $this->getChildElementsCount($headElement, 'tr') == 2;
        $data = array();
        $index = 1;
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $cellElement */
        foreach ($this->getChildElements($this->getChildElement($headElement, 'tr'), 'th') as $cellElement) {
            $name = trim($cellElement->text() ? : str_replace('col', '', $cellElement->attribute('class')));
            $name = $name !== 'item' ? $name : 'product_name';
            $qtyColspan = (int)$cellElement->attribute('colspan');
            if ($qtyColspan > 1) {
                if ($isExlAndInclInHead) {
                    $data[$index] = $name . self::EXCLTAX;
                    $data[$index + 1] = $name . self::INCLTAX;
                } else {
                    $data[$index] = $name;
                }
                $index = $index + $qtyColspan;
            } else {
                $data[$index++] = $name;
            }
        }
        $data = array_diff($data, array(''));
        foreach ($data as $key => &$value) {
            $value = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $value)), '_');
            if ($value == 'action') {
                unset($data[$key]);
            }
        }
        return array_flip($data);
    }

    /**
     * Get all Products info in Shopping Cart
     *
     * @param array $skipFields list of fields to skip from scraping (default value is set for EE)
     *
     * @return array
     */
    public function getProductInfoInTable($skipFields = array('move_to_wish_list', 'move_to_wishlist', 'remove'))
    {
        $productValues = array();

        $tableRowNames = $this->getColumnNamesAndNumbers();
        /** @var $element PHPUnit_Extensions_Selenium2TestCase_Element */
        $index = 1;
        foreach ($this->getControlElements('pageelement', 'product_line', null, false) as $element) {
            $productValues['product_' . $index] = array();
            foreach ($tableRowNames as $key => $value) {
                if (in_array($key, $skipFields)) {
                    continue;
                }
                $cellElement = $this->getChildElement($element, "//td[$value]");
                $inputQtyElement = $this->childElementIsPresent($cellElement, '//input[@value]');
                $cellOptionElements = $this->getChildElements($cellElement,
                    "//*[@class='item-options' or @class='item options']", false);
                if ($key == 'qty' && $inputQtyElement) {
                    $productValues['product_' . $index][$key] = trim($inputQtyElement->value());
                } elseif ($key == 'product_name' && !empty($cellOptionElements)) {
                    $productInfo = $cellElement->text();
                    /** @var $optionElement PHPUnit_Extensions_Selenium2TestCase_Element */
                    foreach ($cellOptionElements as $optionElement) {
                        $optionText = $optionElement->text();
                        $productValues['product_' . $index] = array_merge(
                            $productValues['product_' . $index],
                            $this->parseProductOptions($optionText)
                        );
                        $productInfo = trim(str_replace($optionText, '', $productInfo));
                    }
                    $productValues['product_' . $index][$key] = $productInfo;
                } else {
                    $text = $cellElement->text();
                    if (preg_match('/Excl. Tax/', $text)) {
                        preg_match_all(
                            '#([a-z (\.)?]+: ([a-z \.]+: )?)?\$([\d]+(\.|,)[\d]+(\.[\d]+)?)|([\d]+)#i',
                            $text,
                            $prices
                        );
                        $values = array_map('trim', $prices[0]);
                        foreach ($values as $v) {
                            list($name, $priceValue) = explode(':', $v);
                            $name = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $name)), '_');
                            $productValues['product_' . $index][$key . '_' . $name] = trim($priceValue);
                        }
                    } elseif ($key == 'qty' && preg_match('/Ordered/', $text)) {
                        $htmlTagName = '//' . $this->getChildElement($cellElement, "//*[*='Ordered']")->name();
                        foreach ($this->getChildElements($cellElement, $htmlTagName) as $qtyTypeLine) {
                            $array = array();
                            /** @var $qtyLineData PHPUnit_Extensions_Selenium2TestCase_Element */
                            foreach ($this->getChildElements($qtyTypeLine, '*') as $qtyLineData) {
                                $array[] = $qtyLineData->text();
                            }
                            list($type, $qty) = $array;
                            $newKey = $key . '_' . strtolower(preg_replace('#[^0-9a-z]+#i', '', $type));
                            $productValues['product_' . $index][$newKey] = $qty;
                        }
                    } else {
                        $productValues['product_' . $index][$key] = trim($text);
                    }
                }
            }
            $index++;
        }
        foreach ($productValues as &$productData) {
            $productData = array_diff($productData, array(''));
            foreach ($productData as &$fieldValue) {
                if (preg_match('/([\d]+\.[\d]+)|([\d]+)/', $fieldValue)) {
                    preg_match_all('/^([\D]+)?(([\d]+\.[\d]+)|([\d]+))(\%)?/', $fieldValue, $price);
                    $fieldValue = $price[0][0];
                }
                if (preg_match('/SKU:/', $fieldValue)) {
                    $fieldValue = trim(substr($fieldValue, 0, strpos($fieldValue, ':') - 3));
                }
            }
        }

        return $productValues;
    }

    /**
     * @param string $optionTest
     * @return array
     */
    protected function parseProductOptions($optionTest)
    {
        //Gift Wrapping for multiple checkout
        if (strpos($optionTest, "Gift Wrapping\nDesign") !== false) {
            return array('gift_wrapping' => trim(str_replace("Gift Wrapping\nDesign", '', $optionTest)));
        }
        //@TODO parse product parameters and add to return data
        return array();
    }

    /**
     * Get all order prices info in Shopping Cart
     *
     * @return array
     */
    public function getOrderPriceData()
    {
        $returnData = array();
        /** @var $element PHPUnit_Extensions_Selenium2TestCase_Element */
        foreach ($this->getControlElements('pageelement', 'price_totals_line') as $element) {
            $name = trim($this->getChildElement($element, '*[1]')->text());
            $value = trim($this->getChildElement($element, '*[2]')->text());
            if (!preg_match('/\$\(([\d]+\.[\d]+)|([\d]+)\%\)/', $name)) {
                $name = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $name)), '_');
            }
            $returnData[$name] = trim($value, "\x00..\x1F");
        }
        if (isset($returnData['gift_cards'])) { //@TODO Temp fix for tests with tax
            unset($returnData['gift_cards']);
        }
        return array_diff($returnData, array(''));
    }

    /**
     * Verify prices data on page
     *
     * @param string|array $productData
     * @param string|array $orderPriceData
     */
    public function verifyPricesDataOnPage($productData, $orderPriceData)
    {
        $productData = $this->fixtureDataToArray($productData);
        //Get Products data and order prices data
        $actualProductData = $this->getProductInfoInTable();
        $actualOrderPriceData = $this->getOrderPriceData();
        //Verify Products data
        $actualQty = count($actualProductData);
        $expectedQty = count($productData);
        if ($actualQty != $expectedQty) {
            $this->fail(
                "'" . $actualQty . "' product(s) added to Shopping cart but must be '" . $expectedQty . "'"
            );
        }
        $this->assertEquals($productData, $actualProductData, 'Data for products are wrong');
        //Verify order prices data
        $this->assertEquals($orderPriceData, $actualOrderPriceData);
    }

    /**
     * @param $actualArray
     * @param $expectedArray
     */
    protected function _unsetArrays(&$actualArray, &$expectedArray)
    {
        foreach ($actualArray as $key => $value) {
            if (array_key_exists($key, $expectedArray) && (strcmp($expectedArray[$key], trim($value)) == 0)) {
                unset($expectedArray[$key]);
                unset($actualArray[$key]);
            }
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
        $this->_unsetArrays($actualArray, $expectedArray);
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
            $this->addVerificationMessage(trim($actualErrors, "\x00..\x1F"));
        }
        if (isset($expectedErrors)) {
            $this->addVerificationMessage(trim($expectedErrors, "\x00..\x1F"));
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
        $shippingAddress = $this->fixtureDataToArray($shippingAddress);
        if (!$this->isControlExpanded('fieldset', 'estimate_shipping')) {
            $this->clickControl('fieldset', 'estimate_shipping', false);
        }
        $this->fillFieldset($shippingAddress, 'estimate_shipping');
        $this->clickButton('get_quote');
        if (!$this->isControlExpanded('fieldset', 'estimate_shipping')) { //@TODO temporary fix
            $this->clickControl('fieldset', 'estimate_shipping', false);
        }
        $this->chooseShipping($shippingMethod, $validate);
        $this->clickButton('update_total');
    }

    /**
     * @param string $couponCode
     */
    public function frontApplyCouponCode($couponCode)
    {
        if (!$this->isControlExpanded('fieldset', 'discount_codes')) {
            $this->clickControl('fieldset', 'discount_codes', false);
        }
        $this->addParameter('couponCode', $couponCode);
        $this->fillField('coupon_code', $couponCode);
        $this->clickButton('apply_coupon');
    }

    /**
     *
     * @param array $shippingMethod
     */
    public function chooseShipping($shippingMethod)
    {
        $shippingMethod = $this->fixtureDataToArray($shippingMethod);
        $shipService = (isset($shippingMethod['shipping_service'])) ? $shippingMethod['shipping_service'] : null;
        $shipMethod = (isset($shippingMethod['shipping_method'])) ? $shippingMethod['shipping_method'] : null;
        if (!$shipService or !$shipMethod) {
            $this->addVerificationMessage('Shipping Service(or Shipping Method) is not set');
        } else {
            $this->addParameter('shipService', $shipService);
            $this->addParameter('shipMethod', $shipMethod);
            if ($this->controlIsPresent('field', 'ship_service_name')) {
                if ($this->controlIsPresent('radiobutton', 'ship_method')) {
                    $this->fillRadiobutton('ship_method', 'Yes');
                } else {
                    $this->addVerificationMessage(
                        'Shipping Method "' . $shipMethod . '" for "' . $shipService . '" is currently unavailable.'
                    );
                }
            } else {
                $this->addVerificationMessage('Shipping Service "' . $shipService . '" is currently unavailable.');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Open and clear Shopping Cart
     */
    public function frontClearShoppingCart()
    {
        if ($this->getArea() == 'frontend' && !$this->controlIsVisible('link', 'empty_my_cart')) {
            $this->clickControl('link', 'my_cart', false);
            $this->clickControl('link', 'view_cart');
            if ($this->controlIsVisible('button', 'clear_shopping_cart')) { //@TODO bug
                $this->clickButton('clear_shopping_cart');
            }
            $this->assertMessagePresent('success', 'shopping_cart_is_empty');
        }
    }

    /**
     * Moves products to the wishlist from Shopping Cart
     *
     * @param string $productName
     */
    public function frontMoveToWishlist($productName)
    {
        $this->addParameter('productName', $productName);
        $this->clickControl('link', 'move_to_wishlist');
    }

    /**
     * Verifies if the product(s) are in the Shopping Cart
     *
     * @param string|array $productNameSet Product name (string) or array of product names to check
     * @param string $controlType , default value = 'link'
     *
     * @return bool|array True if the products are all present.
     *                    Otherwise returns an array of product names that are absent.
     */
    public function frontShoppingCartHasProducts($productNameSet, $controlType = 'link')
    {
        if (is_string($productNameSet)) {
            $productNameSet = array($productNameSet);
        }
        $absentProducts = array();
        foreach ($productNameSet as $productName) {
            $this->addParameter('productName', $productName);
            if (!$this->controlIsPresent($controlType, 'product_name')) {
                $absentProducts[] = $productName;
            }
        }
        return (empty($absentProducts)) ? true : $absentProducts;
    }
}
