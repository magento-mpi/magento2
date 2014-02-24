<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringProfile\Model\ProductType;

class PluginTest extends \PHPUnit_Framework_TestCase
{

    public function testAroundHasOptions()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getIsRecurring', '__wakeup', '__sleep'],
            [],
            '',
            false
        );
        $product->expects($this->once())->method('getIsRecurring')->will($this->returnValue(true));

        $chain = $this->getMock('Magento\Code\Plugin\InvocationChain', [], [], '', false);

        $model = $objectHelper->getObject('Magento\RecurringProfile\Model\ProductType\Plugin');
        $this->assertEquals(true, $model->aroundHasOptions([$product], $chain));
    }
}
