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
namespace Magento\Framework\Pricing\Render;

/**
 * Test class for \Magento\Framework\Pricing\Render\AbstractAdjustment
 */
class AbstractAdjustmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractAdjustment | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrency;

    /**
     * @var array
     */
    protected $data;

    public function setUp()
    {
        $this->priceCurrency = $this->getMock('Magento\Framework\Pricing\PriceCurrencyInterface');
        $this->data = ['argument_one' => 1];

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructorArgs = $objectManager->getConstructArguments(
            'Magento\Framework\Pricing\Render\AbstractAdjustment',
            array(
                'priceCurrency' => $this->priceCurrency,
                'data' => $this->data
            )
        );
        $this->model = $this->getMockBuilder('Magento\Framework\Pricing\Render\AbstractAdjustment')
            ->setConstructorArgs($constructorArgs)
            ->setMethods(['getData', 'setData', 'apply'])
            ->getMockForAbstractClass();
    }

    public function testConvertAndFormatCurrency()
    {
        $amount = '100';
        $includeContainer = true;
        $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION;

        $result = '100.0 grn';

        $this->priceCurrency->expects($this->once())
            ->method('convertAndFormat')
            ->with($amount, $includeContainer, $precision)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->convertAndFormatCurrency($amount, $includeContainer, $precision));
    }

    public function testRender()
    {
        $amountRender = $this->getMock('Magento\Framework\Pricing\Render\Amount', [], [], '', false);
        $arguments = ['argument_two' => 2];
        $mergedArguments = ['argument_one' => 1, 'argument_two' => 2];

        $this->model->expects($this->at(0))
            ->method('getData')
            ->will($this->returnValue($this->data));
        $this->model->expects($this->at(1))
            ->method('setData')
            ->with($mergedArguments);
        $this->model->expects($this->at(2))
            ->method('apply');
        $this->model->expects($this->at(3))
            ->method('setData')
            ->with($this->data);

        $this->model->render($amountRender, $arguments);
    }

    public function testGetAmountRender()
    {
        $amountRender = $this->getMock('Magento\Framework\Pricing\Render\Amount', [], [], '', false);
        $this->model->expects($this->at(0))
            ->method('getData')
            ->will($this->returnValue($this->data));
        $this->model->render($amountRender);
        $this->assertEquals($amountRender, $this->model->getAmountRender());
    }

    public function testGetPriceType()
    {
        $amountRender = $this->getMock('Magento\Framework\Pricing\Render\Amount', [], [], '', false);
        $price = $this->getMockForAbstractClass('Magento\Framework\Pricing\Price\PriceInterface');
        $sealableItem = $this->getMockForAbstractClass('Magento\Framework\Pricing\Object\SaleableInterface');
        $priceInfo = $this->getMock('Magento\Framework\Pricing\PriceInfo\Base', [], [], '', false);
        $priceCode = 'regular_price';

        $amountRender->expects($this->once())
            ->method('getSaleableItem')
            ->will($this->returnValue($sealableItem));
        $sealableItem->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));
        $priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($priceCode)
            ->will($this->returnValue($price));

        $this->model->expects($this->at(0))
            ->method('getData')
            ->will($this->returnValue($this->data));
        $this->model->render($amountRender);
        $this->assertEquals($price, $this->model->getPriceType($priceCode));
    }

    public function testGetPrice()
    {
        $price = 100;
        $amountRender = $this->getMock('Magento\Framework\Pricing\Render\Amount', [], [], '', false);
        $amountRender->expects($this->once())
            ->method('getPrice')
            ->with()
            ->will($this->returnValue($price));

        $this->model->expects($this->at(0))
            ->method('getData')
            ->will($this->returnValue($this->data));
        $this->model->render($amountRender);
        $this->assertEquals($price, $this->model->getPrice());
    }

    public function testGetSealableItem()
    {
        $sealableItem = $this->getMockForAbstractClass('Magento\Framework\Pricing\Object\SaleableInterface');
        $amountRender = $this->getMock('Magento\Framework\Pricing\Render\Amount', [], [], '', false);
        $amountRender->expects($this->once())
            ->method('getSaleableItem')
            ->with()
            ->will($this->returnValue($sealableItem));

        $this->model->expects($this->at(0))
            ->method('getData')
            ->will($this->returnValue($this->data));
        $this->model->render($amountRender);
        $this->assertEquals($sealableItem, $this->model->getSaleableItem());
    }

    public function testGetAdjustment()
    {
        $amountRender = $this->getMock('Magento\Framework\Pricing\Render\Amount', [], [], '', false);
        $adjustment = $this->getMockForAbstractClass('Magento\Framework\Pricing\Adjustment\AdjustmentInterface');
        $sealableItem = $this->getMockForAbstractClass('Magento\Framework\Pricing\Object\SaleableInterface');
        $priceInfo = $this->getMock('Magento\Framework\Pricing\PriceInfo\Base', [], [], '', false);
        $adjustmentCode = 'tax';

        $amountRender->expects($this->once())
            ->method('getSaleableItem')
            ->will($this->returnValue($sealableItem));
        $sealableItem->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));
        $priceInfo->expects($this->once())
            ->method('getAdjustment')
            ->with($adjustmentCode)
            ->will($this->returnValue($adjustment));

        $this->model->expects($this->at(0))
            ->method('getData')
            ->will($this->returnValue($this->data));
        $this->model->expects($this->once())
            ->method('getAdjustmentCode')
            ->will($this->returnValue($adjustmentCode));
        $this->model->render($amountRender);
        $this->assertEquals($adjustment, $this->model->getAdjustment());
    }
}
