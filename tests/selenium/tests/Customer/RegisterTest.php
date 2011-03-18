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
 * Customer registration tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Customer_RegisterTest extends Mage_Selenium_TestCase {

    /**
     * Make sure that customer is not logged in, and navigate to homepage
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->frontend('home'));
        $this->assertTrue($this->logoutCustomer());
    }

    public function test_WithRequiredFieldsOnly()
    {
        $userData = $this->loadData('customer_account_register', NULL, NULL);
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->clickButton('submit');
        $this->assertFalse($this->errorMessage(), $this->messages);
//      @TODO
//        $this->assertTrue($this->navigated('customer_account'),
//                'After succesfull registration customer should be redirected to account dashboard');
        $this->assertTrue($this->successMessage('success_registration'), 'No success message is displayed');
    }

    public function test_WithEmailThatAlreadyExists()
    {
        $userData = $this->loadData('customer_account_register', NULL, NULL);
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->clickButton('submit');
        $this->assertTrue($this->errorMessage('email_exists'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    public function test_WithLongValues()
    {
        $userData = $this->loadData(
                        'customer_account_register',
                        array(
                            'first_name' => $this->generate('string', 255, ':alnum:'),
                            'last_name' => $this->generate('string', 255, ':alnum:'),
                            'email' => $this->generate('email', 255, 'valid')
                        ),
                        NULL
        );
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->clickButton('submit');
        $this->assertFalse($this->errorMessage(), $this->messages);
//      @TODO
//        $this->assertTrue($this->navigated('customer_account'),
//                'After succesfull registration customer should be redirected to account dashboard');
        $this->assertTrue($this->successMessage('success_registration'),
                'No success message is displayed');
//        @TODO
//        $this->clickControl('tab', 'account_information', FALSE);
//        foreach ($longValues as $key => $value) {
//            $xpath = $this->getCurrentLocationUimapPage()->getMainForm()->getTab('account_information')->findField($key);
//            $this->assertEquals(strlen($this->getValue($xpath)), 255);
//        }
    }

    /**
     * @dataProvider data_EmptyField
     */
    public function test_WithRequiredFieldsEmpty($field)
    {
        $userData = $this->loadData('customer_account_register', $field, 'email');
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->clickButton('submit');
        foreach ($field as $key => $value) {
            $xpath = $this->getCurrentLocationUimapPage()->findFieldset('account_info')->findField($key);
        }
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $xpath)));
        $this->assertTrue($this->errorMessage('empty_reqired_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array(array('first_name' => null)),
            array(array('last_name' => null)),
            array(array('email' => null)),
            array(array('password' => null)),
            array(array('password_confirmation' => null)),
        );
    }

    /**
     * @TODO
     */
    public function test_WithSpecialCharacters()
    {
        $userData = $this->loadData(
                        'customer_account_register',
                        array(
                            'first_name' => $this->generate('string', 25, ':punct:'),
                            'last_name' => $this->generate('string', 25, ':punct:'),
                        ),
                        'email'
        );
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->clickButton('submit');
        $this->assertFalse($this->errorMessage(), $this->messages);
//      @TODO
//        $this->assertTrue($this->navigated('customer_account'),
//                'After succesfull registration customer should be redirected to account dashboard');
        $this->assertTrue($this->successMessage('success_registration'), 'No success message is displayed');
    }

    /**
     * @TODO
     * @dataProvider data_LongValues_NotValid
     */
    public function test_WithLongValues_NotValid($longValue)
    {
        $userData = $this->loadData('customer_account_register', $longValue, 'email');
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->clickButton('submit');
        foreach ($longValue as $key => $value) {
            $fieldName = $key;
        }
        $this->assertFalse($this->successMessage(), $this->messages);
        $this->assertTrue($this->errorMessage("not_valid_length_$fieldName"), 'No success message is displayed');
    }

    public function data_LongValues_NotValid()
    {
        return array(
            array(array('first_name' => $this->generate('string', 256, ':punct:'))),
            array(array('last_name' => $this->generate('string', 256, ':punct:'))),
            array(array('email' => $this->generate('email', 256, 'valid'))),
        );
    }

    /**
     * @TODO
     */
    public function test_WithInvalidEmail()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidPassword()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidDateOfBirth()
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

}
