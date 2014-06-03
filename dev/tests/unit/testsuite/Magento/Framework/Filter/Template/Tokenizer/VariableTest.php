<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filter\Template\Tokenizer;

class VariableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Filter\Template\Tokenizer\Variable
     */
    protected $_filter;

    protected function setUp()
    {
        $this->_filter = new Variable();
    }

    public function testTokenize()
    {
        $this->assertEquals([], $this->_filter->tokenize());
    }

    public function testGetString()
    {
        $this->assertEquals('', $this->_filter->getString());
    }

    public function testIsNumeric()
    {
        $this->assertFalse($this->_filter->isNumeric());
    }

    public function testIsQuote()
    {
        $this->assertFalse($this->_filter->isQuote());
    }

    public function testGetMethodArgs()
    {
        $this->assertEquals([], $this->_filter->getMethodArgs());
    }

    public function testGetNumber()
    {
        $this->assertEquals(0, $this->_filter->getNumber());
    }

}
