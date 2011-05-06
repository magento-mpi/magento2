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
 * Test deletion customer.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Customer_DeleteTest extends Mage_Selenium_TestCase {

    /**
     * Preconditions:
     *
     * Log in to Backend.
     *
     * Navigate to System -> Manage Customers
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->assertTrue($this->admin());
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
    }

    /**
     * Create customer for 'test_Single' test
     *
     * @return array
     */
    public function test_CreateCustomer()
    {
        //Data
        $userData = $this->loadData('generic_customer_account',
                        array('email' => $this->generate('email', 20, 'valid')));
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->saveForm('save_customer');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');

        return $userData;
    }

    /**
     * Delete customer.
     *
     * Steps:
     *
     * 1. Search and open customer.
     *
     * 2. Click 'Delete Customer' button.
     *
     * Expected result:
     *
     * Customer is deleted.
     *
     * Success Message is displayed.
     *
     * @depends test_CreateCustomer
     */
    public function test_Single(array $userData)
    {
        //Data
        $searchData = $this->loadData('search_customer', array('email' => $userData['email']));
        //Steps
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($searchData), 'Customer is not found');
        $this->deleteElement('delete_customer', 'confirmation_for_delete');
        //Verifying
        $this->assertTrue($this->successMessage('success_deleted_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
    }

    /**
     * @TODO
     */
    public function test_ThroughMassAction()
    {
        // @TODO
        $this->markTestIncomplete();
    }

}
