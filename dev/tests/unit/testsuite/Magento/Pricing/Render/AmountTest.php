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
     * @var RendererPool $rendererPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rendererPool;

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

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Pricing\Render\Amount',
            [
                'priceCurrency' => $this->priceCurrency,
                'rendererPool' => $this->rendererPool
            ]
        );
    }

    public function testConvertAndFormatCurrency()
    {
        $amount = '100';
        $includeContainer = true;
        $precision = \Magento\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION;
        $store = 1;
        $currency = null;

        $result = '100.0 grn';

        $this->priceCurrency->expects($this->once())
            ->method('convertAndFormat')
            ->with($amount, $includeContainer, $precision, $store, $currency)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->convertAndFormatCurrency(
            $amount,
            $includeContainer,
            $precision,
            $store,
            $currency
        ));
    }
}
