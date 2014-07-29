<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Plugin;

use Magento\Bundle\Model\Product\Price;
use Magento\TestFramework\Helper\ObjectManager;

class PriceBackendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Backend\Price|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var \Magento\Framework\Object|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $object;

    /**
     * @var \Closure
     */
    protected $proceed;

    /**
     * @var \Magento\Bundle\Model\Plugin\PriceBackend
     */
    protected $model;

    protected function setUp()
    {
        $this->subject = $this->getMock('Magento\Catalog\Model\Product\Attribute\Backend\Price', [], [], '', false);
        $this->product = $this->getMock('Magento\Catalog\Model\Product', ['getPriceType', '__wakeup'], [], '', false);
        $this->object = $this->getMock('\Magento\Framework\Object');
        $this->proceed = function ($product) {
            return $product;
        };
        $this->model = (new ObjectManager($this))->getObject('Magento\Bundle\Model\Plugin\PriceBackend');
    }

    public function testAroundValidateWithDynamicTypePrice()
    {
        $this->product->expects($this->once())->method('getPriceType')
            ->will($this->returnValue(Price::PRICE_TYPE_DYNAMIC));

        $this->assertTrue($this->model->aroundValidate($this->subject, $this->proceed, $this->product));
    }

    public function testAroundValidateWithFixedTypePrice()
    {
        $this->product->expects($this->once())->method('getPriceType')
            ->will($this->returnValue(Price::PRICE_TYPE_FIXED));

        $this->assertEquals(
            $this->product,
            $this->model->aroundValidate($this->subject, $this->proceed, $this->product)
        );
    }

    public function testAroundValidateWithNotProductObject()
    {
        $this->assertEquals(
            $this->object,
            $this->model->aroundValidate($this->subject, $this->proceed, $this->object)
        );
    }
}
