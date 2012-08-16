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
    public function setUpBeforeTestsClass()
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
     * @dataProvider tagDataProvider
     * @TestlinkId TL-MAGE-2329, TL-MAGE-2332, TL-MAGE-2352, TL-MAGE-2353
     */
    public function deletingTagsUsingMassAction($tagArea)
    {
        $tagData = array();
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
        $this->navigate($tagArea);
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
        $this->navigate($tagArea);
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
     * @dataProvider tagDataProvider
     * @TestlinkId TL-MAGE-2331, TL-MAGE-2354
     */

    public function changingStatusToDisabledUsingMassAction($tagArea)
    {
        //Preconditions
        // Create three tags with the status "Pending"
        $tagData = array();
        for ($i=0; $i<3; $i++) {
            $tagData[$i] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate($tagArea);
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
        $this->checkCurrentPage($tagArea);
        $this->addParameter('qtyDeletedTags', $tagsUpdatedCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Step 6
        $this->navigate('all_tags');
        //Verify
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
            'tag_name' => $tagData[0]['tag_name'],
            'tags_status' => 'Disabled')));
        //Step 7
        $this->navigate($tagArea);
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
        $this->checkCurrentPage($tagArea);
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
     * @dataProvider tagDataProvider
     * @TestlinkId TL-MAGE-2335, TL-MAGE-2359
     */
    public function changingStatusToPendingUsingMassAction($tagArea)
    {
        //Preconditions
        // Create three tags with the status "Pending"
        $tagData = array();
        for ($i=0; $i<3; $i++) {
            $tagData[$i] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate($tagArea);
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
        $this->checkCurrentPage($tagArea);
        $this->addParameter('qtyDeletedTags', $tagsCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
            'tag_name' => $tagData[0]['tag_name'])));
        //Step 6
        $this->navigate($tagArea);
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
        $this->checkCurrentPage($tagArea);
        $this->addParameter('qtyDeletedTags', $tagsCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Verify status of second tag
        $this->assertTrue((bool) $this->tagsHelper()->search(array(
                'tag_name' => $tagData[1]['tag_name']
            )
        ));
        //Verify status of third tag
        $this->navigate($tagArea);
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
     * @dataProvider tagDataProvider
     * @TestlinkId TL-MAGE-2338, TL-MAGE-2362
     */

    public function changingStatusToApprovedUsingMassAction($tagArea)
    {
        //Preconditions
        // Create three tags with the status "Pending"
        $tagData = array();
        for ($i=0; $i<3; $i++) {
            $tagData[$i] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate($tagArea);
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
        $this->checkCurrentPage($tagArea);
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
        $this->navigate($tagArea);
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
        $this->checkCurrentPage($tagArea);
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
     * @dataProvider tagDataProvider
     * @TestlinkId TL-MAGE-2334, TL-MAGE-2337, TL-MAGE-2339, TL-MAGE-2358, TL-MAGE-2361, TL-MAGE-2363
     */

    public function changingStatusUsingMassActionNegative($tagArea)
    {
        //Step 1
        $this->navigate($tagArea);
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

    /**
     * Selecting tags options
     * Precondition: two pages of tags with "Pending" status.
     * Steps:
     * 1. Go to the Catalog -> Tags -> Pending Tags.
     * 2. Click on "Select all" link.
     * Expected: all tags on all pages should be selected.
     * 3. Click on "Unselect All" link.
     * Expected: all tags on all pages should be unselected.
     * 4. Click on "Select Visible" link.
     * Expected: all tags on the current page should be selected.
     * 5. Click on "Unselect Visible" link.
     * Expected: all tags on the current page should be unselected.
     *
     * @test
     * @dataProvider tagDataProvider
     * @TestlinkId TL-MAGE-2340, TL-MAGE-2364
     */
    public function selectingTagsOptions($tagArea)
    {
        $tagData = array();
        //Precondition
        for ($i=0; $i<21; $i++) {
            $tagData[$i] = array(
                'tag_name' => 'tag_' . str_pad($i, 2, 0, STR_PAD_LEFT),
                'tag_status' => 'Pending',
            );
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate($tagArea);
        //Step 2
        $this->clickControl('link', 'select_all', false);
        //Verifying
        foreach ($tagData as $value) {
            $this->assertTrue($this->tagsHelper()->isTagSelected(array('tag_name' => $value['tag_name'])),
                'Tag ' . $value['tag_name'] . ' is not selected');
        }
        //Step 3
        $this->clickControl('link', 'unselect_all', false);
        //Verifying
        foreach ($tagData as $value) {
            $this->assertFalse($this->tagsHelper()->isTagSelected(array('tag_name' => $value['tag_name'])),
                'Tag ' . $value['tag_name'] . ' is not selected');
        }
        //Step 4
        $this->clickButton('reset_filter', false);
        $this->navigate($tagArea);
        $this->clickControl('link', 'select_visible', false);
        //Verifying
        foreach ($tagData as $key => $value) {
            if ($key != '20') {
                $this->assertTrue($this->tagsHelper()->isTagSelected(array('tag_name' => $value['tag_name'])),
                    'Tag ' . $value['tag_name'] . ' is not selected');
            } else {
                $this->assertFalse($this->tagsHelper()->isTagSelected(array('tag_name' => $value['tag_name'])),
                    'Tag ' . $value['tag_name'] . ' is selected');
            }
        }
        //Step 5
        $this->clickButton('reset_filter', false);
        $this->navigate($tagArea);
        $this->clickControl('link', 'unselect_visible', false);
        //Verifying
        foreach ($tagData as $value) {
            $this->assertFalse($this->tagsHelper()->isTagSelected(array('tag_name' => $value['tag_name'])),
                'Tag ' . $value['tag_name'] . ' is selected');
        }
    }
    public function tagDataProvider()
    {
        return array(
            array('pending_tags'),
            array('all_tags')
        );
    }
}