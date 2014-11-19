<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute;

class LockValidatorCompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Attribute\LockValidatorComposite
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('\Magento\Framework\ObjectManagerInterface');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCompositionsWithInvalidValidatorInstance()
    {
        $validators = array('Magento\Catalog\Model');
        $this->model = new \Magento\Catalog\Model\Attribute\LockValidatorComposite(
            $this->objectManagerMock,
            $validators
        );
    }

    public function testValidateWithValidValidatorInstance()
    {
        $validators = array('Magento\Catalog\Model\Attribute\LockValidatorComposite');
        $lockValidatorMock = $this->getMock('Magento\Catalog\Model\Attribute\LockValidatorInterface');
        $this->objectManagerMock->expects(
            $this->any()
        )->method(
            'get'
        )->with(
            'Magento\Catalog\Model\Attribute\LockValidatorComposite'
        )->will(
            $this->returnValue($lockValidatorMock)
        );

        $this->model = new \Magento\Catalog\Model\Attribute\LockValidatorComposite(
            $this->objectManagerMock,
            $validators
        );
        $abstractModelHelper = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false, false);
        $lockValidatorMock->expects($this->once())->method('validate')->with($abstractModelHelper);
        $this->model->validate($abstractModelHelper);
    }
}
