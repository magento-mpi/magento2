<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Status\History;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $history = $this->getMock('Magento\Sales\Model\Resource\Order\Status\History', ['hasData'], [], '', false);
        $history->expects($this->any())
            ->method('hasData')
            ->will($this->returnValue(true));
        $validator = new Validator();
        $this->assertEquals([], $validator->validate($history));
    }

    public function testValidateNegative()
    {
        $history = $this->getMock('Magento\Sales\Model\Resource\Order\Status\History', ['hasData'], [], '', false);
        $history->expects($this->any())
            ->method('hasData')
            ->with('parent_id')
            ->will($this->returnValue(false));
        $validator = new Validator();
        $this->assertEquals(['Order Id is a required field'], $validator->validate($history));
    }
} 