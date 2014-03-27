<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Pricing\Render;

class AdjustmentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAdjustmentCode()
    {
        // Instantiate/mock objects
        /** @var \Magento\View\Element\Template\Context $context */
        $context = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Pricing\PriceCurrencyInterface $priceCurrency */
        $priceCurrency = $this->getMockBuilder('Magento\Pricing\PriceCurrencyInterface')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Weee\Helper\Data $helper */
        $helper = $this->getMockBuilder('Magento\Weee\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $model = new Adjustment($context, $priceCurrency, $helper);

        // Run tested method
        $code = $model->getAdjustmentCode();

        // Check expectations
        $this->assertNotEmpty($code);
    }

    /**
     * Also tests \Magento\Pricing\Render\AbstractAdjustment::render() method
     *
     * @param int $typeOfDisplay
     * @param float $amount
     * @param bool $expectedResult
     * @dataProvider showInclDescrDataProvider
     */
    public function testShowInclDescr($typeOfDisplay, $amount, $expectedResult)
    {
        $html = '<p>some_html</p>';
        $expectedHtml = '<p>expected_html</p>';

        // Instantiate/mock objects
        /** @var \Magento\View\Element\Template\Context $context */
        $context = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Pricing\PriceCurrencyInterface $priceCurrency */
        $priceCurrency = $this->getMockBuilder('Magento\Pricing\PriceCurrencyInterface')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Weee\Helper\Data $helper */
        $helper = $this->getMockBuilder('Magento\Weee\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(['typeOfDisplay', 'getAmount'])
            ->getMock();

        /** @var \Magento\Pricing\Render\Amount $amountRender */
        $amountRender = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->setMethods(['getSaleableItem'])
            ->getMock();

        /** @var \Magento\Catalog\Model\Product $saleable */
        $saleable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();

        /** @var \Magento\Weee\Pricing\Render\Adjustment $model */
        $model = $this->getMockBuilder('Magento\Weee\Pricing\Render\Adjustment')
            ->setConstructorArgs([$context, $priceCurrency, $helper])
            ->setMethods(['toHtml'])
            ->getMock();

        // Avoid executing irrelevant functionality
        $model->expects($this->any())->method('toHtml')->will($this->returnValue($expectedHtml));
        $callback = function ($argument) use ($typeOfDisplay) {
            if (is_array($argument)) {
                return in_array($typeOfDisplay, $argument);
            } else {
                return $argument == $typeOfDisplay;
            }
        };
        $helper->expects($this->any())->method('typeOfDisplay')->will($this->returnCallback($callback));
        $helper->expects($this->any())->method('getAmount')->will($this->returnValue($amount));
        $amountRender->expects($this->any())->method('getSaleableItem')->will($this->returnValue($saleable));

        // Run tested method
        $resultHtml = $model->render($html, $amountRender);
        $result = $model->showInclDescr();

        // Check expectations
        $this->assertEquals($expectedHtml, $resultHtml);
        $this->assertEquals($expectedResult, $result);
    }

    public function showInclDescrDataProvider()
    {
        return [
            [\Magento\Weee\Model\Tax::DISPLAY_INCL, 1.23, false],
            [\Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR, 1.23, true],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL, 1.23, false],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL, 1.23, false],
            [4, 1.23, false],
            [\Magento\Weee\Model\Tax::DISPLAY_INCL, 0, false],
            [\Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR, 0, false],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL, 0, false],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL, 0, false],
            [4, 0, false],
        ];
    }

    /**
     * Also tests \Magento\Pricing\Render\AbstractAdjustment::render() method
     *
     * @param int $typeOfDisplay
     * @param float $amount
     * @param bool $expectedResult
     * @dataProvider showExclDescrInclDataProvider
     */
    public function testShowExclDescrIncl($typeOfDisplay, $amount, $expectedResult)
    {
        $html = '<p>some_html</p>';
        $expectedHtml = '<p>expected_html</p>';

        // Instantiate/mock objects
        /** @var \Magento\View\Element\Template\Context $context */
        $context = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Pricing\PriceCurrencyInterface $priceCurrency */
        $priceCurrency = $this->getMockBuilder('Magento\Pricing\PriceCurrencyInterface')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Weee\Helper\Data $helper */
        $helper = $this->getMockBuilder('Magento\Weee\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(['typeOfDisplay', 'getAmount'])
            ->getMock();

        /** @var \Magento\Pricing\Render\Amount $amountRender */
        $amountRender = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->setMethods(['getSaleableItem'])
            ->getMock();

        /** @var \Magento\Catalog\Model\Product $saleable */
        $saleable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();

        /** @var \Magento\Weee\Pricing\Render\Adjustment $model */
        $model = $this->getMockBuilder('Magento\Weee\Pricing\Render\Adjustment')
            ->setConstructorArgs([$context, $priceCurrency, $helper])
            ->setMethods(['toHtml'])
            ->getMock();

        // Avoid executing irrelevant functionality
        $model->expects($this->any())->method('toHtml')->will($this->returnValue($expectedHtml));
        $callback = function ($argument) use ($typeOfDisplay) {
            if (is_array($argument)) {
                return in_array($typeOfDisplay, $argument);
            } else {
                return $argument == $typeOfDisplay;
            }
        };
        $helper->expects($this->any())->method('typeOfDisplay')->will($this->returnCallback($callback));
        $helper->expects($this->any())->method('getAmount')->will($this->returnValue($amount));
        $amountRender->expects($this->any())->method('getSaleableItem')->will($this->returnValue($saleable));

        // Run tested method
        $resultHtml = $model->render($html, $amountRender);
        $result = $model->showExclDescrIncl();

        // Check expectations
        $this->assertEquals($expectedHtml, $resultHtml);
        $this->assertEquals($expectedResult, $result);
    }

    public function showExclDescrInclDataProvider()
    {
        return [
            [\Magento\Weee\Model\Tax::DISPLAY_INCL, 1.23, false],
            [\Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR, 1.23, false],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL, 1.23, true],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL, 1.23, false],
            [4, 1.23, false],
            [\Magento\Weee\Model\Tax::DISPLAY_INCL, 0, false],
            [\Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR, 0, false],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL, 0, false],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL, 0, false],
            [4, 0, false],
        ];
    }

    /**
     * Also tests \Magento\Pricing\Render\AbstractAdjustment::render() method
     *
     * @param int $typeOfDisplay
     * @param array $attributes
     * @param array $expectedResult
     * @dataProvider getWeeeTaxAttributesDataProvider
     */
    public function testGetWeeeTaxAttributes($typeOfDisplay, $attributes, $expectedResult)
    {
        $html = '<p>some_html</p>';
        $expectedHtml = '<p>expected_html</p>';

        // Instantiate/mock objects
        /** @var \Magento\View\Element\Template\Context $context */
        $context = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Pricing\PriceCurrencyInterface $priceCurrency */
        $priceCurrency = $this->getMockBuilder('Magento\Pricing\PriceCurrencyInterface')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Weee\Helper\Data $helper */
        $helper = $this->getMockBuilder('Magento\Weee\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(['typeOfDisplay', 'getProductWeeeAttributesForDisplay'])
            ->getMock();

        /** @var \Magento\Pricing\Render\Amount $amountRender */
        $amountRender = $this->getMockBuilder('Magento\Pricing\Render\Amount')
            ->disableOriginalConstructor()
            ->setMethods(['getSaleableItem'])
            ->getMock();

        /** @var \Magento\Catalog\Model\Product $saleable */
        $saleable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();

        /** @var \Magento\Weee\Pricing\Render\Adjustment $model */
        $model = $this->getMockBuilder('Magento\Weee\Pricing\Render\Adjustment')
            ->setConstructorArgs([$context, $priceCurrency, $helper])
            ->setMethods(['toHtml'])
            ->getMock();

        // Avoid executing irrelevant functionality
        $model->expects($this->any())->method('toHtml')->will($this->returnValue($expectedHtml));
        $callback = function ($argument) use ($typeOfDisplay) {
            if (is_array($argument)) {
                return in_array($typeOfDisplay, $argument);
            } else {
                return $argument == $typeOfDisplay;
            }
        };
        $helper->expects($this->any())->method('typeOfDisplay')->will($this->returnCallback($callback));
        $helper->expects($this->any())
            ->method('getProductWeeeAttributesForDisplay')
            ->will($this->returnValue($attributes));
        $amountRender->expects($this->any())->method('getSaleableItem')->will($this->returnValue($saleable));

        // Run tested method
        $resultHtml = $model->render($html, $amountRender);
        $result = $model->getWeeeTaxAttributes();

        // Check expectations
        $this->assertEquals($expectedHtml, $resultHtml);
        $this->assertEquals($expectedResult, $result);
    }

    public function getWeeeTaxAttributesDataProvider()
    {
        return [
            [\Magento\Weee\Model\Tax::DISPLAY_INCL, [1,2,3], []],
            [\Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR, [1,2,3], [1,2,3]],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL, [1,2,3], [1,2,3]],
            [\Magento\Weee\Model\Tax::DISPLAY_EXCL, [1,2,3], []],
            [4, [1,2,3], []],
        ];
    }

    /**
     * Also tests \Magento\Pricing\Render\AbstractAdjustment::convertAndFormatCurrency()
     *
     * @param \Magento\Object $attribute
     * @param string $expectedResult
     * @dataProvider renderWeeeTaxAttributeDataProvider
     */
    public function testRenderWeeeTaxAttribute($attribute, $expectedResult)
    {
        // Instantiate/mock objects
        /** @var \Magento\View\Element\Template\Context $context */
        $context = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Pricing\PriceCurrencyInterface $priceCurrency */
        $priceCurrency = $this->getMock('Magento\Pricing\PriceCurrencyInterface');

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $objectManager->getObject('Magento\Weee\Pricing\Render\Adjustment', array(
            'context' => $context,
            'priceCurrency' => $priceCurrency
        ));

        // Avoid executing irrelevant functionality
        $priceCurrency->expects($this->any())->method('convertAndFormat')->will($this->returnArgument(0));

        // Run tested method
        $result = $model->renderWeeeTaxAttribute($attribute);

        // Check expectations
        $this->assertEquals($expectedResult, $result);
    }

    public function renderWeeeTaxAttributeDataProvider()
    {
        return [
            [new \Magento\Object(['name' => 'name1', 'amount' => 51]), 'name1: 51'],
            [new \Magento\Object(['name' => 'name1', 'amount' => false]), 'name1: '],
            [new \Magento\Object(['name' => false, 'amount' => 51]), ': 51'],
            [new \Magento\Object(['name' => false, 'amount' => false]), ': '],
        ];
    }
}
