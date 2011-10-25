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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CmsPolls_Helper extends Mage_Selenium_TestCase
{

    public $isVisibleIn = false;

    /**
     * Add answers to Poll
     *
     * @param array $answersSet Array of answers for poll
     */
    public function addAnswer(array $answersSet)
    {
        if (!$answersSet) {
            return true;
        }
        $tabXpath = $this->getCurrentLocationUimapPage()->findTab('poll_answers')->getXpath();
        $isTabOpened = $this->getAttribute($tabXpath . '/parent::*/@class');
        if (!preg_match('/active/', $isTabOpened)) {
            $this->clickControl('tab', 'poll_answers', false);
        }
        $answerId = 1;
        foreach ($answersSet as $value) {
            $this->clickButton('add_new_answer', FALSE);
            $this->addParameter('answerId', '-' . $answerId);
            $this->fillForm($value);
            $answerId++;
        }
    }

    /**
     * Create Poll
     *
     * @param array $pollData Array of Poll data
     */
    public function createPoll($pollData)
    {
        $pollData = $this->arrayEmptyClear($pollData);
        $answers = (isset($pollData['assigned_answers_set'])) ? $pollData['assigned_answers_set'] : array();
        if ($this->controlIsPresent('dropdown', 'filter_visible_in')) {
            $this->isVisibleIn = true;
        }
        $this->clickButton('add_new_poll');
        if (!$this->isVisibleIn && isset($pollData['visible_in']))
            unset($pollData['visible_in']);
        $this->fillForm($pollData, 'poll_information');
        $this->addAnswer($answers);
        $this->saveForm('save_poll');
    }

    /**
     * Open Poll
     *
     * @param string|array $pollData Poll to open. Either a dataset name (string) or the whole data set (array).
     */
    public function openPoll($searchPollData)
    {
        $searchPollData = $this->arrayEmptyClear($searchPollData);
        if ($this->controlIsPresent('dropdown', 'filter_visible_in')) {
            $this->isVisibleIn = true;
        }
        if (array_key_exists('filter_visible_in', $searchData) && !$this->isVisibleIn) {
            unset($data['visible_in']);
        }
        $xpathTR = $this->search($searchData, 'poll_grid');
        $this->assertNotEquals(null, $xpathTR, 'Poll is not found');
        $names = $this->shoppingCartHelper()->getColumnNamesAndNumbers('poll_grid_head', false);
        if (array_key_exists('Poll Question', $names)) {
            $text = $this->getText($xpathTR . '//td[' . $names['Poll Question'] . ']');
            $this->addParameter('pollName', $text);
        }
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $names['Poll Question'] . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Check Poll exists on frontend
     *
     * @param string $pollTitle "Poll Question"
     * @return boolean Return TRUE if poll is visible on home page
     */
    public function frontCheckPoll($pollTitle)
    {
        $this->addParameter('pollTitle', $pollTitle);
        $page = $this->getCurrentLocationUimapPage();
        $pollXpath = $page->findFieldset('community_poll')->getXpath();
        if (!$this->isElementPresent($pollXpath)) {
            $this->fail('Community poll block is not displayed on the page');
        }
        $pollTitleXPath = $page->findPageelement('poll_title');

        return $this->isElementPresent($pollTitleXPath);
    }

    /**
     * Change Poll state
     *
     * @param string $pollData Array of Poll data
     */
    public function setPollState($searchPollData, $state)
    {
        $this->openPoll($searchPollData);
        $this->fillForm(array('poll_status' => $state), 'poll_information');
        $this->saveForm('save_poll');
    }

    /**
     * Change state for all opened polls to Close
     */
    public function closeAllPolls()
    {
        $this->clickButton('reset_filter');
        $xpathTR = $this->search(array('filter_status' => 'Open'));
        while ($this->isElementPresent($xpathTR)) {
            $itemId = $this->defineIdFromTitle($xpathTR);
            $this->addParameter('id', $itemId);
            $this->clickAndWait($xpathTR . '/td');
            $this->validatePage();
            $this->fillForm(array('poll_status' => 'Closed'), 'poll_information');
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
        //@TODO Fix for verifyForm function. Multiselect label will have space before value
        if (isset($pollData['visible_in']))
            $pollData['visible_in'] = '    ' . $pollData['visible_in'];
        if (array_key_exists($key, $searchData) && !$this->isVisibleIn)
            unset($pollData['visible_in']);
        $this->assertTrue($this->verifyForm($pollData, 'poll_information', array('assigned_answers_set'),
                        $this->messages));
        $this->clickControl('tab', 'poll_answers', false);
        $this->assertTrue($this->verifyForm($pollData['assigned_answers_set'], 'poll_answers'));
        $this->clickButton('back');
    }

    /**
     * Delete a Poll
     *
     * @param array $pollData Array of Poll data
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
     * @param string $pollAnswer Answer to vote
     */
    public function vote($pollTitle, $pollAnswer)
    {
        $this->addParameter('pollTitle', $pollTitle);
        $pollTitleXPath = $this->getCurrentLocationUimapPage()->findPageelement('poll_title');
        $isPresent = $this->isElementPresent($pollTitleXPath);
        $this->addParameter('answer', $pollAnswer);
        if ($isPresent and $this->controlIsPresent('radiobutton', 'vote')) {
            $this->clickControl('radiobutton', 'vote', false);
            $this->clickButton('vote');
        } else {
            $this->fail("Could not vote");
        }
    }

}
