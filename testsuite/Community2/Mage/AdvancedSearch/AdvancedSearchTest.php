<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdvancedSearchTest
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Creating Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_AdvancedSearch_AdvancedSearchTest extends Mage_Selenium_TestCase {

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SID/SID_disable');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate Home -> Advanced Search.</p>
     */
    public function assertPreConditions()
    {
        $this->frontend('advanced_search');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SID/SID_enable');
    }

    /**
     * <p>Execute search with all empty fields</p>
     * <p>1. Open Advanced Search page.</p>
     * <p>2. Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears: 'Please specify at least one search term.'</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5993
     */
    public function withAllEmptyFields()
    {
        //Steps
        $this->clickButton('search');
        //Verifying
        $this->assertMessagePresent('error', 'error_message');
    }

    /**
     * <p>Execute search with not existing data.</p>
     * <p>Fill all field with not existing data.</p>
     * <p>Expected result:</p>
     * <p>Error message appears: 'No items were found using the following search criteria'.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5994
     */
    public function fillFieldsWithNotExistingData()
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search',
            array('name' => $this->generate('string', 32, ':punct:'),
                'description' => $this->generate('string', 32, ':punct:'),
                'short_description' => $this->generate('string', 32, ':punct:'),
                'sku' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        $this->clickButton('search');
        //Verify
        $this->assertMessagePresent('error', 'error_message_wrong_entered_data');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Creating Simple product.</p>
     *
     * @return array $productData
     *
     * @test
     * @TestlinkId TL-MAGE-3411
     */
    public function allFieldsInSimple()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        //Data
        $productData = $this->loadDataSet('AdvancedSearch', 'product_create');
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Fill all fields.</p>
     * <p>Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Product is found</p>
     *
     * @param array $productData
     *
     * @test
     * @depends allFieldsInSimple
     * @TestlinkId TL-MAGE-5995
     */
    public function fillAllFields($productData)
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price',
            array('name' => $productData['general_name'],
                'description' => $productData['general_description'],
                'short_description' => $productData['general_short_description'],
                'sku' => $productData['general_sku']));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        $this->clickButton('search');
        //Verifying
        $this->addParameter('name', $searchData['name']);
        $this->addParameter('description', $searchData['description']);
        $this->addParameter('short_description', $searchData['short_description']);
        $this->addParameter('sku', $searchData['sku']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'name'),
            'There is no Name block in search result');
        $this->assertTrue($this->controlIsPresent('pageelement', 'description'),
            'There is no Description block in search result');
        $this->assertTrue($this->controlIsPresent('pageelement', 'short_description'),
            'There is no Short Description block in search result');
        $this->assertTrue($this->controlIsPresent('pageelement', 'sku'),
            'There is no SKU block in search result');
    }

    /**
     * <p>Fill all 'Price_from' and 'Price_to' fields.</p>
     * <p>Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Product is found</p>
     *
     * @param $productData
     *
     * @test
     * @depends allFieldsInSimple
     * @TestlinkId TL-MAGE-5996
     */
    public function fillPriceFields($productData)
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price',
            array('price_from' => $productData['prices_special_price'],
                'price_to' => $productData['prices_price']));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        $this->clickButton('search');
        //Verifying
        $this->addParameter('price_from', $searchData['price_from']);
        $this->addParameter('price_to', $searchData['price_to']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'price'),
            'There is no Price block in search result');
    }

    /**
     * <p>Fill all 'Price_from' and 'Price_to' fields with incorrect data.</p>
     * <p>Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Error message with text "Please enter a valid number in this field." will appear</p>
     *
     * @param $productData
     *
     * @test
     * @depends allFieldsInSimple
     * @TestlinkId TL-MAGE-5997
     */
    public function incorrectDataInPrices($productData)
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price',
            array('price_from' => $productData['general_news_from'],
                'price_to' => $productData['general_news_to']));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        $this->clickButton('search', false);
        //Verifying
        $this->assertMessagePresent('validation', 'required_price');
        $this->assertMessagePresent('validation', 'required_price_to');
    }

    /**
     * <p>Fill all fields.</p>
     * <p>Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Product is not found</p>
     *
     * @param array $productData
     *
     * @test
     * @depends allFieldsInSimple
     * @TestlinkId TL-MAGE-6007
     */
    public function fillWithExistAndWrongData($productData)
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price',
            array('name' => $productData['general_name'],
                'description' => $this->generate('string', 32, ':punct:'),
                'short_description' => $this->generate('string', 32, ':punct:'),
                'sku' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        $this->clickButton('search');
        //Verifying
        $this->assertMessagePresent('error', 'error_message_wrong_entered_data');
    }

    /**
     * <p>Fill just one field</p>
     * <p>Click 'Search' button.</p>
     * <p>Expected result:</p>
     * <p>Product is found</p>
     *
     * @param $key
     * @param $val
     * @param array $productData
     *
     * @test
     * @dataProvider searchWithOneFieldDataProvider
     * @depends allFieldsInSimple
     * @TestlinkId TL-MAGE-5998
     */
    public function searchByOneField($key, $val, $productData)
    {
        //Data
        $searchData = array($key => $productData[$val]);
        $checkValues = array(
            'name'=>'name',
            'description'=>'description',
            'short_description'=>'short_description',
            'sku'=>'sku'
        );
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        $this->clickButton('search');
        //Verifying
        $this->addParameter($key, $productData[$val]);
        $this->assertTrue($this->controlIsPresent('pageelement', $key));
        unset($checkValues[$key]);
        foreach ($checkValues as $value) {
            $this->addParameter($value, $productData[$val]);
            if ($this->controlIsPresent('pageelement', $value)) {
                $this->addVerificationMessage("Control is present in Block: $value");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function searchWithOneFieldDataProvider()
    {
        return array(
            array('name','general_name'),
            array('description','general_description'),
            array('short_description','general_short_description'),
            array('sku','general_sku'));
    }
}