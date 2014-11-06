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

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Select
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMetadataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeValueMock;

    protected function setUp()
    {
        $this->optionMock =
            $this->getMock('\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option', [], [], '', false);
        $this->optionMetadataMock =
            $this->getMock('\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata', [], [], '', false);
        $this->attributeValueMock =
            $this->getMock('\Magento\Framework\Api\AttributeValue', [], [], '', false);
        $this->model = new Select();
    }

    public function testConverter()
    {
        $this->optionMock
            ->expects($this->any())
            ->method('getMetadata')
            ->will($this->returnValue(array('select' => $this->optionMetadataMock)));
        $this->optionMetadataMock->expects($this->any())->method('getPrice')->will($this->returnValue(99.99));
        $this->optionMetadataMock->expects($this->any())->method('getPriceType')->will($this->returnValue('USD'));
        $this->optionMetadataMock->expects($this->any())->method('getSku')->will($this->returnValue('product_sku'));
        $this->optionMetadataMock
            ->expects($this->any())
            ->method('getOptionTypeId')
            ->will($this->returnValue('value option_type_id'));
        $this->optionMetadataMock
            ->expects($this->any())
            ->method('getCustomAttributes')
            ->will($this->returnValue(array($this->attributeValueMock)));
        $this->attributeValueMock
            ->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code'));
        $this->attributeValueMock
            ->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('attribute_value'));
        $expectedValues= array(
            'values' => array(
                '0' => array(
                    Metadata::PRICE => 99.99,
                    Metadata::PRICE_TYPE => 'USD',
                    Metadata::SKU => 'product_sku',
                    'attribute_code' => 'attribute_value'
                )
        ));
        $this->assertEquals($expectedValues, $this->model->convert($this->optionMock));
    }
}
