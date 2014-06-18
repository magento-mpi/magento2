<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\OptionValue;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $prodRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optValueBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueReaderMock;

    protected function setUp()
    {
        $this->prodRepositoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductRepository', [], [], '', false
        );
        $this->optValueBuilderMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValueBuilder', [], [], '', false
        );
        $this->valueReaderMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ReaderInterface'
        );

        $this->service = new ReadService($this->optValueBuilderMock, $this->prodRepositoryMock, $this->valueReaderMock);
    }

    /**
     * @covers \Magento\Catalog\Service\V1\Product\CustomOptions\OptionValue\ReadService::getList
     */
    public function testGetList()
    {
        $productSku = 'prodSku';
        $optionId = 123;

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $optionMock = $this->getMock('\Magento\Catalog\Model\Product\Option', [], [], '', false);
        $object = $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionType', [], [], '', false);

        $this->prodRepositoryMock->expects($this->once())->method('get')
            ->with($productSku)->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getOptionById')
            ->with($optionId)->will($this->returnValue($optionMock));
        $optionMock->expects($this->once())->method('getId')
            ->will($this->returnValue($optionId));
        $this->valueReaderMock->expects($this->once())->method('read')
            ->with($optionMock)->will($this->returnValue($object));

        $this->assertEquals($object, $this->service->getList($productSku, $optionId));
    }

    /**
     * @covers \Magento\Catalog\Service\V1\Product\CustomOptions\OptionValue\ReadService::getList
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetListWrongOptionId()
    {
        $productSku = 'prodSku';

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);

        $this->prodRepositoryMock->expects($this->once())->method('get')
            ->with($productSku)->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getOptionById')
            ->will($this->returnValue(null));
        $this->valueReaderMock->expects($this->never())->method('read');

        $this->service->getList($productSku, 123);
    }
}
