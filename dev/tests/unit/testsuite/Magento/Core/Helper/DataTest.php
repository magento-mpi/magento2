<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

use Magento\Pricing\PriceCurrencyInterface;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Data
     */
    protected $model;

    /**
     * @var \Magento\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrency;

    public function setUp()
    {
        $this->priceCurrency = $this->getMock('Magento\Pricing\PriceCurrencyInterface');

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Core\Helper\Data', array(
            'priceCurrency' => $this->priceCurrency
        ));
    }

    /**
     * @param string $amount
     * @param bool $format
     * @param bool $includeContainer
     * @param string $result
     * @dataProvider currencyDataProvider
     */
    public function testCurrency($amount, $format, $includeContainer, $result)
    {
        if ($format) {
            $this->priceCurrency->expects($this->once())
                ->method('convertAndFormat')
                ->with($amount, $includeContainer)
                ->will($this->returnValue($result));
        } else {
            $this->priceCurrency->expects($this->once())
                ->method('convert')
                ->with($amount)
                ->will($this->returnValue($result));
        }
        $this->assertEquals($result, $this->model->currency($amount, $format, $includeContainer));
    }

    public function currencyDataProvider()
    {
        return array(
            array('amount' => '100', 'format' => true, 'includeContainer' => true, 'result' => '100rgn.'),
            array('amount' => '115', 'format' => true, 'includeContainer' => false, 'result' => '1150'),
            array('amount' => '120', 'format' => false, 'includeContainer' => null, 'result' => '1200'),
        );
    }

    /**
     * @param string $amount
     * @param string $store
     * @param bool $format
     * @param bool $includeContainer
     * @param string $result
     * @dataProvider currencyByStoreDataProvider
     */
    public function testCurrencyByStore($amount, $store, $format, $includeContainer, $result)
    {
        if ($format) {
            $this->priceCurrency->expects($this->once())
                ->method('convertAndFormat')
                ->with($amount, $includeContainer, PriceCurrencyInterface::DEFAULT_PRECISION, $store)
                ->will($this->returnValue($result));
        } else {
            $this->priceCurrency->expects($this->once())
                ->method('convert')
                ->with($amount, $store)
                ->will($this->returnValue($result));
        }
        $this->assertEquals($result, $this->model->currencyByStore($amount, $store, $format, $includeContainer));
    }

    public function currencyByStoreDataProvider()
    {
        return array(
            array('amount' => '10', 'store' => 1, 'format' => true, 'includeContainer' => true, 'result' => '10rgn.'),
            array('amount' => '115', 'store' => 4,  'format' => true, 'includeContainer' => false, 'result' => '1150'),
            array('amount' => '120', 'store' => 5,  'format' => false, 'includeContainer' => null, 'result' => '1200'),
        );
    }

    public function testFormatCurrency()
    {
        $amount = '120';
        $includeContainer = false;
        $result = '10rgn.';

        $this->priceCurrency->expects($this->once())
            ->method('convertAndFormat')
            ->with($amount, $includeContainer)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->formatCurrency($amount, $includeContainer));
    }

    public function testFormatPrice()
    {
        $amount = '120';
        $includeContainer = false;
        $result = '10rgn.';

        $this->priceCurrency->expects($this->once())
            ->method('format')
            ->with($amount, $includeContainer)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->formatPrice($amount, $includeContainer));
    }
}
