<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Type;

class AbstractTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testHasOptions()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getHasOptions', '__wakeup', '__sleep'],
            [],
            '',
            false
        );
        $product->expects($this->once())->method('getHasOptions')->will($this->returnValue(true));

        /** @var \Magento\Catalog\Model\Product\Type\Simple $model */
        $model = $objectHelper->getObject('Magento\Catalog\Model\Product\Type\Simple');
        $this->assertEquals(true, $model->hasOptions($product));
    }
}
