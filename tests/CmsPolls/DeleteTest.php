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
class CmsPolls_DeleteTest extends Mage_Selenium_TestCase {

    protected static $visibleIn = false;

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests() {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to CMS -> Polls</p>
     */
    protected function assertPreConditions() {
        $this->navigate('poll_manager');
        self::$visibleIn = $this->controlIsPresent('dropdown', 'poll_visible_in');
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->messages);
        $this->addParameter('id', '0');
    }

    /**
     * <p>Delete a Poll</p>
     * <p>Steps:</p>
     * <p>1. Create a Poll</p>
     * <p>2. Open the Poll</p>
     * <p>3. Delete the Poll</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Poll has been deleted.</p>
     *
     * @test
     */
    public function deleteNewPoll() {
        //Data
        $pollData = $this->loadData('poll_open', null, 'poll_question');
        if (!self::$visibleIn)
            unset($pollData['visible_in']);
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_poll'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->messages);
        //Steps
        $this->cmsPollsHelper()->openPoll($pollData['poll_question']);
        $this->cmsPollsHelper()->deletePoll();
        //Verifying
        $this->assertTrue($this->successMessage('success_deleted_poll'), $this->messages);
    }

}
