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
 * Products creation tests with synchronized Inventory and Stock field on Product Details tab
 */
class Core_Mage_Product_Create_QuantityStockControlTest extends Mage_Selenium_TestCase
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
     * @return array
     * @test
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

        return array('attribute' => $attributeData['admin_title'],
                     'option'    => $attributeData['option_1']['admin_option_name']);
    }

    /**
     *<p>Set Default value "In Stock" for quantity_and_stock_status attribute</p>
     *
     * @return array
     * @test
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
     * @return array
     * @test
     */
    public function createAttributeSet()
    {
        $this->markTestIncomplete('MAGETWO-6268');
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
        $this->assertEquals($productData['general_qty'], $this->getControlAttribute('field', 'inventory_qty', 'value'),
            'Inventory Qty data is not equal to entered on General tab data');
        $this->assertEquals($productData['general_stock_availability'],
            $this->getControlAttribute('dropdown', 'inventory_stock_availability', 'selectedLabel'),
            'Selected value for Stock Availability is not equal to entered on General tab data');
    }

    /**
     * <p>Verify that Quantity part of Stock and Quantity control on General tab is disabled for Grouped and</p>
     * <p>Configurable products</p>
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
            $productData = $this->loadDataSet('Product', 'configurable_product_visible', null,
                array('var1_attr_value1'    => $attributeData['option'],
                      'general_attribute_1' => $attributeData['attribute']));
        } else {
            $productData = $this->loadDataSet('Product', 'grouped_product_visible');
        }
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, $productType);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->assertEquals($productData['inventory_stock_availability'],
            $this->getControlAttribute('dropdown', 'general_stock_availability', 'selectedLabel'),
            'Stock Availability Dropdown on General tab is not the same as entered data');
        $this->assertFalse($this->controlIsEditable('field', 'general_qty'),
            'Quantity field is absent or is editable for this product type');
    }

    /**
     * Data Provider for composite product types without Qty control on Inventory tab
     *
     * @return array
     */
    public function productTypeDataProvider()
    {
        return array(
            array('configurable'),
            array('grouped')
        );
    }

    /**
     * <p>Verify that Stock Availability dropdown on General tab is disabled if Qty field is empty</p>
     * <p>Selected value for Qty and Stock dropdown on General tab is default value for qty_and_stock attribute</p>
     *
     * @param array $defaultValue
     *
     * @test
     * @depends setDefaultValue
     * @TestlinkId TL-MAGE-6370
     */

    public function emptyGeneralQuantity($defaultValue)
    {
        $this->markTestIncomplete('MAGETWO-6266');
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        //Verifying
        $this->openTab('general');
        $this->assertFalse($this->controlIsEditable('dropdown', 'general_stock_availability'),
            'Stock Availability dropdown on Product Details tab is editable but should not');
        $this->assertEquals($defaultValue['default_stock_status'],
            $this->getControlAttribute('dropdown', 'general_stock_availability', 'selectedLabel'),
            'Selected value for Stock Availability is not equal to default value of attribute');
    }

    /**
     * <p>Verify that value entered in Qty field on General tab is validated in Qty (Inventory) field<p>
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
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alnum:'))
        );
    }

    /**
     * <p>Verify that last entered data in Inventory controls are saved and synchronized<p>
     * <p>between General and Inventory tab for New Product<p>
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
        $this->assertEquals($productData['inventory_qty'], $this->getControlAttribute('field', 'general_qty', 'value'));
        $this->assertEquals($productData['inventory_stock_availability'],
            $this->getControlAttribute('dropdown', 'general_stock_availability', 'selectedLabel'));
        $this->openTab('inventory');
        $this->assertEquals($productData['inventory_qty'],
            $this->getControlAttribute('field', 'inventory_qty', 'value'));
        $this->assertEquals($productData['inventory_stock_availability'],
            $this->getControlAttribute('dropdown', 'inventory_stock_availability', 'selectedLabel'));
    }

    /**
     * <p>Verify that last entered data in Inventory controls are saved and synchronized<p>
     * <p>between General and Inventory tab for Edit Product<p>
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
        $this->assertEquals($qty, $this->getControlAttribute('field', 'general_qty', 'value'));
        $this->assertEquals($stock,
            $this->getControlAttribute('dropdown', 'general_stock_availability', 'selectedLabel'));
        $this->openTab('inventory');
        $this->assertEquals($qty, $this->getControlAttribute('field', 'inventory_qty', 'value'));
        $this->assertEquals($stock,
            $this->getControlAttribute('dropdown', 'inventory_stock_availability', 'selectedLabel'));
    }

    /**
     * <p>Verify that last entered data in Inventory controls are saved and synchronized<p>
     * <p>between General and Inventory tab without saving product<p>
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
        $selectedStock =
            $this->getControlAttribute('dropdown', 'inventory_stock_availability', 'selectedLabel');
        $this->assertEquals($productData['general_stock_availability'], $selectedStock,
            'Stock Availability is not synchronized for Inventory tab');
        $this->assertEquals($productData['general_qty'], $this->getControlAttribute('field', 'inventory_qty', 'value'),
            'Qty is not synchronized for Inventory tab');
        //Steps
        $this->fillDropdown('inventory_stock_availability', $newStockData);
        $this->fillField('inventory_qty', $qty);
        $this->openTab('general');
        //Verifying
        $this->assertEquals($qty, $this->getControlAttribute('field', 'general_qty', 'value'),
            'Qty is not synchronized for General tab');
        $this->assertEquals($newStockData,
            $this->getControlAttribute('dropdown', 'general_stock_availability', 'selectedLabel'),
            'Stock Availability is not synchronized for General tab');
    }

    /**
     * <p>Verify that entered inventory data (Qty and Stock) is saved after changing attribute set <p>
     *
     * @param array $attributeSet
     *
     * @test
     * @depends createAttributeSet
     * @TestlinkId TL-MAGE-6376
     */
    public function syncDataAfterChangeAttributeSet($attributeSet)
    {
        $this->markTestIncomplete('MAGETWO-6268');
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->changeAttributeSet($attributeSet['set_name']);
        //Verifications
        $this->openTab('general');
        $this->assertTrue($this->controlIsEditable('dropdown', 'general_stock_availability'),
            'General Stock Availability control is disabled or is absent');
        $this->assertTrue($this->controlIsEditable('field', 'general_qty'),
            'General Qty control is disabled or is absent');
        $this->assertEquals($productData['inventory_qty'], $this->getControlAttribute('field', 'general_qty', 'value'),
            'Qty is not saved after Attribute Set has been changed');
        $this->assertEquals($productData['inventory_stock_availability'],
            $this->getControlAttribute('dropdown', 'general_stock_availability', 'selectedLabel'),
            'Stock Availability is not saved after Attribute Set has been changed');
    }

    /**
     *  <p>Verify that Qty and Stock Availability values are saved after product duplication</p>
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
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->clickButton('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $this->openTab('inventory');
        $this->assertEquals($productData['general_qty'], $this->getControlAttribute('field', 'inventory_qty', 'value'),
            'Qty is not saved after product duplication');
        $this->assertEquals($productData['general_stock_availability'],
            $this->getControlAttribute('dropdown', 'inventory_stock_availability', 'selectedLabel'),
            'Stock Availability is not saved after product duplication');
        $this->productHelper()->verifyProductInfo(array('general_sku' => $this->productHelper()
            ->getGeneratedSku($productData['general_sku'])));
    }
}