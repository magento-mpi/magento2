<?php
/**
 * Customer Backward Compatibility Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Tags_ChangeStatusesTest extends Mage_Selenium_TestCase
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
    /**
     * Changing the status of tags to Disabled using mass action
     * Preconditions:
     * Three tags are created with the status "Pendidng"
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
        $tagFirstData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagSecondData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagThirdData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $arr = array($tagFirstData, $tagSecondData, $tagThirdData);
        foreach ($arr as $value) {
            $this->tagsHelper()->addTag($value);
            $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate('pending_tags');
        // Step 2
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagFirstData['tag_name']));
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
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagFirstData['tag_name'],
            'tags_status' => 'Disabled')));
        //Step 7
        $this->navigate('pending_tags');
        //Step 8
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagSecondData['tag_name']));
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagThirdData['tag_name']));
        $tagsUpdatedCountTwo = 2;
        //Step 9
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 10
        $this->fillDropdown('tags_status', 'Disabled');
        //Step 11
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $tagsUpdatedCountTwo);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Step 12
        $this->navigate('all_tags');
        // Verifying second tag
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagSecondData['tag_name'],
            'tags_status' => 'Disabled')));
        // Verifying third tag
        $this->navigate('all_tags');
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagThirdData['tag_name'],
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
        $tagFirstData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagSecondData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagThirdData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $arr = array($tagFirstData, $tagSecondData, $tagThirdData);
        foreach ($arr as $value) {
            $this->tagsHelper()->addTag($value);
            $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate('pending_tags');
        // Step 2
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagFirstData['tag_name']));
        $updatedCount = 1;
        // Step 3
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 4
        $this->fillDropdown('tags_status', 'Pending');
        //Step 5
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $updatedCount);
        $this->assertMessagePresent('success', 'success_changed_status');
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagFirstData['tag_name'])));
        //Step 6
        $this->navigate('pending_tags');
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagSecondData['tag_name']));
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagThirdData['tag_name']));
        $updatedCountTwo = 2;
        //Step 7
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 8
        $this->fillDropdown('tags_status', 'Pending');
        //Step 9
        $this->clickButton('submit');
        // Verify message
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $updatedCountTwo);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Verify status of second tag
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagSecondData['tag_name']
            )
        ));
        //Verify status of third tag
        $this->navigate('pending_tags');
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagThirdData['tag_name']
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
        $tagFirstData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagSecondData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $tagThirdData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
        $arr = array($tagFirstData, $tagSecondData, $tagThirdData);
        foreach ($arr as $value) {
            $this->tagsHelper()->addTag($value);
            $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate('pending_tags');
        // Step 2
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagFirstData['tag_name']));
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
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagFirstData['tag_name'],
            'tags_status' => 'Approved')
        ));
        //Step 7
        $this->navigate('pending_tags');
        //Step 8
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagSecondData['tag_name']));
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagThirdData['tag_name']));
        $tagsCountTwo = 2;
        //Step 9
        $this->fillDropdown('tags_massaction', 'Change status');
        //Step 10
        $this->fillDropdown('tags_status', 'Approved');
        //Step 11
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('pending_tags');
        $this->addParameter('qtyDeletedTags', $tagsCountTwo);
        $this->assertMessagePresent('success', 'success_changed_status');
        //Step 12
        $this->navigate('all_tags');
        // Verifying second tag
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagSecondData['tag_name'],
            'tags_status' => 'Approved')
        ));
        // Verifying third tag
        $this->navigate('all_tags');
        $this->assertNotNull($this->tagsHelper()->search(array(
            'tag_name' => $tagThirdData['tag_name'],
            'tags_status' => 'Approved')
        ));
    }
}