<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Reorder;

class SidebarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Block\Reorder\Sidebar|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $block;

    protected function setUp()
    {
        $this->block = $this->getMock('Magento\Sales\Block\Reorder\Sidebar', array('getItems'), array(), '', false);
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $productTags = array('catalog_product_1');

        $product = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $product->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue($productTags));

        $item = $this->getMock('Magento\Sales\Model\Resource\Order\Item', array('getProduct', '__wakeup'), array(), '', false);
        $item->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $this->block->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue(array($item)));

        $this->assertEquals(
            $productTags,
            $this->block->getIdentities()
        );
    }
}
