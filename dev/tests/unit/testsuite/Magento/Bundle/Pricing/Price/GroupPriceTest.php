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

class GroupPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GroupPrice
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

    public function setUp()
    {
        $this->saleable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getPriceInfo', 'getCustomerGroupId', 'getData', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');

        $this->saleable->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfo));

        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectHelper->getObject('Magento\Bundle\Pricing\Price\GroupPrice', [
            'saleableItem' => $this->saleable
        ]);
    }

    /**
     * @param float $basePrice
     * @param [] $storedGroupPrice
     * @param float $value
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($basePrice, $storedGroupPrice, $value)
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
            $price = $this->getMock('Magento\Pricing\Price\PriceInterface');
            $this->priceInfo->expects($this->once())
                ->method('getPrice')
                ->with(\Magento\Catalog\Pricing\Price\BasePrice::PRICE_CODE)
                ->will($this->returnValue($price));
            $price->expects($this->once())
                ->method('getValue')
                ->will($this->returnValue($basePrice));
        }

        $this->assertEquals($value, $this->model->getValue());
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return array(
            ['basePrice' => 100, 'storedGroupPrice' => [['cust_group' => 234, 'website_price' => 40]], 'value' => 60],
            ['basePrice' => 75, 'storedGroupPrice' => [['cust_group' => 234, 'website_price' => 40]], 'value' => 45],
            ['basePrice' => 75, 'storedGroupPrice' => [], 'value' => false],
        );
    }
}
