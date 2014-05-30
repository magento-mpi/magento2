<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\CollectionProvider;

class CrosssellTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLinkedProducts()
    {
        $product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $expected = [$product];
        $product->expects($this->once())->method('getCrossSellProducts')->will($this->returnValue($expected));
        $model = new Crosssell();
        $this->assertEquals($expected, $model->getLinkedProducts($product));
    }
}
