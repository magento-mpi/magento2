<?php
/**
 * Created by PhpStorm.
 * User: tshevchenko
 * Date: 23.04.14
 * Time: 14:13
 */

namespace Magento\Tax\Pricing\Price\Plugin;

/**
 * Class AttributePrice
 *
 * @package Magento\Tax\Pricing\Price\Plugin
 */
class AttributePriceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Tax\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $taxHelperMock;

    /** @var \Magento\Tax\Model\Calculation|\PHPUnit_Framework_MockObject_MockObject */
    protected $calculationMock;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $productMock;

    /** @var \Magento\Pricing\PriceInfo\Base|\PHPUnit_Framework_MockObject_MockObject */
    protected $priceInfoMock;

    /** @var \Magento\Tax\Pricing\Adjustment|\PHPUnit_Framework_MockObject_MockObject */
    protected $adjustmentMock;

    /** @var  \Magento\Tax\Pricing\Price\Plugin\AttributePrice */
    protected $model;

    public function setUp()
    {
        $this->taxHelperMock = $this->getMock(
            'Magento\Tax\Helper\Data',
            ['displayPriceIncludingTax', 'displayBothPrices'],
            [],
            '',
            false,
            false
        );
        $this->calculationMock = $this->getMock(
            'Magento\Tax\Model\Calculation',
            ['getRate', 'getRateRequest'],
            [],
            '',
            false,
            false
        );
        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['__wakeUp', 'getTaxClassId', 'getPriceInfo'],
            [],
            '',
            false,
            false
        );
        $this->priceInfoMock = $this->getMock(
            'Magento\Pricing\PriceInfo\Base',
            ['getAdjustment'],
            [],
            '',
            false,
            false
        );
        $this->adjustmentMock = $this->getMock(
            'Magento\Tax\Pricing\Adjustment',
            ['isIncludedInBasePrice'],
            [],
            '',
            false,
            false
        );
        $this->model = new \Magento\Tax\Pricing\Price\Plugin\AttributePrice(
            $this->taxHelperMock,
            $this->calculationMock
        );

    }

    public function afterPrepareAdjustmentConfigTest()
    {
        $taxClassId = 'tax_class_id';
        $this->taxHelperMock->expects($this->once())
            ->method('displayPriceIncludingTax')
            ->will($this->returnValue(true));
        $this->taxHelperMock->expects($this->once())
            ->method('displayBothPrices')
            ->will($this->returnValue(true));

        $this->adjustmentMock->expects($this->once())
            ->method('isIncludedInBasePrice')
            ->will($this->returnValue(true));

        $this->priceInfoMock->expects($this->once())
            ->method('getAdjustment')
            ->with($this->equalTo(\Magento\Tax\Pricing\Adjustment::ADJUSTMENT_CODE))
            ->will($this->returnValue($this->adjustmentMock));

        $this->productMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->productMock->expects($this->once())
            ->method('getTaxClassId')
            ->will($this->returnValue($taxClassId));

        $rateRequest1 = new \Magento\Object();
        $defaultTax = 20;
        $this->calculationMock->expects($this->at(0))
            ->method('getRateRequest')
            ->with($this->equalTo(false), $this->equalTo(false), $this->equalTo(false), $this->equalTo(null))
            ->will($this->returnValue($rateRequest1));
        $rateRequest1->setProductClassId($taxClassId);
        $this->calculationMock->expects($this->at(0))
            ->method('getRate')
            ->will($this->returnValue($defaultTax));
        $rateRequest2 = new \Magento\Object();
        $currentTax = 15;
        $this->calculationMock->expects($this->at(0))
            ->method('getRateRequest')
            ->with($this->equalTo(null), $this->equalTo(null), $this->equalTo(null), $this->equalTo(null))
            ->will($this->returnValue($rateRequest2));
        $rateRequest2->setProductClassId($taxClassId);
        $this->calculationMock->expects($this->at(0))
            ->method('getRate')
            ->will($this->returnValue($currentTax));

        $attributeMock = $this->getMock('Magento\ConfigurableProduct\Pricing\Price\AttributePrice');
        $result = [];
        $result = $this->model->afterPrepareAdjustmentConfig($attributeMock, $result);


    }
}
