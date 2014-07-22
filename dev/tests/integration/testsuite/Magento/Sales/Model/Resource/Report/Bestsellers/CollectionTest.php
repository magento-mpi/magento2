<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Report\Bestsellers;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\Report\Bestsellers\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Resource\Report\Bestsellers\Collection'
        );
        $this->_collection->setPeriod('day')->setDateRange(null, null)->addStoreFilter(array(1));
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDataFixture Magento/Sales/_files/report_bestsellers.php
     */
    public function testGetItems()
    {
        $expectedResult = array(1 => 2);
        $actualResult = array();
        /** @var \Magento\Reports\Model\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[$reportItem->getData('product_id')] = $reportItem->getData('qty_ordered');
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider tableForPeriodDataProvider
     *
     * @param $period
     * @param $expectedTable
     * @param $dateFrom
     * @param $dateTo
     */
    public function testTableSelection($period, $expectedTable, $dateFrom, $dateTo)
    {
        $this->_collection->setPeriod($period);
        $this->_collection->setDateRange($dateFrom, $dateTo);
        $this->_collection->load();

        $from = $this->_collection->getSelect()->getPart('from');

        $this->assertArrayHasKey($expectedTable, $from);

        $this->assertArrayHasKey('tableName', $from[$expectedTable]);
        $actualTable = $from[$expectedTable]['tableName'];

        $this->assertEquals($expectedTable, $actualTable);
    }

    /**
     * Data provider for testTableSelection
     *
     * @return array
     */
    public function tableForPeriodDataProvider()
    {
        $dateNow = date('Y-m-d', time());
        $dateYearAgo = date('Y-m-d', strtotime($dateNow . ' -1 year'));
        return array(
            [
                'period'    => 'year',
                'table'     => 'sales_bestsellers_aggregated_yearly',
                'date_from' => null,
                'date_to'   => null
            ],
            [
                'period'    => 'month',
                'table'     => 'sales_bestsellers_aggregated_monthly',
                'date_from' => null,
                'date_to'   => null
            ],
            [
                'period'    => 'day',
                'table'     => 'sales_bestsellers_aggregated_daily',
                'date_from' => null,
                'date_to'   => null
            ],
            [
                'period'    => 'undefinedPeriod',
                'table'     => 'sales_bestsellers_aggregated_daily',
                'date_from' => null,
                'date_to'   => null
            ],
            [
                'period'    => null,
                'table'     => 'sales_bestsellers_aggregated_daily',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateNow
            ],
            [
                'period'    => null,
                'table'     => 'sales_bestsellers_aggregated_daily',
                'date_from' => $dateNow,
                'date_to'   => $dateNow
            ],
            [
                'period'    => null,
                'table'     => 'sales_bestsellers_aggregated_daily',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateYearAgo
            ],
            [
                'period'    => null,
                'table'     => 'sales_bestsellers_aggregated_yearly',
                'date_from' => null,
                'date_to'   => null
            ],
        );
    }
}
