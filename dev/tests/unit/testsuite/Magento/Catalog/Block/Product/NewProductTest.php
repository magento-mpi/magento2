<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product;

class NewProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Catalog\Block\Product\NewProduct');
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $this->assertEquals(
            array(\Magento\Catalog\Model\Product::CACHE_TAG),
            $this->block->getIdentities()
        );
    }
}
