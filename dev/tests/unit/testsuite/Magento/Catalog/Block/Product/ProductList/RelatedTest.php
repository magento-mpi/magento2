<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\ProductList;

class RelatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\ProductList\Related
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Catalog\Block\Product\ProductList\Related');
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $productTag = 'compare_item_1';
        $product = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $product->expects($this->once())->method('getIdentities')->will($this->returnValue($productTag));

        $itemsCollection = new \ReflectionProperty(
            'Magento\Catalog\Block\Product\ProductList\Related',
            '_itemCollection'
        );
        $itemsCollection->setAccessible(true);
        $itemsCollection->setValue($this->block, array($product));

        $this->assertEquals(array($productTag), $this->block->getIdentities());
    }
}
