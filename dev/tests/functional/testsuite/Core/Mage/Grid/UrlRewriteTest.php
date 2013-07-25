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
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_category');
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Add Rewrite Rule
        $this->navigate('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        $this->fillDropdown('create_url_rewrite_dropdown', 'For category');
        $this->addParameter('subName', $categoryData['name']);
        $this->clickControl('link', 'sub_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('category'));
        $this->validatePage();
        $this->fillField('request_path', $fieldData['request_path']);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');
        $locator = $this->search(array('filter_request_path' => $fieldData['request_path']), 'url_rewrite_grid');
        $this->assertNotNull($locator, 'URL Rewrite Rule is not added');
    }
}