<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Reader;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;

class DefaultReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;

    /**
     * @var DefaultReader
     */
    protected $service;

    protected function setUp()
    {
        $this->valueBuilderMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\MetadataBuilder', [], [], '', false
        );
        $this->optionMock = $this->getMock(
            '\Magento\Catalog\Model\Product\Option', ['getPrice', 'getPriceType', 'getSku', '__wakeup'], [], '', false
        );
        $this->service = new DefaultReader($this->valueBuilderMock);
    }

    public function testRead()
    {
        $this->optionMock->expects($this->once())->method('getPrice')->will($this->returnValue('10'));
        $this->optionMock->expects($this->once())->method('getPriceType')->will($this->returnValue('USD'));
        $this->optionMock->expects($this->once())->method('getSku')->will($this->returnValue('product_sku'));
        $fields = [
            Metadata::PRICE => '10',
            Metadata::PRICE_TYPE => 'USD' ,
            Metadata::SKU => 'product_sku'
        ];
        $this->valueBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($fields)
            ->will($this->returnValue($this->valueBuilderMock));
        $this->valueBuilderMock->expects($this->once())->method('create')->will($this->returnValue('Expected value'));
        $this->assertEquals(array('Expected value'), $this->service->read($this->optionMock));
    }
}
