<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Stdlib;

/**
 * Magento\Stdlib\StringTest test case
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Stdlib\String
     */
    protected $_string;

    protected function setUp()
    {
        $this->_string = new String();
    }

    /**
     * @covers \Magento\Stdlib\String::split
     */
    public function testStrSplit()
    {
        $this->assertEquals(array(), $this->_string->split(''));
        $this->assertEquals(array('1', '2', '3', '4'), $this->_string->split('1234', 1));
        $this->assertEquals(array('1', '2', ' ', '3', '4'), $this->_string->split('12 34', 1, false, true));
        $this->assertEquals(array(
                '12345', '123', '12345', '6789'
            ), $this->_string->split('12345  123    123456789', 5, true, true));
    }

    /**
     * @covers \Magento\Stdlib\String::splitInjection
     */
    public function testSplitInjection()
    {
        $string = '1234567890';
        $this->assertEquals('1234 5678 90', $this->_string->splitInjection($string, 4));
    }

    /**
     * @covers \Magento\Stdlib\String::cleanString
     */
    public function testCleanString()
    {
        $string = '12345';
        $this->assertEquals($string, $this->_string->cleanString($string));
    }

    /**
     * @covers \Magento\Stdlib\String::strpos
     */
    public function testStrpos()
    {
        $this->assertEquals(1, $this->_string->strpos('123', 2));
    }

    /**
     * @param string $testString
     * @param string $expected
     *
     * @dataProvider upperCaseWordsDataProvider
     */
    public function testUpperCaseWords($testString, $expected)
    {
        $actual = $this->_string->upperCaseWords($testString);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function upperCaseWordsDataProvider()
    {
        return array(
            array(
                'test test2',
                'Test_Test2',
            ),
            array(
                'test_test2',
                'Test_Test2',
            ),
            array(
                'test_test2 test3',
                'Test_Test2_Test3',
            ),
        );
    }

    /**
     * @param string $testString
     * @param string $sourceSeparator
     * @param string $destinationSeparator
     * @param string $expected
     *
     * @dataProvider upperCaseWordsWithSeparatorsDataProvider
     */
    public function testUpperCaseWordsWithSeparators($testString, $sourceSeparator, $destinationSeparator, $expected)
    {
        $actual = $this->_string->upperCaseWords($testString, $sourceSeparator, $destinationSeparator);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function upperCaseWordsWithSeparatorsDataProvider()
    {
        return array(
            array(
                'test test2_test3\test4|test5',
                '|',
                '\\',
                'Test\Test2_test3\test4\Test5',
            ),
        );
    }
}
