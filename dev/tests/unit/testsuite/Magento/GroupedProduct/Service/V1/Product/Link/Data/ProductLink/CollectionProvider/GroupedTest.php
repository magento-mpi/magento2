<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLink\CollectionProvider;

use Magento\GroupedProduct\Model\Product\Link\CollectionProvider\Grouped;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLinkedProducts()
    {
        $typeInstance = $this->getMock('\Magento\GroupedProduct\Model\Product\Type\Grouped', [], [], '', false);
        $product = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $expected = [$product];

        $product->expects($this->once())->method('getTypeInstance')->will($this->returnValue($typeInstance));
        $typeInstance->expects($this->once())
            ->method('getAssociatedProducts')
            ->with($product)
            ->will($this->returnValue($expected));

        $model = new Grouped();
        $this->assertEquals($expected, $model->getLinkedProducts($product));
    }
}
