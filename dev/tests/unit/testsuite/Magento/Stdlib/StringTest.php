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
    protected $string;

    protected function setUp()
    {
        $this->string = new String(new StringIconv());
    }

    /**
     * @covers \Magento\Stdlib\String::_construct
     * @covers \Magento\Stdlib\String::split
     */
    public function testStrSplit()
    {
        $this->assertEquals(array(), $this->string->split(''));
        $this->assertEquals(array('1', '2', '3', '4'), $this->string->split('1234', 1));
        $this->assertEquals(array('1', '2', ' ', '3', '4'), $this->string->split('12 34', 1, false, true));
        $this->assertEquals(array(
                '12345', '123', '12345', '6789'
            ), $this->string->split('12345  123    123456789', 5, true, true));
    }
}
