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
 * Duplicate product tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_DuplicateTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->runMassAction('Delete', 'all');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * Test Realizing precondition for creating configurable product.
     *
     * @test
     * @return array $attrData
     */
    public function createConfigurableAttribute()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrData['advanced_attribute_properties']['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $attrData;
    }

    /**
     * Test Realizing precondition for duplicating products.
     *
     * @param array $attrData
     *
     * @return array $productData
     * @test
     * @depends createConfigurableAttribute
     */
    public function createProducts($attrData)
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $simple['general_user_attr']['dropdown'][$attrData['advanced_attribute_properties']['attribute_code']] =
            $attrData['option_1']['admin_option_name'];
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible');
        $virtual['general_user_attr']['dropdown'][$attrData['advanced_attribute_properties']['attribute_code']] =
            $attrData['option_2']['admin_option_name'];
        $downloadable = $this->loadDataSet('SalesOrder', 'downloadable_product_for_order',
            array('downloadable_links_purchased_separately' => 'No'));
        $downloadable['general_user_attr']['dropdown'][$attrData['advanced_attribute_properties']['attribute_code']] =
            $attrData['option_3']['admin_option_name'];

        $productData = array('simple' => $simple, 'downloadable' => $downloadable, 'virtual' => $virtual);
        //Steps
        foreach ($productData as $key => $value) {
            $this->productHelper()->createProduct($value, $key);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_product');
        }

        return array(
            'related_search_sku' => $simple['general_sku'],
            'up_sells_search_sku' => $downloadable['general_sku'],
            'cross_sells_search_sku' => $virtual['general_sku']
        );
    }

    /**
     * <p>Creating duplicated simple product</p>
     *
     * @param array $attrData
     * @param array $assignData
     *
     * @test
     * @depends createConfigurableAttribute
     * @depends createProducts
     * @TestlinkId TL-MAGE-3431
     */
    public function duplicateSimple($attrData, $assignData)
    {
        //Data
        $simple = $this->loadDataSet('Product', 'duplicate_simple', $assignData);
        $simple['general_user_attr']['dropdown'][$attrData['advanced_attribute_properties']['attribute_code']] =
            $attrData['option_1']['admin_option_name'];
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $simple['general_sku']));
        //Steps
        $this->productHelper()->createProduct($simple);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $simple['general_sku'] = $this->productHelper()->getGeneratedSku($simple['general_sku']);
        $this->productHelper()->verifyProductInfo($simple, array('product_attribute_set', 'product_online_status'));
    }

    /**
     * <p>Creating duplicated virtual product</p>
     *
     * @param array $attrData
     * @param array $assignData
     *
     * @test
     * @depends createConfigurableAttribute
     * @depends createProducts
     * @TestlinkId TL-MAGE-3432
     */
    public function duplicateVirtual($attrData, $assignData)
    {
        //Data
        $virtual = $this->loadDataSet('Product', 'duplicate_virtual', $assignData);
        $virtual['general_user_attr']['dropdown'][$attrData['advanced_attribute_properties']['attribute_code']] =
            $attrData['option_2']['admin_option_name'];
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $virtual['general_sku']));
        //Steps
        $this->productHelper()->createProduct($virtual, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $virtual['general_sku'] = $this->productHelper()->getGeneratedSku($virtual['general_sku']);
        $this->productHelper()->verifyProductInfo($virtual, array('product_attribute_set', 'product_online_status'));
    }

    /**
     * <p>Creating duplicated downloadable product</p>
     *  <p>$linkStatus - Yes, if Links can be purchased separately</p>
     *  <p>$linkStatus - No, if Links can not be purchased separately</p>
     *
     * @param array $attrData
     * @param array $assignData
     * @param string $linksSeparately
     * @param string|float $linkPrice
     *
     * @test
     * @depends createConfigurableAttribute
     * @depends createProducts
     * @dataProvider linkInfoDataProvider
     * @TestlinkId TL-MAGE-3429
     */
    public function duplicateDownloadable($linksSeparately, $linkPrice, $attrData, $assignData)
    {
        //Data
        $downloadable = $this->loadDataSet('Product', 'duplicate_downloadable', $assignData);
        $downloadable['general_user_attr']['dropdown'][$attrData['advanced_attribute_properties']['attribute_code']] =
            $attrData['option_3']['admin_option_name'];
        $downloadable['downloadable_information_data']['downloadable_links_purchased_separately'] = $linksSeparately;
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $downloadable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($downloadable, 'downloadable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $downloadable['general_sku'] = $this->productHelper()->getGeneratedSku($downloadable['general_sku']);
        $downloadable['downloadable_information_data']['downloadable_link_1']['downloadable_link_row_price']
            = $linkPrice;
        $this->productHelper()->verifyProductInfo($downloadable,
            array('product_attribute_set', 'product_online_status'));
    }

    public function linkInfoDataProvider()
    {
        return array(
            array('Yes', '12'),
            array('No', '0.00')
        );
    }

    /**
     * <p>Creating grouped product with associated products</p>
     *
     * @param array $assignData
     *
     * @test
     * @depends createProducts
     * @TestlinkId TL-MAGE-3430
     */
    public function duplicateGrouped($assignData)
    {
        //Data
        $grouped = $this->loadDataSet('Product', 'duplicate_grouped', $assignData,
            array('product_1' => $assignData['related_search_sku'],
                'product_2' => $assignData['up_sells_search_sku'],
                'product_3' => $assignData['cross_sells_search_sku']));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $grouped['general_sku']));
        //Steps
        $this->productHelper()->createProduct($grouped, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $grouped['general_sku'] = $this->productHelper()->getGeneratedSku($grouped['general_sku']);
        $this->productHelper()->verifyProductInfo($grouped, array('product_attribute_set', 'product_online_status'));
    }

    /**
     * <p>Creating duplicated Bundle Product</p>
     *
     * @param $data
     * @param array $assignData
     *
     * @test
     * @dataProvider duplicateBundleDataProvider
     * @depends createProducts
     * @TestlinkId TL-MAGE-3427
     */
    public function duplicateBundle($data, $assignData)
    {
        //Data
        $bundle = $this->loadDataSet('Product', $data, $assignData, array(
            'product_1' => $assignData['related_search_sku'],
            'product_2' => $assignData['cross_sells_search_sku']
        ));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $bundle['general_sku']));
        //Steps
        $this->productHelper()->createProduct($bundle, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $bundle['general_sku'] = $this->productHelper()->getGeneratedSku($bundle['general_sku']);
        $this->productHelper()->verifyProductInfo($bundle, array('product_attribute_set', 'product_online_status'));
    }

    public function duplicateBundleDataProvider()
    {
        return array(
            array('duplicate_fixed_bundle'),
            array('duplicate_dynamic_bundle')
        );
    }

    /**
     * <p>Duplicate Configurable product with associated products</p>
     *
     * @param array $attrData
     * @param array $assignData
     *
     * @test
     * @depends createConfigurableAttribute
     * @depends createProducts
     * @TestlinkId TL-MAGE-3428
     */
    public function duplicateConfigurable($attrData, $assignData)
    {
        $this->markTestIncomplete('MAGETWO-4511');
        //Data
        $configurable = $this->loadDataSet('Product', 'duplicate_configurable', $assignData, array(
            'var1_attr_value1' => $attrData['option_1']['admin_option_name'],
            'general_attribute_1' => $attrData['attribute_properties']['attribute_label']
        ));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_duplicated_product');
        //Steps
        $this->productHelper()->fillConfigurableSettings($configurable);
        //Verifying
        $configurable['general_sku'] = $this->productHelper()->getGeneratedSku($configurable['general_sku']);
        $this->productHelper()->verifyProductInfo($configurable,
            array('product_attribute_set', 'product_online_status'));
    }
}
