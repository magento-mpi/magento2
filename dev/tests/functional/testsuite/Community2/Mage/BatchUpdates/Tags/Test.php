<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Community2_Mage_BatchUpdates_Tags_Test
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tags deleting/updating using batch updates tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_BatchUpdates_Tags_MassActionTest extends Mage_Selenium_TestCase
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

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
    }
    /**
     * <p>Updating created tags using Batch Updates</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tag"</p>
     * <p>2. Fill in the fields in General Information</p>
     * <p>3. Click button "Save Tag"</p>
     * <p>4. Click button "Add New Tag"</p>
     * <p>5. Fill in the fields in General Information</p>
     * <p>6. Click button "Save Tag"</p>
     * <p>7. Select created tags by checkboxes"</p>
     * <p>8. Select value "Change Status in "Action dropdown"</p>
     * <p>9. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the tags has been updated.</p>
     *
     * @test
     * @TestlinkId
     */
    public function changeStatusTagsByBatchUpdates()
    {
        $tagQty = 2;
        for ($i = 1; $i <= $tagQty; $i++) {
            //Data
            $tagData = $this->loadDataSet('Tag', 'backend_new_tag');
            ${'searchData' . $i} =
                $this->loadDataSet('Tag', 'backend_search_tag', array('tag_name' => $tagData['tag_name']));
            //Steps
            $this->tagsHelper()->addTag($tagData);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        for ($i = 1; $i <= $tagQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i});
        }
        $this->addParameter('qtyDeletedTags', $tagQty);
        $xpath = $this->_getControlXpath('dropdown', 'tags_massaction');
        $this->select($xpath, 'Change status');
        $xpath = $this->_getControlXpath('dropdown', 'tags_status');
        $this->select($xpath, 'Disabled');
        $this->clickButton('submit', true);
        //Verifying
        $this->assertMessagePresent('success', 'success_changed_status');
    }
    /**
     * <p>Deleting created tags using Batch Updates</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tag"</p>
     * <p>2. Fill in the fields in General Information</p>
     * <p>3. Click button "Save Tag"</p>
     * <p>4. Click button "Add New Tag"</p>
     * <p>5. Fill in the fields in General Information</p>
     * <p>6. Click button "Save Tag"</p>
     * <p>7. Select created tags by checkboxes</p>
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
        $tagQty = 2;
        for ($i = 1; $i <= $tagQty; $i++) {
            //Data
            $tagData = $this->loadDataSet('Tag', 'backend_new_tag');
            ${'searchData' . $i} =
                $this->loadDataSet('Tag', 'backend_search_tag', array('tag_name' => $tagData['tag_name']));
            //Steps
            $this->tagsHelper()->addTag($tagData);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        for ($i = 1; $i <= $tagQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i});
        }
        $this->addParameter('qtyDeletedTags', $tagQty);
        $xpath = $this->_getControlXpath('dropdown', 'tags_massaction');
        $this->select($xpath, 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
    }
}