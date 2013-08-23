<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Verification Manage Shopping Cart Button
 */
class Enterprise_Mage_PageCache_BreadcrumbsTest extends Mage_Selenium_TestCase
{
    static protected $_isFpcOnBeforeTests;

    /**
     * Log in to backend
     * Set current full page cache status
     * Create customer
     * Create product
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('cache_storage_management');
        self::$_isFpcOnBeforeTests = $this->cacheStorageManagementHelper()->isFullPageCacheEnabled();

        $this->cacheStorageManagementHelper()->enableFullPageCache();
    }

    /**
     * Enable/disable full page cache if it was enabled/disabled before
     */
    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('cache_storage_management');
        if (self::$_isFpcOnBeforeTests) {
            $this->cacheStorageManagementHelper()->enableFullPageCache();
        } else {
            $this->cacheStorageManagementHelper()->disableFullPageCache();
        }
    }

    /**
     * <p>Create category</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTestBreadcrumb()
    {
        // data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        // steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        // verifying
        $this->assertMessagePresent('success', 'success_saved_category');

        // refresh page cache to make new category appear in main menu
        $this->navigate('cache_storage_management');
        $this->cacheStorageManagementHelper()->refreshFullPageCache();

        return $category;
    }

    /**
     * @param array $category
     *
     * @test
     * @depends preconditionsForTestBreadcrumb
     * @TestlinkId TL-MAGE-6445
     */
    public function testBreadcrumb(array $category)
    {
        $this->goToArea('frontend');

        // magento will generate category page content first end put page content with placeholders for blocks to FPC
        // on the second refresh placeholders will be replaced with block html using applyInApp() method
        // of each placeholder and put to FPC
        // on the third refresh placeholders will be replaced with cached blocks content using applyWithoutApp()
        for ($i = 0; $i < 3; $i++) {
            $this->categoryHelper()->frontOpenCategory($category['name']);
            // verifying
            $this->assertTrue((bool)$this->elementIsPresent("//*[@class='breadcrumbs']"));
            $categoryId = $this->categoryHelper()->defineIdFromUrl();
            $this->elementIsPresent("//*[@class='category" .$categoryId . "']");
        }
    }
}
