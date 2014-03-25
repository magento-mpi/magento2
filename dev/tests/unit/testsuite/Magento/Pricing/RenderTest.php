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

    public function testRenderWithoutRenderList()
    {
        $objectType = 'simple';
        $priceType = 'final';
        $arguments = ['param' => 1];
        $result = '';

        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $saleable->expects($this->atLeastOnce())
            ->method('getTypeId')
            ->will($this->returnValue($objectType));

        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('price.render.prices')
            ->will($this->returnValue(false));

        $this->assertEquals($result, $this->model->render($priceType, $saleable, $arguments));
    }

    public function testRender()
    {
        $objectType = 'simple';
        $priceType = 'final';
        $arguments = ['param' => 1];
        $result = 'simple.final';

        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $saleable->expects($this->atLeastOnce())
            ->method('getTypeId')
            ->will($this->returnValue($objectType));

        $priceRender = $this->getMockBuilder('Magento\View\Element\AbstractBlock')
            ->setMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock();
        $priceRender->expects($this->once())
            ->method('render')
            ->with($priceType, $saleable, array_replace($this->model->getData(), $arguments))
            ->will($this->returnValue($result));

        $renderList = $this->getMockBuilder('Magento\View\Element\AbstractBlock')
            ->disableOriginalConstructor()
            ->getMock();
        $renderList->expects($this->any())
            ->method('getChildBlock')
            ->will($this->returnValueMap([
            [$objectType . '.' . $priceType, $priceRender],
            ['default.' . $priceType, false],
            ['default.default', false]
        ]));

        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('price.render.prices')
            ->will($this->returnValue($renderList));

        $this->assertEquals($result, $this->model->render($priceType, $saleable, $arguments));
    }

    public function testRenderDefault()
    {
        $objectType = 'simple';
        $priceType = 'special';
        $arguments = ['param' => 15];
        $result = 'default.special';

        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $saleable->expects($this->atLeastOnce())
            ->method('getTypeId')
            ->will($this->returnValue($objectType));

        $priceRender = $this->getMockBuilder('Magento\View\Element\AbstractBlock')
            ->setMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock();
        $priceRender->expects($this->once())
            ->method('render')
            ->with($priceType, $saleable, array_replace($this->model->getData(), $arguments))
            ->will($this->returnValue($result));

        $renderList = $this->getMockBuilder('Magento\View\Element\AbstractBlock')
            ->disableOriginalConstructor()
            ->getMock();
        $renderList->expects($this->any())
            ->method('getChildBlock')
            ->will($this->returnValueMap([
            [$objectType . '.' . $priceType, false],
            ['default.' . $priceType, $priceRender],
            ['default.default', false]
        ]));

        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('price.render.prices')
            ->will($this->returnValue($renderList));

        $this->assertEquals($result, $this->model->render($priceType, $saleable, $arguments));
    }

    public function testRenderDefaultDefault()
    {
        $objectType = 'bundle';
        $priceType = 'final';
        $arguments = ['param' => 15];
        $result = 'default.default';

        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $saleable->expects($this->atLeastOnce())
            ->method('getTypeId')
            ->will($this->returnValue($objectType));

        $priceRender = $this->getMockBuilder('Magento\View\Element\AbstractBlock')
            ->setMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock();
        $priceRender->expects($this->once())
            ->method('render')
            ->with($priceType, $saleable, array_replace($this->model->getData(), $arguments))
            ->will($this->returnValue($result));

        $renderList = $this->getMockBuilder('Magento\View\Element\AbstractBlock')
            ->disableOriginalConstructor()
            ->getMock();
        $renderList->expects($this->any())
            ->method('getChildBlock')
            ->will($this->returnValueMap([
            [$objectType . '.' . $priceType, false],
            ['default.' . $priceType, false],
            ['default.default', $priceRender]
        ]));

        $this->priceLayout->expects($this->once())
            ->method('getBlock')
            ->with('price.render.prices')
            ->will($this->returnValue($renderList));

        $this->assertEquals($result, $this->model->render($priceType, $saleable, $arguments));
    }
}
