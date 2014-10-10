<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Resource\Report;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Resource\Report\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\SalesRule\Model\Resource\Report\Collection'
        );
        $this->_collection->setPeriod('day')->setDateRange(null, null)->addStoreFilter(array(1));
    }

    /**
     * @magentoDataFixture Magento/SalesRule/_files/order_with_coupon.php
     * @magentoDataFixture Magento/SalesRule/_files/report_coupons.php
     */
    public function testGetItems()
    {
        $expectedResult = array(array('coupon_code' => '1234567890', 'coupon_uses' => 1));
        $actualResult = array();
        /** @var \Magento\Reports\Model\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider periodDataProvider
     * @magentoDataFixture Magento/SalesRule/_files/order_with_coupon.php
     * @magentoDataFixture Magento/SalesRule/_files/report_coupons.php
     *
     * @param $period
     * @param $expectedPeriod
     * @param $dateFrom
     * @param $dateTo
     */
    public function testPeriod($period, $dateFrom, $dateTo, $expectedPeriod)
    {
        $this->_collection->setPeriod($period);
        $this->_collection->setDateRange($dateFrom, $dateTo);
        $items = $this->_collection->getItems();
        $this->assertCount(1, $items);
        $this->assertEquals($expectedPeriod, $items[0]->getPeriod());
    }

    /**
     * Data provider for testTableSelection
     *
     * @return array
     */
    public function periodDataProvider()
    {
        return array(
            [
                'period'    => 'year',
                'date_from' => null,
                'date_to'   => null,
                'expected_period' => date('Y', time())
            ],
            [
                'period'    => 'month',
                'date_from' => null,
                'date_to'   => null,
                'expected_period' => date('Y-m', time())
            ],
            [
                'period'    => 'day',
                'date_from' => null,
                'date_to'   => null,
                'expected_period' => date('Y-m-d', time())
            ],
            [
                'period'    => 'undefinedPeriod',
                'date_from' => null,
                'date_to'   => null,
                'expected_period' => date('Y-m-d', time())
            ],
            [
                'period'    => null,
                'date_from' => date('Y-m-d',strtotime('-1 year', time())),
                'date_to'   => date('Y-m-d', time()),
                'expected_period' => date('Y-m-d', time())
            ]
        );
    }
}
