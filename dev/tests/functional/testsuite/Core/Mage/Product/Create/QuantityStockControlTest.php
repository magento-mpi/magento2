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
            array('Product Details' => $attributeData['attribute_code']));
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
        $productData['inventory_manage_stock_default'] = 'No';
        $productData['inventory_manage_stock'] = 'Yes';
        $productData['inventory_qty'] = $productData['general_qty'];
        $productData['inventory_stock_availability'] = $productData['general_stock_availability'];
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Verify that Quantity control on General tab is disabled for Grouped and Configurable products</p>
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
        $byValueParam = ($productType == 'configurable')
            ? array('var1_attr_value1'    => $attributeData['option'],
                    'general_attribute_1' => $attributeData['attribute'])
            : null;
        $productData = $this->loadDataSet('Product', $productType . '_product_visible', null, $byValueParam);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, $productType);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $productData['inventory_manage_stock_default'] = 'Yes';
        $productData['inventory_manage_stock'] = 'Yes';
        $productData['inventory_stock_availability'] = $productData['general_stock_availability'];
        $this->productHelper()->verifyProductInfo($productData);
        $this->productHelper()->openProductTab('general');
        $this->assertFalse($this->controlIsEditable('field', 'general_qty'),
            'Quantity control is editable on General Tab');
        $this->assertTrue($this->controlIsEditable('dropdown', 'general_stock_availability'),
            'Stock_availability control is not editable on General Tab');
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
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        //Verifying
        $this->productHelper()->openProductTab('general');
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
        $this->productHelper()->openProductTab('inventory');
        $this->addFieldIdToMessage('field', 'inventory_qty');
        $this->assertMessagePresent('validation', 'enter_valid_number');
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
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
        $product = $this->loadDataSet('Product', 'simple_product_sync_inventory');
        $inventory = array('inventory_stock_availability' => 'In Stock', 'inventory_qty' => '9');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product, 'simple', false);
        $this->fillTab($inventory, 'inventory');
        $this->addParameter('elementTitle', $product['general_name']);
        $this->productHelper()->saveProduct('continueEdit');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $product['general_qty'] = $inventory['inventory_qty'];
        $product['general_stock_availability'] = $inventory['inventory_stock_availability'];
        $product = array_merge($product, $inventory);
        $this->productHelper()->verifyProductInfo($product);
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
        $verifyData = array('general_stock_availability' => $stock, 'inventory_stock_availability' => $stock,
                            'inventory_qty'              => $qty, 'general_qty' => $qty);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->fillField('general_qty', $qty);
        $this->fillDropdown('general_stock_availability', $stock);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($verifyData);
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
        $product = $this->loadDataSet('Product', 'simple_product_sync_inventory');
        $newStockData = 'In Stock';
        $qty = $this->generate('string', 5, ':digit:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product, 'simple', false);
        //Verifying
        $this->productHelper()->verifyProductInfo(
            array('inventory_stock_availability' => $product['general_stock_availability'],
                  'inventory_qty'                => $product['general_qty'])
        );
        //Steps
        $this->fillDropdown('inventory_stock_availability', $newStockData);
        $this->fillField('inventory_qty', '');
        $this->fillDropdown('inventory_manage_stock', 'Yes');
        $this->fillField('inventory_qty', $qty);
        $this->productHelper()->verifyProductInfo(array('general_stock_availability' => $newStockData,
                                                        'general_qty'                => $qty));
    }

    /**
     * <p>Verify that entered inventory data (Qty and Stock) is saved after changing attribute set <p>
     *
     * @test
     * @TestlinkId TL-MAGE-6376
     */
    public function syncDataAfterChangeAttributeSet()
    {
        //Data
        $setData = $this->loadDataSet('AttributeSet', 'attribute_set');
        $productData = $this->loadDataSet('Product', 'simple_product_sync_inventory');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($setData);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->changeAttributeSet($setData['set_name']);
        //Verifications
        $productData['inventory_manage_stock_default'] = 'No';
        $productData['inventory_manage_stock'] = 'Yes';
        $productData['inventory_qty'] = $productData['general_qty'];
        $productData['inventory_stock_availability'] = $productData['general_stock_availability'];
        $productData['product_attribute_set'] = $setData['set_name'];
        $this->productHelper()->verifyProductInfo($productData);
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
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $productData['general_sku'] = $this->productHelper()->getGeneratedSku($productData['general_sku']);
        $productData['general_qty'] = '';
        $this->productHelper()->verifyProductInfo($productData,
            array('product_attribute_set', 'product_online_status', 'general_stock_availability'));
    }
}