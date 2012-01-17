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
class Mage_Selenium_TestCaseTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_TestCase::__construct
     */
    public function test__construct()
    {
        $instance = new Mage_Selenium_TestCase();
        $this->assertInstanceOf('Mage_Selenium_TestCase', $instance);
    }

    /**
     * @covers Mage_Selenium_TestCase::assertEmptyVerificationErrors
     *
     * @TODO need to clear messages
     */
    public function testAssertEmptyVerificationErrorsTrue()
    {
        $instance = new Mage_Selenium_TestCase();
        $instance->assertEmptyVerificationErrors();

        $instance->addMessage('error', 'testAssertEmptyVerificationErrors error');
        $instance->assertEmptyVerificationErrors();

        $instance->addMessage('success', 'testAssertEmptyVerificationErrors success');
        $instance->assertEmptyVerificationErrors();

        $instance->addMessage('validation', 'testAssertEmptyVerificationErrors validation');
        $instance->assertEmptyVerificationErrors();
    }

    /**
     * @covers Mage_Selenium_TestCase::assertEmptyVerificationErrors
     */
    public function testAssertEmptyVerificationErrorsFalse()
    {
        $instance = new Mage_Selenium_TestCase();
        $instance->addVerificationMessage('testAssertEmptyVerificationErrorsFalse');
        try {
            $instance->assertEmptyVerificationErrors();
        } catch (PHPUnit_Framework_ExpectationFailedException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @covers Mage_Selenium_TestCase::addVerificationMessage
     * @covers Mage_Selenium_TestCase::getParsedMessages
     */
    public function testAddGetVerificationMessage()
    {
        $instance = new Mage_Selenium_TestCase();
        $this->assertEmpty($instance->getParsedMessages('verification'));
        $message1 = 'Verification message';
        $instance->addVerificationMessage($message1);
        $this->assertEquals($instance->getParsedMessages('verification'), array($message1));
        $message2 = 'Second verification message';
        $instance->addVerificationMessage($message2);
        $this->assertEquals($instance->getParsedMessages('verification'), array($message1, $message2));
    }

    /**
     * @covers Mage_Selenium_TestCase::loadData
     */
    public function testLoadData()
    {
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadData('unit_test_load_data');
        $this->assertNotEmpty($formData);
        $this->assertInternalType('array', $formData);
        $this->assertEquals($formData, $instance->loadData('unit_test_load_data', null));
        $this->assertEquals($formData, $instance->loadData('unit_test_load_data', null, null));
    }

    /**
     * @covers Mage_Selenium_TestCase::loadData
     */
    public function testLoadDataOverriden()
    {
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadData('unit_test_load_data');

        $formDataOverriddenName =
                $instance->loadData('unit_test_load_data', array('key' => 'new Value'));
        $this->assertEquals($formDataOverriddenName['key'], 'new Value');

        $formDataWithNewKey = $instance->loadData('unit_test_load_data', array('new key' => 'new Value'));
        $test = array_diff($formDataWithNewKey, $formData);
        $this->assertEquals(array_diff($formDataWithNewKey, $formData), array('new key' => 'new Value'));
    }

    /**
     * @covers Mage_Selenium_TestCase::loadData
     */
    public function testLoadDataRandomized()
    {
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadData('unit_test_load_data');
        $this->assertEquals($formData, $instance->loadData('unit_test_load_data', null, 'not existing key'));
        $this->assertNotEquals($formData, $instance->loadData('unit_test_load_data', null, 'key'));
    }
}