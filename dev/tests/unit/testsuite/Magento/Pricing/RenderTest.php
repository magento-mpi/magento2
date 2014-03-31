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

    public function setUp()
    {
        $this->priceLayout = $this->getMockBuilder('Magento\Pricing\Render\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Pricing\Render', array(
            'priceLayout' => $this->priceLayout
        ));
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

        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');

        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue(false));

        $this->assertEquals($result, $this->model->render($priceType, $saleable, $arguments));
    }

    public function testRender()
    {
        $priceType = 'final';
        $arguments = ['param' => 1];
        $result = 'simple.final';

        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $renderPool = $this->getMock('Magento\Pricing\Render\RendererPool', [],[], '', false, true, true, false);
        $pricingRender = $this->getMock('Magento\Pricing\Render', [],[], '', false, true, true, false);
        $renderPool->expects($this->once())
            ->method('createPriceRender')
            ->will($this->returnValue($pricingRender));
        $pricingRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('simple.final'));
        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue($renderPool));
        $this->assertEquals($result, $this->model->render($priceType, $saleable, $arguments));
    }

    public function testRenderDefault()
    {
        $priceType = 'special';
        $arguments = ['param' => 15];
        $result = 'default.special';
        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $renderPool = $this->getMock('Magento\Pricing\Render\RendererPool', [],[], '', false, true, true, false);
        $pricingRender = $this->getMock('Magento\Pricing\Render', [],[], '', false, true, true, false);
        $renderPool->expects($this->once())
            ->method('createPriceRender')
            ->will($this->returnValue($pricingRender));
        $pricingRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('default.special'));
        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue($renderPool));

        $this->assertEquals($result, $this->model->render($priceType, $saleable, $arguments));
    }

    public function testRenderDefaultDefault()
    {
        $priceType = 'final';
        $arguments = ['param' => 15];
        $result = 'default.default';

        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $renderPool = $this->getMock('Magento\Pricing\Render\RendererPool', [],[], '', false, true, true, false);
        $pricingRender = $this->getMock('Magento\Pricing\Render', [],[], '', false, true, true, false);
        $renderPool->expects($this->once())
            ->method('createPriceRender')
            ->will($this->returnValue($pricingRender));
        $pricingRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('default.default'));
        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue($renderPool));

        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('render.product.prices')
            ->will($this->returnValue($renderPool));

        $this->assertEquals($result, $this->model->render($priceType, $saleable, $arguments));
    }
}
