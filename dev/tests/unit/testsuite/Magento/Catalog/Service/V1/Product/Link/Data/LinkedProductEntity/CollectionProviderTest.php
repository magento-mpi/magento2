<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\LinkedProductEntity;

class CollectionProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    protected function setUp()
    {
        $this->product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Collection provider for type someType is not registered
     */
    public function testGetCollectionWithInvalidType()
    {
        $provider = new CollectionProvider();
        $provider->getCollection($this->product, 'someType');
    }


    public function testGetCollection()
    {
        $product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);

        $resultA = ['resultA'];
        $providerA = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\Link\Data\LinkedProductEntity\CollectionProviderInterface'
        );
        $providerA->expects($this->once())
            ->method('getLinkedProducts')
            ->with($product)
            ->will($this->returnValue($resultA));

        $resultB = ['resultB'];
        $providerB = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\Link\Data\LinkedProductEntity\CollectionProviderInterface'
        );
        $providerB->expects($this->once())
            ->method('getLinkedProducts')
            ->with($product)
            ->will($this->returnValue($resultB));

        $provider = new CollectionProvider(
            [['code' => 'typeA', 'provider' => $providerA], ['code' => 'typeB', 'provider' => $providerB]]
        );

        $this->assertEquals($resultA, $provider->getCollection($this->product, 'typeA'));
        $this->assertEquals($resultB, $provider->getCollection($this->product, 'typeB'));
    }
} 
