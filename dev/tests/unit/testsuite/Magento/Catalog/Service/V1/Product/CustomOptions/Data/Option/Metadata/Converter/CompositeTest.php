<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Converter;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ConverterInterface;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Composite
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterSelectMock;

    protected function setUp()
    {
        $this->converterMock = $this->getMock(
            'Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ConverterInterface'
        );
        $this->converterSelectMock = $this->getMock(
            'Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Converter\Select',
            [],
            [],
            '',
            false
        );
        $this->model = new Composite(['default' => $this->converterMock, 'select' => $this->converterSelectMock]);
    }

    public function testConverterWithSelectType()
    {
        $this->optionMock =
            $this->getMock(
                '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option',
                [],
                [],
                '',
                false
            );
        $this->optionMock->expects($this->once())->method('getType')->will($this->returnValue('select'));
        $this->converterSelectMock
            ->expects($this
            ->once())
            ->method('convert')
            ->with($this->optionMock)
            ->will($this->returnValue('Expected Result'));
        $this->assertEquals('Expected Result', $this->model->convert($this->optionMock));
    }

    public function testConverterWithDefaultType()
    {
        $this->optionMock =
            $this->getMock(
                '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option',
                [],
                [],
                '',
                false
            );
        $this->optionMock->expects($this->once())->method('getType')->will($this->returnValue('other'));
        $this->converterMock
            ->expects($this
            ->once())
            ->method('convert')
            ->with($this->optionMock)
            ->will($this->returnValue('Expected Result'));
        $this->assertEquals('Expected Result', $this->model->convert($this->optionMock));
    }
}
