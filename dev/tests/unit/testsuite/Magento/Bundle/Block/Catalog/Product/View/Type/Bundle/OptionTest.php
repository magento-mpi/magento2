<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Catalog\Product\View\Type\Bundle;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option
     */
    protected $block;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    protected function setUp()
    {
        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getPriceInfo', 'hasPreconfiguredValues', 'getPreconfiguredValues', '__wakeup'])
            ->getMock();

        $registry = $this->getMockBuilder('Magento\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $registry->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($this->product));


        $this->layout = $this->getMock('Magento\Framework\View\LayoutInterface');

        $context = $this->getMockBuilder('Magento\Framework\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->atLeastOnce())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));


        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManagerHelper->getObject(
            '\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option',
            ['registry' => $registry, 'context' => $context]
        );
    }

    public function testSetOption()
    {
        $this->product->expects($this->atLeastOnce())
            ->method('hasPreconfiguredValues')
            ->will($this->returnValue(true));
        $this->product->expects($this->atLeastOnce())
            ->method('getPreconfiguredValues')
            ->will($this->returnValue(new \Magento\Object(array('bundle_option' => array(15 => 315, 16 => 316)))));

        $option = $this->getMock('\Magento\Bundle\Model\Option', array(), array(), '', false);
        $option->expects($this->any())->method('getId')->will($this->returnValue(15));

        $otherOption = $this->getMock('\Magento\Bundle\Model\Option', array(), array(), '', false);
        $otherOption->expects($this->any())->method('getId')->will($this->returnValue(16));

        $selection = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('getSelectionId', '__wakeup'),
            array(),
            '',
            false
        );
        $selection->expects($this->atLeastOnce())->method('getSelectionId')->will($this->returnValue(315));

        $this->assertSame($this->block, $this->block->setOption($option));
        $this->assertTrue($this->block->isSelected($selection));

        $this->block->setOption($otherOption);
        $this->assertFalse(
            $this->block->isSelected($selection),
            'Selected value should change after new option is set'
        );
    }

    public function testRenderPriceString()
    {
        $includeContainer = false;
        $priceHtml = 'price-html';

        $selection = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $bundlePrice = $this->getMockBuilder('Magento\Bundle\Pricing\Price\BundleOptionPrice')
            ->disableOriginalConstructor()
            ->getMock();

        $priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');
        $amount = $this->getMock('Magento\Pricing\Amount\AmountInterface');

        $priceRenderBlock = $this->getMockBuilder('Magento\Pricing\Render')
            ->disableOriginalConstructor()
            ->setMethods(['renderAmount'])
            ->getMock();

        $this->product->expects($this->atLeastOnce())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));

        $priceInfo->expects($this->atLeastOnce())
            ->method('getPrice')
            ->with('bundle_option')
            ->will($this->returnValue($bundlePrice));

        $bundlePrice->expects($this->atLeastOnce())
            ->method('getOptionSelectionAmount')
            ->with($selection)
            ->will($this->returnValue($amount));

        $this->layout->expects($this->atLeastOnce())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->will($this->returnValue($priceRenderBlock));

        $priceRenderBlock->expects($this->atLeastOnce())
            ->method('renderAmount')
            ->with($amount, $bundlePrice, $selection, ['include_container' => $includeContainer])
            ->will($this->returnValue($priceHtml));

        $this->assertEquals($priceHtml, $this->block->renderPriceString($selection, $includeContainer));
    }
}
