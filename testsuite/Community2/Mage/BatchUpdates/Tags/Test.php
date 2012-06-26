<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tags deleting using batch updates tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_BatchUpdates_Tags_Test extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Tags -> All tags</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
    }

    /**
     * <p>Deleting all tags using Batch Updates</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tag"</p>
     * <p>2. Fill in the fields in General Information</p>
     * <p>3. Click button "Save Tag"</p>
     * <p>4. Click button "Add New Tag"</p>
     * <p>5. Fill in the fields in General Information</p>
     * <p>6. Click button "Save Tag"</p>
     * <p>7. Click link "Select All"</p>
     * <p>8. Select value "Delete" in "Action dropdown"</p>
     * <p>9. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the tags has been deleted.</p>
     *
     * @test
     * @TestlinkId
     */
    public function deleteTagsByBatchUpdates()
    {
        //Data
        $setData1 = $this->loadDataSet('Tag', 'backend_new_tag');
        $setData2 = $this->loadDataSet('Tag', 'backend_new_tag');
        //Steps
        $this->tagsHelper()->addTag($setData1);
        $this->tagsHelper()->addTag($setData2);
        //Verify
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_saved_tag');
        //Steps
        $this->tagsHelper()->deleteAllTags();
    }
}