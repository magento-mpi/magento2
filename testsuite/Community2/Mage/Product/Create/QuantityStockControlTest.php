<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products creation tests with synchronized Inventory ans Stock field on Product Details tab
 */
class Community2_Mage_Product_Create_QuantityStockControlTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log into backend as admin</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Setup System Configuration before tests:</p>
     * <p>System - Configuration - CATALOG - Inventory - Product Stock Options - Manage Stock - Yes</p>
     */
    public function setUpBeforeTests()
    {
        //Data
        $config = $this->loadDataSet('Inventory', 'manage_stock_options');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Preconditions for creating configurable product.</p>
     * <p>Create dropdown attribute, Global scope and assign it to Default Attribute Set</p>
     *
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeData['attribute_code']));
        $productData['general_user_attr_dropdown'] = $attributeData['option_1']['admin_option_name'];
        //Steps (attribute)
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps (attribute set)
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array('attribute' => $attributeData['admin_title']);
    }

    /**
     *<p>Set Default value "In Stock" for quantity_and_stock_status attribute</p>
     *
     * @test
     * @return array
     */
    public function setDefaultValue()
    {
        //Data
        $attributeData = $this->loadDataSet('SystemAttributes', 'quantity_and_stock_status');
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute(array('attribute_code' => $attributeData['attribute_code']));
        //Verifying
        $this->productAttributeHelper()->verifySystemAttribute($attributeData);
        //Steps
        $this->productAttributeHelper()->processAttributeValue($attributeData);
        $this->saveForm('save_attribute');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return array('default_stock_status' => $attributeData['default_value']);
    }

    /**
     *<p>Create Attribute Set, based on Minimal Attribute Set</p>
     *
     * @test
     * @return array
     */
    public function createAttributeSet()
    {
        //Data
        $setData = $this->loadDataSet('AttributeSet', 'mini_attribute_set');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($setData);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array('set_name' => $setData['set_name']);
    }

    /**
     * <p>Create simple product per fulfilling Inventory data on Product Details tab</p>
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Start to create simple product</p>
     *  <p>4. Fulfill information about quantity in field Quantity and Stock (e.g. 15)</p>
     *  <p>select Stock Availability in dropdown (e.g. In Stock) on Product Details tab</p>
     *  <p>5. Fulfill another required fields except Qty on Inventory tab and press the "Save" button</p>
     *  <p>6. Search created product in Manage Products grid and open it - General tab</p>
     *  <p>7. Open Inventory tab</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 5:  Product has been successfully created, success message appears</p>
     *  <p>After Step 6: Values in Quantity ans Stock fields is the same as entered data</p>
     *  <p>After Step 7: Values in Qty field (e.g. 15) and Stock Availability dropdown (e.g. In Stock)</p>
     *  <p>on the Inventory tab are the same as entered on General tab data</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6368
     */
    public function simpleProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_sync_inventory');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->openTab('inventory');
        $this->assertEquals($productData['general_qty'],
            $this->getValue($this->_getControlXpath('field', 'inventory_qty')),
            'Inventory Qty data is not equal to entered on General tab data');
        $this->assertEquals($productData['general_stock_availability'], $this->getText(
                $this->_getControlXpath('dropdown', 'inventory_stock_availability') . "//option[@selected='selected']"),
            'Selected value for Stock Availability is not equal to entered on General tab data');
    }

    /**
     * <p>Verify that Quantity part of Stock and Quantity control on General tab is disabled for Grouped and</p>
     * <p>Configurable products</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Start to create configurable or grouped product</p>
     *  <p>4. Fulfill all required fields (and Stock Availability on Inventory tab) and press the "Save" button</p>
     *  <p>5. Search created product in Manage Products grid and open it</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 4: Product has been successfully created, success message appears</p>
     *  <p>After Step 5: Selected value in Quantity and Stock dropdown is the same as entered data</p>
     *  <p>Qty field (General tab, Stock and Quantity control) is disabled</p>
     *
     * @param array $productType
     * @param array $attributeData
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider productTypeDataProvider
     * @TestLinkId TL-MAGE-6369
     */

    public function verifyUneditabilityForComposite($productType, $attributeData)
    {
        //Data
        if ($productType == 'configurable') {
            $productData = $this->loadDataSet('Product', 'configurable_product_visible',
                array('configurable_attribute_title' => $attributeData['attribute']));
        } else {
            $productData = $this->loadDataSet('Product', 'grouped_product_visible');
        }
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, $productType);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->assertEquals($productData['inventory_stock_availability'], $this->getText(
                $this->_getControlXpath('dropdown', 'general_stock_availability') . "//option[@selected='selected']"),
            'Stock Availability Dropdown on General tab is not the same as entered data');
        $fieldXpath = $this->_getControlXpath('field', 'general_qty');
        $this->assertTrue($this->isElementPresent($fieldXpath) && !$this->isEditable($fieldXpath),
            'Quantity field is absent or is editable for this product type');
    }

    /**
     * Data Provider for composite product types without Qty control on Inventory tab
     *
     * @return array
     */
    public function productTypeDataProvider()
    {
        return array(array('configurable'), array('grouped'));
    }

    /**
     * <p>Verify that Stock Availability dropdown on General tab is disabled if Qty field is empty</p>
     * <p>Selected value for Qty and Stock dropdown on General tab is default value for qty_and_stock attribute</p>
     *
     * <p>Preconditions:</p>
     *  <p>Navigate Catalog - Manage Attributes</p>
     *  <p>Find qty_and_stock attribute in Manage Attributes grid and open it</p>
     *  <p>Select "In Stock" as default value for attribute</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Start to create simple product</p>
     *  <p>4. Fulfill all required information except Inventory fields: Qty and Stock on General tab,</p>
     *  <p>Qty and Stock availability on Inventory tab</p>
     *  <p>5. Open General tab</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 5: Qty field is empty, Stock Availability dropdown is disabled</p>
     *  <p>Selected value for disabled Stock Availability dropdown is equal to default value for attribute</p>
     *
     * @param array $defaultValue
     *
     * @depends setDefaultValue
     * @test
     * @TestlinkId TL-MAGE-6370
     */

    public function emptyGeneralQuantity($defaultValue)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        //Verifying
        $this->openTab('general');
        $dropdownXpath = $this->_getControlXpath('dropdown', 'general_stock_availability');
        $this->assertTrue($this->isElementPresent($dropdownXpath) && !$this->isEditable($dropdownXpath),
            'Stock Availability dropdown on Product Details tab is editable but should not');
        $this->assertEquals($defaultValue['default_stock_status'], $this->getText(
                $this->_getControlXpath('dropdown', 'general_stock_availability') . "//option[@selected='selected']"),
            'Selected value for Stock Availability is not equal to default value of attribute');
    }

    /**
     * <p>Verify that value entered in Qty field on General tab is validated in Qty (Inventory) field<p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Start to create simple product</p>
     *  <p>4. Fulfill non-numerical value in Qty field on General tab</p>
     *  <p>5. Fulfill all required fields for product creation except Qty on Inventory tab</p>
     *  <p>6. Press the Save button</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 6: Validation error has been shown under Qty field on Inventory tab</p>
     *
     * @param string $qty
     *
     * @test
     * @dataProvider qtyGeneralDataProvider
     * @TestlinkId TL-MAGE-6371
     */
    public function generalQtyValidation($qty)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_sync_inventory', array('general_qty' => $qty));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->openTab('inventory');
        $this->addFieldIdToMessage('field', 'inventory_qty');
        $this->assertMessagePresent('validation', 'enter_valid_number');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Data Provider for validation Qty field on Product Details tab
     *
     * @return array
     */
    public function qtyGeneralDataProvider()
    {
        return array(array($this->generate('string', 9, ':punct:')), array($this->generate('string', 9, ':alnum:')));
    }

    /**
     * <p>Verify that last entered data in Inventory controls are saved and synchronized<p>
     * <p>between General and Inventory tab for New Product<p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Start to create simple product</p>
     *  <p>4. Fulfill '15' and 'Out of Stock' in Quantity and Stock control on General tab</p>
     *  <p>5. Fulfill another required fields and '37'(Qty) and 'In Stock'(Stock Availability) on Inventory tab</p>
     *  <p>6. Press the Save button</p>
     *  <p>7. Find created product in Manage products grid and open it</p>
     *  <p>8. Open Inventory tab</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 6: Product has been successfully created. Success message appears</p>
     *  <p>After Step 7: Stock and Quantity values are the same as entered the last -</p>
     *  <p>'37'(Qty) and 'In Stock'(Stock Availability)</p>
     *  <p>After Step 8: Quantity and Stock Availability values are the same as entered the last -</p>
     *  <p>'37'(Qty) and 'In Stock'(Stock Availability)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6373
     */
    public function saveLastEnteredDataInventoryNewProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_sync_inventory',
            array('inventory_qty' => '37', 'inventory_stock_availability' => 'Out of Stock'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->assertEquals($productData['inventory_qty'],
            $this->getValue($this->_getControlXpath('field', 'general_qty')));
        $this->assertEquals($productData['inventory_stock_availability'], $this->getText(
                $this->_getControlXpath('dropdown', 'general_stock_availability') . "//option[@selected='selected']"));
        $this->openTab('inventory');
        $this->assertEquals($productData['inventory_qty'],
            $this->getValue($this->_getControlXpath('field', 'inventory_qty')));
        $this->assertEquals($productData['inventory_stock_availability'], $this->getText(
                $this->_getControlXpath('dropdown', 'inventory_stock_availability')
                . "//option[@selected='selected']"));
    }

    /**
     * <p>Verify that last entered data in Inventory controls are saved and synchronized<p>
     * <p>between General and Inventory tab for Edit Product<p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Start to create simple product</p>
     *  <p>4. Fulfill '15' and 'Out of Stock' in Quantity and Stock control on General tab</p>
     *  <p>5. Fulfill another required fields</p>
     *  <p>6. Press the Save button</p>
     *  <p>7. Find created product in Manage products grid and open it</p>
     *  <p>8. Fulfill some valid value and 'In Stock' in Quantity and Stock control on General tab</p>
     *  <p>9. Open Inventory tab</p>
     *  <p>10. Open General tab</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 6: Product has been successfully created. Success message appears</p>
     *  <p>After Step 9: Stock and Quantity values are the same as entered the last -</p>
     *  <p>entered value (Qty) and 'In Stock'(Stock Availability)</p>
     *  <p>After Step 10: Quantity and Stock Availability values are the same as entered the last -</p>
     *  <p>entered value(Qty) and 'In Stock'(Stock Availability)</p>
     *
     *
     * @test
     * @TestlinkId TL-MAGE-6375
     */
    public function saveLastEnteredDataInventoryEditProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_sync_inventory');
        $qty = $this->generate('string', 5, ':digit:');
        $stock = 'In Stock';
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->fillField('general_qty', $qty);
        $this->fillDropdown('general_stock_availability', $stock);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->assertEquals($qty, $this->getValue($this->_getControlXpath('field', 'general_qty')));
        $this->assertEquals($stock, $this->getText(
            $this->_getControlXpath('dropdown', 'general_stock_availability') . "//option[@selected='selected']"));
        $this->openTab('inventory');
        $this->assertEquals($qty, $this->getValue($this->_getControlXpath('field', 'inventory_qty')));
        $this->assertEquals($stock, $this->getText(
            $this->_getControlXpath('dropdown', 'inventory_stock_availability') . "//option[@selected='selected']"));
    }

    /**
     * <p>Verify that last entered data in Inventory controls are saved and synchronized<p>
     * <p>between General and Inventory tab without saving product<p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Start to create simple product</p>
     *  <p>4. Fulfill '15' and 'Out of Stock' in Stock and Quantity on General tab</p>
     *  <p>5. Fulfill another required fields</p>
     *  <p>6. Open Inventory tab</p>
     *  <p>7. Fulfill some valid value in Qty field and 'In Stock' in Stock Availability dropdown</p>
     *  <p>8. Open General tab</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 6: Qty data ans Stock Availability is the same as entered on General tab</p>
     *  <p>After Step 8: Qty data and Stock Availability is the same as entered on Inventory tab</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6374
     */
    public function verifySyncInventoryControlsInline()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_sync_inventory');
        $newStockData = 'In Stock';
        $qty = $this->generate('string', 5, ':digit:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        //Verifying
        $this->openTab('inventory');
        $selectedStock = $this->getText(
            $this->_getControlXpath('dropdown', 'inventory_stock_availability') . "//option[@selected='selected']");
        $this->assertEquals($productData['general_stock_availability'], $selectedStock,
            'Stock Availability is not synchronized for Inventory tab');
        $this->assertEquals($productData['general_qty'],
            $this->getValue($this->_getControlXpath('field', 'inventory_qty'),
                'Qty is not synchronized for Inventory tab'));
        //Steps
        $this->fillDropdown('inventory_stock_availability', $newStockData);
        $this->fillField('inventory_qty', $qty);
        $this->openTab('general');
        //Verifying
        $this->assertEquals($qty, $this->getValue($this->_getControlXpath('field', 'general_qty')));
        $this->assertEquals($newStockData, $this->getText(
            $this->_getControlXpath('dropdown', 'general_stock_availability') . "//option[@selected='selected']"));
    }

    /**
     * <p>Verify that entered inventory data (Qty and Stock) is saved after changing attribute set <p>
     *
     * <p>Preconditions:</p>
     * <p>Attribute Set based on Minimal has been created </p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Create simple product</p>
     *  <p>4. Find product in Manage Products grid and open it</p>
     *  <p>5. Change Attribute Set for product to created Attribute Set</p>
     *  <p>6. Open General tab</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 3: Product has been successfully created. Success message appears</p>
     *  <p>After Step 3: Qty data and Stock Availability is the same as entered on Inventory tab</p>
     *  <p>before changing Attribute Set</p>
     *
     * @test
     * @depends createAttributeSet
     * @TestlinkId TL-MAGE-6376
     */
    public function syncDataAfterChangeAttributeSet($attributeSet)
    {
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->changeAttributeSet($attributeSet['set_name']);
        //Verifications
        $this->openTab('general');
        $dropdownXpath = $this->_getControlXpath('dropdown', 'general_stock_availability');
        $this->assertTrue($this->isElementPresent($dropdownXpath) && $this->isEditable($dropdownXpath),
            'General Stock Availability control is disabled or is absent');
        $fieldXpath = $this->_getControlXpath('field', 'general_qty');
        $this->assertTrue($this->isElementPresent($fieldXpath) && $this->isEditable($fieldXpath),
            'General Qty control is disabled or is absent');
        $this->assertEquals($productData['inventory_qty'], $this->getValue($fieldXpath),
            'Qty is not saved after Attribute Set has been changed');
        $this->assertEquals($productData['inventory_stock_availability'],
            $this->getText($dropdownXpath . "//option[@selected='selected']"),
            'Stock Availability is not saved after Attribute Set has been changed');
    }

    /**
     *  <p>Verify that Qty and Stock Availability values are saved after product duplication</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Admin.</p>
     *  <p>2. Navigate Catalog - Manage Products.</p>
     *  <p>3. Create simple product</p>
     *  <p>4. Search created product in the Manage products grid and open it</p>
     *  <p>5. Press the Duplicate button</p>
     *
     * <p>Expected Results:</p>
     *  <p>After Step 5: Success message appears</p>
     *  <p>Values in Qty field and Stock Availability dropdown are the same as in initial product</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6372
     */
    public function saveDataDuplication()
    {
        $productData = $this->loadDataSet('Product', 'simple_product_sync_inventory');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->clickButton('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $this->openTab('inventory');
        //Verifying
        $this->assertEquals($productData['general_qty'],
            $this->getValue($this->_getControlXpath('field', 'inventory_qty')));
        $this->assertEquals($productData['general_stock_availability'], $this->getText(
                $this->_getControlXpath('dropdown', 'inventory_stock_availability')
                . "//option[@selected='selected']"));
        $this->productHelper()->verifyProductInfo(array('general_sku' => $productData['general_sku'] . '-1'));
    }
}
