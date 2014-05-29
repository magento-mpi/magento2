<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLinkEntity\ProductEntity;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity;
use \Magento\Framework\Service\Data\Eav\AttributeValue;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLinkEntity\ProductEntity\Converter::convert
     */
    public function testConvert()
    {
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getTypeId', 'getAttributeSetId', 'getPosition', 'getSku', 'getQty', '__wakeup', '__sleep'],
            [], '', false
        );

        $expected = [
            ProductLinkEntity::TYPE             => 1,
            ProductLinkEntity::ATTRIBUTE_SET_ID => 2,
            ProductLinkEntity::SKU              => 3,
            ProductLinkEntity::POSITION         => 4,
            ProductLinkEntity::CUSTOM_ATTRIBUTES_KEY => [
                [AttributeValue::ATTRIBUTE_CODE => 'qty',AttributeValue::VALUE => 5]
            ]
        ];

        $productMock->expects($this->once())->method('getTypeId')->will($this->returnValue(1));
        $productMock->expects($this->once())->method('getAttributeSetId')->will($this->returnValue(2));
        $productMock->expects($this->once())->method('getSku')->will($this->returnValue(3));
        $productMock->expects($this->once())->method('getPosition')->will($this->returnValue(4));
        $productMock->expects($this->once())->method('getQty')->will($this->returnValue(5));

        $model = new Converter();
        $this->assertEquals($expected, $model->convert($productMock));
    }
}
