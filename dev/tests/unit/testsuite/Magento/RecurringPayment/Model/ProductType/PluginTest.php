<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\ProductType;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Plugin
     */
    protected $object;

    /**
     * @var \Magento\Catalog\Model\Product\Type\AbstractType|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    protected function setUp()
    {
        $this->subject = $this->getMock(
            'Magento\Catalog\Model\Product\Type\AbstractType',
            [],
            [],
            '',
            false
        );
        $this->product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getIsRecurring', '__wakeup', '__sleep'],
            [],
            '',
            false
        );
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->object = $objectManager->getObject('Magento\RecurringPayment\Model\ProductType\Plugin');
    }

    public function testAroundHasOptionsForProductWithRecurringPayment()
    {
        $this->product->expects($this->once())->method('getIsRecurring')->will($this->returnValue(true));
        $closure = function () {
            throw new \Exception();
        };
        $this->assertEquals(true, $this->object->aroundHasOptions($this->subject, $closure, $this->product));
    }

    public function testAroundHasOptionsForProductWithoutRecurringPayment()
    {
        $this->product->expects($this->once())->method('getIsRecurring')->will($this->returnValue(false));
        $closure = function ($product) {
            return $product;
        };
        $this->assertSame($this->product, $this->object->aroundHasOptions($this->subject, $closure, $this->product));
    }
}
