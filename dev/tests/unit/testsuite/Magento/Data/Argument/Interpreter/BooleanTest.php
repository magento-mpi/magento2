<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

class BooleanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Boolean
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_booleanUtils;

    protected function setUp()
    {
        $this->_booleanUtils = $this->getMock('\Magento\Stdlib\BooleanUtils');
        $this->_model = new Boolean($this->_booleanUtils);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Boolean value is missing
     */
    public function testEvaluateException()
    {
        $this->_model->evaluate(array());
    }

    public function testEvaluate()
    {
        $input = new \stdClass();
        $expected = new \stdClass();
        $this->_booleanUtils
            ->expects($this->once())
            ->method('toBoolean')
            ->with($this->identicalTo($input))
            ->will($this->returnValue($expected))
        ;
        $actual = $this->_model->evaluate(array('value' => $input));
        $this->assertSame($expected, $actual);
    }
}
