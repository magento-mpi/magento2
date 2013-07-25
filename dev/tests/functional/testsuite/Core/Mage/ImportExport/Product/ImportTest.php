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
class Core_Mage_ImportExport_Product_ImportTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->runMassAction('Delete', 'all');
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
     * <p>Precondition:</p>
     * <p>Create new product</p>
     *
     * @test
     * @return array
     */
    public function preconditionAppendImport()
    {
        $options = $this->loadDataSet('Product', 'custom_options_data',
            array('option_10' => '%noValue%'));
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('custom_options_data' => $options));
        $this->navigate('manage_products');
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Existing Custom Option - Yes, Imported Custom Option - No</p>
     *
     * @depends preconditionAppendImport
     * @test
     * @TestlinkId TL-MAGE-1120
     */
    public function appendWithoutOptions(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //$csv = array_slice($csv, 0, 1);
        //Remove custom options columns from export csv
        $fieldsPosition = array_search("_custom_option_store", array_keys($csv[0]));
        $csv[0] = array_slice($csv[0], 0, $fieldsPosition);
        //Import csv file without custom options
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Append Complex Data');
        $importResult = $this->importExportHelper()->import(array($csv[0]));
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Existing Custom Option - No, Imported Custom Option - Yes</p>
     *
     * @depends preconditionAppendImport
     * @test
     * @TestlinkId TL-MAGE-1121
     */
    public function appendWithOptions(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Clear Products Custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->productHelper()->deleteAllCustomOptions();
        $this->productHelper()->saveProduct();
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
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Precondition:</p>
     * <p>Create new product</p>
     *
     * @test
     * @return array
     */
    public function preconditionReplaceImport()
    {
        $options = $this->loadDataSet('Product', 'custom_options_data',
            array('option_10' => '%noValue%'));
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('custom_options_data' => $options));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Existing Custom Option - Yes, Imported Custom Option - No</p>
     *
     * @depends preconditionReplaceImport
     * @test
     * @TestlinkId TL-MAGE-1141
     *
     * @param array $productData
     */
    public function replaceWithoutOptions(array $productData)
    {
        $this->markTestIncomplete('MAGETWO-2556');
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Remove custom options columns from export csv
        $csvWithoutOptions = array($csv[0]);
        $customOptionsData = array(
            '_custom_option_store', '_custom_option_type', '_custom_option_title', '_custom_option_is_required',
            '_custom_option_price', '_custom_option_sku', '_custom_option_max_characters',
            '_custom_option_sort_order', '_custom_option_row_title', '_custom_option_row_price',
            '_custom_option_row_sku', '_custom_option_row_sort'
        );
        foreach ($customOptionsData as $value) {
            $csvWithoutOptions[0][$value] = '';
        }
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Replace Existing Complex Data');
        $importResult = $this->importExportHelper()->import($csvWithoutOptions);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->openProductTab('custom_options');
        $this->assertEquals(0, $this->getControlCount('fieldset', 'custom_option_set'),
            'Custom options were not deleted');
    }

    /**
     * <p>Existing Custom Option - No, Imported Custom Option - Yes</p>
     * <p>Delete product with custom options</p>
     *
     * @depends preconditionReplaceImport
     * @test
     * @TestlinkId TL-MAGE-1142, TL-MAGE-1161
     */
    public function replaceWithOptions(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        //Clear Products Custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->productHelper()->deleteAllCustomOptions();
        $this->productHelper()->saveProduct();
        //Import csv file with custom options
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Replace Existing Complex Data');
        $importResult = $this->importExportHelper()->import($csv);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $productSearch = $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
        //Steps
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Products', 'Delete Entities');
        $importResult = $this->importExportHelper()->import($csv);
        //Verify import result
        $this->assertArrayHasKey('import', $importResult,
            "Import has not been finished successfully: " . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            "Import has not been finished successfully" . print_r($importResult, true));
        //Open Product and verify custom options
        $this->navigate('manage_products');
        $this->assertFalse($this->productHelper()->isProductPresentInGrid($productSearch),
            'Product is found with data: ' . print_r($productSearch, true));
    }

    /**
     * <p>Import product with custom options when importing file or Magento has ambiguity</p>
     *
     * @dataProvider ambiguousData
     * @test
     * @TestlinkId TL-MAGE-5830, 5834
     */
    public function importAmbiguousData(array $productData, array $customOptionCsv, array $validationMessage)
    {
        //Precondition: create product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Precondition: create csv file
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        $csv = $this->importExportHelper()->export();
        $this->assertNotNull($csv, 'Export has not been finished successfully');
        $csv = array($csv[0]);
        $csv[0] = $customOptionCsv[0] + $csv[0];
        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Products', 'Append Complex Data');
        //Steps 4-5
        $importResult = $this->importExportHelper()->import($csv);
        //Verifying
        $this->assertEquals($validationMessage, $importResult, 'Import has been finished with issues');
    }

    public function ambiguousData()
    {
        $returnData = array();
        //Create first product
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] =
            array('custom_options_field' => $this->loadDataSet('Product', 'custom_options_field',
                array('custom_options_general_title' => 'brand')),);
        $customOptionCsv = array('_custom_option_type' => 'area', '_custom_option_price' => '18.0000',);
        $validation = array('validation' => array(
            'error' => array(
                "Custom options have different types. in rows: 1"
            ),
            'validation' => array(
                "File is totally invalid. Please fix errors and re-upload file.",
                "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1"
            )
        ));
        $returnData[] = array($productData, array($customOptionCsv), $validation);
        //Create second product
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] = array(
            'custom_options_area1' => $this->loadDataSet('Product', 'custom_options_area', array(
                'custom_options_general_title' => 'brand')),
            'custom_options_area2' => $this->loadDataSet('Product', 'custom_options_area', array(
                'custom_options_general_title' => 'brand')),
        );
        $customOptionCsv = array('_custom_option_price' => '20.0000',);
        $validation = array('validation' => array(
            'error' => array(
                "There are several existing custom options with such name. in rows: 1"
            ),
            'validation' => array(
                "File is totally invalid. Please fix errors and re-upload file.",
                "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")),
        );
        $returnData[] = array($productData, array($customOptionCsv), $validation);
        return $returnData;
    }
}
