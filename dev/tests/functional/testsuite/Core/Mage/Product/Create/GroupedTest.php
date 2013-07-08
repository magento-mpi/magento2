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
 * Grouped product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Create_GroupedTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->runMassAction('Delete', 'all');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Creating product with required fields only</p>
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-3409
     */
    public function onlyRequiredFieldsInGrouped()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'grouped_product_required');
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData;
    }

    /**
     * <p>Creating product with all fields</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3400
     * @test
     */
    public function allFieldsInGrouped()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'grouped_product');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with existing SKU</p>
     *
     * @param array $productData
     *
     * @test
     * @depends onlyRequiredFieldsInGrouped
     * @TestlinkId TL-MAGE-3402
     */
    public function existSkuInGrouped($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped', false);
        $this->addParameter('elementTitle', $productData['general_name']);
        $this->productHelper()->saveProduct('continueEdit');
        //Verifying
        $newSku = $this->productHelper()->getGeneratedSku($productData['general_sku']);
        $this->addParameter('productSku', $newSku);
        $this->addParameter('productName', $productData['general_name']);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'sku_autoincremented');
        $productData['general_sku'] = $newSku;
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with empty required fields</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @TestlinkId TL-MAGE-15
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends onlyRequiredFieldsInGrouped
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $field = key($emptyField);
        $product = $this->loadDataSet('Product', 'grouped_product_required', $emptyField);
        //Steps
        $this->productHelper()->createProduct($product, 'grouped');
        //Verifying
        $this->addFieldIdToMessage($fieldType, $field);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array(array('general_name' => '%noValue%'), 'field'),
            array(array('general_sku' => ''), 'field'),
        );
    }

    /**
     * <p>Creating product with special characters into required fields</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3410
     * @test
     */
    public function specialCharactersInTextFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'grouped_product_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_sku'               => $this->generate('string', 32, ':punct:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with long values from required fields</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3408
     * @test
     */
    public function longValuesInTextFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'grouped_product_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_sku'               => $this->generate('string', 64, ':alnum:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with SKU length more than 64 characters.</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3407
     * @test
     */
    public function incorrectSkuLengthInGrouped()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'grouped_product_required',
            array('general_sku' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating Grouped product with Simple product</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3405
     * @test
     */
    public function groupedWithSimpleProduct()
    {
        //Data
        $simpleData = $this->loadDataSet('Product', 'simple_product_required');
        $groupedData = $this->loadDataSet('Product', 'grouped_product_required',
            array('associated_search_sku' => $simpleData['general_sku']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $groupedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($simpleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($groupedData);

        return $simpleData['general_sku'];
    }

    /**
     * <p>Creating Grouped product with Virtual product</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3406
     * @test
     */
    public function groupedWithVirtualProduct()
    {
        //Data
        $virtualData = $this->loadDataSet('Product', 'virtual_product_required');
        $groupedData = $this->loadDataSet('Product', 'grouped_product_required',
            array('associated_search_sku' => $virtualData['general_sku']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $groupedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($virtualData, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($groupedData);

        return $virtualData['general_sku'];
    }

    /**
     * <p>Creating Grouped product with Downloadable product</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3404
     * @test
     */
    public function groupedWithDownloadableProduct()
    {
        //Data
        $downloadableData = $this->loadDataSet('Product', 'downloadable_product_required',
            array('downloadable_links_purchased_separately' => 'No'));
        $groupedData = $this->loadDataSet('Product', 'grouped_product_required',
            array('associated_search_sku' => $downloadableData['general_sku']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $groupedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($downloadableData, 'downloadable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($groupedData);

        return $downloadableData['general_sku'];
    }

    /**
     * <p>Creating Grouped product with All types of products type</p>
     *
     * @param string $simpleSku
     * @param string $virtualSku
     * @param string $downloadableSku
     *
     * @test
     * @depends groupedWithSimpleProduct
     * @depends groupedWithVirtualProduct
     * @depends groupedWithDownloadableProduct
     * @TestlinkId TL-MAGE-3403
     */
    public function groupedWithAllTypesProduct($simpleSku, $virtualSku, $downloadableSku)
    {
        //Data
        $groupedData =
            $this->loadDataSet('Product', 'grouped_product_required', array('associated_search_sku' => $simpleSku));
        $groupedData['general_grouped_data']['associated_grouped_2'] =
            $this->loadDataSet('Product', 'associated_grouped', array('associated_search_sku' => $virtualSku));
        $groupedData['general_grouped_data']['associated_grouped_3'] =
            $this->loadDataSet('Product', 'associated_grouped', array('associated_search_sku' => $downloadableSku));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $groupedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($groupedData, 'grouped');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($groupedData);
    }
}
