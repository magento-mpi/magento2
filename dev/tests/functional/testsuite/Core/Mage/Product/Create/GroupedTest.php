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
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
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
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill all fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
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
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields using exist SKU;</p>
     * <p>5. Click 'Save and Continue Edit' button;</p>
     * <p>Expected result:</p>
     * <p>1. Product is saved, confirmation message appears;</p>
     * <p>2. Auto-increment is added to SKU;</p>
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
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->verifyProductInfo(array('general_sku' => $this->productHelper()->getGeneratedSku(
            $productData['general_sku'])));
    }

    /**
     * <p>Creating product with empty required fields</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Leave one required field empty and fill in the rest of fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>6. Verify error message;</p>
     * <p>7. Repeat scenario for all required fields for both tabs;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
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
        if ($emptyField == 'general_visibility') {
            $overrideData = array($emptyField => '-- Please Select --');
        } else {
            $overrideData = array($emptyField => '%noValue%');
        }
        $productData = $this->loadDataSet('Product', 'grouped_product_required', $overrideData);
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('general_name', 'field'),
            array('general_description', 'field'),
            array('general_short_description', 'field'),
            array('general_sku', 'field'),
            array('general_status', 'dropdown'),
            array('general_visibility', 'dropdown')
        );
    }

    /**
     * <p>Creating product with special characters into required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with special symbols ("General" tab), rest - with normal data;
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3410
     * @test
     */
    public function specialCharactersInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'grouped_product_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_description'       => $this->generate('string', 32, ':punct:'),
                  'general_short_description' => $this->generate('string', 32, ':punct:'),
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
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with long values ("General" tab), rest - with normal data;
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3408
     * @test
     */
    public function longValuesInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'grouped_product_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_description'       => $this->generate('string', 255, ':alnum:'),
                  'general_short_description' => $this->generate('string', 255, ':alnum:'),
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
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields, use for sku string with length more than 64 characters</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
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
     * <p>Preconditions</p>
     * <p>Physical Simple product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Goto "Associated products" tab;</p>
     * <p>6. Select created Simple product;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
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
     * <p>Preconditions</p>
     * <p>Physical Simple product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Goto "Associated products" tab;</p>
     * <p>6. Select created Virtual product;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
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
     * <p>Preconditions</p>
     * <p>Physical Simple product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Goto "Associated products" tab;</p>
     * <p>6. Select created Downloadable product;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends onlyRequiredFieldsInGrouped
     *
     * @TestlinkId TL-MAGE-3404
     * @test
     */
    public function groupedWithDownloadableProduct()
    {
        //Data
        $downloadableData = $this->loadDataSet('Product', 'downloadable_product_required');
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
     * <p>Preconditions</p>
     * <p>Physical Simple, Virtual, Downloadable products created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Goto "Associated products" tab;</p>
     * <p>6. Select created products;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
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
        $groupedData['associated_grouped_data']['associated_grouped_2'] =
            $this->loadDataSet('Product', 'associated_grouped', array('associated_search_sku' => $virtualSku));
        $groupedData['associated_grouped_data']['associated_grouped_3'] =
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