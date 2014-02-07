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
 * Product Advanced Search on Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdvancedSearch_AdvancedSearchTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SID/SID_disable');
    }

    public function assertPreConditions()
    {
        $this->frontend();
        $this->clickControl('link', 'advanced_search');
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
    public function preconditionsForTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        //Data
        $productData = $this->loadDataSet('Product', 'advanced_search_product');
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Execute search with all empty fields</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5993
     */
    public function withAllEmptyFields()
    {
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch();
        //Verifying
        $this->assertMessagePresent('error', 'specify_search_term');
    }

    /**
     * <p>Execute search with not existing data.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5994
     */
    public function fillFieldsWithNotExistingData()
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search', array(
            'name' => $this->generate('string', 10, ':punct:'),
            'description' => $this->generate('string', 10, ':punct:'),
            'short_description' => $this->generate('string', 10, ':punct:'),
            'sku' => $this->generate('string', 10, ':punct:')
        ));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        //Verify
        $this->assertMessagePresent('error', 'not_find_any_items');
    }

    /**
     * <p>Fill all fields.</p>
     *
     * @param array $productData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5995
     */
    public function fillAllFields($productData)
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price', array(
            'name' => $productData['general_name'],
            'sku' => $productData['general_sku']
        ));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        //Verifying
        $this->addParameter('name', $searchData['name']);
        $this->addParameter('sku', $searchData['sku']);
        $this->assertTrue($this->controlIsVisible('pageelement', 'name'), 'There is no Name block in search result');
        $this->assertTrue($this->controlIsVisible('pageelement', 'sku'), 'There is no SKU block in search result');
    }

    /**
     * <p>Fill all 'Price_from' and 'Price_to' fields.</p>
     *
     * @param $productData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5996
     */
    public function fillPriceFields($productData)
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price', array(
            'price_from' => $productData['prices_special_price'],
            'price_to' => $productData['general_price']
        ));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        //Verifying
        $this->addParameter('price_from', $searchData['price_from']);
        $this->addParameter('price_to', $searchData['price_to']);
        $this->assertTrue($this->controlIsVisible('pageelement', 'price'), 'There is no Price block in search result');
    }

    /**
     * <p>Fill all 'Price_from' and 'Price_to' fields with incorrect data.</p>
     *
     * @param $productData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5997
     */
    public function incorrectDataInPrices($productData)
    {
        //Data
        $search = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price', array(
            'price_from' => 'wrongData',
            'price_to' => $productData['general_price']
        ));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($search);
        //Verifying
        $this->addFieldIdToMessage('field', 'price_from');
        $this->assertMessagePresent('validation', 'enter_valid_number');
        $this->frontend();
        $this->clickControl('link', 'advanced_search');
        //Data
        $search = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price', array(
            'price_to' => 'wrongData',
            'price_from' => $productData['general_price']
        ));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($search);
        $this->addFieldIdToMessage('field', 'price_to');
        $this->assertMessagePresent('validation', 'enter_valid_number');
    }

    /**
     * <p>Fill all fields.</p>
     *
     * @param array $productData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6007
     */
    public function fillWithExistAndWrongData($productData)
    {
        //Data
        $searchData = $this->loadDataSet('AdvancedSearch', 'generic_product_advanced_search_with_price', array(
            'name' => $productData['general_name'],
            'description' => $this->generate('string', 32, ':punct:'),
            'short_description' => $this->generate('string', 32, ':punct:'),
            'sku' => $this->generate('string', 32, ':punct:')
        ));
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch($searchData);
        //Verifying
        $this->assertMessagePresent('error', 'not_find_any_items');
    }

    /**
     * <p>Fill just one field</p>
     *
     * @param string $field
     * @param array $productData
     *
     * @test
     * @dataProvider searchWithOneFieldDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5998
     */
    public function searchByOneField($field, $productData)
    {
        //Steps
        $checkValues = array('name', 'sku');
        //Steps
        $this->advancedSearchHelper()->frontCatalogAdvancedSearch(array($field => $productData['general_' . $field]));
        //Verifying
        $this->addParameter($field, $productData['general_' . $field]);
        $this->assertTrue($this->controlIsVisible('pageelement', $field));
        unset($checkValues[array_search($field, $checkValues)]);
        foreach ($checkValues as $value) {
            $this->addParameter($value, $productData['general_' . $value]);
            if ($this->controlIsVisible('pageelement', $value)) {
                $this->addVerificationMessage("Field '$value' is used fot search");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function searchWithOneFieldDataProvider()
    {
        return array(
            array('name'),
            array('sku')
        );
    }
}