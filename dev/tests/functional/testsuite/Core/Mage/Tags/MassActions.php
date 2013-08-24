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
require_once 'TagsFixtureAbstract.php';
/**
 * Tag creation tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tags_MassActionsTest extends Core_Mage_Tags_TagsFixtureAbstract
{
    /**
     * Deleting tags using mass action
     *
     * @test
     * @dataProvider tagDataProvider
     * @TestlinkId TL-MAGE-2329, TL-MAGE-2332, TL-MAGE-2352, TL-MAGE-2353
     */
    public function deletingTagsUsingMassAction($tagArea)
    {
        $tagData = array();
        //Precondition
        for ($i = 0; $i < 4; $i++) {
            $tagData[$i] = array('tag_name' => 'tag_' . $this->generate('string', 5, ':lower:'),
                                 'tag_status' => 'Pending');
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 2
        $this->navigate($tagArea);
        //Step 3
        $this->searchAndChoose(array('tag_name' => $tagData[0]['tag_name']), 'tags_grid');
        //Step 4
        $this->fillDropdown('tags_massaction', 'Delete');
        //Steps 5-6
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        //Verifying
        $this->addParameter('qtyDeletedTags', '1');
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
        $this->assertNull($this->search(array('tag_name' => $tagData[0]['tag_name']), 'tags_grid'),
            'Tag has been found');
        unset($tagData[0]);
        //Step 7
        foreach ($tagData as $tag) {
            $this->searchAndChoose(array('tag_name' => $tag['tag_name']), 'tags_grid');
        }
        //Step 8
        $this->fillDropdown('tags_massaction', 'Delete');
        //Steps 9-10
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        //Verifying
        $this->addParameter('qtyDeletedTags', '3');
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
        foreach ($tagData as $tag) {
            $this->assertNull($this->search(array('tag_name' => $tag['tag_name']), 'tags_grid'), 'Tag has been found');
        }
        //Step 11
        $this->navigate($tagArea);
        $this->fillDropdown('tags_massaction', 'Delete');
        //Step 12
        $this->clickButton('submit', false);
        //Verifying
        $this->assertEquals('Please select items.', $this->alertText(),
            "Message 'Please select items.' is not present");
        $this->acceptAlert();
    }

    /**
     * Changing the status of tags to Disabled using mass action
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
        for ($i = 0; $i < 3; $i++) {
            $tagData[$i] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate($tagArea);
        // Step 2
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[0]['tag_name']), 'tags_grid');
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
        $this->assertNotNull($this->search(array('tag_name' => $tagData[0]['tag_name'], 'tags_status' => 'Disabled'),
            'tags_grid'));
        //Step 7
        $this->navigate($tagArea);
        //Step 8
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[1]['tag_name']), 'tags_grid');
        $this->tagsHelper()->searchAndChoose(array('tag_name' => $tagData[2]['tag_name']), 'tags_grid');
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
        $this->assertNotNull($this->search(array('tag_name' => $tagData[1]['tag_name'], 'tags_status' => 'Disabled'),
            'tags_grid'));
        // Verifying third tag
        $this->assertNotNull($this->search(array('tag_name' => $tagData[2]['tag_name'], 'tags_status' => 'Disabled'),
            'tags_grid'));
    }

    /**
     * Changing the status of tags to Pending using mass action
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
        for ($i = 0; $i < 3; $i++) {
            $tagData[$i] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate($tagArea);
        // Step 2
        $this->searchAndChoose(array('tag_name' => $tagData[0]['tag_name']), 'tags_grid');
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
        $this->assertNotNull($this->search(array('tag_name' => $tagData[0]['tag_name']), 'tags_grid'));
        //Step 6
        $this->navigate($tagArea);
        $this->searchAndChoose(array('tag_name' => $tagData[1]['tag_name']), 'tags_grid');
        $this->searchAndChoose(array('tag_name' => $tagData[2]['tag_name']), 'tags_grid');
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
        $this->assertNotNull($this->search(array('tag_name' => $tagData[1]['tag_name']), 'tags_grid'));
        //Verify status of third tag
        $this->assertNotNull($this->search(array('tag_name' => $tagData[2]['tag_name']), 'tags_grid'));
    }

    /**
     * Changing the status of tags to Approved using mass action
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
        for ($i = 0; $i < 3; $i++) {
            $tagData[$i] = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_status' => 'Pending'));
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        //Step 1
        $this->navigate($tagArea);
        // Step 2
        $this->searchAndChoose(array('tag_name' => $tagData[0]['tag_name']), 'tags_grid');
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
        $this->assertNotNull($this->search(array('tag_name' => $tagData[0]['tag_name'], 'tags_status' => 'Approved'),
            'tags_grid'));
        //Step 7
        $this->navigate($tagArea);
        //Step 8
        $this->searchAndChoose(array('tag_name' => $tagData[1]['tag_name']), 'tags_grid');
        $this->searchAndChoose(array('tag_name' => $tagData[2]['tag_name']), 'tags_grid');
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
        $this->assertNotNull($this->search(array('tag_name' => $tagData[1]['tag_name'], 'tags_status' => 'Approved'),
            'tags_grid'));
        // Verifying third tag
        $this->assertNotNull($this->search(array('tag_name' => $tagData[2]['tag_name'], 'tags_status' => 'Approved'),
            'tags_grid'));
    }

    /**
     * Changing the status of tags to Disabled/Pending/Approved using mass action
     *
     * @test
     * @dataProvider tagDataProvider
     * @TestlinkId TL-MAGE-2334, TL-MAGE-2337, TL-MAGE-2339, TL-MAGE-2358, TL-MAGE-2361, TL-MAGE-2363
     */
    public function changingStatusUsingMassActionNegative($tagArea)
    {
        //Step 1
        $this->navigate($tagArea);
        $tagStatuses = array('Disabled', 'Pending', 'Approved');
        foreach ($tagStatuses as $value) {
            //Step 2
            $this->fillDropdown('tags_massaction', 'Change status');
            //Step 3
            $this->fillDropdown('tags_status', $value);
            //Step 4
            $this->clickButton('submit', false);
            //Verifying
            //Verifying
            $this->assertSame('Please select items.', $this->alertText(),
                'actual and expected confirmation message does not match');
            $this->acceptAlert();
        }
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        return parent::_preconditionsForMassActionsTests();
    }

    /**
     * Selecting tags options
     *
     * @test
     * @dataProvider tagDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2340, TL-MAGE-2364
     */
    public function selectingTagsOptions($tagArea, $tagData)
    {
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
            array('all_tags')
        );
    }
}
