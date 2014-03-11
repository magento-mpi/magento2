<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Block\Product;

class ViewedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reports\Block\Product\Viewed
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Reports\Block\Product\Viewed');
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

        $collection = new \ReflectionProperty('Magento\Reports\Block\Product\Viewed', '_collection');
        $collection->setAccessible(true);
        $collection->setValue($this->block, array($product));

        $this->assertEquals(
            $productTags,
            $this->block->getIdentities()
        );
    }
}
