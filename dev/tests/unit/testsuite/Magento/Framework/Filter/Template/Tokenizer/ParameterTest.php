<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filter\Template\Tokenizer;

class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Filter\Template\Tokenizer\Parameter
     */
    protected $_filter;

    protected function setUp()
    {
        $this->_filter = new Parameter();
    }

    public function testTokenize()
    {
        $this->assertEquals([], $this->_filter->tokenize());
    }

    public function testGetValue()
    {
        $this->assertEquals('', $this->_filter->getValue());
    }
}
