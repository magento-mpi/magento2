<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Price;

class SpecialPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SpecialPrice
     */
    protected $model;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleable;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfo;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDate;

    public function setUp()
    {
        $this->saleable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->localeDate = $this->getMock('Magento\Stdlib\DateTime\TimezoneInterface');
        $this->priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');

        $this->saleable->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfo));

        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectHelper->getObject('Magento\Bundle\Pricing\Price\SpecialPrice', [
            'salableItem' => $this->saleable,
            'localeDate' => $this->localeDate
        ]);
    }

    /**
     * @param float $basePrice
     * @param float $specialPrice
     * @param bool $isScopeDateInInterval
     * @param float $value
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($basePrice, $specialPrice, $isScopeDateInInterval, $value)
    {
        $specialFromDate =  'some date from';
        $specialToDate =  'som date to';

        $this->saleable->expects($this->once())
            ->method('getSpecialPrice')
            ->will($this->returnValue($specialPrice));

        $store = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $this->saleable->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));
        $this->saleable->expects($this->once())
            ->method('getSpecialFromDate')
            ->will($this->returnValue($specialFromDate));
        $this->saleable->expects($this->once())
            ->method('getSpecialToDate')
            ->will($this->returnValue($specialToDate));

        $this->localeDate->expects($this->once())
            ->method('isScopeDateInInterval')
            ->with($store, $specialFromDate, $specialToDate)
            ->will($this->returnValue($isScopeDateInInterval));

        if ($isScopeDateInInterval) {
            $price = $this->getMock('Magento\Pricing\Price\PriceInterface');
            $this->priceInfo->expects($this->once())
                ->method('getPrice')
                ->with(\Magento\Catalog\Pricing\Price\BasePrice::PRICE_CODE, null)
                ->will($this->returnValue($price));
            $price->expects($this->once())
                ->method('getValue')
                ->will($this->returnValue($basePrice));
        }

        $this->assertEquals($value, $this->model->getValue());
        $this->assertEquals($value, $this->model->getValue());
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return array(
            ['basePrice' => 100, 'specialPrice' => 40, 'isScopeDateInInterval' => true, 'value' => 60],
            ['basePrice' => 75, 'specialPrice' => 40, 'isScopeDateInInterval' => true, 'value' => 45],
            ['basePrice' => 75, 'specialPrice' => 40, 'isScopeDateInInterval' => false, 'value' => false],
        );
    }
}
