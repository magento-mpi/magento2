<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
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
class Core_Mage_BatchUpdates_Tags_MassActionTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
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
            $this->searchAndChoose(${'searchData' . $i}, 'tags_grid');
        }
        $this->addParameter('qtyDeletedTags', $tagQty);
        $this->fillDropdown('tags_massaction', 'Change status');
        $this->fillDropdown('tags_status', 'Disabled');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_changed_status');
    }

    /**
     * <p>Deleting created tags using Batch Updates</p>
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
            $this->searchAndChoose(${'searchData' . $i}, 'tags_grid');
        }
        $this->addParameter('qtyDeletedTags', $tagQty);
        $this->fillDropdown('tags_massaction', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
    }
}