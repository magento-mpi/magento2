<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Pricing;

use Magento\Weee\Helper\Data as WeeeHelper;
use Magento\Pricing\Object\SaleableInterface;

class AdjustmentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAdjustmentCode()
    {
        // Instantiate/mock objects
        /** @var WeeeHelper $weeHelper */
        $weeHelper = $this->getMockBuilder('Magento\Weee\Helper\Data')->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $model = new Adjustment($weeHelper);

        // Run tested method
        $code = $model->getAdjustmentCode();

        // Check expectations
        $this->assertNotEmpty($code);
    }

    public function testIsIncludedInBasePrice()
    {
        // Instantiate/mock objects
        /** @var WeeeHelper|\PHPUnit_Framework_MockObject_MockObject $weeeHelper */
        $weeeHelper = $this->getMockBuilder('Magento\Weee\Helper\Data')->disableOriginalConstructor()->getMock();
        $model = new Adjustment($weeeHelper);

        // Run tested method
        $result = $model->isIncludedInBasePrice();

        // Check expectations
        $this->assertInternalType('bool', $result);
    }

    /**
     * @dataProvider isIncludedInDisplayPriceDataProvider
     */
    public function testIsIncludedInDisplayPrice($expectedResult)
    {
        // Instantiate/mock objects
        /** @var WeeeHelper|\PHPUnit_Framework_MockObject_MockObject $weeeHelper */
        $weeeHelper = $this->getMockBuilder('Magento\Weee\Helper\Data')->disableOriginalConstructor()
            ->setMethods(array('typeOfDisplay'))
            ->getMock();
        $model = new Adjustment($weeeHelper);

        // Avoid execution of irrelevant functionality
        $weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with(
                $this->equalTo(
                    [
                        \Magento\Weee\Model\Tax::DISPLAY_INCL,
                        \Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR,
                        4
                    ]
                )
            )
            ->will($this->returnValue($expectedResult));

        // Run tested method
        $result = $model->isIncludedInDisplayPrice();

        // Check expectations
        $this->assertInternalType('bool', $result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function isIncludedInDisplayPriceDataProvider()
    {
        return [[false], [true]];
    }

    /**
     * @param float $amount
     * @param float $expectedResult
     * @dataProvider extractAdjustmentDataProvider
     */
    public function testExtractAdjustment($amount, $expectedResult)
    {
        // Instantiate/mock objects
        /** @var WeeeHelper|\PHPUnit_Framework_MockObject_MockObject $weeeHelper */
        $weeeHelper = $this->getMockBuilder('Magento\Weee\Helper\Data')->disableOriginalConstructor()
            ->setMethods(array('getAmount'))
            ->getMock();
        /** @var SaleableInterface|\PHPUnit_Framework_MockObject_MockObject $saleableItem */
        $saleableItem = $this->getMockBuilder('Magento\Pricing\Object\SaleableInterface')->getMock();
        $model = new Adjustment($weeeHelper);

        // Avoid execution of irrelevant functionality
        $weeeHelper->expects($this->any())
            ->method('getAmount')
            ->will($this->returnValue($amount));

        // Run tested method
        $result = $model->extractAdjustment('anything_here', $saleableItem);

        // Check expectations
        $this->assertInternalType('float', $result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function extractAdjustmentDataProvider()
    {
        return [
            [1.1, 1.1],
            [0.0, 0.0],
        ];
    }

    /**
     * @param float $amount
     * @param float $amountOld
     * @param float $expectedResult
     * @dataProvider applyAdjustmentDataProvider
     */
    public function testApplyAdjustment($amount, $amountOld, $expectedResult)
    {
        // Instantiate/mock objects
        /** @var WeeeHelper|\PHPUnit_Framework_MockObject_MockObject $weeeHelper */
        $weeeHelper = $this->getMockBuilder('Magento\Weee\Helper\Data')->disableOriginalConstructor()
            ->setMethods(array('getAmount'))
            ->getMock();
        /** @var SaleableInterface|\PHPUnit_Framework_MockObject_MockObject $taxHelper */
        $object = $this->getMockBuilder('Magento\Pricing\Object\SaleableInterface')->getMock();
        $model = new Adjustment($weeeHelper);

        // Avoid execution of irrelevant functionality
        $weeeHelper->expects($this->any())
            ->method('getAmount')
            ->will($this->returnValue($amountOld));

        // Run tested method
        $result = $model->applyAdjustment($amount, $object);

        // Check expectations
        $this->assertInternalType('float', $result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function applyAdjustmentDataProvider()
    {
        return [
            [1.1, 2.2, 3.3],
            [0.0, 2.2, 2.2],
            [1.1, 0.0, 1.1],
        ];
    }

    public function testIsExcludedWith()
    {
        $adjustmentCode = 'some_random_adjustment_code123';

        // Instantiate/mock objects
        /** @var WeeeHelper|\PHPUnit_Framework_MockObject_MockObject $weeeHelper */
        $weeeHelper = $this->getMockBuilder('Magento\Weee\Helper\Data')->disableOriginalConstructor()->getMock();
        $model = new Adjustment($weeeHelper);

        // Run tested method
        $result = $model->isExcludedWith($adjustmentCode);

        // Check expectations
        $this->assertInternalType('bool', $result);
        $this->assertFalse($result);
    }
}
