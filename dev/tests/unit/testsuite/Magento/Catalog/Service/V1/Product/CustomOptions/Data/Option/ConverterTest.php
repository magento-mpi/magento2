<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataConverterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;


    protected function setUp()
    {
        $this->metadataConverterMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ConverterInterface'
        );
        $this->optionMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option', [], [], '', false
        );

        $this->service = new Converter($this->metadataConverterMock);
    }

    public function testConvert()
    {
        $this->optionMock->expects($this->any())->method('getOptionId')->will($this->returnValue('123456'));
        $this->optionMock->expects($this->any())->method('getTitle')->will($this->returnValue('Some Title'));
        $this->optionMock->expects($this->any())->method('getType')->will($this->returnValue('Type'));
        $this->optionMock->expects($this->any())->method('getSortOrder')->will($this->returnValue('12'));
        $this->optionMock->expects($this->any())->method('getIsRequire')->will($this->returnValue(true));
        $options = [
            'option_id' => '123456',
            'title' => 'Some Title',
            'type' => 'Type',
            'sort_order' => '12',
            'is_require' => true
        ];
        $this->metadataConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($this->optionMock)
            ->will($this->returnValue($options));
        $this->assertEquals($options, $this->service->convert($this->optionMock));
    }
}
