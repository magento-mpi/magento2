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

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\PriceInterface;

/**
 * Test class for \Magento\Pricing\Render\Amount
 */
class AmountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Amount
     */
    protected $model;

    /**
     * @var \Magento\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrency;

    /**
     * @var RendererPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rendererPool;

    /**
     * @var \Magento\Core\Model\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Pricing\Amount\AmountInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $amount;

    /**
     * @var PriceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceMock;

    public function setUp()
    {
        $this->priceCurrency = $this->getMock('Magento\Pricing\PriceCurrencyInterface');
        $data = [
            'default' => [
                'adjustments' => [
                    'base_price_test' => [
                        'tax' => [
                            'adjustment_render_class' => 'Magento\View\Element\Template',
                            'adjustment_render_template' => 'template.phtml'
                        ]
                    ]
                ]
            ]
        ];

        $this->rendererPool = $this->getMock(
            'Magento\Pricing\Render\RendererPool',
            [],
            ['data' => $data],
            '',
            false,
            false
        );

        $this->layout = $this->getMock('Magento\Core\Model\Layout', [], [], '', false);
        $this->amount = $this->getMockForAbstractClass('Magento\Pricing\Amount\AmountInterface');
        $this->saleableItemMock = $this->getMockForAbstractClass('Magento\Pricing\Object\SaleableInterface');
        $this->priceMock = $this->getMockForAbstractClass('Magento\Pricing\Price\PriceInterface');

        $eventManager = $this->getMock('Magento\Event\ManagerStub', [], [], '', false);
        $config = $this->getMock('Magento\Core\Model\Store\Config', [], [], '', false);

        $context = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $context->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));
        $context->expects($this->any())
            ->method('getStoreConfig')
            ->will($this->returnValue($config));
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Pricing\Render\Amount',
            [
                'context' => $context,
                'priceCurrency' => $this->priceCurrency,
                'rendererPool' => $this->rendererPool,
                'amount' => $this->amount,
                'saleableItem' => $this->saleableItemMock,
                'price' => $this->priceMock
            ]
        );
    }

    public function testConvertAndFormatCurrency()
    {
        $amount = '100';
        $includeContainer = true;
        $precision = \Magento\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION;

        $result = '100.0 grn';

        $this->priceCurrency->expects($this->once())
            ->method('convertAndFormat')
            ->with($amount, $includeContainer, $precision)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->convertAndFormatCurrency($amount, $includeContainer, $precision));
    }

    /**
     * Test case for getAdjustmentRenders method through toHtml()
     */
    public function testToHtmlSkipAdjustments()
    {
        $this->model->setData('skip_adjustments', true);
        $this->rendererPool->expects($this->never())
            ->method('getAdjustmentRenders');

        $this->model->toHtml();
    }

    /**
     * Test case for getAdjustmentRenders method through toHtml()
     */
    public function testToHtmlGetAdjustmentRenders()
    {
        $data = ['key1' => 'data1', 'css_classes' => 'class1 class2'];
        $expectedData = [
            'key1' => 'data1',
            'css_classes' => 'class1 class2',
            'module_name' => null,
            'adjustment_css_classes' => 'class1 class2 render1 render2'
        ];

        $this->model->setData($data);

        $adjustmentRender1 = $this->getAdjustmentRenderMock($expectedData);
        $adjustmentRender2 = $this->getAdjustmentRenderMock($expectedData);
        $adjustmentRenders = ['render1' => $adjustmentRender1, 'render2' => $adjustmentRender2];
        $this->rendererPool->expects($this->once())
            ->method('getAdjustmentRenders')
            ->will($this->returnValue($adjustmentRenders));

        $this->model->toHtml();
    }

    public function testGetDisplayValueExiting()
    {
        $displayValue = 5.99;
        $this->model->setDisplayValue($displayValue);
        $this->assertEquals($displayValue, $this->model->getDisplayValue());
    }

    public function testGetDisplayValue()
    {
        $amountValue = 100.99;
        $this->amount->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($amountValue));
        $this->assertEquals($amountValue, $this->model->getDisplayValue());
    }

    public function testGetAmount()
    {
        $this->assertEquals($this->amount, $this->model->getAmount());
    }

    public function testGetSealableItem()
    {
        $this->assertEquals($this->saleableItemMock, $this->model->getSaleableItem());
    }

    public function testGetPrice()
    {
        $this->assertEquals($this->priceMock, $this->model->getPrice());
    }

    public function testAdjustmentsHtml()
    {
        $adjustmentHtml1 = 'adjustment_1_html';
        $adjustmentHtml2 = 'adjustment_2_html';

        $this->assertFalse($this->model->hasAdjustmentsHtml());

        $this->model->addAdjustmentHtml('adjustment_1', $adjustmentHtml1);
        $this->model->addAdjustmentHtml('adjustment_2', $adjustmentHtml2);

        $this->assertTrue($this->model->hasAdjustmentsHtml());

        $this->assertEquals($adjustmentHtml1 . $adjustmentHtml2, $this->model->getAdjustmentsHtml());
    }

    protected function getAdjustmentRenderMock($data)
    {
        $adjustmentRender = $this->getMockForAbstractClass('Magento\Pricing\Render\AdjustmentRenderInterface');
        $adjustmentRender->expects($this->once())
            ->method('render')
            ->with($this->model, $data);
        return $adjustmentRender;
    }
}
