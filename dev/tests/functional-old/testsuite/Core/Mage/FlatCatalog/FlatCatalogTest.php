<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_FlatCatalog
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configure Flat Catalog in System Configuration tests
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_FlatCatalog_FlatCatalogTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->reindexInvalidedData();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('FlatCatalog/flat_catalog_default');
        $this->flushCache();
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('FlatCatalog/flat_catalog_default');
        $this->flushCache();
        $this->reindexInvalidedData();
    }

    /**
     *<p>Preconditions for tests</p>
     *
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $catPath = $category['parent_category'] . '/' . $category['name'];
        $simple = $this->loadDataSet('Product', 'simple_product_visible', array('general_categories' => $catPath));
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');

        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('simple' => $simple['general_name'], 'catName' => $category['name'], 'catPath' => $catPath);
    }

    /**
     * <p>Flat Catalog Category is turned</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1964
     */
    public function flatCategoryIsEnabled()
    {
        //Data
        $flatCatalog = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend',
            array('use_flat_catalog_category' => 'Yes'));
        //Steps
        $this->systemConfigurationHelper()->configure($flatCatalog);
        $this->flushCache();
        $this->reindexInvalidedData();
        //Verification
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $this->systemConfigurationHelper()
            ->verifyConfigurationOptions($flatCatalog['tab_1']['configuration'], 'catalog_catalog');
    }

    /**
     * <p>Configure Search Engine Optimization settings:Popular Search Terms(Enable)</p>
     *
     * @param string $searchTerms
     *
     * @test
     * @dataProvider flatCategorySearchTermsDataProvider
     * @TestlinkId TL-MAGE-1885/1886
     */
    public function flatCategorySearchTerms($searchTerms)
    {
        //Data
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_search_engine_optimizations',
            array('popular_search_terms' => $searchTerms));
        //Steps
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->flushCache();
        $this->reindexInvalidedData();
        $this->frontend();
        //Verification
        if ($searchTerms == 'Enable') {
            $this->assertTrue($this->controlIsPresent('link', 'popular_search_terms'));
        } else {
            $this->assertFalse($this->controlIsPresent('link', 'popular_search_terms'));
        }
    }

    public function flatCategorySearchTermsDataProvider()
    {
        return array(
            array('Enable'),
            array('Disable')
        );
    }

    /**
     * <p>Allow Guest to Write Reviews (set No), when Flat Catalog Category is used</p>
     *
     * @param $testData
     * @param $allowReviews
     *
     * @test
     * @dataProvider flatCategoryAllowReviewsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-1882/1881
     */
    public function flatCategoryAllowReviews($allowReviews, $testData)
    {
        //Data
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_reviews',
            array('allow_guests_to_write_reviews' => $allowReviews));
        //Steps
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->flushCache();
        $this->reindexInvalidedData();
        $this->frontend();
        $this->productHelper()->frontOpenProduct($testData['simple']);
        $this->clickControl('link', 'first_review');
        //Verification
        if ($allowReviews == 'Yes') {
            $this->assertTrue($this->buttonIsPresent('submit_review'));
        } else {
            $this->assertFalse($this->buttonIsPresent('submit_review'));
        }
    }

    public function flatCategoryAllowReviewsDataProvider()
    {
        return array(
            array('Yes'),
            array('No')
        );
    }

    /**
     * <p>Configure frontend settings: Product Listing Sort by</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-1880
     */
    public function flatCategoryListingSortBy($testData)
    {
        //Data
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend',
            array('product_listing_sort_by' => 'Name'));
        //Steps
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->flushCache();
        $this->reindexInvalidedData();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        //Verification
        $this->assertSame('Name', $this->getControlAttribute('dropdown', 'sort_by', 'selectedLabel'));
    }

    /**
     * <p>Configure frontend setting: Allow All Products per Page(No)</p>
     *
     * @param array $testData
     * @param $allProductsPerPage
     *
     * @test
     * @dataProvider flatCategoryAllProductsPerPageDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-1879/1878
     */
    public function flatCategoryAllProductsPerPage($allProductsPerPage, $testData)
    {
        //Data
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend',
            array('allow_all_products_per_page' => $allProductsPerPage));
        //Steps
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->flushCache();
        $this->reindexInvalidedData();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        //Verification
        $showVariants = $this->select($this->getControlElement('dropdown', 'show_per_page'))->selectOptionLabels();
        $showVariants = array_map('trim', $showVariants);
        if ($allProductsPerPage == 'Yes') {
            $this->assertTrue(in_array('All', $showVariants));
        } else {
            $this->assertFalse(in_array('All', $showVariants));
        }
    }

    public function flatCategoryAllProductsPerPageDataProvider()
    {
        return array(
            array('Yes'),
            array('No')
        );
    }

    /**
     * <p>Configure frontend settings: Product per Page on List</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-1877
     */
    public function flatCategoryProductsOnList($testData)
    {
        //Data
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend', array(
            'products_per_page_on_grid_allowed_values' => '%noValue%',
            'products_per_page_on_grid_default_value' => '%noValue%',
            'products_per_page_on_list_allowed_values' => '10,20,30,40',
            'products_per_page_on_list_default_value' => '10',
            'list_mode' => 'List Only'
        ));
        //Steps
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->flushCache();
        $this->reindexInvalidedData();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        //Verification
        $val = explode(',',
            $flatCatalogData['tab_1']['configuration']['frontend']['products_per_page_on_list_allowed_values']);
        $actual = $this->select($this->getControlElement('dropdown', 'show_per_page'))->selectOptionLabels();
        $actual = array_map('trim', $actual);
        $this->assertEquals($val, $actual);
    }

    /**
     * <p>Configure frontend settings: Product per Page on Grid</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-1876
     */
    public function flatCategoryProductsOnGrid($testData)
    {
        //Data
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend', array(
            'products_per_page_on_grid_allowed_values' => '8,18,28',
            'products_per_page_on_grid_default_value' => '8',
            'products_per_page_on_list_allowed_values' => '%noValue%',
            'products_per_page_on_list_default_value' => '%noValue%',
            'list_mode' => 'Grid Only'
        ));
        //Steps
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->flushCache();
        $this->reindexInvalidedData();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        //Verification
        $val = explode(',',
            $flatCatalogData['tab_1']['configuration']['frontend']['products_per_page_on_grid_allowed_values']);
        $actual = $this->select($this->getControlElement('dropdown', 'show_per_page'))->selectOptionLabels();
        $actual = array_map('trim', $actual);
        $this->assertEquals($val, $actual);
    }

    /**
     * <p>Configure frontend settings: List Mode</p>
     *
     * @param array $testData
     * @param $listMode
     *
     * @test
     * @dataProvider flatCategoryListModeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-1875
     */
    public function flatCategoryListMode($listMode, $testData)
    {
        //Data
        $override = array('list_mode' => $listMode);
        if ($listMode == 'List Only' || $listMode == 'Grid Only') {
            $type = $listMode == 'List Only' ? 'grid' : 'list';
            $override['products_per_page_on_' . $type . '_allowed_values'] = '%noValue%';
            $override['products_per_page_on_' . $type . '_default_value'] = '%noValue%';
        }
        $flCatalogDataList = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend', $override);
        //Steps
        $this->systemConfigurationHelper()->configure($flCatalogDataList);
        $this->flushCache();
        $this->reindexInvalidedData();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        //Verification
        $this->addParameter('productName', $testData['simple']);
        if ($listMode == 'List Only') {
            $this->assertFalse($this->controlIsPresent('link', 'grid'));
            $this->assertFalse($this->controlIsPresent('link', 'list'));
            $this->assertTrue($this->controlIsPresent('link', 'learn_more'));
        } elseif ($listMode == 'Grid Only') {
            $this->assertFalse($this->controlIsPresent('link', 'grid'));
            $this->assertFalse($this->controlIsPresent('link', 'list'));
            $this->assertFalse($this->controlIsPresent('link', 'learn_more'));
        } elseif ($listMode == 'List (default) / Grid') {
            $this->assertTrue($this->controlIsPresent('link', 'grid'));
            $this->assertTrue($this->controlIsPresent('link', 'learn_more'));
            $this->url($this->getControlAttribute('link', 'grid', 'href'));
            $this->assertTrue($this->controlIsPresent('link', 'list'));
        } elseif ($listMode == 'Grid (default) / List') {
            $this->assertTrue($this->controlIsPresent('link', 'list'));
            $this->assertFalse($this->controlIsPresent('link', 'learn_more'));
            $this->url($this->getControlAttribute('link', 'list', 'href'));
            $this->assertTrue($this->controlIsPresent('link', 'grid'));
        }
    }

    public function flatCategoryListModeDataProvider()
    {
        return array(
            array('List Only'),
            array('Grid Only'),
            array('List (default) / Grid'),
            array('Grid (default) / List')
        );
    }
}