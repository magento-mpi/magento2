<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product;

class ListProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $block;

    /**
     * @var \Magento\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->registryMock = $this->getMock('Magento\Registry', array(), array(), '', false);
        $this->layerMock = $this->getMock('Magento\Catalog\Model\Layer', array(), array(), '', false);
        $this->block = $objectManager->getObject(
            'Magento\Catalog\Block\Product\ListProduct',
            array('registry' => $this->registryMock, 'catalogLayer' => $this->layerMock)
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $productTag = 'catalog_product_1';
        $categoryTag = 'catalog_category_1';

        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getIdentities', '__wakeup'),
            array(),
            '',
            false
        );
        $product->expects($this->once())->method('getIdentities')->will($this->returnValue(array($productTag)));

        $itemsCollection = new \ReflectionProperty('Magento\Catalog\Block\Product\ListProduct', '_productCollection');
        $itemsCollection->setAccessible(true);
        $itemsCollection->setValue($this->block, array($product));

        $currentCategory = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $currentCategory->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue(array($categoryTag)));

        $this->layerMock->expects($this->once())
            ->method('getCurrentCategory')
            ->will($this->returnValue($currentCategory));

        $this->assertEquals(
            array($categoryTag, $productTag),
            $this->block->getIdentities()
        );
    }
}
