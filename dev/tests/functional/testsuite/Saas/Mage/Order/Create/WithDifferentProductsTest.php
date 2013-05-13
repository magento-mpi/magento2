<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Order
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order creation with different type of products
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Order_Create_WithDifferentProductsTest extends Core_Mage_Order_Create_WithDifferentProductsTest
{
    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     * <p>Setup Flat Rate.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

     /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     * <p>Navigate to 'Manage Products' page</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Create all types of products
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attrCode = $attrData['attribute_code'];
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrData['attribute_code']));
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $simple['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible');
        $virtual['general_user_attr']['dropdown'][$attrCode] = $attrData['option_2']['admin_option_name'];
        $bundle = $this->loadDataSet('SalesOrder', 'fixed_bundle_for_order', null,
            array(
                'add_product_1' => $simple['general_sku'],
                'add_product_2' => $virtual['general_sku']
            ));
        $configurable = $this->loadDataSet('SalesOrder', 'configurable_product_for_order', null,
            array(
                'general_attribute_1' => $attrData['admin_title'],
                'var1_attr_value1' => $attrData['option_1']['admin_option_name'],
                'var1_attr_value2' => $attrData['option_2']['admin_option_name'],
                'var1_attr_value3' => $attrData['option_3']['admin_option_name']
            ));
        $grouped = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', null,
            array(
                'associated_1' => $simple['general_sku'],
                'associated_2' => $virtual['general_sku'],
            ));
        //Steps and Verification
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($bundle, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('simple_name'         => $simple['general_name'],
                     'simple_sku'          => $simple['general_sku'],
                     'simple_option'       => $attrData['option_1']['admin_option_name'],
                     'virtual_name'        => $virtual['general_name'],
                     'virtual_sku'         => $virtual['general_sku'],
                     'virtual_option'      => $attrData['option_2']['admin_option_name'],
                     'bundle_name'         => $bundle['general_name'],
                     'bundle_sku'          => $bundle['general_sku'],
                     'configurable_name'   => $configurable['general_name'],
                     'configurable_sku'    => $configurable['general_sku'],
                     'grouped_name'        => $grouped['general_name'],
                     'grouped_sku'         => $grouped['general_sku'],
                     'title'               => $attrData['admin_title']);
    }

    /**
     * <p>Creating order with downloadable products</p>
     *
     * @test
     * @TestlinkId	TL-MAGE-3280
     */
    public function withDownloadableConfigProduct()
    {
        $this->markTestIncomplete('Functionality is absent in Magento Go.');
    }

       public function productDataProvider()
    {
        return array(
            array('simple', 'order_physical'),
            array('virtual', 'order_virtual')
        );
    }

    public function withoutOptionsDataProvider()
    {
        return array(
            array('simple', 'order_physical'),
            array('virtual', 'order_virtual'),
        );
    }
}
