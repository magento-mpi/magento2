<?php
/**
 * Magento
 *
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
class Community2_Mage_ImportExport_Import_ProductTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Import/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
    }

    /**
     * <p>Precondition:</p>
     * <p>Create new product</p>
     *
     * @test
     * @return array
     */
    public function preconditionImport()
    {
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] = $this->loadDataSet('Product', 'custom_options_data');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Existing Custom Option - Yes, Imported Custom Option - No</p>
     *
     * @depends preconditionImport
     * @test
     * @TestlinkId TL-MAGE-1120
     */
    public function importWithoutOptions(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv,
            'Export has not been finished successfully');
        //$csv = array_slice($csv, 0, 1);
        //Remove custom options columns from export csv
        $optionsFieldsPosition = array_search("_custom_option_store", array_keys($csv[0]));
        $csv[0] = array_slice($csv[0], 0, $optionsFieldsPosition);
        //Import csv file without custom options
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
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Existing Custom Option - No, Imported Custom Option - Yes</p>
     *
     * @depends preconditionImport
     * @test
     * @TestlinkId TL-MAGE-1121
     */
    public function importWithOptions(array $productData)
    {
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Products');
        //set filter by sku
        $this->importExportHelper()->setFilter(array('sku' => $productData['general_sku']));
        //Perform export
        $csv = $this->importExportHelper()->export();
        //Verify export result
        $this->assertNotNull($csv,
            'Export has not been finished successfully');
        //Clear Products Custom options
        $this->navigate('manage_products');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->productHelper()->deleteCustomOptions();
        $this->productHelper()->saveForm('save');
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
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Import product with custom options when importing file or Magento has ambiguity</p>
     * <p>Precondition:</p>
     * <p>TC1. A product created with with custom option 'brand', field input type</p>
     * <p>Csv file contains product with custom option 'brand', area input type, price $10.</p>
     * <p>TC2. Product created with two custom options with the same name 'brand', field and area input type.</p>
     * <p>Csv file contains product with custom option 'brand', area input type.</p>
     * <p>Steps:</p>
     * <p>1. Go to System -> Import/Export -> Import</p>
     * <p>2. Select 'Products' entity type</p>
     * <p>3. Select 'Append Complex Data' Import Behavior</p>
     * <p>4. Select file from precondition</p>
     * <p>5. Click 'Check Data' button</p>
     * <p>Expected:</p>
     * <p>Validation error is shown. Message 'Please fix errors and re-upload file' is shown.</p>
     *
     * @dataProvider AmbiguousData
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

    public function AmbiguousData()
    {
        $productData1 = $this->loadDataSet('Product', 'simple_product_required');
        $productData1['custom_options_data'] = array(
            'custom_options_field' => $this->loadDataSet('Product', 'custom_options_field', array(
                'custom_options_general_title' => 'brand')),
        );
        $customOptionCsv1 = array(
            '_custom_option_type' => 'area',
            '_custom_option_price' => '18.0000',
        );
        $validation1 = array('validation' => array(
            'error' => array(
                "Customer with such email and website code doesn't exist in rows: 1"
            ),
            'validation' => array(
                "TBD",
                "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")),
        );
        $productData2 = $this->loadDataSet('Product', 'simple_product_required');
        $productData2['custom_options_data'] = array(
            'custom_options_area1' => $this->loadDataSet('Product', 'custom_options_area', array(
                'custom_options_general_title' => 'brand')),
            'custom_options_area2' => $this->loadDataSet('Product', 'custom_options_area', array(
                'custom_options_general_title' => 'brand')),
        );
        $customOptionCsv2 = array(
            '_custom_option_price' => '20.0000',
        );
        $validation2 = array('validation' => array(
            'error' => array(
                "TBD"
            ),
            'validation' => array(
                "File is totally invalid. Please fix errors and re-upload file",
                "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")),
        );
        return array(
            array($productData1, array($customOptionCsv1), $validation1),
            array($productData2, array($customOptionCsv2), $validation2),
        );
    }
}
