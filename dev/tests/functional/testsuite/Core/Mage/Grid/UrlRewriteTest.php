<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Grid
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * URL Rewrite Grid Tests
 */
class Core_Mage_Grid_UrlRewriteTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->markTestIncomplete('MAGETWO-11231');
        $this->loginAdminUser();
    }

    /**
     * <p>PreConditions</p>
     * <p>Create Category</p>
     * <p>Steps:</p>
     * <p>Open URL Rewrite Management Page</p>
     * <p>Count Rows in URL Rewrite Grid</p>
     * <p>Click 'Add New Rewrite' button</p>
     * <p>At Create URL rewrite dropdown fill request path</p>
     * <p>Verifications:</p>
     * <p>Check that Category URL was successfully added to grid list</p>
     *
     * @test
     */
    public function checkUrlCatalogAdded()
    {
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_category',array(
            'category'=> $categoryData['parent_category'] . '/' . $categoryData['name']
        ));
        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Add Rewrite Rule
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($fieldData);
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');
        $locator = $this->search(array('filter_request_path' => $fieldData['rewrite_info']['request_path']), 'url_rewrite_grid');
        $this->assertNotNull($locator, 'URL Rewrite Rule is not added');
    }
}