<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Converter;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;

class DefaultConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultConverter
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMetadata;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeValueMock;

    protected function setUp()
    {
        $this->optionMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option',
            [],
            [],
            '',
            false
        );
        $this->attributeValueMock = $this->getMock(
            '\Magento\Framework\Service\Data\AttributeValue',
            [],
            [],
            '',
            false
        );
        $this->model = new DefaultConverter();
    }

    public function testConverter()
    {
        $this->optionMetadata = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata',
            [],
            [],
            '',
            false
        );
        $this->optionMock
            ->expects($this->once())
            ->method('getMetadata')
            ->will($this->returnValue(array($this->optionMetadata)));
        $this->optionMetadata->expects($this->once())->method('getPrice')->will($this->returnValue(100.12));
        $this->optionMetadata->expects($this->once())->method('getPriceType')->will($this->returnValue('USD'));
        $this->optionMetadata->expects($this->once())->method('getSku')->will($this->returnValue('product_sku'));
        $this->optionMetadata
            ->expects($this->once())
            ->method('getCustomAttributes')
            ->will($this->returnValue(array($this->attributeValueMock)));
        $this->attributeValueMock
            ->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute'));
        $this->attributeValueMock
            ->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('value'));
        $expectedOutput = array(
            Metadata::PRICE => 100.12,
            Metadata::PRICE_TYPE => 'USD',
            Metadata::SKU => 'product_sku',
            'attribute' => 'value'
        );
        $this->assertEquals($expectedOutput, $this->model->convert($this->optionMock));
    }

}
