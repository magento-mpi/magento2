<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Block\Stockqty\Type;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Block\Stockqty\Type\Grouped
     */
    protected $block;

    /**
     * @var \Magento\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->registry = $this->getMock('Magento\Registry', array(), array(), '', false);
        $this->block = $objectManager->getObject(
            'Magento\GroupedProduct\Block\Stockqty\Type\Grouped',
            array('registry' => $this->registry)
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $productTags = array('catalog_product_1');
        $childProduct = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $childProduct->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue($productTags));
        $typeInstance = $this->getMock(
            'Magento\GroupedProduct\Model\Product\Type\Grouped',
            array(),
            array(),
            '',
            false
        );
        $typeInstance->expects($this->once())
            ->method('getAssociatedProducts')
            ->will($this->returnValue(array($childProduct)));
        $product = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $product->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($typeInstance));
        $this->registry->expects($this->any())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($product));
        $this->assertEquals(
            $productTags,
            $this->block->getIdentities()
        );
    }
}
