<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Pricing\Render;

class AdjustmentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAdjustmentCode()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $objectManager->getObject('Magento\Tax\Pricing\Render\Adjustment');

        $this->assertNotEmpty($model->getAdjustmentCode());
    }

    public function testDisplayBothPrices()
    {
        $expectedResult = true;

        /** @var \Magento\Catalog\Helper\Product\Price $helper */
        $helper = $this->getMockBuilder('Magento\Catalog\Helper\Product\Price')
            ->disableOriginalConstructor()
            ->setMethods(['displayBothPrices'])
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $objectManager->getObject('Magento\Tax\Pricing\Render\Adjustment', array('helper' => $helper));

        // Avoid executing irrelevant functionality
        $helper->expects($this->any())->method('displayBothPrices')->will($this->returnValue($expectedResult));

        // Run tested method
        $result = $model->displayBothPrices();

        // Check expectations
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Also tests:
     *  \Magento\Pricing\Render\AbstractAdjustment::render()
     *  \Magento\Pricing\Render\AbstractAdjustment::convertAndFormatCurrency()
     */
    public function testGetDisplayAmountExclTax()
    {
        $html = '<p>some_html</p>';
        $expectedHtml = '<p>expected_html</p>';
        $expectedPriceValue = 1.23;
        $expectedPrice = '$4.56';

        // Instantiate/mock objects
        /** @var \Magento\Pricing\PriceCurrencyInterface $priceCurrency */
        $priceCurrency = $this->getMock('Magento\Pricing\PriceCurrencyInterface');

        /** @var \Magento\Pricing\Render\Amount $amountRender */
        $amountRender = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->setMethods(['getAmount'])
            ->getMock();

        /** @var \Magento\Pricing\Amount\Base $baseAmount */
        $baseAmount = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments('Magento\Tax\Pricing\Render\Adjustment', array(
            'priceCurrency' => $priceCurrency
        ));

        /** @var \Magento\Tax\Pricing\Render\Adjustment $model */
        $model = $this->getMockBuilder('Magento\Tax\Pricing\Render\Adjustment')
            ->setConstructorArgs($constructArguments)
            ->setMethods(['toHtml'])
            ->getMock();

        // Avoid executing irrelevant functionality; Set values to return;
        $model->expects($this->any())->method('toHtml')->will($this->returnValue($expectedHtml));
        $amountRender->expects($this->any())->method('getAmount')->will($this->returnValue($baseAmount));
        $baseAmount->expects($this->any())->method('getValue')->will($this->returnValue($expectedPriceValue));
        $priceCurrency->expects($this->any())->method('convertAndFormat')->will($this->returnValue($expectedPrice));


        // Run tested method
        $resultHtml = $model->render($html, $amountRender);
        $result = $model->getDisplayAmountExclTax();

        // Check expectations
        $this->assertEquals($expectedHtml, $resultHtml);
        $this->assertEquals($expectedPrice, $result);
    }

    /**
     * Also tests:
     *  \Magento\Pricing\Render\AbstractAdjustment::render()
     *  \Magento\Pricing\Render\AbstractAdjustment::convertAndFormatCurrency()
     *
     * @param bool $includeContainer
     * @dataProvider getDisplayAmountDataProvider
     */
    public function testGetDisplayAmount($includeContainer)
    {
        $html = '<p>some_html</p>';
        $expectedHtml = '<p>expected_html</p>';
        $expectedPriceValue = 1.23;
        $expectedPrice = '$4.56';

        // Instantiate/mock objects
        /** @var \Magento\Pricing\PriceCurrencyInterface $priceCurrency */
        $priceCurrency = $this->getMock('Magento\Pricing\PriceCurrencyInterface');

        /** @var \Magento\Pricing\Render\Amount $amountRender */
        $amountRender = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->setMethods(['getAmount'])
            ->getMock();

        /** @var \Magento\Pricing\Amount\Base $baseAmount */
        $baseAmount = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments('Magento\Tax\Pricing\Render\Adjustment', array(
            'priceCurrency' => $priceCurrency
        ));
        /** @var \Magento\Tax\Pricing\Render\Adjustment $model */
        $model = $this->getMockBuilder('Magento\Tax\Pricing\Render\Adjustment')
            ->setConstructorArgs($constructArguments)
            ->setMethods(['toHtml'])
            ->getMock();

        // Avoid executing irrelevant functionality; Set values to return;
        $model->expects($this->any())->method('toHtml')->will($this->returnValue($expectedHtml));
        $amountRender->expects($this->any())->method('getAmount')->will($this->returnValue($baseAmount));
        $baseAmount->expects($this->any())->method('getValue')->will($this->returnValue($expectedPriceValue));
        $priceCurrency->expects($this->any())
            ->method('convertAndFormat')
            ->with($this->anything(), $this->equalTo($includeContainer))
            ->will($this->returnValue($expectedPrice));


        // Run tested method
        $resultHtml = $model->render($html, $amountRender);
        $result = $model->getDisplayAmount($includeContainer);

        // Check expectations
        $this->assertEquals($expectedHtml, $resultHtml);
        $this->assertEquals($expectedPrice, $result);
    }

    public function getDisplayAmountDataProvider()
    {
        return [[true], [false]];
    }

    /**
     * Also tests \Magento\Pricing\Render\AbstractAdjustment::render() method
     *
     * @param string $prefix
     * @param mixed $saleableId
     * @param mixed $suffix
     * @param string $expectedResult
     * @dataProvider buildIdWithPrefixDataProvider
     */
    public function testBuildIdWithPrefix($prefix, $saleableId, $suffix, $expectedResult)
    {
        $html = '<p>some_html</p>';
        $expectedHtml = '<p>expected_html</p>';

        // Instantiate/mock objects
        /** @var \Magento\Pricing\Render\Amount $amountRender */
        $amountRender = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->setMethods(['getSaleableItem'])
            ->getMock();

        /** @var \Magento\Catalog\Model\Product $saleable */
        $saleable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getId', '__wakeup'])
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments('Magento\Tax\Pricing\Render\Adjustment');

        /** @var \Magento\Tax\Pricing\Render\Adjustment $model */
        $model = $this->getMockBuilder('Magento\Tax\Pricing\Render\Adjustment')
            ->setConstructorArgs($constructArguments)
            ->setMethods(['toHtml'])
            ->getMock();

        // Avoid executing irrelevant functionality; Set values to return;
        $model->setIdSuffix($suffix);
        $model->expects($this->any())->method('toHtml')->will($this->returnValue($expectedHtml));
        $amountRender->expects($this->any())->method('getSaleableItem')->will($this->returnValue($saleable));
        $saleable->expects($this->any())->method('getId')->will($this->returnValue($saleableId));

        // Run tested method
        $resultHtml = $model->render($html, $amountRender);
        $result = $model->buildIdWithPrefix($prefix);

        // Check expectations
        $this->assertEquals($expectedHtml, $resultHtml);
        $this->assertEquals($expectedResult, $result);
    }

    public function buildIdWithPrefixDataProvider()
    {
        return [
            ['some_prefix_', null, '_suffix', 'some_prefix__suffix'],
            ['some_prefix_', false, '_suffix', 'some_prefix__suffix'],
            ['some_prefix_', 123, '_suffix', 'some_prefix_123_suffix'],
            ['some_prefix_', 123, null, 'some_prefix_123'],
            ['some_prefix_', 123, false, 'some_prefix_123'],
        ];
    }
}
