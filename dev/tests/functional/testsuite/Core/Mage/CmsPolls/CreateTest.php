<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsPolls
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CmsPolls_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->clearInvalidedCache();
    }

    /**
     * <p>Preconditions:</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('poll_manager');
        $this->cmsPollsHelper()->closeAllPolls();
    }

    /**
     * <p>Creating a new Poll</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3217
     */
    public function createNew()
    {
        //Data
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open');
        $name = $pollData['poll_question'];
        $searchPollData = $this->loadDataSet('CmsPoll', 'search_poll', array('filter_question' => $name));
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_poll');
        $this->cmsPollsHelper()->openPoll($searchPollData);
        $this->cmsPollsHelper()->checkPollData($pollData);
        $this->clearInvalidedCache();
        $this->frontend('about_us');
        $this->assertTrue($this->cmsPollsHelper()->frontCheckPoll($name), 'There is no ' . $name . ' poll on the page');
    }

    /**
     * <p>Creating a new poll with empty required fields.</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @TestlinkId    TL-MAGE-3220
     */
    public function withEmptyRequiredFields($emptyField, $fieldType)
    {
        //Data
        $pollData = $this->loadDataSet('CmsPoll', 'poll_empty_required', array($emptyField => ''));
        if ($emptyField == 'visible_in') {
            $this->navigate('manage_stores');
            $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
            $this->assertMessagePresent('success', 'success_saved_store_view');
            $this->navigate('poll_manager');
        }
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * @return array
     */
    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('poll_question', 'field'),
            array('visible_in', 'multiselect'),
            array('answer_title', 'field'),
            array('votes_count', 'field')
        );
    }

    /**
     * <p>Creating a new poll without answers</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3221
     */
    public function withoutAnswer()
    {
        //Data
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open', array('assigned_answers_set' => '%noValue%'));
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertMessagePresent('error', 'add_answers');
    }

    /**
     * <p>Creating a new poll with few identical answers</p>
     *
     * @test
     * @depends createNew
     * @TestlinkId TL-MAGE-3218
     */
    public function identicalAnswer()
    {
        //Data
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open', array('answer_title' => 'duplicate'));
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertMessagePresent('validation', 'duplicate_poll_answer');
    }

    /**
     * <p>Closed poll is not displayed</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3216
     */
    public function closedIsNotDisplayed()
    {
        //Data
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open');
        $name = $pollData['poll_question'];
        $searchPollData = $this->loadDataSet('CmsPoll', 'search_poll', array('filter_question' => $name));
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_poll');
        $this->clearInvalidedCache();
        $this->frontend('about_us');
        $this->assertTrue($this->cmsPollsHelper()->frontCheckPoll($name), 'There is no ' . $name . ' poll on the page');
        //Steps
        $this->loginAdminUser();
        $this->navigate('poll_manager');
        $this->cmsPollsHelper()->setPollState($searchPollData, 'Closed');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_poll');
        $this->clearInvalidedCache();
        $this->frontend('about_us');
        $this->assertFalse($this->cmsPollsHelper()->frontCheckPoll($name), 'There is ' . $name . ' poll on the page');
    }

    /**
     * <p>Vote a poll</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3219
     */
    public function votePoll()
    {
        //Data
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open');
        $name = $pollData['poll_question'];
        //Steps and Verifying
        $this->cmsPollsHelper()->createPoll($pollData);
        $this->assertMessagePresent('success', 'success_saved_poll');
        $this->clearInvalidedCache();
        $this->frontend();
        $this->navigate('about_us');
        $this->assertTrue($this->cmsPollsHelper()->frontCheckPoll($name), 'There is no ' . $name . ' poll on the page');
        $this->cmsPollsHelper()->vote($name, $pollData['assigned_answers_set']['answer_1']['answer_title']);
        //Verifying
        $this->navigate('about_us');
        $this->assertFalse($this->cmsPollsHelper()->frontCheckPoll($name), 'There is ' . $name . ' poll on the page');
    }
}
