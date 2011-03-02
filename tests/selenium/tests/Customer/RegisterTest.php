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
class Customer_RegisterTest extends Mage_Selenium_TestCase
{

    /**
     * Make sure that customer is not logged in, and navigate to homepage
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->front('home'));
    }

    /**
     * @TODO
     */
    public function test_FromCreateAccountPage()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_FromOnePageCheckoutPage()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_FromMultipleCheckoutPage()
    {
        // @TODO
    }

    public function testNavigation()
    {
        $this->assertTrue($this->navigate('my_account'));
        $this->assertTrue($this->clickButton('register'), 'There is no "Register" button on the page');
        $this->assertTrue($this->navigated('customer_account_create'), 'Wrong page is displayed');
        $this->assertTrue($this->navigate('customer_account_create'), 'Wrong page is displayed when accessing direct URL');
        $this->assertTrue($this->controlIsPresent('link','back'), 'There is no "Back" link on the page');
    }

    public function testRegistration_Smoke()
    {
        $this->assertTrue(
            $this->navigate('my_account')->clickButton('register')->navigated('customer_account_create'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->data('customer_account_create', null, 'email'));
        $this->clickButton('submit');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('customer_account_index'), 'After succesfull registration customer should be redirected to account dashboard');
    }

    public function testLongValues()
    {
        $this->assertTrue($this->navigate('customer_account_create'));
        $this->fillForm($this->data('customer_account_create', array(
            'firstname' => $this->generate('string', 260, ':alnum:'),
            'lastname'  => $this->generate('string', 260, ':alnum:'),
            'email'     => $this->generate('email', 260, 'valid', $this->uid),
        )));
        $this->clickButton('submit');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('customer_account_index'), 'After succesfull registration customer should be redirected to account dashboard');
    }
    
}
