<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink;

class CollectionProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterMock;

    protected function setUp()
    {
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->converterPoolMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity\ConverterPool',
            [], [], '', false
        );
        $this->converterMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity\ConverterInterface'
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Collection provider is not registered
     */
    public function testGetCollectionWithInvalidType()
    {
        $provider = new CollectionProvider($this->converterPoolMock);
        $provider->getCollection($this->productMock, 'someType');
    }

    /**
     * @covers \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\CollectionProvider::getCollection
     * @covers \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\CollectionProvider::__construct
     */
    public function testGetCollection()
    {
        $productA = $this->getMock('\Magento\Catalog\Model\Product', ['getId', '__wakeup'], [], '', false);
        $productA->expects($this->once())->method('getId')->will($this->returnValue('resultA'));
        $providerA = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\CollectionProviderInterface'
        );
        $providerA->expects($this->once())
            ->method('getLinkedProducts')
            ->with($this->productMock)
            ->will($this->returnValue([$productA]));
        $resultA = ['resultA' => $productA];

        $productB = $this->getMock('\Magento\Catalog\Model\Product', ['getId', '__wakeup'], [], '', false);
        $productB->expects($this->once())->method('getId')->will($this->returnValue('resultB'));
        $providerB = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\CollectionProviderInterface'
        );
        $providerB->expects($this->once())
            ->method('getLinkedProducts')
            ->with($this->productMock)
            ->will($this->returnValue([$productB]));
        $resultB = ['resultB' => $productB];

        $this->converterPoolMock
            ->expects($this->any())
            ->method('getConverter')
            ->will($this->returnValue($this->converterMock));

        $this->converterMock
            ->expects($this->any())
            ->method('convert')
            ->will($this->returnArgument(0));

        $provider = new CollectionProvider(
            $this->converterPoolMock,
            [
                'typeA' => $providerA,
                'typeB' => $providerB
            ]
        );

        $this->assertEquals($resultA, $provider->getCollection($this->productMock, 'typeA'));
        $this->assertEquals($resultB, $provider->getCollection($this->productMock, 'typeB'));
    }
}
