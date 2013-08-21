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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CmsPolls_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Add answers to Poll
     *
     * @param array $answersSet Array of answers for poll
     */
    public function addAnswer(array $answersSet)
    {
        $answerId = 1;
        $this->openTab('poll_answers');
        foreach ($answersSet as $value) {
            $this->clickButton('add_new_answer', false);
            $this->addParameter('answerId', '-' . $answerId++);
            $this->assertTrue($this->controlIsPresent('pageelement', 'assigned_answers_block'),
                'New Assigned Answers block is not added');
            $this->fillForm($value, 'poll_answers');
        }
    }

    /**
     * Create Poll
     *
     * @param array $pollData Array of Poll data
     */
    public function createPoll(array $pollData)
    {
        $answers = (isset($pollData['assigned_answers_set'])) ? $pollData['assigned_answers_set'] : array();
        $this->clickButton('add_new_poll');
        if (!$this->controlIsPresent('multiselect', 'visible_in') && isset($pollData['visible_in'])) {
            unset($pollData['visible_in']);
        }
        $this->fillForm($pollData, 'poll_information');
        $this->addAnswer($answers);
        $this->saveForm('save_poll');
    }

    /**
     * Open Poll
     *
     * @param array $searchData
     */
    public function openPoll(array $searchData)
    {
        if (isset($searchData['filter_visible_in']) && !$this->controlIsVisible('dropdown', 'filter_visible_in')) {
            unset($searchData['filter_visible_in']);
        }
        //Search Poll
        $searchData = $this->_prepareDataForSearch($searchData);
        $pollLocator = $this->search($searchData, 'poll_grid');
        $this->assertNotNull($pollLocator, 'Poll is not found with data: ' . print_r($searchData, true));
        $pollRowElement = $this->getElement($pollLocator);
        $pollUrl = $pollRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Poll Question');
        $cellElement = $this->getChildElement($pollRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($pollUrl));
        //Open Poll
        $this->url($pollUrl);
        $this->validatePage('edit_poll');
    }

    /**
     * Check if Poll exists on frontend
     *
     * @param string $pollTitle "Poll Question"
     *
     * @return boolean Return TRUE if poll is visible on the page
     */
    public function frontCheckPoll($pollTitle)
    {
        $this->addParameter('pollTitle', $pollTitle);
        if (!$this->controlIsPresent('fieldset', 'community_poll')) {
            return false;
        }

        return $this->controlIsPresent('pageelement', 'poll_title');
    }

    /**
     * Change Poll state
     *
     * @param array $searchPollData
     * @param string $state
     *
     * @internal param string $pollData Array of Poll data
     */
    public function setPollState($searchPollData, $state)
    {
        $this->openPoll($searchPollData);
        $this->fillDropdown('poll_status', $state);
        $this->saveForm('save_poll');
    }

    /**
     * Change state for all opened polls to Close
     */
    public function closeAllPolls()
    {
        $this->fillDropdown('filter_status', 'Open');
        $this->clickButton('search');
        $xpathTR = $this->formSearchXpath(array('filter_status' => 'Open'));
        $cellId = $this->getColumnIdByName('Poll Question');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        while ($this->controlIsPresent('pageelement', 'table_line_cell_index')) {
            $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
            $this->addParameter('elementTitle', $param);
            $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
            $this->clickControl('pageelement', 'table_line_cell_index');
            $this->fillDropdown('poll_status', 'Closed');
            $this->saveForm('save_poll');
        }
    }

    /**
     * Check poll information
     *
     * @param array $pollData Array of Poll data
     */
    public function checkPollData($pollData)
    {
        if (!$this->controlIsPresent('multiselect', 'visible_in') && isset($pollData['visible_in'])) {
            unset($pollData['visible_in']);
        }
        $answers = (isset($pollData['assigned_answers_set'])) ? $pollData['assigned_answers_set'] : array();

        $this->assertTrue($this->verifyForm($pollData, 'poll_information'), $this->getParsedMessages());

        $answersCount = $this->getControlCount('fieldset', 'assigned_answers_set');
        if (count($answers) == $answersCount) {
            $param = 1;
            foreach ($answers as $value) {
                $this->addParameter('index', $param);
                $this->addParameter('elementXpath', $this->_getControlXpath('fieldset', 'assigned_answers_set'));
                $attId = $this->getControlAttribute('pageelement', 'element_index', 'id');
                $answerId = explode("_", $attId);
                $this->addParameter('answerId', end($answerId));
                $this->assertTrue($this->verifyForm($value, 'poll_answers'), $this->getParsedMessages());
                $param++;
            }
        } else {
            $this->fail("Unexpected count of answers: " . count($answers) . "!= $answersCount");
        }
    }

    /**
     * Delete a Poll
     *
     * @param array $searchPollData Array of Poll data
     */
    public function deletePoll($searchPollData)
    {
        $this->openPoll($searchPollData);
        $this->clickButtonAndConfirm('delete_poll', 'confirmation_for_delete');
    }

    /**
     * Vote
     *
     * @param string $pollTitle "Poll Question"
     * @param string $pollAnswer Answer to votes
     */
    public function vote($pollTitle, $pollAnswer)
    {
        $this->addParameter('pollTitle', $pollTitle);
        $this->addParameter('answer', $pollAnswer);
        if ($this->controlIsPresent('pageelement', 'poll_title') && $this->controlIsPresent('radiobutton', 'vote')) {
            $this->clickControl('radiobutton', 'vote', false);
            $this->clickButton('vote');
        } else {
            $this->fail("Could not vote");
        }
    }
}