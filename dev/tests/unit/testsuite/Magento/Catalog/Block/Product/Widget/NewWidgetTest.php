<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\Widget;

class NewWidgetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\Widget\NewWidget|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $contextMock = $this->getMock('Magento\Catalog\Block\Product\Context', [], [], '', false, false);
        $this->layout = $this->getMock('Magento\Core\Model\Layout', [], [], '', false);

        $contextMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));

        $this->block = $objectManager->getObject(
            'Magento\Catalog\Block\Product\Widget\NewWidget',
            [
                'context' => $contextMock
            ]
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetProductPrice()
    {
        $id = 6;
        $expectedHtml = '
        <div class="price-box price-final_price">
            <span class="regular-price" id="product-price-' . $id . '">
                <span class="price">$0.00</span>
            </span>
        </div>';
        $type = 'widget-new-list';
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false, false);
        $productMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));
        $arguments = [
            'price_id' => 'old-price-' . $id . '-' . $type,
            'display_minimal_price' => true,
            'include_container' => true
        ];

        $priceBoxMock = $this->getMock('Magento\Pricing\Render', ['render'], [], '', false, false);

        $this->layout->expects($this->once())
            ->method('getBlock')
            ->with($this->equalTo('product.price.render.default'))
            ->will($this->returnValue($priceBoxMock));

        $priceBoxMock->expects($this->once())
            ->method('render')
            ->with('final_price', $productMock, $arguments)
            ->will($this->returnValue($expectedHtml));

        $result = $this->block->getProductPrice($productMock, $type);
        $this->assertEquals($expectedHtml, $result);
    }
}
