<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftRegistry
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Grid_UrlRewriteTest extends Mage_Selenium_TestCase
{
    /**
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTestClass()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>PreConditions</p>
     * <p>Create Category</p>
     * <p>Steps:</p>
     * <p>Open URL Rewrite Management Page</p>
     * <p>Count Rows in URL Rewrite Grid</p>
     * <p>Click 'Add New Rewrite' button</p>
     * <p>At Create URL rewrite dropdown fill request path</p>
     * <p>Check that Category URL was successfull added to grid list</p>
     *
     * @test
     * @author Viktoriia Gumeniuk
     */
    public function checkUrlCatalogAdded()
    {
        //PreConditions: Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Count rows in grid before adding CategoryURL
        $this->navigate('url_rewrite_management');
        $gridElement = $this->getControlElement('pageelement', 'page_grid');
        $before= count($this->getChildElements($gridElement, 'tbody/tr', false));
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite', 'true');
        //At Create URL rewrite dropdown select For category
        $this->fillDropdown('create_url_rewrite_dropdown', 'For category');
        //Select Subcategory by name and detect it's id from url
        $this->addParameter('subName', $categoryData['name']);
        $this->clickControl('link', 'sub_category', false);
        $this->waitForPageToLoad();
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_category');
        $this->fillField('request_path', $fieldData['request_path']);
        $this->clickButton('save', false);
        $this->waitForPageToLoad();
        //count rows after URL added
        $this->navigate('url_rewrite_management');
        $gridElement2 = $this->getControlElement('pageelement', 'page_grid');
        $after = count($this->getChildElements($gridElement2, 'tbody/tr', false));
        // Check that Category URL was successfull added to grid list
        $this->assertEquals($before+1,$after,'Category URL was wrong add to grid list ');
    }
}