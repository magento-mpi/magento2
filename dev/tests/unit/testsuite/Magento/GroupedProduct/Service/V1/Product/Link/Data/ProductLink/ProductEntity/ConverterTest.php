<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLink\ProductEntity;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink;
use \Magento\Framework\Api\AttributeValue;
use Magento\GroupedProduct\Model\Product\Link\ProductEntity\Converter;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLink\ProductEntity\Converter::convert
     */
    public function testConvert()
    {
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getTypeId', 'getPosition', 'getSku', 'getQty', '__wakeup', '__sleep'],
            [], '', false
        );

        $expected = [
            ProductLink::TYPE             => 1,
            ProductLink::SKU              => 3,
            ProductLink::POSITION         => 4,
            ProductLink::CUSTOM_ATTRIBUTES_KEY => [
                [AttributeValue::ATTRIBUTE_CODE => 'qty',AttributeValue::VALUE => 5]
            ]
        ];

        $productMock->expects($this->once())->method('getTypeId')->will($this->returnValue(1));
        $productMock->expects($this->once())->method('getSku')->will($this->returnValue(3));
        $productMock->expects($this->once())->method('getPosition')->will($this->returnValue(4));
        $productMock->expects($this->once())->method('getQty')->will($this->returnValue(5));

        $model = new Converter();
        $this->assertEquals($expected, $model->convert($productMock));
    }
}
