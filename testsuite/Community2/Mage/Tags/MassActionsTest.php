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

    /**
     * Changing the status of tags to Disabled using mass action
     * Preconditions:
     * Three tags are created with the status "Pending"
     * Steps:
     * 1. Go to the Catalog -> Tags -> Pending Tags
     * 2. Select checkbox near first tag name
     * 3. Choose "Change statuses" in mass action section
     * 4. Choose "Disabled" status
     * 5. Press "Submit" button
     * Expected Result: Tag should receive "Disabled" status and absent on "Pending Tags" page
     * 6. Go to the Catalog -> Tags -> All Tags
     * Expected Result: Tag should receive "Disabled" status
     * 7. Go back to the Catalog -> Tags -> Pending Tags
     * 8. Select several tags
     * 9. Choose "Change statuses" in mass action section
     * 10. Choose "Disabled" status
     * 11. Press "Submit" button
     * Expected Result: Tags should receive "Disabled" status and absent on "Pending Tags" page
     * 12. Go to the Catalog -> Tags -> All Tags
     * Expected Result: Tags should have "Disabled" status
     *
     * @test
     * @TestlinkId TL-MAGE-2331
     */

    public function changingStatusToDisabledUsingMassAction()
    {
        //Preconditions
        // Create three tags with the status "Pending"
        $tagData[0] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagData[1] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagData[2] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $arr = array($tagData[0], $tagData[1], $tagData[2]);
        foreach ($arr as $value) {
            $this->tagsHelper()->addTag($value);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate('pending_tags');
        // Step 2
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[0]['tag_name']));
        $tagsUpdatedCount = 1;
        // Step 3
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 4
        $this->fillDropdown('tags_status', 'Disabled');
        //Step 5
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $tagsUpdatedCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Step 6
        $this->navigate('all_tags');
        //Verify
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
            'tag_name' => $tagData[0]['tag_name'],
            'tags_status' => 'Disabled')));
        //Step 7
        $this->navigate('pending_tags');
        //Step 8
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[1]['tag_name']));
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[2]['tag_name']));
        $tagsCount = 2;
        //Step 9
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 10
        $this->fillDropdown('tags_status', 'Disabled');
        //Step 11
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $tagsCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Step 12
        $this->navigate('all_tags');
        // Verifying second tag
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
            'tag_name' => $tagData[1]['tag_name'],
            'tags_status' => 'Disabled')));
        // Verifying third tag
        $this->navigate('all_tags');
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
            'tag_name' => $tagData[2]['tag_name'],
            'tags_status' => 'Disabled')));
    }
    /**
     * Changing the status of tags to Pending using mass action
     * Preconditions:
     * Three tags are created with the status "Pendidng"
     * Steps:
     * 1. Go to the Catalog -> Tags -> Pending Tags
     * 2. Select checkbox near first tag name
     * 3. Choose "Change statuses" in mass action section
     * 4. Choose "Pending" status
     * 5. Press "Submit" button
     * Expected Result: Tag should receive "Pendidng" status
     * 6. In Catalog -> Tags -> Pending Tags select several tags
     * 7. Choose "Change statuses" in mass action section
     * 8. Choose "Pending" status
     * 9. Press "Submit" button
     * Expected Result: Tags should receive "Pending" status
     *
     * @test
     * @TestlinkId TL-MAGE-2335
     */
    public function changingStatusToPendingUsingMassAction()
    {
        //Preconditions
        // Create three tags with the status "Pending"
        $tagData[0] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagData[1] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagData[2] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $arr = array($tagData[0], $tagData[1], $tagData[2]);
        foreach ($arr as $value) {
            $this->tagsHelper()->addTag($value);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate('pending_tags');
        // Step 2
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[0]['tag_name']));
        $tagsCount = 1;
        // Step 3
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 4
        $this->fillDropdown('tags_status', 'Pending');
        //Step 5
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $tagsCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
            'tag_name' => $tagData[0]['tag_name'])));
        //Step 6
        $this->navigate('pending_tags');
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[1]['tag_name']));
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[2]['tag_name']));
        $tagsCount = 2;
        //Step 7
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 8
        $this->fillDropdown('tags_status', 'Pending');
        //Step 9
        $this->clickButton('submit');
        // Verify message
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $tagsCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Verify status of second tag
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
                'tag_name' => $tagData[1]['tag_name']
            )
        ));
        //Verify status of third tag
        $this->navigate('pending_tags');
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
                'tag_name' => $tagData[2]['tag_name']
            )
        ));
    }
    /**
     * Changing the status of tags to Approved using mass action
     * Preconditions:
     * Three tags are created with the status "Pendidng"
     * Steps:
     * 1. Go to the Catalog -> Tags -> Pending Tags
     * 2. Select checkbox near first tag name
     * 3. Choose "Change statuses" in mass action section
     * 4. Choose "Approved" status
     * 5. Press "Submit" button
     * Expected Result: Tag should receive "Approved" status and absent on "Pending Tags" page
     * 6. Go to the Catalog -> Tags -> All Tags
     * Expected Result: Tag should receive "Approved" status
     * 7. Go back to the Catalog -> Tags -> Pending Tags
     * 8. Select several tags
     * 9. Choose "Change statuses" in mass action section
     * 10. Choose "Approved" status
     * 11. Press "Submit" button
     * Expected Result: Tags should receive "Disabled" status and absent on "Approved Tags" page
     * 12. Go to the Catalog -> Tags -> All Tags
     * Expected Result: Tags should have "Approved" status
     *
     * @test
     * @TestlinkId TL-MAGE-2338
     */

    public function changingStatusToApprovedUsingMassAction()
    {
        //Preconditions
        // Create three tags with the status "Pending"
        $tagData[0] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagData[1] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagData[2] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $arr = array($tagData[0], $tagData[1], $tagData[2]);
        foreach ($arr as $value) {
            $this->tagsHelper()->addTag($value);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate('pending_tags');
        // Step 2
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[0]['tag_name']));
        $tagsCount = 1;
        // Step 3
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 4
        $this->fillDropdown('tags_status', 'Approved');
        //Step 5
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $tagsCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Step 6
        $this->navigate('all_tags');
        //Verify
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
                'tag_name' => $tagData[0]['tag_name'],
                'tags_status' => 'Approved')
        ));
        //Step 7
        $this->navigate('pending_tags');
        //Step 8
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[1]['tag_name']));
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[2]['tag_name']));
        $tagsCount = 2;
        //Step 9
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 10
        $this->fillDropdown('tags_status', 'Approved');
        //Step 11
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $tagsCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Step 12
        $this->navigate('all_tags');
        // Verifying second tag
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
                'tag_name' => $tagData[1]['tag_name'],
                'tags_status' => 'Approved')
        ));
        // Verifying third tag
        $this->navigate('all_tags');
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
                'tag_name' => $tagData[2]['tag_name'],
                'tags_status' => 'Approved')
        ));
    }

    /**
     * Changing the status of tags to Disabled/Pending/Approved using mass action
     * Steps:
     * 1. Go to the Catalog -> Tags -> Pending Tags
     * 2. Select "Change status" in mass action section.
     * 3. Choose "Disabled"/"Pending"/"Approved" status.
     * 4. Click "Submit" button.
     * Expected: The message "Please select items" should appear.
     *
     * @test
     * @TestlinkId TL-MAGE-2334, TL-MAGE-2337, TL-MAGE-2339
     */

    public function changingStatusUsingMassActionNegative()
    {
        //Step 1
        $this->navigate('pending_tags');
        $tagStatuses = array(
            'Disabled',
            'Pending',
            'Approved',
        );
        foreach ($tagStatuses as $value) {
            //Step 2
            $this->fillDropdown('tags_massaction', 'Change status');
            //Step 3
            $this->fillDropdown('tags_status', $value);
            //Step 4
            $this->clickButton('submit', false);
            //Verifying
            $this->assertEquals('Please select items.', $this->getAlert(),
                "Message 'Please select items.' is not present");
        }
    }
}