<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unit test for Mage_Selenium_TestCase helper
 */
class Mage_Selenium_TestCaseTest extends Mage_PHPUnit_TestCase
{
    /**
     * Testing Mage_Selenium_TestCase::fillForm()
     */
    public function testFillForm()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();
        $this->assertNotNull($_testCaseInst);

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->admin('dashboard'));
        $this->assertNotNull($_testCaseInst->navigate('manage_customers'));
        $this->assertEquals($this->getUimapPage($_testCaseInst->getArea(), $_testCaseInst->getCurrentPage())->getTitle(), $_testCaseInst->getTitle());

        $this->assertNotNull($_testCaseInst->clickButton('add_new_customer'));
        $this->assertEquals($this->getUimapPage($_testCaseInst->getArea(), $_testCaseInst->getCurrentPage())->getTitle(), $_testCaseInst->getTitle());

        $_formData = $_testCaseInst->loadData('all_fields_customer_account');
        $this->assertNotEmpty($_formData);
        $this->assertInternalType('array', $_formData);

        $_testCaseInst->click('//*[@id="add_address_button"]');

        $params = new Mage_Selenium_Helper_Params();
        $params->setParameter('address_number', 1);
        $_testCaseInst->appendParamsDecorator($params);

        $this->assertNotNull($_testCaseInst->fillForm($_formData, 'account_information'));
    }

    /**
     * Testing Mage_Selenium_TestCase::controlIsPresent()
     */
    public function testControlIsPresent()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();
        $this->assertNotNull($_testCaseInst);

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $this->assertTrue($_testCaseInst->controlIsPresent('button', 'save_customer'));

        $_testCaseInst->click('//*[@id="add_address_button"]');
        $_testCaseInst->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number'=>1)));

        $this->assertTrue($_testCaseInst->controlIsPresent('field', 'prefix'));
        $this->assertFalse($_testCaseInst->controlIsPresent('field', 'invalid-field'));
    }

    /**
     * Testing Mage_Selenium_TestCase::clickControl()
     */
    public function testClickControl()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();
        $this->assertNotNull($_testCaseInst);

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $this->assertNotNull($_testCaseInst->clickControl('button', 'save_customer', false));
        $this->assertTrue($_testCaseInst->checkCurrentPage('create_customer'));
        $this->assertFalse($_testCaseInst->checkCurrentPage('create_customer_bla_bla_bla'));
        $this->assertNotNull($_testCaseInst->clickControl('tab', 'addresses', false));

    }

    /**
     * Testing Mage_Selenium_TestCase::loadData()
     */
    public function testLoadData()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();
        $this->assertNotNull($_testCaseInst);

        $_formData = $_testCaseInst->loadData('all_fields_customer_account', null, 'associate_to_website');
//        var_dump($_formData);

        $_formData = $_testCaseInst->loadData('all_fields_customer_account', null, array('first_name', 'middle_name_initial', 'last_name'));
//        var_dump($_formData);

        $this->assertNotEmpty($_formData);
        $this->assertInternalType('array', $_formData);

        
    }

    /**
     * Testing Mage_Selenium_TestCase::errorMessage()
     */
    public function testErrorMessage()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $_formData = $_testCaseInst->loadData('generic_customer_account', array('email' => $this->_config->getDataGenerator()->generateEmailAddress(100)));
        $_testCaseInst->click('//*[@id="add_address_button"]');

        $_testCaseInst->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number'=>1)));
        $this->assertNotNull($_testCaseInst->fillForm($_formData));
        $_testCaseInst->click('//*[@id="delete_button21"]');
        $_testCaseInst->getConfirmation();
        $_testCaseInst->clickButton('save_customer', false);

        sleep(5);

        $this->assertFalse($_testCaseInst->successMessage());
        $this->assertTrue($_testCaseInst->errorMessage());
    }

    /**
     * Testing Mage_Selenium_TestCase::successMessage()
     */
    public function testSuccessMessage()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $_formData = $_testCaseInst->loadData('generic_customer_account');
        $_formData['email'] = $_testCaseInst->generate('email', 20, 'valid');

        $_testCaseInst->click('//*[@id="add_address_button"]');

        $_testCaseInst->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number'=>1)));

        $this->assertNotNull($_testCaseInst->fillForm($_formData));
        $_testCaseInst->click('//*[@id="delete_button21"]');
        $_testCaseInst->getConfirmation();
/*
        $_testCaseInst->clickButton('save_customer');

        $this->assertTrue($_testCaseInst->successMessage());
        $this->assertFalse($_testCaseInst->errorMessage());
*/
    }

    /**
     * Testing Mage_Selenium_TestCase::validationMessage()
     */
    public function testValidationMessage()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $_testCaseInst->clickButton('save_customer', false);

        $this->assertTrue($_testCaseInst->validationMessage());
        $this->assertFalse($_testCaseInst->successMessage());
        $this->assertFalse($_testCaseInst->errorMessage());
    }


    /**
     * Testing Mage_Selenium_TestCase::checkMessage()
     */
    public function testCheckMessage()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $_formData = $_testCaseInst->loadData('generic_customer_account');
        $_formData['email'] = $_testCaseInst->generate('email', 20, 'valid');

        $_testCaseInst->click('//*[@id="add_address_button"]');

        $_testCaseInst->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number'=>1)));

        $this->assertNotNull($_testCaseInst->fillForm($_formData));
        $_testCaseInst->click('//*[@id="delete_button21"]');
        $_testCaseInst->getConfirmation();
        $_testCaseInst->clickButton('save_customer');

        $this->assertTrue($_testCaseInst->checkMessage('success_saved_customer'));
        $this->assertFalse($_testCaseInst->checkMessage('invalid-message'));
    }


    /**
     * Testing Mage_Selenium_TestCase::checkMessageByXpath()
     */
    public function testCheckMessageByXpath()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $_formData = $_testCaseInst->loadData('generic_customer_account');
        $_formData['email'] = $_testCaseInst->generate('email', 20, 'valid');

        $_message = $this->getUimapPage($_testCaseInst->getArea(), 'create_customer')->findMessage('success_saved_customer');

        $_testCaseInst->click('//*[@id="add_address_button"]');

        $_testCaseInst->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number'=>1)));

        $this->assertNotNull($_testCaseInst->fillForm($_formData));
        $_testCaseInst->click('//*[@id="delete_button21"]');
        $_testCaseInst->getConfirmation();

        $_testCaseInst->clickButton('save_customer');

        $this->assertTrue($_testCaseInst->checkMessageByXpath($_message));
        $this->assertFalse($_testCaseInst->checkMessageByXpath('invalid-xpath'));
    }

    /**
     * Testing Mage_Selenium_TestCase::getSuccessMessages()
     */
    public function testGetSuccessMessages()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $_formData = $_testCaseInst->loadData('generic_customer_account');
        $_formData['email'] = $_testCaseInst->generate('email', 20, 'valid');

        $_testCaseInst->click('//*[@id="add_address_button"]');
        $_testCaseInst->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number'=>1)));
        $this->assertNotNull($_testCaseInst->fillForm($_formData));
        $_testCaseInst->click('//*[@id="delete_button21"]');
        $_testCaseInst->getConfirmation();
        $_testCaseInst->clickButton('save_customer');

        $this->assertInternalType('array', $_testCaseInst->getSuccessMessages());
        $this->assertNotEmpty($_testCaseInst->getSuccessMessages());
        $this->assertEmpty($_testCaseInst->getErrorMessages());
    }

    /**
     * Testing Mage_Selenium_TestCase::getErrorMessages()
     */
    public function testGetErrorMessages()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $_formData = $_testCaseInst->loadData('generic_customer_account', array('email' => $this->_config->getDataGenerator()->generateEmailAddress()));
        $_testCaseInst->click('//*[@id="add_address_button"]');

        $_testCaseInst->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number'=>1)));

        $this->assertNotNull($_testCaseInst->fillForm($_formData));
        $_testCaseInst->click('//*[@id="delete_button21"]');
        $_testCaseInst->getConfirmation();
        $_testCaseInst->clickButton('save_customer', false);

        sleep(5);

        $this->assertInternalType('array', $_testCaseInst->getErrorMessages());
        $this->assertNotEmpty($_testCaseInst->getErrorMessages());
        $this->assertEmpty($_testCaseInst->getSuccessMessages());
    }

    /**
     * Testing Mage_Selenium_TestCase::getValidationMessages()
     */
    public function testGetValidationMessages()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $_testCaseInst->clickButton('save_customer', false);

        $this->assertInternalType('array', $_testCaseInst->getValidationMessages());
        $this->assertNotEmpty($_testCaseInst->getValidationMessages());
        $this->assertEmpty($_testCaseInst->getSuccessMessages());
        $this->assertEmpty($_testCaseInst->getErrorMessages());
    }

    /**
     * Testing Mage_Selenium_TestCase::searchAndOpen()
     */
    public function testSearchAndOpen()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $_testCaseInst->navigate('manage_customers');

        $data = array(
            'email' => 'magento.com'
        );

        $data = array(
            'email' => 'testst@magento.com'
        );

        $this->assertNotNull($_testCaseInst->searchAndOpen($data));
    }

}
