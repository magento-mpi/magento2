<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
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

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Helper\String');
    }

    /**
     * @covers \Magento\Core\Helper\String::truncate
     * @covers \Magento\Core\Helper\String::strlen
     * @covers \Magento\Core\Helper\String::substr
     */
    public function testTruncate()
    {
        $string = '1234567890';
        $this->assertEquals('12...', $this->_helper->truncate($string, 5));

        $words = '123 456 789';
        $remainder = '';
        $this->assertEquals('123...', $this->_helper->truncate($words, 8, '...', $remainder, false));
    }

    /**
     * @covers \Magento\Core\Helper\String::splitInjection
     * @covers \Magento\Core\Helper\String::strrev
     */
    public function testSplitInjection()
    {
        $string = '1234567890';
        $this->assertEquals('1234 5678 90', $this->_helper->splitInjection($string, 4));
    }

    public function testStrSplit()
    {
        $this->assertEquals(array(), $this->_helper->strSplit(''));
        $this->assertEquals(array('1', '2', '3', '4'), $this->_helper->strSplit('1234', 1));
        $this->assertEquals(array('1', '2', ' ', '3', '4'), $this->_helper->strSplit('12 34', 1, false, true));
        $this->assertEquals(array(
            '12345', '123', '12345', '6789'
        ), $this->_helper->strSplit('12345  123    123456789', 5, true, true));
    }
}
