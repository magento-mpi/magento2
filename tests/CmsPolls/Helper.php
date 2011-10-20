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

    /**
     * Add answers to  Poll
     *
     * @param array $answersSet
     */
    public function addAnswer($answersSet) {
        $waitAjax = false;
        $tabXpath = $this->getCurrentLocationUimapPage()->findTab('poll_answers')->getXpath();
        $isTabOpened = $this->getAttribute($tabXpath . '/parent::*/@class');
        if (!preg_match('/active/', $isTabOpened)) {
            if (preg_match('/ajax/', $isTabOpened)) {
                $waitAjax = true;
            }
            $this->clickControl('tab', 'poll_answers', false);
            if ($waitAjax) {
                $this->pleaseWait();
            }
        }
        foreach ($answersSet as $key => $value) {
            $this->clickButton('add_new_answer', FALSE);
            $this->addParameter('answerId', $key);
            $this->fillForm($value);
        }
    }

    /**
     * Create Poll
     *
     * @param array $pollData
     */
    public function createPoll($pollData)
    {
        $pollData = $this->arrayEmptyClear($pollData);
        $this->clickButton('add_new_poll');
        $this->fillForm($pollData, 'poll_information');
        if (array_key_exists('assigned_answers_set', $pollData) && is_array($pollData['assigned_answers_set'])) {
            $this->addAnswer($pollData['assigned_answers_set']);
        }
        $this->saveForm('save_poll');
    }

    /**
     * Open Poll
     *
     * @param string $pollTitle
     */
    public function openPoll($pollData) {
        if (is_array($pollData) and isset($pollData['poll_question'])) {
            $pollName = $pollData['poll_question'];
        }
        $searchData = $this->loadData('search_poll', array('poll_question' => $pollName));
        $this->arrayEmptyClear($searchData);
        $this->assertTrue($this->searchAndOpen($searchData), "Poll with name '$pollName' is not found");
    }

    /**
     * Check Poll exists on frontend
     *
     * @param array $pollData
     */
    public function frontCheckPoll($pollData) {
        $currentPage = $this->getCurrentPage();
        $this->frontend();

        $this->addParameter('pollTitle', $pollData['poll_question']);
        $pollXpath = $this->getCurrentLocationUimapPage()->findFieldset('community_poll')->getXpath();
        $this->assertTrue($this->isElementPresent($pollXpath), "There is no any poll on Home page");

        $this->getCurrentUimapPage()->findPageelement($key);
        $pollTitleXPath = $this->getCurrentLocationUimapPage()->findPageelement('poll_title');
        $this->assertTrue($this->isElementPresent($pollTitleXPath),
            "There is no " . $pollData['poll_question'] . " poll on Home page");

        $this->admin();
        $this->navigate($currentPage);
    }

    /**
     * Change Poll state to Close
     *
     * @param array $pollData
     */
    public function setPollCloseState($pollData) {
        $this->openPoll($pollData);
        $pollData['poll_status'] = 'Closed';
        $this->fillForm($pollData, 'poll_information');
        $this->saveForm('save_poll');
    }

    /**
     * Change Poll state to Open
     *
     * @param array $pollData

     */
    public function setPollOpenState($pollData) {
        $this->openPoll($pollData);
        $pollData['poll_status'] = 'Open';
        $this->fillForm($pollData, 'poll_information');
        $this->saveForm('save_poll');
    }

    /**
     * Change state for all opened polls to Close
     */
    public function closeAllPolls() {
        $searchData = $this->loadData('search_poll', array('poll_status' => 'Open'));
        $this->arrayEmptyClear($searchData);
        $xpathTR = $this->search($searchData);
        $pollData['poll_status'] = 'Closed';
        while ($this->isElementPresent($xpathTR)) {
            $this->click($xpathTR);
            $this->waitForPageToLoad();
            $this->fillForm($pollData, 'poll_information');
            $this->saveForm('save_poll');
        }
    }

}
