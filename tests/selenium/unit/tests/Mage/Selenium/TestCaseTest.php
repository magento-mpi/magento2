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
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Unit test for Mage_Selenium_Uid helper
 */
class Mage_Selenium_TestCaseTest extends Mage_PHPUnit_TestCase
{
    public function  __construct() {
        parent::__construct();

        //var_dump($this->_config);
        //die;
    }

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
        $this->assertEquals($this->_config->getUimapValue($_testCaseInst->getArea(), $_testCaseInst->getCurrentPage().'/title'), $_testCaseInst->getTitle());

        $this->assertNotNull($_testCaseInst->clickButton('add_new_customer'));
        $this->assertEquals($this->_config->getUimapValue($_testCaseInst->getArea(), $_testCaseInst->getCurrentPage().'/title'), $_testCaseInst->getTitle());

        $_formData = $_testCaseInst->loadData('all_fields_customer_account');
        $this->assertNotEmpty($_formData);
        $this->assertInternalType('array', $_formData);

        //@TODO Replace with $_testCaseInst->clickButton
        $_testCaseInst->click('//*[@id="add_address_button"]');

        $_testCaseInst->setParameter('address_number', 1);
        $this->assertNotNull($_testCaseInst->fillForm($_formData));
    }

    /**
     * Testing Mage_Selenium_TestCase::сontrolIsPresent()
     */
    public function testControlIsPresent()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();
        $this->assertNotNull($_testCaseInst);

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->navigate('create_customer'));

        $this->assertTrue($_testCaseInst->controlIsPresent('button', 'save_customer'));
        $this->assertTrue($_testCaseInst->controlIsPresent('field', 'prefix'));
        //$this->assertFalse($_testCaseInst->controlIsPresent('field', 'invalid-field'));
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

}
