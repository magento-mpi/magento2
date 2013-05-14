<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AddBySku
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 */
class Enterprise_Mage_AddBySku_Helper extends Mage_Selenium_AbstractHelper
{
    //---------------------------------------------------Backend------------------------------
    /**
     * Add next product
     *
     * @param bool $pressButton
     * @param array $productsToAdd
     * @param bool $isShoppingCart, if false, method is used for Sales - Orders
     */
    public function addProductsBySkuToShoppingCart(array $productsToAdd, $pressButton = true, $isShoppingCart = true)
    {
        if ($isShoppingCart && !$this->controlIsPresent('pageelement', 'opened_add_to_shopping_cart_by_sku')) {
            $this->clickControl('link', 'expand_add_to_shopping_cart_by_sku', false);
            $this->waitForAjax();
        }
        if (!$isShoppingCart) {
            $this->clickButton('add_products_by_sku', false);
        }
        $this->_fillSkuQty($productsToAdd);
        if ($pressButton && $isShoppingCart) {
            $this->clickButton('add_selected_products_to_shopping_cart', false);
        }
        if ($pressButton && !$isShoppingCart) {
            $this->clickButton('submit_sku_form');
        }
        $this->assertTrue($this->checkoutOnePageHelper()->verifyNotPresetAlert(), $this->getParsedMessages());
        $this->pleaseWait();
    }

    /**
     * Get parameter for product in Shopping Cart
     *
     * @param array $productsToAdd
     */
    protected function _fillSkuQty(array $productsToAdd)
    {
        $item = 0;
        foreach ($productsToAdd as $value) {
            if ($item > 0) {
                $this->clickButton('add_row', false);
                $this->waitForAjax();
            }
            $this->addParameter('itemId', $item++);
            if (is_array($value)) {
                $this->fillField('sku', $value['sku']);
                $this->fillField('sku_qty', $value['qty']);
                $this->waitForAjax();
            } else {
                $this->fail('Got incorrect parameter');
            }
        }
    }

    /**
     * Open shopping cart
     */
    public function openShoppingCart()
    {
        $this->clickButton('manage_shopping_cart', false);
        $this->waitForPageToLoad();
        $this->addParameter('store', $this->defineParameterFromUrl('store'));
        $this->addParameter('customer', $this->defineParameterFromUrl('customer'));
        $this->validatePage('customer_shopping_cart');
    }

    /**
     * Gets products info from table
     *
     * @param string $tableHeadName
     * @param string $productTableLine
     * @param array $skipFields
     * @return array
     */
    public function getProductInfoInTable(
        $tableHeadName = 'product_table_head',
        $productTableLine = 'product_line',
        $skipFields = array('move_to_wishlist', 'remove')
    )
    {
        $productValues = array();
        $tableRowNames = $this->shoppingCartHelper()->getColumnNamesAndNumbers($tableHeadName);
        $productLine = $this->_getControlXpath('pageelement', $productTableLine);
        $productCount = $this->getControlCount('pageelement', $productTableLine);
        for ($index = 1; $index <= $productCount; $index++) {
            foreach ($tableRowNames as $key => $value) {
                if (in_array($key, $skipFields)) {
                    continue;
                }
                $xpathValue = $productLine . "[$index]//td[$value]";
                if ($key == 'qty') {
                    $productValues['product_' . $index][$key] = $this->getElement($xpathValue . '/input')->value();
                } else {
                    $productValues = $this->_defineProductValues($productValues, $xpathValue, $index, $key);
                }
            }
        }

        $productValues = $this->_defineSkuValues($productValues);

        return $productValues;
    }

    /**
     * Defines product values without qty
     *
     * @param $productValues
     * @param $xpathValue
     * @param $index
     * @param $key
     * @return mixed
     */
    protected function _defineProductValues($productValues, $xpathValue, $index, $key)
    {
        $text = $this->getElement($xpathValue)->attribute('text');
        if (preg_match('/Excl. Tax/', $text)) {
            $productValues = $this->_defineExclTax($productValues, $text, $index, $key);
        } elseif (preg_match('/Ordered/', $text)) {
            $values = explode(' ', $text);
            $values = array_map('trim', $values);
            foreach ($values as $k => $v) {
                if ($k % 2 != 0 && isset($values[$k - 1])) {
                    $indexKey = $key . '_' . strtolower(preg_replace('#[^0-9a-z]+#i', '', $values[$k - 1]));
                    $productValues['product_' . $index][$indexKey] = $v;
                }
            }
        } else {
            $productValues['product_' . $index][$key] = trim($text);
        }

        return $productValues;
    }

    protected function _defineExclTax($productValues, $text, $index, $key)
    {
        $text = preg_replace("/ \\n/", ':', $text);
        $values = explode(':', $text);
        $values = array_map('trim', $values);
        foreach ($values as $k => $v) {
            if ($v == 'Excl. Tax' && isset($values[$k + 1])) {
                $productValues['product_' . $index][$key . '_excl_tax'] = $values[$k + 1];
            }
            if ($v == 'Incl. Tax' && isset($values[$k + 1])) {
                $productValues['product_' . $index][$key . '_incl_tax'] = $values[$k + 1];
            }
        }

        return $productValues;
    }

    /**
     * Returns sku values
     *
     * @param $productValues
     * @return mixed
     */
    protected function _defineSkuValues($productValues)
    {
        foreach ($productValues as &$productData) {
            $productData = array_diff($productData, array(''));
            foreach ($productData as &$fieldValue) {
                if (preg_match('/([\d]+\.[\d]+)|([\d]+)/', $fieldValue)) {
                    preg_match_all('/^([\D]+)?(([\d]+\.[\d]+)|([\d]+))(\%)?/', $fieldValue, $price);
                    $fieldValue = $price[0][0];
                }
                if (preg_match('/SKU:/', $fieldValue)) {
                    $skuArr = explode('SKU:', $fieldValue);
                    $sku = end($skuArr);
                    $productData['sku'] = $sku;
                    $fieldValue = substr($fieldValue, 0, strpos($fieldValue, ':') - 3);
                }
            }
        }

        return $productValues;
    }

    /**
     * Clears shopping cart
     */
    public function removeAllItemsFromShoppingCart()
    {
        if ($this->controlIsVisible('button', 'clear_shop_cart')) {
            $this->clickButtonAndConfirm('clear_shop_cart', 'confirmation_to_clear_shopping_cart', false);
            $this->pleaseWait();
        }
    }

    /**
     * Removes all items from error table
     */
    public function removeAllItemsFromAttentionTable()
    {
        if ($this->controlIsVisible('button', 'remove_all')) {
            $this->clickButton('remove_all', false);
            $this->pleaseWait();
        }
    }

    /**
     * Checks if shopping cart table is empty
     *
     * @return bool
     */
    public function isShoppingCartEmpty()
    {
        if ($this->getControlCount('pageelement', 'table_row') == 1
            && $this->controlIsVisible('pageelement', 'no_items')
        ) {
            return true;
        }
        return false;
    }

    /**
     * Checks if attention table is empty
     *
     * @return bool
     */
    public function isAttentionTableEmpty()
    {
        if ($this->controlIsVisible('pageelement', 'error_table_head')) {
            $productValues = $this->getProductInfoInTable('error_table_head', 'error_table_line');
            if (empty($productValues)) {
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * Remove items from Attention Table
     *
     * @param array $productsToRemove
     */
    public function removeItemsFromAttentionTable(array $productsToRemove)
    {
        if ($this->isAttentionTableEmpty()) {
            return;
        }
        $xpath = $this->_getControlXpath('pageelement', 'error_table_grid');
        foreach ($productsToRemove as $key => $productSku) {
            $count = $this->getControlCount('pageelement', 'error_table_grid');
            $i = 1;
            $productSku = 'sku_' . trim($productSku);
            while ($i <= $count) {
                $value = $this->getElement($xpath . "[$i]/td/div")->attribute('id');
                if (trim($value) == $productSku) {
                    $this->addParameter('rowIndex', $i);
                    $this->clickButton('remove_item', false);
                    unset($productsToRemove[$key]);
                    $i = $count + 1;
                    $this->pleaseWait();
                } else {
                    $i++;
                }
            }
        }
    }

    /**
     * Remove items from shopping cart table
     *
     * @param array $productsToRemove
     */
    public function removeItemsFromShoppingCartTable(array $productsToRemove)
    {
        if (!$this->isShoppingCartEmpty()) {
            $productsData = $this->getProductInfoInTable('product_table_head', 'table_row');
            $rowNumber = 1;
            foreach ($productsData as $value) {
                if (in_array(trim($value['sku']), $productsToRemove)) {
                    $this->addParameter('rowNumber', $rowNumber);
                    $this->fillDropdown('grid_massaction_select', 'Remove');
                    $this->pleaseWait();
                }
                $rowNumber++;
            }
        }
        $this->clickButton('update_items_and_qty', false);
        $this->pleaseWait();
    }

    /**
     * Configure product in Required Attention grid and add it to Shopping cart if possible
     *
     * @param array $product
     * @param array $msgShoppingCart
     * @param string $msgAttentionGrid
     */
    public function configureProduct(array $product, array $msgShoppingCart, $msgAttentionGrid)
    {
        if ($msgShoppingCart !== null && $msgAttentionGrid !== null && $msgAttentionGrid == 'specify_option') {
            $popupXpath = $this->_getControlXpath('fieldset', 'product_composite_configure_form');
            $this->addParameter('sku', $product['sku']);
            $this->clickControl('button', 'configure_item', false);
            $this->waitForElement($popupXpath);
            $this->waitForAjax();
            $this->orderHelper()->configureProduct($product['Options_backend']);
            $uimap = $this->_findUimapElement('fieldset', 'product_composite_configure_form');
            $this->getControlElement('button', 'ok', $uimap)->click();
        } else {
            $this->fail('Added product is not composite product');
        }
    }

    //---------------------------------------------------Frontend-------------------------------------------------------
    /**
     * Fulfill product SKU and qty fields to the according row
     *
     * @param array $data
     * @param array $rows, default value = 1
     */
    public function frontFulfillSkuQtyRows(array $data, $rows = array('1'))
    {
        foreach ($rows as $row) {
            $this->addParameter('number', $row);
            $this->fillFieldset($data, 'add_by_sku');
        }
    }

    /**
     * Delete item  is specified row
     *
     * @param array $rows, default value = 1
     */
    public function frontDeleteItems($rows = array('1'))
    {
        foreach ($rows as $row) {
            $this->addParameter('row_number', $row);
            $this->clickControl('link', 'remove_item');
            $this->assertMessagePresent('success', 'item_removed');
        }
    }

    /**
     * Verifying sku, price and qty fields for added product in Required attention grid
     *
     * @param array $product
     * @param bool $priceIsVisible
     * @param bool $qtyIsEnabled
     */
    public function frontCheckFields(array $product, $priceIsVisible, $qtyIsEnabled)
    {
        $this->addParameter('sku', $product['sku']);
        $this->assertTrue($this->controlIsVisible('pageelement', 'sku'),
            'Product is not added to required attention grid. ');
        if ($priceIsVisible) {
            $this->assertTrue($this->controlIsVisible('pageelement', 'price'),
                'Unit price is not available for added product. ');
        } else {
            $this->assertFalse($this->controlIsVisible('pageelement', 'price'),
                'Unit price is available for added product. ');
        }
        $this->assertEquals($this->getControlAttribute('field', 'qty', 'value'), $product['qty'],
            'Entered qty is not correspond to added qty');
        if ($qtyIsEnabled) {
            $this->assertTrue($this->controlIsEditable('field', 'qty'), 'Qty field is disabled. ');
        } else {
            $this->assertFalse($this->controlIsEditable('field', 'qty'),
                'Qty field available for editing. ');
        }
    }

    /**
     * Click the "Specify the product's options" link
     *
     * @param string $productName
     */
    public function clickSpecifyLink($productName)
    {
        $this->clickControl('link', 'specify_product', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('id'));
        $this->addParameter('qty', $this->defineParameterFromUrl('qty'));
        $this->addParameter('sku', $this->defineParameterFromUrl('sku'));
        if (is_string($productName)) {
            $this->addParameter('elementTitle', $productName);
        }
        $this->validatePage();
    }

    /**
     * Open Shopping Cart and clear all products in Required attention grid
     */
    public function frontClearRequiredAttentionGrid()
    {
        if ($this->getArea() == 'frontend') {
            $this->frontend('shopping_cart');
            if ($this->controlIsPresent('fieldset', 'products_requiring_attention')) {
                $this->clickButton('remove_all');
                $this->assertMessagePresent('success', 'items_removed');
            }
        }
    }

    /**
     * Configure product which require specifying link in Required Attention grid
     *
     * @param array $product
     * @param string $productType
     */
    protected function _frontSpecifyOptions(array $product, $productType)
    {
        if (strlen(strstr($productType, 'grouped')) > 0) {
            $this->frontCheckFields($product, false, false);
        } else {
            $this->frontCheckFields($product, true, true);
        }
        $this->clickSpecifyLink($product['product_name']);
        $this->productHelper()->frontFillBuyInfo($product['Options']);
        $this->clickButton('update_cart');
        //Verifying
        if (strlen(strstr($productType, 'grouped')) > 0) {
            $this->addParameter('productName',
                $product['Options']['option_1']['parameters']['subproductName']);
        }
    }

    /**
     * Configure product which require quantity modification in Required Attention grid
     *
     * @param array $product
     * @param $productType
     * @param array $msgShoppingCart
     * @param array $msgGrid
     */
    protected function _frontRequestedQuantity(array $product, $productType, array $msgShoppingCart, array $msgGrid)
    {
        $this->frontCheckFields($product, true, true);
        if ($productType == 'simpleMin') {
            $qty = $product['qty'] + 1;
        } else {
            $qty = $product['qty'] - 1;
        }
        $this->addParameter('qty', $qty);
        $this->assertMessagePresent($msgShoppingCart['type'], $msgGrid['messageTwo']);
        $this->fillFieldset(array('qty' => $qty), 'products_requiring_attention');
        $this->clickButton('add_to_cart');
    }

    /**
     * Configure product in Required Attention grid and add it to Shopping cart if possible
     *
     * @param array $product
     * @param string $productType
     * @param array $msgShoppingCart
     * @param array $msgAttentionGrid
     */
    public function frontConfigureProduct(array $product, $productType, array $msgShoppingCart, array $msgAttentionGrid)
    {
        if ($msgAttentionGrid['messageOne'] != 'null') {
            $this->assertMessagePresent($msgShoppingCart['type'], $msgAttentionGrid['messageOne']);
            switch ($msgAttentionGrid['messageOne']) {
                case 'qty_not_available':
                case 'requested_qty':
                    $this->_frontRequestedQuantity($product, $productType, $msgShoppingCart, $msgAttentionGrid);
                    break;
                case 'specify_option':
                    $this->_frontSpecifyOptions($product, $productType);
                    break;
                case 'out_of_stock':
                    $this->frontCheckFields($product, true, false);
                    break;
                default:
                    $this->frontCheckFields($product, false, false);
                    break;
            }
        } else {
            if ($productType == 'simpleNotVisible') {
                $this->addParameter('productName', $product['product_name']);
                $this->frontCheckFields($product, true, true);
            }
        }
    }
}
