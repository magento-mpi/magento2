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
class CmsPolls_Helper extends Mage_Selenium_TestCase {

    public $isVisibleIn = false;
    /**
     * Add answers to Poll
     *
     * @param array $answersSet Array of answers for poll
     */
    public function addAnswer($answersSet)
    {
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
        $this->clickButton('add_new_poll');
        $this->isVisibleIn = $this->controlIsPresent('multiselect', 'visible_in');
        if (!$this->isVisibleIn)
            unset($pollData['visible_in']);
        $this->fillForm($pollData, 'poll_information');
        if (array_key_exists('assigned_answers_set', $pollData) && is_array($pollData['assigned_answers_set'])) {
            $this->addAnswer($pollData['assigned_answers_set']);
        }
        $this->saveForm('save_poll');
    }

    /**
     * Open Poll
     *
     * @param string|array $pollData Poll to open. Either a dataset name (string) or the whole data set (array).
     */
    public function openPoll($pollData)
    {
        if (is_string($pollData))
        $pollData = $this->loadData($pollData);
        $pollData = $this->arrayEmptyClear($pollData);
        //@TODO temporary fix for search
        unset($pollData['visible_in']);
        //remove "Answers" array from seacrh array - may cause fail of searchAndOpen function
        //$this->click($xpathTR . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
        unset($pollData['assigned_answers_set']);
        if (!empty ($pollData['poll_question']))
            $this->addParameter('pollName', $pollData['poll_question']);
        $this->assertTrue($this->searchAndOpen($pollData),
                "Poll with name " . $pollData['poll_question'] . " is not found");
    }

    /**
     * Check Poll exists on frontend
     *
     * @param string $pollTitle "Poll Question"
     * @return boolean Return TRUE if poll is visible on home page
     */
    public function frontCheckPoll($pollTitle)
    {
        $isPresent = false;
        $this->addParameter('pollTitle', $pollTitle);
        $pollXpath = $this->getCurrentLocationUimapPage()->findFieldset('community_poll')->getXpath();
        $isPresent = $this->isElementPresent($pollXpath);
        $pollTitleXPath = $this->getCurrentLocationUimapPage()->findPageelement('poll_title');
        $isPresent = $this->isElementPresent($pollTitleXPath);
        return $isPresent;
    }

    /**
     * Change Poll state
     *
     * @param string $pollData Array of Poll data
     */
    public function setPollState($pollData, $state)
    {
        $this->openPoll($pollData);
        $pollData = array('poll_status' => $state);
        $this->fillForm($pollData, 'poll_information');
        $this->saveForm('save_poll');
    }

    /**
     * Change state for all opened polls to Close
     */
    public function closeAllPolls()
    {
        $searchData = array('poll_status' => 'Open');
        $xpathTR = $this->search($searchData);
        $searchData['poll_status'] = 'Closed';
        while ($this->isElementPresent($xpathTR)) {
            $this->click($xpathTR);
            $this->waitForPageToLoad();
            $this->addParameter('id', $this->defineIdFromUrl());
            $this->fillForm($searchData, 'poll_information');
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
        $this->openPoll($pollData);
        //@TODO Fix for verifyForm function. Multiselect label will have space before value
        if (isset($pollData['visible_in']))
            $pollData['visible_in'] = '    ' . $pollData['visible_in'];
        if (!$this->isVisibleIn)
            unset($pollData['visible_in']);
        $this->assertTrue($this->verifyForm($pollData, 'poll_information',
                array('assigned_answers_set'), "Poll information is wrong"));
        $this->clickControl('tab', 'poll_answers', false);
        $this->assertTrue($this->verifyForm($pollData['assigned_answers_set'], 'poll_answers'));
        $this->clickButton('back');
    }

    /**
     * Delete a Poll
     *
    * @param array $pollData Array of Poll data
     */
    public function deletePoll($pollData)
    {
        $this->openPoll($pollData);
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
