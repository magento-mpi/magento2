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
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Customer_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * Assert if Admin Dashboard is loaded.
     */
   protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin('dashboard'));
    }


    /**
     * Test creating customer filling only required fields
     * Expected result:
     * Redirect to manage customers page, sucsess massage appeared
     */
    public function test_WithRequiredFieldsOnly()
    {
        $this->assertTrue(
            $this->navigate('manage_customers')->clickButton('add_new_customer')->navigated('create_customer'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('newCustomerForm', null, null));
        $this->clickButton('save_customer');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('manage_customers'), 'After creating customer admin should be redirected to manage customers page');
    }

    /**
     * Test create customer using email that alreadi exist
     * Expected result:
     * 'Customer with the same email already exists.' message appeared
     */
    public function test_WithEmailThatAlreadyExists()
    {
        $this->assertTrue(
            $this->navigate('manage_customers')->clickButton('add_new_customer')->navigated('create_customer'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('newCustomerForm', array('email'=>'test@magento.com')));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage(), '"Customer with the same email already exists." message should appear');
    }

    /**
     * Test create customer with empty reqired 'First Name' field
     * Expected result:
     * 'This is a required field' message appeared under 'First Name' field
     */
    public function test_WithRequiredFieldsEmpty_EmptyFirstName()
    {
        $this->assertTrue($this->navigate('create_customer'));
        $this->fillForm($this->loadData('create_customer', array('first_name' => '')));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage(), '"This is a required field" message should appear under "First Name" field');
    }

    /**
     * Test create customer with empty reqired 'Last Name' field
     * Expected result:
     * 'This is a required field' message appeared under 'Last Name' field
     */
    public function test_WithRequiredFieldsEmpty_EmptyLastName()
    {
        $this->assertTrue($this->navigate('create_customer'));
        $this->fillForm($this->loadData('create_customer', array('last_name' => '')));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage(), '"This is a required field" message should appear under "Last Name" field');
    }

    /**
     * Test create customer with empty reqired 'Email' field
     * Expected result:
     * 'This is a required field' message appeared under 'Email' field
     */
    public function test_WithRequiredFieldsEmpty_EmptyEmail()
    {
        $this->assertTrue($this->navigate('create_customer'));
        $this->fillForm($this->loadData('create_customer', array('email' => '')));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage(), '"This is a required field" message should appear under "Email" field');
    }

    /**
     * Test create customer with empty reqired 'Password' field
     * Expected result:
     * 'This is a required field' message appeared under 'Password' field
     */
    public function test_WithRequiredFieldsEmpty_EmptyPassword()
    {
        $this->assertTrue($this->navigate('create_customer'));
        $this->fillForm($this->loadData('create_customer', array('password' => '')));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage(), '"This is a required field" message should appear under "Password" field');
    }

    /**
     * @TODO
     */
    public function test_WithSpecialCharacters()
    {
       // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithLongValues()
    {
        // @TODO
    }

    /**
     * Test create customer with invalid 'Email' field
     * Expected result:
     * '"Email" is not a valid email address.' message appeared under 'Email' field
     */
    public function test_WithInvalidEmail()
    {
        $this->assertTrue($this->navigate('create_customer'));
        $this->fillForm($this->loadData('create_customer', array('email' => '123123')));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage(), 'Invalid email message should appear');
    }

    /**
     * Test create customer with invalid 'Password' field
     * Expected result:
     * 'Please enter 6 or more characters. Leading or trailing spaces will be ignored.' message appeared under 'Password' field
     */
    public function test_WithInvalidPassword()
    {
        $this->assertTrue($this->navigate('create_customer'));
        $this->fillForm($this->loadData('create_customer', array('password' => '1')));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage(), 'Invalid password message should appear');
    }

    /**
     * @TODO
     */
    public function test_WithAddress()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithoutAddress()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_OnOrderPage_WithAddress()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_OnOrderPage_WithoutAddress()
    {
        // @TODO
    }
}
