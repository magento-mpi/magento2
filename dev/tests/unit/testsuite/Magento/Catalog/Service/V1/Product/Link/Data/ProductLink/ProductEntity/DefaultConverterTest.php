<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink;

class DefaultConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var DefaultConverter
     */
    protected $converter;

    protected function setUp()
    {
        $this->product = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getTypeId', 'getSku', 'getPosition', '__sleep', '__wakeup'],
            [],
            '',
            false
        );

        $this->converter = new DefaultConverter();
    }

    public function testConvert()
    {
        $this->product->expects($this->once())->method('getTypeId')->will($this->returnValue('simple'));
        $this->product->expects($this->once())->method('getSku')->will($this->returnValue('simple-sku'));
        $this->product->expects($this->once())->method('getPosition')->will($this->returnValue(1));

        $expected = [
            ProductLink::TYPE => 'simple',
            ProductLink::SKU => 'simple-sku',
            ProductLink::POSITION => 1
        ];

        $this->assertEquals($expected, $this->converter->convert($this->product));
    }
}
