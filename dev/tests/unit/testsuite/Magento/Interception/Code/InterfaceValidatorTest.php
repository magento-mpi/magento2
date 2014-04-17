<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Interception\Code;

class InterfaceValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $argumentsReaderMock;

    /**
     * @var \Magento\Interception\Code\InterfaceValidator
     */
    protected $model;

    protected function setUp()
    {
        $this->argumentsReaderMock = $this->getMock(
            '\Magento\Framework\Code\Reader\ArgumentsReader', array(), array(), '', false
        );

        $this->argumentsReaderMock->expects($this->any())->method('isCompatibleType')
            ->will($this->returnCallback(function ($arg1, $arg2) {
                return ltrim($arg1, '\\') == ltrim($arg2, '\\');
            }));

        $this->model = new InterfaceValidator($this->argumentsReaderMock);
    }

    /**
     * @covers \Magento\Interception\Code\InterfaceValidator::validate
     * @covers \Magento\Interception\Code\InterfaceValidator::getMethodParameters
     * @covers \Magento\Interception\Code\InterfaceValidator::getMethodType
     * @covers \Magento\Interception\Code\InterfaceValidator::getOriginMethodName
     * @covers \Magento\Interception\Code\InterfaceValidator::getParametersType
     * @covers \Magento\Interception\Code\InterfaceValidator::__construct
     */
    public function testValidate()
    {
        $this->model->validate(
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin\ValidPlugin',
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments'
        );
    }

    /**
     * @expectedException \Magento\Interception\Code\ValidatorException
     * @expectedExceptionMessage Incorrect interface in
     * @covers \Magento\Interception\Code\InterfaceValidator::validate
     */
    public function testValidateIncorrectInterface()
    {
        $this->model->validate(
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin\IncompatibleInterface',
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\Item'
        );
    }

    /**
     * @expectedException \Magento\Interception\Code\ValidatorException
     * @expectedExceptionMessage Invalid [\Magento\Interception\Custom\Module\Model\Item] $subject type
     * @covers \Magento\Interception\Code\InterfaceValidator::validate
     */
    public function testValidateIncorrectSubjectType()
    {
        $this->model->validate(
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin\IncorrectSubject',
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\Item'
        );
    }

    /**
     * @expectedException \Magento\Interception\Code\ValidatorException
     * @expectedExceptionMessage Invalid method signature. Invalid method parameters count
     * @covers \Magento\Interception\Code\InterfaceValidator::validate
     * @covers \Magento\Interception\Code\InterfaceValidator::validateMethodsParameters
     */
    public function testValidateIncompatibleMethodArgumentsCount()
    {
        $this->model->validate(
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin\IncompatibleArgumentsCount',
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\Item'
        );
    }

    /**
     * @expectedException \Magento\Interception\Code\ValidatorException
     * @expectedExceptionMessage Incompatible parameter type
     * @covers \Magento\Interception\Code\InterfaceValidator::validate
     * @covers \Magento\Interception\Code\InterfaceValidator::validateMethodsParameters
     */
    public function testValidateIncompatibleMethodArgumentsType()
    {
        $this->model->validate(
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin\IncompatibleArgumentsType',
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments'
        );
    }

    /**
     * @expectedException \Magento\Interception\Code\ValidatorException
     * @expectedExceptionMessage Invalid method signature. Detected extra parameters
     * @covers \Magento\Interception\Code\InterfaceValidator::validate
     */
    public function testValidateExtraParameters()
    {
        $this->model->validate(
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin\ExtraParameters',
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\Item'
        );
    }

    /**
     * @expectedException \Magento\Interception\Code\ValidatorException
     * @expectedExceptionMessage Invalid [] $name type in
     * @covers \Magento\Interception\Code\InterfaceValidator::validate
     */
    public function testValidateInvalidProceed()
    {
        $this->model->validate(
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin\InvalidProceed',
            '\Magento\Interception\Custom\Module\Model\InterfaceValidator\Item'
        );
    }
}
