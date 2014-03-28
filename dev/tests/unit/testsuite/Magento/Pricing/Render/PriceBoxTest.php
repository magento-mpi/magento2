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
namespace Magento\Pricing\Render;

/**
 * Test class for \Magento\Pricing\Render\PriceBox
 */
class PriceBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PriceBox
     */
    protected $model;

    /**
     * @var AmountRenderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountRenderFactory;

    /**
     * @var \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    public function setUp()
    {
        $this->amountRenderFactory = $this->getMockBuilder('Magento\Pricing\Render\AmountRenderFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->layout = $this->getMock('Magento\View\LayoutInterface');
        $context = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = $objectManager->getConstructArguments('Magento\Pricing\Render\PriceBox', array(
            'context' => $context,
            'amountRenderFactory' => $this->amountRenderFactory
        ));
        $this->model = $this->getMockBuilder('Magento\Pricing\Render\PriceBox')
            ->setMethods(['toHtml'])
            ->setConstructorArgs($arguments)
            ->getMock();
    }

    /**
     * @covers \Magento\Pricing\Render\PriceBox::render
     * @covers \Magento\Pricing\Render\PriceBox::getSaleableItem
     * @covers \Magento\Pricing\Render\PriceBox::getPrice
     */
    public function testRender()
    {
        $priceType = 'final';
        $arguments = ['param' => 'some_value'];
        $resultHtml = 'some html';

        $price = $this->getMock('Magento\Pricing\Price\PriceInterface');

        $priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');
        $priceInfo->expects($this->atLeastOnce())
            ->method('getPrice')
            ->with($priceType)
            ->will($this->returnValue($price));

        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $saleable->expects($this->atLeastOnce())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));

        $this->model->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($resultHtml));

        $origArguments = $this->model->getData();
        $this->assertEquals($resultHtml, $this->model->render($priceType, $saleable, $arguments));
        $this->assertEquals($origArguments, $this->model->getData());
        $this->assertEquals($saleable, $this->model->getSaleableItem());
        $this->assertEquals($price, $this->model->getPrice());
    }

    public function testGetPriceType()
    {
        $priceType = 'final';
        $priceCode = 'final';
        $quantity = 2;

        $priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');
        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $saleable->expects($this->atLeastOnce())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));
        $this->model->render($priceType, $saleable);

        $priceType = $this->getMock('Magento\Pricing\Price\PriceInterface');
        $priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($priceCode, $quantity)
            ->will($this->returnValue($priceType));
        $this->assertEquals($priceType, $this->model->getPriceType($priceCode, $quantity));
    }

    /**
     * @dataProvider renderAmountDataProvider
     */
    public function testRenderAmount($amountRenderClass, $amountRenderTemplate, $amountRenderData)
    {
        $priceType = 'final';
        $argumentsRenderAmount = ['some_param' => 'some_value'];
        $resultRender = 'result_render';

        if ($amountRenderClass) {
            $this->model->setData('amount_render', $amountRenderClass);
        }
        $this->model->setData('amount_render_template', $amountRenderTemplate);
        if ($amountRenderData) {
            $this->model->setData('amount_render_data', $amountRenderData);
        }

        $priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');
        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $saleable->expects($this->atLeastOnce())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));
        $this->model->render($priceType, $saleable);


        $price = $this->getMock('Magento\Pricing\Price\PriceInterface');

        $amountRender = $this->getMock('Magento\Pricing\Render\AmountRenderInterface');
        $amountRender->expects($this->once())
            ->method('render')
            ->with($price, $saleable, $argumentsRenderAmount)
            ->will($this->returnValue($resultRender));

        $this->amountRenderFactory->expects($this->once())
            ->method('create')
            ->with(
                $this->layout,
                ($amountRenderClass ? :  AmountRenderFactory::AMOUNT_RENDERER_DEFAULT),
                $amountRenderTemplate,
                ($amountRenderData ? : [])
            )
            ->will($this->returnValue($amountRender));

        $this->assertEquals($resultRender, $this->model->renderAmount($price, $argumentsRenderAmount));
    }

    /**
     * @return array
     */
    public function renderAmountDataProvider()
    {
        return array(
            array(
                'amountRender' => 'Some\Render\Class',
                'amountRenderTemplate' => 'path_to_template',
                'amountRenderData' => ['some_key' => 'some_value']
            ),
            array(
                'amountRender' => false,
                'amountRenderTemplate' => 'path_to_template',
                'amountRenderData' => []
            ),
            array(
                'amountRender' => 'Some\Render\Class',
                'amountRenderTemplate' => 'path_to_template',
                'amountRenderData' => false
            ),

        );
    }
}
