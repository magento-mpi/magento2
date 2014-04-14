<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing;

/**
 * Test class for \Magento\Pricing\Render
 */
class RenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Render
     */
    protected $model;

    /**
     * @var Render\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceLayout;

    /**
     * @var \Magento\Catalog\Pricing\Price\BasePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $price;

    /**
     * @var \Magento\Pricing\Amount\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amount;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItem;

    /**
     * @var \Magento\Pricing\Render\RendererPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $renderPool;

    public function setUp()
    {
        $this->priceLayout = $this->getMockBuilder('Magento\Pricing\Render\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $this->price = $this->getMockBuilder('\Magento\Catalog\Pricing\Price\BasePrice')
            ->disableOriginalConstructor()
            ->getMock();

        $this->amount = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->getMock();

        $this->saleableItem = $this->getMockBuilder('\Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->renderPool = $this->getMockBuilder('Magento\Pricing\Render\RendererPool')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Pricing\Render',
            [
                'priceLayout' => $this->priceLayout
            ]
        );
    }

    public function testSetLayout()
    {
        $priceRenderHandle = 'price_render_handle';

        $this->priceLayout->expects($this->once())
            ->method('addHandle')
            ->with($priceRenderHandle);

        $this->priceLayout->expects($this->once())
            ->method('loadLayout');

        $layout = $this->getMock('Magento\View\LayoutInterface');
        $this->model->setPriceRenderHandle($priceRenderHandle);
        $this->model->setLayout($layout);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRenderWithoutRenderList()
    {
        $priceType = 'final';
        $arguments = ['param' => 1];
        $result = '';

        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue(false));

        $this->assertEquals($result, $this->model->render($priceType, $this->saleableItem, $arguments));
    }

    public function testRender()
    {
        $priceType = 'final';
        $arguments = ['param' => 1];
        $result = 'simple.final';

        $pricingRender = $this->getMock('Magento\Pricing\Render', [], [], '', false, true, true, false);
        $this->renderPool->expects($this->once())
            ->method('createPriceRender')
            ->will($this->returnValue($pricingRender));
        $pricingRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('simple.final'));
        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue($this->renderPool));
        $this->assertEquals($result, $this->model->render($priceType, $this->saleableItem, $arguments));
    }

    public function testRenderDefault()
    {
        $priceType = 'special';
        $arguments = ['param' => 15];
        $result = 'default.special';
        $pricingRender = $this->getMock('Magento\Pricing\Render', [], [], '', false, true, true, false);
        $this->renderPool->expects($this->once())
            ->method('createPriceRender')
            ->will($this->returnValue($pricingRender));
        $pricingRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('default.special'));
        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue($this->renderPool));

        $this->assertEquals($result, $this->model->render($priceType, $this->saleableItem, $arguments));
    }

    public function testRenderDefaultDefault()
    {
        $priceType = 'final';
        $arguments = ['param' => 15];
        $result = 'default.default';

        $pricingRender = $this->getMock('Magento\Pricing\Render', [], [], '', false, true, true, false);
        $this->renderPool->expects($this->once())
            ->method('createPriceRender')
            ->will($this->returnValue($pricingRender));
        $pricingRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('default.default'));
        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue($this->renderPool));

        $this->assertEquals($result, $this->model->render($priceType, $this->saleableItem, $arguments));
    }

    public function testAmountRender()
    {
        $arguments = ['param' => 15];
        $expectedResult = 'default.default';

        $pricingRender = $this->getMock('Magento\Pricing\Render\Amount', [], [], '', false, true, true, false);
        $this->renderPool->expects($this->once())
            ->method('createAmountRender')
            ->with(
                $this->equalTo($this->amount),
                $this->equalTo($this->saleableItem),
                $this->equalTo($this->price),
                $this->equalTo($arguments)
            )
            ->will($this->returnValue($pricingRender));
        $pricingRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('default.default'));
        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue($this->renderPool));

        $result = $this->model->renderAmount($this->amount, $this->price, $this->saleableItem, $arguments);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Wrong Price Rendering layout configuration. Factory block is missed
     */
    public function testAmountRenderNoRenderPool()
    {
        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue(false));

        $this->model->renderAmount($this->amount, $this->price, $this->saleableItem);
    }
}
