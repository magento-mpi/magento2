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
class Community2_Mage_ImportExport_ImportCustomOptions_ProductTest extends Mage_Selenium_TestCase
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
     * test
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
     * <p>Precondition:</p>
     * <p>Create new product</p>
     *
     * @test
     * @return array
     */
    public function preconditionStoreViewImport()
    {
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['custom_options_data'] =
            array (
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
        $this->storeHelper()->selectStoreView(
            'choose_store_view',
            'Main Website',
            'Main Website Store',
            $storeViewData['store_view_name']
        );
        $this->openTab('custom_options');
        //Need to update custom option, get optionId by Title
        $optionId = $this->productHelper()->getCustomOptionId('Custom Option Field');
        $this->addParameter('optionId', $optionId);
        $this->fillCheckbox('custom_options_default_value','No');
        $this->fillField('custom_options_general_title','Custom Option Field ' . $storeViewData['store_view_name']);
        $this->productHelper()->saveForm('save');
        //switch to all views
        $this->storeHelper()->defaultStoreView('choose_store_view');
        return $productData;
    }

    /**
     * <p>Import existing custom option</p>
     *
     * depends preconditionImport
     * test
     * TestlinkId TL-MAGE-5828
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
        $this->assertNotNull($csv,
            'Export has not been finished successfully');
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
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Import custom option if its name matches to existing default name</p>
     *
     * @depends preconditionStoreViewImport
     * @test
     * @TestlinkId TL-MAGE-5829
     */
    public function importWithDifferentStoreViews(array $productData)
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
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $csv[0]['sku']));
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $productData['custom_options_data'][] =
            array (
                'custom_options_field1' => $this->loadDataSet('Product', 'custom_options_field',
                                            array('custom_options_general_title' => $csv[0]['_custom_option_title']))
            );
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Import custom option with different dropdown options</p>
     *
     * @depends preconditionImport
     * @test
     * @TestlinkId TL-MAGE-5831
     */
    public function importWithOptions1(array $productData)
    {
    }

    /**
     * <p>Import custom option with part of dropdown options</p>
     *
     * @depends preconditionImport
     * @test
     * @TestlinkId TL-MAGE-5832
     */
    public function importWithOptions2(array $productData)
    {
    }

    /**
     * <p>Empty custom option price field in csv file</p>
     *
     * @depends preconditionImport
     * @test
     * @TestlinkId TL-MAGE-5833
     */
    public function importWithOptions3(array $productData)
    {
    }

    /**
     * <p>Adding new custom option via import</p>
     *
     * @depends preconditionImport
     * @test
     * @TestlinkId TL-MAGE-5837
     */
    public function importWithOptions4(array $productData)
    {

    }

}
