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
     * @covers Mage_Selenium_TestCase::clearMessages
     * @covers Mage_Selenium_TestCase::getParsedMessages
     */
    public function testClearMessages()
    {
        $instance = new Mage_Selenium_TestCase();

        $instance->clearMessages();
        $this->assertEmpty($instance->getParsedMessages());

        $instance->addMessage('error', 'testClearMessages error');
        $this->assertNotEmpty($instance->getParsedMessages());
        $instance->clearMessages();
        $this->assertEmpty($instance->getParsedMessages());

        $instance->addMessage('success', 'testClearMessages success');
        $this->assertNotEmpty($instance->getParsedMessages());
        $instance->clearMessages();
        $this->assertEmpty($instance->getParsedMessages());

        $instance->addMessage('validation', 'testClearMessages validation');
        $this->assertNotEmpty($instance->getParsedMessages());
        $instance->clearMessages();
        $this->assertEmpty($instance->getParsedMessages());
    }

    /**
     * @covers Mage_Selenium_TestCase::getParsedMessages
     * @covers Mage_Selenium_TestCase::addMessage
     * @covers Mage_Selenium_TestCase::clearMessages
     */
    public function testGetParsedMessages()
    {
        $instance = new Mage_Selenium_TestCase();

        $instance->clearMessages();
        $this->assertNull($instance->getParsedMessages());

        $errorMessage = 'testGetParsedMessages error message';
        $successMessage = 'testGetParsedMessages success message';
        $validationMessage = 'testGetParsedMessages validation message';
        $verificationMessage = 'testGetParsedMessages verification message';

        $instance->addMessage('error', $errorMessage);
        $foo = $instance->getParsedMessages();
        $this->assertEquals($instance->getParsedMessages(), array('error' => array($errorMessage)));
        $this->assertEquals($instance->getParsedMessages('error'), array($errorMessage));

        $instance->addMessage('success', $successMessage);
        $this->assertEquals($instance->getParsedMessages(),
                array('error' => array($errorMessage),
                      'success' => array($successMessage)));
        $this->assertEquals($instance->getParsedMessages('success'), array($successMessage));

        $instance->addMessage('validation', $validationMessage);
        $this->assertEquals($instance->getParsedMessages(),
                array('error' => array($errorMessage),
                      'success' => array($successMessage),
                      'validation' => array($validationMessage)));
        $this->assertEquals($instance->getParsedMessages('validation'), array($validationMessage));

        $instance->addMessage('verification', $verificationMessage);
        $this->assertEquals($instance->getParsedMessages(),
                array('error' => array($errorMessage),
                      'success' => array($successMessage),
                      'validation' => array($validationMessage),
                      'verification' => array($verificationMessage)));
        $this->assertEquals($instance->getParsedMessages('verification'), array($verificationMessage));
    }

    /**
     * @covers Mage_Selenium_TestCase::getParsedMessages
     */
    public function testGetParsedMessagesNull()
    {
        $instance = new Mage_Selenium_TestCase();
        $this->assertNull($instance->getParsedMessages('foo'));
    }

    /**
     * @covers Mage_Selenium_TestCase::assertEmptyVerificationErrors
     *
     */
    public function testAssertEmptyVerificationErrorsTrue()
    {
        $instance = new Mage_Selenium_TestCase();

        $instance->clearMessages();
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
        } catch (PHPUnit_Framework_AssertionFailedError $expected) {
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

        $instance->clearMessages();
        $instance->assertEmptyVerificationErrors();
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

    /**
     * Return count of $search_value occurrences in $input
     *
     * @param array $input
     * @param $search_value
     * @return int
     */
    function getValuesCount(array $input, $search_value = null)
    {
        $count = (is_null($search_value)) ? count(array_keys($input)) : count(array_keys($input, $search_value));
        foreach($input as $value) {
            if (is_array($value)) {
                $count += $this->getValuesCount($value, $search_value);
            }
        }
        return $count;
    }

    /**
     * @covers Mage_Selenium_TestCase::clearDataArray
     */
    public function testClearDataArrayString()
    {
        $instance = new Mage_Selenium_TestCase();
        $this->assertFalse($instance->clearDataArray('Some string'), "Works with string as input param");
    }

    /**
     * @covers Mage_Selenium_TestCase::clearDataArray
     *
     * @dataProvider testClearDataArrayDataProvider
     *
     * @param $inputArray
     * @param $expectedCount
     */
    public function testClearDataArray($inputArray, $expectedCount)
    {
        //Setps
        $instance = new Mage_Selenium_TestCase();
        $inputArray = $instance->clearDataArray($inputArray);
        $this->assertEquals($expectedCount, $this->getValuesCount($instance->clearDataArray($inputArray)));
    }

    /**
     * DataProvider for testClearDataArray
     *
     * @return array
     */
    public function testClearDataArrayDataProvider()
    {
        return array(
            array(
                array( //$inputArray
                    0 => '%someValue0%',
                    1 => '%someValue1%',
                    2 => array(
                        0 => '%someValue0%',
                        1 => '%someValue1%',
                        2 =>array(
                            '0' => '%noValue%',
                        ),
                        3 => '%some Value0%',
                    )
                ), 2 //$expectedCount
            ),
            array(
                array(
                    0 => 'someValue0',
                    1 => '%someValue1%',
                    2 => 'someValue1%',
                    3 => '%someValue1',
                    4 => array(
                        0 => '%someValue0%',
                        1 => 'someValue1%',
                        2 =>array(
                            '%noValue%' => 'noValue',
                            'someValue' => 'noValue',
                        )
                    )
                ), 8
            )
        );
    }

    /**
     * @covers Mage_Selenium_TestCase::setDataParams
     *
     * @dataProvider testSetDataParamsDataProvider
     *
     * @param $inputString
     * @param $expected
     */
    public function testSetDataParams($inputString, $expected)
    {
        $instance = new Mage_Selenium_TestCase();
        $instance->setDataParams($inputString, null);
        $this->assertTrue((bool)preg_match($expected, $inputString));
    }

    /**
     * DataProvider for testSetDataParams
     *
     * @return array
     */
    public function testSetDataParamsDataProvider()
    {
        return array(
            array('test_data_%randomize%', '/^test_data_\w{5}$/'),
            array('test_data_randomize%', '/test_data_randomize%/'),
            array('test_%randomize%_data', '/^test_\w{5}_data$/'),
            array('test_%randomize_data', '/^test_%randomize_data$/'),
            array('%longValue255%', '/^[\w\s]{255}$/'),
            array('test%longValue255%', '/^test%longValue255%$/'),
            array('%specialValue11%', '/^[[:punct:]]{11}$/'),
            array('%specialValue1%', '/^[[:punct:]]{1}$/'),
            array('test_%specialValue1%', '/^test_%specialValue1%$/'),
            array('test_data_%currentDate%', '/^test_data_' . preg_quote(date("n/j/y"), '/') . '$/')
        );
    }

    /**
     * @covers Mage_Selenium_TestCase::overrideDataByCondition
     *
     * @dataProvider testOverrideDataByConditionDataProvider
     *
     * @param $overrideArray
     * @param $overrideKey
     * @param $overrideValue
     * @param $condition
     * @param $expCount
     */
    public function testOverrideDataByCondition($overrideArray, $overrideKey, $overrideValue,  $condition, $expCount)
    {
        $instance = new Mage_Selenium_TestCase();
        $instance->overrideDataByCondition($overrideKey, $overrideValue, $overrideArray, $condition);
        $this->assertEquals($expCount, $this->getValuesCount($overrideArray, '%someValue0%'));
    }

    /**
     * DataProvider for testOverrideDataByCondition
     *
     * @return array
     */
    public function testOverrideDataByConditionDataProvider()
    {
        return array(
            array(
                array( //$inputArray
                    0 => '%someValue0%',
                    1 => '%someValue0%',
                    2 => array(
                        0 => '%someValue0%',
                        1 => '%someValue0%',
                        2 =>array(
                            '0' => '%someValue0%',
                        )
                    )
                ), 0, 1,'byValueKey', 2
            ),
            array(
                array(
                    0 => '%someValue0%',
                    1 => '%someValue0%',
                    2 => '%someValue0%',
                    3 => '%someValue0%',
                    4 => array(
                        0 => '%someValue0%',
                        1 => '%someValue0%',
                        2 =>array(
                            '%noValue%' => '%someValue0%',
                        )
                    )
                ), 'someValue0', 1,'byValueParam', 0
            ),
        );
    }

    /**
     * @covers Mage_Selenium_TestCase::loadDataSet
     *
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testLoadDataSetNotExisting()
    {
        $instance = new Mage_Selenium_TestCase();
        $instance->loadDataSet('notExistingDataSet');
    }

    /**
     * @covers Mage_Selenium_TestCase::loadDataSet
     */
    public function testLoadDataSet()
    {
        //Expected Data
        $expectedArray = array('key' => 'Value', 'sub_array' => array('key' => 'Value'));
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadDataSet('unit_test_load_data_set_simple');
        $this->assertNotEmpty($formData);
        $this->assertInternalType('array', $formData);
        $this->assertEquals($formData, $expectedArray);
    }

    /**
     * @covers Mage_Selenium_TestCase::loadData
     */
    public function testLoadDataOverrideByValueKey()
    {
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadDataSet('unit_test_load_data_set_recursive');

        $formDataOverriddenName = $instance->loadDataSet('unit_test_load_data_set_recursive', array(
                'key' => 'new Value' ,
                'novalue_key' => 'new Value'));
        $this->assertEquals(6, $this->getValuesCount($formDataOverriddenName, 'new Value'));
        $tst = array_diff($formDataOverriddenName, $formData);
        $this->assertEquals(array('key' => 'new Value', 'novalue_key' => 'new Value'),
            array_diff($formDataOverriddenName, $formData));
    }


    /**
     * @covers Mage_Selenium_TestCase::loadData
     */
    public function testLoadDataOverrideByValueParam()
    {
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadDataSet('unit_test_load_data_set_recursive');

        $formDataOverriddenName = $instance->loadDataSet('unit_test_load_data_set_recursive', null, array(
            'noValue' => 'new Value' ,
            'no Value' => 'new Value'));
        $this->assertEquals(6, $this->getValuesCount($formDataOverriddenName, 'new Value'));
        $this->assertEquals(array('novalue_key' => 'new Value', 'some_key' => 'new Value'),
            array_diff($formDataOverriddenName, $formData));
    }
}