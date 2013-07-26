<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Import Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ImportExport_Product_CustomOptions_ImportTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }


    /**
     * Precondition:
     * Create new product
     *
     * @test
     * @return array
     */
    public function preconditionExistingOptions()
    {
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] = $this->loadDataSet('Product', 'custom_options_data');
        unset($productData['custom_options_data']['custom_options_file']);
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * Import existing custom option
     *
     * @depends preconditionExistingOptions
     * @test
     * @TestlinkId TL-MAGE-5828
     */
    public function importExistingOptions(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Import csv file with custom options
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Append Complex Data');
        $importResult = $this->importExportHelper()->import($csv);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * Precondition:
     * Create new product
     *
     * @test
     * @return array
     */
    public function preconditionWithDifferentStoreViews()
    {
        $this->markTestIncomplete('Need fix for select Store View on product page');
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] = array(
            'custom_options_field' => $this->loadDataSet('Product', 'custom_options_field')
        );
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Create new store view
        $this->navigate('manage_stores');
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Change Title for specific store view
        $this->navigate('manage_products');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->selectStoreScope('dropdown', 'choose_store_view',
            'Main Website/Main Website Store/' . $storeViewData['store_view_name'], true);
        $this->waitForPageToLoad();
        $this->productHelper()->openProductTab('custom_options');
        //Need to update custom option, get optionId by Title
        $optionId = $this->productHelper()->getCustomOptionIdByName('Custom Option Field');
        $this->addParameter('optionId', $optionId);
        $this->fillCheckbox('custom_options_default_value', 'No');
        $this->fillField('custom_options_general_title', 'Custom Option Field ' . $storeViewData['store_view_name']);
        $this->productHelper()->saveProduct();
        //switch to all views
        $this->selectStoreScope('dropdown', 'choose_store_view');
        $this->waitForPageToLoad();
        return $productData;
    }

    /**
     * Import custom option if its name matches to existing default name
     *
     * @depends preconditionWithDifferentStoreViews
     * @test
     * @TestlinkId TL-MAGE-5829
     */
    public function importWithDifferentStoreViews(array $product)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $product['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Modify csv for Test Case condition
        $csv[0]['_custom_option_title'] = $csv[1]['_custom_option_title'];
        //Import csv file with custom options
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Append Complex Data');
        $importResult = $this->importExportHelper()->import($csv);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $product['custom_options_data']['custom_options_field1'] = $this->loadDataSet('Product', 'custom_options_field',
            array('custom_options_general_title' => $csv[0]['_custom_option_title']));
        $this->productHelper()->verifyProductInfo($product);
    }

    /**
     * Precondition:
     * Create new product
     *
     * @test
     * @return array
     */
    public function preconditionDifferentDropdownOptions()
    {
        $this->navigate('manage_products');
        $product = $this->loadDataSet('Product', 'simple_product_required');
        $product['custom_options_data'] = array(
            'custom_options_dropdown' => $this->loadDataSet('Product', 'custom_options_dropdown')
        );
        $this->productHelper()->createProduct($product);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $product['general_sku']));
        $this->productHelper()->openProduct($search);
        $this->productHelper()->verifyProductInfo($product);
        return $product;
    }

    /**
     * Import custom option with different dropdown options
     *
     * @depends preconditionDifferentDropdownOptions
     * @test
     * @TestlinkId TL-MAGE-5831
     */
    public function importDifferentDropdownOptions(array $simple)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $simple['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Modify csv for Test Case condition
        $csv[1] = $csv[0];
        foreach ($csv[1] as &$value) {
            $value = '';
        }
        $csv[0]['_custom_option_row_title'] = 'red';
        $csv[1]['_custom_option_row_title'] = 'black';
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Append Complex Data');
        $importResult = $this->importExportHelper()->import($csv);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        $this->productHelper()->openProduct($productSearch);
        $simple['custom_options_data']['custom_options_dropdown']['custom_option_row_2'] =
            $simple['custom_options_data']['custom_options_dropdown']['custom_option_row_1'];
        unset($simple['custom_options_data']['custom_options_dropdown']['custom_option_row_1']);
        $simple['custom_options_data']['custom_options_dropdown']['custom_option_row_1']['custom_options_title'] =
            $csv[1]['_custom_option_row_title'];
        $simple['custom_options_data']['custom_options_dropdown']['custom_option_row_2']['custom_options_title'] =
            $csv[0]['_custom_option_row_title'];
        $simple['custom_options_data']['custom_options_dropdown']['custom_option_row_2']['custom_options_sort_order'] =
            2;
        $simple['custom_options_data']['custom_options_dropdown']['custom_option_row_1']['custom_options_sort_order'] =
            1;
        $this->productHelper()->verifyProductInfo($simple);
    }

    /**
     * Precondition:
     * Create new product
     *
     * @test
     * @return array
     */
    public function preconditionWithPartOptions()
    {
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] =
            array('custom_options_dropdown' => $this->loadDataSet('Product', 'custom_options_dropdown'));
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * Import custom option with part of dropdown options
     *
     * @depends preconditionWithPartOptions
     * @test
     * @TestlinkId TL-MAGE-5832
     */
    public function importWithPartOptions(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Modify csv for Test Case condition

        $csv[0]['_custom_option_row_title'] = 'red';
        $csv = array_slice($csv, 0, 1);
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Append Complex Data');
        $importResult = $this->importExportHelper()->import($csv);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        $this->productHelper()->openProduct($productSearch);
        $productData['custom_options_data']['custom_options_dropdown']['custom_option_row_1']['custom_options_title'] =
            $csv[0]['_custom_option_row_title'];
        unset($productData['custom_options_data']['custom_options_dropdown']['custom_option_row_2']);
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * Precondition:
     * Create new product
     *
     * @test
     * @return array
     */
    public function preconditionWithEmptyPrice()
    {
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] =
            array('custom_options_field' => $this->loadDataSet('Product', 'custom_options_field'));
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * Empty custom option price field in csv file
     *
     * @depends preconditionWithEmptyPrice
     * @test
     * @TestlinkId TL-MAGE-5833
     */
    public function importWithEmptyPrice(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Modify csv for Test Case condition
        $csv[0]['_custom_option_price'] = '0.00';
        $csv = array_slice($csv, 0, 1);
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Append Complex Data');
        $importResult = $this->importExportHelper()->import($csv);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        $this->productHelper()->openProduct($productSearch);
        $productData['custom_options_data']['custom_options_field']['custom_options_price'] =
            $csv[0]['_custom_option_price'];
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * Precondition:
     * Create new product
     *
     * @test
     * @return array
     */
    public function preconditionAddOptions()
    {
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] = array(
            'custom_options_dropdown' => $this->loadDataSet('Product', 'custom_options_dropdown',
                array('custom_options_general_sort_order' => '%noValue%'))
        );
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * Adding new custom option via import
     *
     * @depends preconditionAddOptions
     * @test
     * @TestlinkId TL-MAGE-5837
     */
    public function importAddOptions(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Modify csv for Test Case condition: add new custom option
        $csv[0]['_custom_option_type'] = 'field';
        $csv[0]['_custom_option_title'] = 'Custom Option Field';
        $csv[0]['_custom_option_row_title'] = '';
        $csv[0]['_custom_option_row_sort'] = '';
        $csv[0]['_custom_option_price'] = '0.00';
        $csv[0]['_custom_option_sku'] = '';
        $csv[0]['_custom_option_sort_order'] = '';
        $csv[0]['_custom_option_is_required'] = '0';
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Append Complex Data');
        $importResult = $this->importExportHelper()->import($csv);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        $this->productHelper()->openProduct($productSearch);
        $productData['custom_options_data']['custom_options_field'] =
            $this->loadDataSet('Product', 'custom_options_field', array(
                'custom_options_general_is_required' => 'No',
                'custom_options_price' => '0.00',
                'custom_options_general_sort_order' => '%noValue%',
                'custom_options_sku' => '',
                'custom_options_max_characters' => '0'
            ));
        $this->productHelper()->verifyCustomOptions($productData['custom_options_data']);
    }
}