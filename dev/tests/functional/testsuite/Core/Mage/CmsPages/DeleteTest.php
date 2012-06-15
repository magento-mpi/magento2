<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsPages
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Delete Page Test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CmsPages_DeleteTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Creates and deletes Page with required fields</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages page</p>
     * <p>2. Create page with required fields</p>
     * <p>3. Open newly created page</p>
     * <p>4. Delete newly created page</p>
     * <p>Expected result</p>
     * <p>Page is created and deleted successfully</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3215
     */
    public function deleteCmsPage()
    {
        //Data
        $pageData = $this->loadDataSet('CmsPage', 'new_cms_page_req');
        $search = array('filter_title'   => $pageData['page_information']['page_title'],
                        'filter_url_key' => $pageData['page_information']['url_key']);
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_page');
        //Steps
        $this->cmsPagesHelper()->deleteCmsPage($search);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_cms_page');
    }
}