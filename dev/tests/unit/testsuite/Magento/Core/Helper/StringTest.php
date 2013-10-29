<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\String
     */
    protected $_helper;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $this->_objectManager->getObject('Magento\Core\Helper\String');
    }

    /**
     * @param string $testString
     * @param string $expected
     *
     * @dataProvider upperCaseWordsDataProvider
     */
    public function testUpperCaseWords($testString, $expected)
    {
        $string = new \Magento\Stdlib\String;
        $actual = $string->upperCaseWords($testString);
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
        $string = new \Magento\Stdlib\String;
        $actual = $string->upperCaseWords($testString, $sourceSeparator, $destinationSeparator);
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
