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
 * Tag creation tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Tags_MassActionsTest extends Mage_Selenium_TestCase
{
    /**
     * Precondition:
     * Delete all existing tags
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
    }
    /**
     * Preconditions:
     * Navigate to Catalog -> Tags -> All tags
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
     * Deleting tags using mass action
     * Precondition: Several tags have "Pending" status.
     * Steps:
     * 1. Log into Backend.
     * 2. Go to the Catalog -> Tags -> Pending Tags.
     * 3. Select checkbox near tag name.
     * 4. Select "Delete" in mass action section.
     * 5. Click on "Submit" button.
     * Expected: The message "Are you sure" appears.
     * 6. Click on OK button.
     * Expected: Selected tag should be deleted.
     * 7. Select several tags.
     * 8. Select "Delete" in mass action section.
     * 9. Click on "Submit" button.
     * Expected: The message "Are you sure" appears.
     * 10. Click on OK button.
     * Expected: Selected tags should be deleted.
     * 11. Select "Delete" in mass action section.
     * 12. Click on "Submit" button.
     * Expected: The message "Please select items" should be appeared.
     *
     * @test
     * @TestlinkId TL-MAGE-2329, TL-MAGE-2332
     */
    public function deletingTagsUsingMassAction()
    {
        //Precondition
        for ($i=0; $i<4; $i++) {
            $tagData[$i] = array(
                'tag_name' => 'tag_' . $this->generate('string', 5, ':lower:'),
                'tag_status' => 'Pending',
            );
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 2
        $this->navigate('pending_tags');
        //Step 3
        $this->searchAndChoose(array('tag_name' => $tagData[0]['tag_name']));
        //Step 4
        $this->fillDropdown('tags_massaction', 'Delete');
        //Steps 5-6
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        //Verifying
        $this->addParameter('qtyDeletedTags', '1');
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
        $this->assertNull($this->search(array('tag_name' => $tagData[0]['tag_name'])), 'Tag has been found');
        //Step 7
        for ($i=1; $i<4; $i++) {
            $this->searchAndChoose(array('tag_name' => $tagData[$i]['tag_name']));
        }
        //Step 8
        $this->fillDropdown('tags_massaction', 'Delete');
        //Steps 9-10
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        //Verifying
        $this->addParameter('qtyDeletedTags', '3');
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
        for ($i=1; $i<4; $i++) {
            $this->assertNull($this->search(array('tag_name' => $tagData[$i]['tag_name'])), 'Tag has been found');
        }
        //Step 11
        $this->navigate('pending_tags');
        $this->fillDropdown('tags_massaction', 'Delete');
        //Step 12
        $this->clickButton('submit', false);
        //Verifying
        $this->assertEquals('Please select items.', $this->getAlert(), "Message 'Please select items.' is not present");
    }
}