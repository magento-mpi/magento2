<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\Compare;

/**
 * Class ListCompareTest
 * @package Magento\Catalog\Block\Product\Compare
 */
class ListCompareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ListCompare
     */
    protected $block;

    /**
     * @var \Magento\Core\Model\Layout | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    protected function setUp()
    {
        $this->layout = $this->getMock('Magento\Core\Model\Layout', ['getBlock'], [], '', false);

        $context = $this->getMock('Magento\Catalog\Block\Product\Context', ['getLayout'], [], '', false);
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject(
            'Magento\Catalog\Block\Product\Compare\ListCompare',
            ['context' => $context]
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetProductPrice()
    {
        //Data
        $expectedResult = 'html';
        $blockName = 'product.price.render.default';
        $productId = 1;

        //Verification
        $product = $this->getMock('Magento\Catalog\Model\Product', ['getId', '__wakeup'], [], '', false);
        $product->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($productId));

        $blockMock = $this->getMock('Magento\Pricing\Render', ['render'], [], '', false);
        $blockMock->expects($this->once())
            ->method('render')
            ->with(
                'final_price',
                $product,
                [
                    'price_id' => 'product-price-' . $productId . '-compare-list-top',
                    'display_minimal_price' => true
                ]
            )
            ->will($this->returnValue($expectedResult));

        $this->layout->expects($this->once())
            ->method('getBlock')
            ->with($blockName)
            ->will($this->returnValue($blockMock));

        $this->assertEquals($expectedResult, $this->block->getProductPrice($product));
    }
}
