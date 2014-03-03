<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Block\Customer;

class SidebarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Wishlist\Block\Customer\Sidebar
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Wishlist\Block\Customer\Sidebar');
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

        $item = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Item',
            array('getProduct', '__wakeup'),
            array(),
            '',
            false
        );
        $item->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $collection = new \ReflectionProperty('Magento\Wishlist\Block\Customer\Sidebar', '_collection');
        $collection->setAccessible(true);
        $collection->setValue($this->block, array($item));

        $this->assertEquals(
            $productTags,
            $this->block->getIdentities()
        );
    }
}
