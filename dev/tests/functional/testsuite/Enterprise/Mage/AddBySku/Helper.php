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
        if ($isShoppingCart) {
            if (!$this->controlIsVisible('fieldset', 'add_to_cart_by_sku')) {
                $this->clickControl('link', 'add_to_cart_by_sku_link', false);
            }
        } else {
            $this->clickButton('add_products_by_sku', false);
        }
        $this->waitForControlVisible('fieldset', 'add_to_cart_by_sku');
        $this->frontFulfillSkuQtyRows($productsToAdd, 'add_to_cart_by_sku');
        if (!$pressButton) {
            return;
        }
        if ($isShoppingCart) {
            $this->clickButton('add_selected_products_to_shopping_cart', false);
            $this->pleaseWait();
        } else {
            $this->clickButton('submit_sku_form');
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
     * @param string $headName
     * @param string $lineName
     * @param array $skipFields
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductInfoInTable(
        $headName = 'product_table_head',
        $lineName = 'product_line',
        $skipFields = array('move_to_wishlist', 'remove')
    )
    {
        $productValues = array();
        $tableRowNames = $this->shoppingCartHelper()->getColumnNamesAndNumbers($headName);
        /** @var $element PHPUnit_Extensions_Selenium2TestCase_Element */
        /** @var $element2 PHPUnit_Extensions_Selenium2TestCase_Element */
        $index = 1;
        foreach ($this->getControlElements('pageelement', $lineName) as $element) {
            foreach ($tableRowNames as $key => $value) {
                if (in_array($key, $skipFields)) {
                    continue;
                }
                if ($key == 'qty' || $key == 'quantity') {
                    $productValues['product_' . $index]['qty'] =
                        $this->getChildElement($element, "td[$value]/input")->value();
                    continue;
                }
                $text = trim($this->getChildElement($element, "td[$value]")->text());
                if ($key == 'product') {
                    if ($headName == 'error_table_head') { //@TODO remove when design fixed
                        foreach ($this->getChildElements($element, "td[$value]/div/div") as $element2) {
                            $text = trim(str_replace($element2->text(), '', $text));
                        }
                    }
                    list($name, $sku) = explode('SKU:', $text);
                    $productValues['product_' . $index][$key . '_name'] = trim($name);
                    $productValues['product_' . $index][$key . '_sku'] = trim($sku);
                    continue;
                }
                if (preg_match('/Excl. Tax/', $text)) {
                    $values = explode("\n", $text);
                    $values = array_map('trim', $values);
                    foreach ($values as $k => $v) {
                        if ($v == 'Excl. Tax' && isset($values[$k + 1])) {
                            $productValues['product_' . $index][$key . '_excl_tax'] = $values[$k + 1];
                        }
                        if ($v == 'Incl. Tax' && isset($values[$k + 1])) {
                            $productValues['product_' . $index][$key . '_incl_tax'] = $values[$k + 1];
                        }
                    }
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
                    $productValues['product_' . $index][$key] = $text;
                }
            }
            $productValues['product_' . $index] = array_diff($productValues['product_' . $index], array(''));
            $index++;
        }

        return $productValues;
    }

    /**
     * Clears shopping cart
     */
    public function removeAllItemsFromShoppingCart()
    {
        if ($this->controlIsVisible('button', 'clear_shop_cart')) {
            $this->clickButtonAndConfirm('clear_shop_cart', 'confirmation_to_clear_shopping_cart');
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
        return ($this->getControlCount('pageelement', 'table_row') == 1
            && $this->controlIsVisible('pageelement', 'no_items'));
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
            return empty($productValues);
        }
        return true;
    }

    /**
     * Remove items from Attention Table
     *
     * @param array|string $skuToRemove
     */
    public function removeItemsFromAttentionTable($skuToRemove)
    {
        if ($this->isAttentionTableEmpty()) {
            return;
        }
        if (is_string($skuToRemove)) {
            $skuToRemove = array($skuToRemove);
        }
        foreach ($skuToRemove as $sku) {
            /** @var $element PHPUnit_Extensions_Selenium2TestCase_Element */
            foreach ($this->getControlElements('pageelement', 'error_table_line') as $key => $element) {
                if (strpos($element->text(), 'SKU: ' . $sku) !== false) {
                    $this->addParameter('rowIndex', $key + 1);
                    $this->clickButton('remove_item', false);
                    $this->pleaseWait();
                    break(1);
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
     */
    public function configureProduct(array $product)
    {
        $this->addParameter('sku', $product['sku']);
        if ($this->getControlAttribute('button', 'configure_item', 'disabled')) {
            return;
        }
        $this->clickControl('button', 'configure_item', false);
        $this->waitForControlVisible('fieldset', 'product_composite_configure_form');
        $this->orderHelper()->configureProduct($product['Options_backend']);
        $this->clickButton('composite_configure_ok', false);
    }

    //---------------------------------------------------Frontend-------------------------------------------------------
    /**
     * Fulfill product SKU and qty fields to the according row
     *
     * @param array $data
     * @param string $fieldset
     */
    public function frontFulfillSkuQtyRows(array $data, $fieldset = 'add_by_sku')
    {
        foreach ($data as $dataRow) {
            $this->addParameter('number', $this->getControlCount('pageelement', 'table_lines'));
            if ($this->getControlAttribute('field', 'sku', 'selectedValue') != ''
                || $this->getControlAttribute('field', 'qty', 'selectedValue') != ''
            ) {
                $this->clickButton('add_row', false);
                $this->addParameter('number', 1 + (int)$this->getParameter('number'));
                if (!$this->controlIsVisible('field', 'sku')) {
                    $this->markTestIncomplete('BUG: Add Row link does not work on frontend');
                }
            }
            $this->fillFieldset($dataRow, $fieldset);
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
        $this->addParameter('elementTitle', $productName);
        $this->validatePage();
    }

    /**
     * Open Shopping Cart and clear all products in Required attention grid
     */
    public function frontClearRequiredAttentionGrid()
    {
        $this->frontend('shopping_cart');
        if ($this->controlIsPresent('fieldset', 'products_requiring_attention')) {
            $this->clickButton('remove_all');
            $this->assertMessagePresent('success', 'items_removed');
        }
    }

    /**
     * Configure product in Required Attention grid and add it to Shopping cart if possible
     *
     * @param array $product
     * @param string $productType
     * @param array $msgShoppingCart
     * @param array $msgAttentionGrid
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function frontConfigureProduct(array $product, $productType, array $msgShoppingCart, array $msgAttentionGrid)
    {
        if ($msgAttentionGrid['messageOne'] != 'null') {
            $this->assertMessagePresent($msgShoppingCart['type'], $msgAttentionGrid['messageOne']);
            switch ($msgAttentionGrid['messageOne']) {
                case 'qty_not_available':
                case 'requested_qty':
                    $this->frontCheckFields($product, true, true);
                    if ($productType == 'simpleMin') {
                        $qty = $product['qty'] + 1;
                    } else {
                        $qty = $product['qty'] - 1;
                    }
                    $this->addParameter('qty', $qty);
                    $this->assertMessagePresent($msgShoppingCart['type'], $msgAttentionGrid['messageTwo']);
                    $this->fillFieldset(array('qty' => $qty), 'products_requiring_attention');
                    $this->clickButton('add_to_cart');
                    break;
                case 'specify_option':
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
