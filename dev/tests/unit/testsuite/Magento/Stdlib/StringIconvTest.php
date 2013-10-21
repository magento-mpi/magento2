<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Stdlib;
/**
 * Magento\Stdlib\StringIconvTest test case
 */
class StringIconvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Stdlib\StringIconv
     */
    protected $_stringIconv;

    protected function setUp()
    {
        $this->_stringIconv = new StringIconv();
    }

    public function testCleanString()
    {
        $string = '12345';
        $this->assertEquals($string, $this->_stringIconv->cleanString($string));
    }

    public function testStrpos()
    {
        $this->assertEquals(1, $this->_stringIconv->strpos('123', 2));
    }
}
