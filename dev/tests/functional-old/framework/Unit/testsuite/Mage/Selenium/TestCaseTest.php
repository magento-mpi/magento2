<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Selenium_TestCaseTest extends Unit_PHPUnit_TestCase
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
     * @covers Mage_Selenium_TestCase::loadDataSet
     */
    public function testLoadDataSet()
    {
        //Expected Data
        $expectedArray = array('key' => 'Value', 'sub_array' => array('key' => 'Value'));
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadDataSet('UnitTestsData', 'unit_test_load_data_set_simple');
        $this->assertNotEmpty($formData);
        $this->assertInternalType('array', $formData);
        $this->assertEquals($formData, $expectedArray);
    }

    /**
     * @covers Mage_Selenium_TestCase::loadDataSet
     *
     * @expectedException RuntimeException
     */
    public function testLoadDataSetNotExisting()
    {
        $instance = new Mage_Selenium_TestCase();
        $instance->loadDataSet('UnitTestsData', 'notExistingDataSet');
    }

    /**
     * @covers Mage_Selenium_TestCase::loadDataSet
     */
    public function testLoadDataSetOverrideByValueKey()
    {
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadDataSet('UnitTestsData', 'unit_test_load_data_set_recursive');

        $formDataOverriddenName = $instance->loadDataSet(
            'UnitTestsData',
            'unit_test_load_data_set_recursive',
            array('key' => 'new Value', 'novalue_key' => 'new Value')
        );
        $this->assertEquals(6, $this->_getValuesCount($formDataOverriddenName, 'new Value'));
        $array = array('key' => 'new Value', 'novalue_key' => 'new Value');
        $expected = $array;
        $expected['sub_array'] = $array;
        $expected['sub_array']['sub_array'] = $array;
        $this->assertEquals($expected, array_diff_recursive($formDataOverriddenName, $formData));
    }

    /**
     * @covers Mage_Selenium_TestCase::loadDataSet
     */
    public function testLoadDataSetOverrideByValueParam()
    {
        $instance = new Mage_Selenium_TestCase();
        $formData = $instance->loadDataSet('UnitTestsData', 'unit_test_load_data_set_recursive');

        $formDataOverriddenName = $instance->loadDataSet(
            'UnitTestsData',
            'unit_test_load_data_set_recursive',
            null,
            array('noValue' => 'new Value', 'no Value' => 'new Value')
        );
        $this->assertEquals(6, $this->_getValuesCount($formDataOverriddenName, 'new Value'));
        $array = array('novalue_key' => 'new Value', 'some_key' => 'new Value');
        $expected = $array;
        $expected['sub_array'] = $array;
        $expected['sub_array']['sub_array'] = $array;
        $this->assertEquals($expected, array_diff_recursive($formDataOverriddenName, $formData));
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
     * @covers       Mage_Selenium_TestCase::clearDataArray
     * @dataProvider testClearDataArrayDataProvider
     *
     * @param $inputArray
     * @param $expectedCount
     */
    public function testClearDataArray($inputArray, $expectedCount)
    {
        //Steps
        $instance = new Mage_Selenium_TestCase();
        $inputArray = $instance->clearDataArray($inputArray);
        $this->assertEquals($expectedCount, $this->_getValuesCount($instance->clearDataArray($inputArray)));
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
                array(
                    0 => '%someValue0%',
                    1 => '%someValue1%',
                    2 => array(
                        0 => '%someValue0%',
                        1 => '%someValue1%',
                        2 => array('0' => '%noValue%'),
                        3 => '%some Value0%'
                    )
                ),
                2
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
                        2 => array('%noValue%' => 'noValue', 'someValue' => 'noValue')
                    )
                ),
                8
            )
        );
    }

    /**
     * @covers       Mage_Selenium_TestCase::setDataParams
     * @dataProvider testSetDataParamsDataProvider
     *
     * @param $inputString
     * @param $expected
     */
    public function testSetDataParams($inputString, $expected)
    {
        $instance = new Mage_Selenium_TestCase();
        $instance->setDataParams($inputString);
        $this->assertRegExp($expected, $inputString);
    }

    /**
     * DataProvider for testSetDataParams
     *
     * @return array
     */
    public function testSetDataParamsDataProvider()
    {
        return array(
            array('test_%longValue255%', '/^test_\w{255}$/'),
            array('test_%specialValue10%', '/^test_[[:punct:]]{10}$/'),
            array('test_data_%currentDate%', '/^test_data_' . preg_quote(date("n/j/Y"), '/') . '$/'),
            array('test_data_%randomize%', '/^test_data_\w{5}$/'),
            array('test_data_randomize%', '/test_data_randomize%/'),
            array('test_%randomize%_data', '/^test_\w{5}_data$/'),
            array('test_%randomize_data', '/^test_%randomize_data$/'),
            array('%longValue255%', '/^[\w\s]{255}$/'),
            array('%specialValue11%', '/^[[:punct:]]{11}$/'),
            array('%specialValue1%', '/^[[:punct:]]{1}$/')
        );
    }

    /**
     * @covers       Mage_Selenium_TestCase::overrideDataByCondition
     * @dataProvider testOverrideDataByConditionDataProvider
     *
     * @param $overrideArray
     * @param $overrideKey
     * @param $overrideValue
     * @param $condition
     * @param $expCount
     */
    public function testOverrideDataByCondition($overrideArray, $overrideKey, $overrideValue, $condition, $expCount)
    {
        $instance = new Mage_Selenium_TestCase();
        $instance->overrideDataByCondition($overrideKey, $overrideValue, $overrideArray, $condition);
        $this->assertEquals($expCount, $this->_getValuesCount($overrideArray, '%someValue0%'));
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
                        2 => array('0' => '%someValue0%')
                    )
                ),
                0,
                1,
                'byFieldKey',
                2
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
                        2 => array('%noValue%' => '%someValue0%')
                    )
                ),
                'someValue0',
                1,
                'byValueParam',
                0
            )
        );
    }

    /**
     * @covers Mage_Selenium_TestCase::generate
     */
    public function testGenerate()
    {
        $instance = new Mage_Selenium_TestCase();
        // Default values
        $this->assertInternalType('string', $instance->generate());
        $this->assertEquals(100, strlen($instance->generate()));

        // String generations
        $this->assertEquals(20, strlen($instance->generate('string', 20, ':alnum:')));
        $this->assertEquals(20, strlen($instance->generate('string', 20, ':alnum:', '')));
        $this->assertEmpty($instance->generate('string', 0, ':alnum:', ''));
        $this->assertEmpty($instance->generate('string', -1, ':alnum:', ''));
        $this->assertEquals(1000000, strlen($instance->generate('string', 1000000, ':alnum:', '')));

        $this->assertEquals(26, strlen($instance->generate('string', 20, ':alnum:', 'prefix')));
        $this->assertStringStartsWith('prefix', $instance->generate('string', 20, '', 'prefix'));

        // Text generations
        $this->assertEquals(26, strlen($instance->generate('text', 20, '', 'prefix')));
        $this->assertStringStartsWith('prefix', $instance->generate('text', 20, '', 'prefix'));

        $this->assertEquals(100, strlen($instance->generate('text')));
        $this->assertEquals(20, strlen($instance->generate('text', 20)));
        $this->assertEmpty($instance->generate('text', 0));
        $this->assertEmpty($instance->generate('text', -1));
        $this->assertEquals(1000000, strlen($instance->generate('text', 1000000)));

        $this->assertEquals(20, strlen($instance->generate('text', 20, '')));
        $this->assertEquals(26, strlen($instance->generate('text', 20, '', 'prefix')));
        $this->assertStringStartsWith('prefix', $instance->generate('text', 20, '', 'prefix'));

        $this->assertStringMatchesFormat('%s', $instance->generate('text', 20, array('class' => ':alnum:')));
        $this->assertRegExp('/[0-9 ]+/', $instance->generate('text', 20, ':digit:'));

        // Email generations
        $this->assertEquals(100, strlen($instance->generate('email')));
        $this->assertEquals(20, strlen($instance->generate('email', 20, 'valid')));
        $this->assertEquals(20, strlen($instance->generate('email', 20, 'some_value')));
        $this->assertEmpty($instance->generate('email', 0));
        $this->assertEmpty($instance->generate('email', -1));
        $this->assertEquals(255, strlen($instance->generate('email', 255, 'valid')));
    }

    /**
     * @covers       Mage_Selenium_TestCase::generate
     * @dataProvider testGenerateModifierDataProvider
     *
     * @param $modifier
     */
    public function testGenerateModifierString($modifier)
    {
        $instance = new Mage_Selenium_TestCase();
        $this->assertRegExp('/[[' . $modifier . ']]{100}/', $instance->generate('string', 100, $modifier));
    }

    /**
     * @covers       Mage_Selenium_TestCase::generate
     * @dataProvider testGenerateModifierDataProvider
     *
     * @param $modifier
     */
    public function testGenerateModifierText($modifier)
    {
        $instance = new Mage_Selenium_TestCase();
        $randomText = $instance->generate('text', 100, array('class' => $modifier, 'para' => 5));
        $this->assertEquals(5, count(explode("\n", $randomText)));
        $this->assertRegExp('/[\s[' . $modifier . ']]{100}/', $randomText);

        $randomText = $instance->generate('text', 100, $modifier);
        $this->assertEquals(1, count(explode("\n", $randomText)));
        $this->assertRegExp('/[\s[' . $modifier . ']]{100}/', $randomText);
    }

    public function testGenerateModifierDataProvider()
    {
        return array(
            array(':alnum:'),
            array(':alpha:'),
            array(':digit:'),
            array(':lower:'),
            array(':punct:'),
            array(':upper:')
        );
    }

    /**
     * @covers Mage_Selenium_TestCase::generate
     */
    public function testGenerateModifierEmail()
    {
        $instance = new Mage_Selenium_TestCase();
        $this->assertTrue((bool)filter_var($instance->generate('email', 20, 'valid'), FILTER_VALIDATE_EMAIL));
        $this->assertFalse((bool)filter_var($instance->generate('email', 20, 'invalid'), FILTER_VALIDATE_EMAIL));
    }

    /**
     * @covers Mage_Selenium_TestCase::getHttpResponse
     */
    public function testGetHttpResponse()
    {
        $instance = new Mage_Selenium_TestCase();
        $response = $instance->getHttpResponse('http://www.w3.org/');
        $this->assertInternalType('array', $response);
        $this->assertArrayHasKey('http_code', $response);
        $this->assertInternalType('int', $response['http_code']);
        $this->assertEquals(200, $response['http_code']);

        $response = $instance->getHttpResponse('http://foo.nowhere/');
        $this->assertInternalType('array', $response);
        $this->assertArrayHasKey('http_code', $response);
        $this->assertEquals(0, $response['http_code']);

        $response = $instance->getHttpResponse('wikipedia.org');
        $this->assertArrayHasKey('http_code', $response);
        $this->assertEquals(301, $response['http_code']);
    }

    /**
     * @covers Mage_Selenium_TestCase::httpResponseIsOK
     */
    public function testHttpResponseIsOK()
    {
        $instance = new Mage_Selenium_TestCase();
        $this->assertTrue($instance->httpResponseIsOK('http://www.w3.org/'));
        $this->assertTrue($instance->httpResponseIsOK('www.w3.org'));
        $this->assertTrue($instance->httpResponseIsOK('wikipedia.org')); //Redirection
        $this->assertFalse($instance->httpResponseIsOK('http://foo.nowhere/'));
    }

    /**
     * @covers Mage_Selenium_TestCase::detectOS
     */
    public function testCheckOsType()
    {
        $instance = $this->getMock('Mage_Selenium_TestCase', array('execute'));
        $instance->expects($this->at(0))->method('execute')->will($this->returnValue('Windows'));
        $instance->expects($this->at(1))->method('execute')->will($this->returnValue('Linux'));
        $instance->expects($this->at(2))->method('execute')->will($this->returnValue('Macintosh'));
        $instance->expects($this->at(3))->method('execute')->will($this->returnValue('PalmOS'));

        /* @var Mage_Selenium_TestCase $instance */
        $this->assertEquals('Windows', $instance->detectOS(), 'System name is incorrect');
        $this->assertEquals('Linux', $instance->detectOS(), 'System name is incorrect');
        $this->assertEquals('MacOS', $instance->detectOS(), 'System name is incorrect');
        $this->assertEquals('Unknown OS', $instance->detectOS(), 'System name is incorrect');
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
        $this->assertEmpty($instance->getParsedMessages());

        $errorMessage = 'testGetParsedMessages error message';
        $successMessage = 'testGetParsedMessages success message';
        $validationMessage = 'testGetParsedMessages validation message';
        $verificationMessage = 'testGetParsedMessages verification message';

        $instance->addMessage('error', $errorMessage);
        $this->assertEquals($instance->getParsedMessages(), array('error' => array($errorMessage)));
        $this->assertEquals($instance->getParsedMessages('error'), array($errorMessage));

        $instance->addMessage('success', $successMessage);
        $this->assertEquals(
            $instance->getParsedMessages(),
            array('error' => array($errorMessage), 'success' => array($successMessage))
        );
        $this->assertEquals($instance->getParsedMessages('success'), array($successMessage));

        $instance->addMessage('validation', $validationMessage);
        $this->assertEquals(
            $instance->getParsedMessages(),
            array(
                'error' => array($errorMessage),
                'success' => array($successMessage),
                'validation' => array($validationMessage)
            )
        );
        $this->assertEquals($instance->getParsedMessages('validation'), array($validationMessage));

        $instance->addMessage('verification', $verificationMessage);
        $this->assertEquals(
            $instance->getParsedMessages(),
            array(
                'error' => array($errorMessage),
                'success' => array($successMessage),
                'validation' => array($validationMessage),
                'verification' => array($verificationMessage)
            )
        );
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
     * @covers       Mage_Selenium_TestCase::setUrlPostfix
     * @covers       Mage_Selenium_TestCase::navigate
     * @dataProvider setUrlPostfixDataProvider
     */
    public function testSetUrlPostfix($urlPostfix)
    {
        $this->_testConfig->getHelper('config')->setArea('frontend');
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $pageUrl = $uimapHelper->getPageUrl('frontend', 'home');

        $instance = $this->getMock('Mage_Selenium_TestCase', array('url', 'execute'));
        $instance->expects($this->at(0))->method('url')->will($this->returnValue('www.site.com'));
        $instance->expects($this->at(1))->method('url')->with($this->equalTo($pageUrl . $urlPostfix));
        $instance->expects($this->at(3))->method('url')->will($this->returnValue($pageUrl . $urlPostfix));
        $instance->expects($this->any())->method('execute')->will($this->returnValue(0));

        /* @var Mage_Selenium_TestCase $instance */
        $instance->setUrlPostfix($urlPostfix);
        $this->assertAttributeEquals($urlPostfix, '_urlPostfix', $instance);
        $instance->navigate('home', false);
        $this->assertSame('home', $instance->getCurrentPage());
    }

    public function setUrlPostfixDataProvider()
    {
        return array(
            array('?someParam=someValue'),
            array(null),
            array('')
        );
    }

    /**
     * Return count of $search_value occurrences in $input
     *
     * @param array $input
     * @param $search_value
     *
     * @return int
     */
    private function _getValuesCount(array $input, $search_value = null)
    {
        $count = (is_null($search_value)) ? count(array_keys($input)) : count(array_keys($input, $search_value));
        foreach ($input as $value) {
            if (is_array($value)) {
                $count += $this->_getValuesCount($value, $search_value);
            }
        }
        return $count;
    }
}