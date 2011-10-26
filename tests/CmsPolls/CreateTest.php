<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CmsPolls_CreateTest extends Mage_Selenium_TestCase {

    protected $pollToBeDeleted = null;

    /**
     * <p>Log in to Backend.</p>
     * <p>Close all opened Polls</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('poll_manager');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to CMS -> Polls</p>
     */
    protected function assertPreConditions()
    {
        $this->admin('poll_manager');
        $this->cmsPollsHelper()->closeAllPolls();
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->messages);
        $this->addParameter('id', '0');
    }

    /**
     * <p>Creating a new Poll</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Poll"</p>
     * <p>2. Fill in the fields</p>
     * <p>3. Click button "Save Poll"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Poll has been saved.</p>
     * <p>Poll is displayed on Homepage</p>
     *
     * @test
     */
    public function createNew()
    {
        //Data
        $pollData = $this->loadData('poll_open', null, 'poll_question');
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_poll'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->messages);
        $this->cmsPollsHelper()->checkPollData($pollData);
        $this->frontend();
        $this->assertTrue($this->cmsPollsHelper()->frontCheckPoll($pollData['poll_question']),
                "There is no " . $pollData['poll_question'] . " poll on home page");
        //Cleanup
        $this->pollToBeDeleted = $pollData;
    }

    /**
     * <p>Creating a new poll with empty required fields.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Poll"</p>
     * <p>2. Fill in the fields, but leave one required field empty;</p>
     * <p>3. Click button "Save Poll".</p>
     * <p>Expected result:</p>
     * <p>Received error message "This is a required field."</p>
     *
     * @dataProvider dataEmptyRequiredFields
     * @test
     */
    public function withEmptyRequiredFields($emptyField, $fieldType)
    {
        //Data
        $pollData = $this->loadData('poll_open', array($emptyField => ''), 'poll_question');
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        $tst = $this->cmsPollsHelper()->isVisibleIn;
        //Verifying
        if (!$this->cmsPollsHelper()->isVisibleIn and $emptyField=='visible_in') {
            $this->assertTrue($this->successMessage(), $this->messages);
            $this->pollToBeDeleted = $pollData;
        } else {
            $this->addFieldIdToMessage($fieldType, $emptyField);
            $this->assertTrue($this->validationMessage(), $this->messages);
            $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        }
    }

    public function dataEmptyRequiredFields()
    {
        return array(
            array('poll_question', 'field'),
            array('visible_in', 'multiselect')
        );
    }

    /**
     * <p>Creating a new poll with few identical answers</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Poll"</p>
     * <p>2. Fill in the fields, but add few identical answers;</p>
     * <p>3. Click button "Save Poll".</p>
     * <p>Expected result:</p>
     * <p>Received error message "Your answers contain duplicates."</p>
     *
     * @depends createNew
     * @test
     */
    public function identicalAnswer()
    {
        //Data
        $pollData = $this->loadData('poll_open', null, 'poll_question');
        //Dublicate answers
        foreach ($pollData['assigned_answers_set'] as $value) {
            $pollData['assigned_answers_set'][] = $value;
        }
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertTrue($this->validationMessage('dublicate_poll_answer'), $this->messages);
    }

    /**
     * <p>Closed poll is not displayed</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Poll".</p>
     * <p>2. Fill in the fields, set state to "Close".</p>
     * <p>3. Click button "Save Poll".</p>
     * <p>4. Check poll on Homepage.</p>
     * <p>Expected result:</p>
     * <p>Poll should not be displayed.</p>
     *
   //  * @depends createNew
     * @test
     */
    public function closedIsNotDispalyed()
    {
        //Data
        $pollData = $this->loadData('poll_open', null, 'poll_question');
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_poll'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->messages);
        $this->frontend();
        $this->assertTrue($this->cmsPollsHelper()->frontCheckPoll($pollData['poll_question']),
                "There is no " . $pollData['poll_question'] . " poll on home page");
        //Steps
        $this->admin('poll_manager');
        $this->cmsPollsHelper()->setPollState($pollData, 'Closed');
        //Verifying
        $this->frontend();
        $this->assertFalse($this->cmsPollsHelper()->frontCheckPoll($pollData['poll_question']),
                "There is " . $pollData['poll_question'] . " poll on home page");
        //Cleanup
        $this->pollToBeDeleted = $pollData;
    }

    /**
     * <p>Vote a poll</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Poll"</p>
     * <p>2. Fill in the fields.</p>
     * <p>3. Click button "Save Poll".</p>
     * <p>4. Vote a poll on Homepage.</p>
     * <p>5. Re-open Homepage.</p>
     * <p>Expected result:</p>
     * <p>Poll should not be available for vote</p>
     *
     * @depends createNew
     * @test
     */
    public function votePoll()
    {
        //Data
        $pollData = $this->loadData('poll_open', null, 'poll_question');
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_poll'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->messages);
        $this->frontend();
        $this->assertTrue($this->cmsPollsHelper()->frontCheckPoll($pollData['poll_question']),
                "There is no " . $pollData['poll_question'] . " poll on home page");
        //Steps
        $this->cmsPollsHelper()->vote($pollData['poll_question'], $pollData['assigned_answers_set'][1]['answer_title']);
        //Verifying
        //Re-open Home page
        $this->frontend();
        $this->assertFalse($this->cmsPollsHelper()->frontCheckPoll($pollData['poll_question']),
                "There is " . $pollData['poll_question'] . " poll on home page");
        //Cleanup
        $this->pollToBeDeleted = $pollData;
    }

    protected function tearDown()
    {
        if (isset($this->pollToBeDeleted) and $this->pollToBeDeleted['poll_question'] != '') {
            $this->admin('poll_manager');
            //search and delete Poll by title
            $this->cmsPollsHelper()->deletePoll(array('poll_question' => $this->pollToBeDeleted['poll_question']));
        }
        $this->pollToBeDeleted = null;
    }

}
