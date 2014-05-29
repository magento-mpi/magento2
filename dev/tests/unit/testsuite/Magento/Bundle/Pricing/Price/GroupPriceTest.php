<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Price;

class GroupPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GroupPrice
     */
    protected $model;

    /**
     * @var \Magento\Framework\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleable;

    /**
     * @var \Magento\Framework\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfo;

    public function setUp()
    {
        $this->saleable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getPriceInfo', 'getCustomerGroupId', 'getData', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->priceInfo = $this->getMock('Magento\Framework\Pricing\PriceInfoInterface');

        $this->saleable->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfo));

        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectHelper->getObject('Magento\Bundle\Pricing\Price\GroupPrice', [
            'saleableItem' => $this->saleable
        ]);
    }

    /**
     * @param $regularPrice
     * @param $storedGroupPrice
     * @param $value
     * @param $percent
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($regularPrice, $storedGroupPrice, $value, $percent)
    {
        $customerGroupId = 234;

        $this->saleable->expects($this->atLeastOnce())
            ->method('getCustomerGroupId')
            ->will($this->returnValue($customerGroupId));

        $this->saleable->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue($storedGroupPrice));

        if (!empty($storedGroupPrice)) {
            $price = $this->getMock('Magento\Framework\Pricing\Price\PriceInterface');
            $this->priceInfo->expects($this->once())
                ->method('getPrice')
                ->with(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)
                ->will($this->returnValue($price));
            $price->expects($this->once())
                ->method('getValue')
                ->will($this->returnValue($regularPrice));
        }
        $this->assertEquals($value, $this->model->getValue());
        $this->assertEquals($percent, $this->model->getDiscountPercent());
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return array(
            ['regularPrice' => 100, 'storedGroupPrice'
                => [['cust_group' => 234, 'website_price' => 40]], 'value' => 60, 'percent' => 60],
            ['regularPrice' => 75, 'storedGroupPrice'
                => [['cust_group' => 234, 'website_price' => 40]], 'value' => 45, 'percent' => 60],
            ['regularPrice' => 75, 'storedGroupPrice'
                => [], 'value' => false, 'percent' => null],
        );
    }
}
