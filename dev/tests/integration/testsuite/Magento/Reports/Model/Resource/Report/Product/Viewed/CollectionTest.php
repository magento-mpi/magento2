<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model\Resource\Report\Product\Viewed;

/**
 * @magentoAppArea adminhtml
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reports\Model\Resource\Report\Product\Viewed\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Reports\Model\Resource\Report\Product\Viewed\Collection'
        );
        $this->_collection->setPeriod('day')->setDateRange(null, null)->addStoreFilter(array(1));
    }

    /**
     * @magentoDataFixture Magento/Reports/_files/viewed_products.php
     */
    public function testGetItems()
    {
        $expectedResult = array(1 => 3, 2 => 1, 21 => 2);
        $actualResult = array();
        /** @var \Magento\Reports\Model\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[$reportItem->getData('product_id')] = $reportItem->getData('views_num');
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
        $dbTableName = $this->_collection->getTable($expectedTable);
        $this->_collection->setPeriod($period);
        $this->_collection->setDateRange($dateFrom, $dateTo);
        $this->_collection->load();
        $from = $this->_collection->getSelect()->getPart('from');

        $this->assertArrayHasKey($dbTableName, $from);

        $this->assertArrayHasKey('tableName', $from[$dbTableName]);
        $actualTable = $from[$dbTableName]['tableName'];

        $this->assertEquals($dbTableName, $actualTable);
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
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => null,
                'date_to'   => null
            ],
            [
                'period'    => 'month',
                'table'     => 'report_viewed_product_aggregated_monthly',
                'date_from' => null,
                'date_to'   => null
            ],
            [
                'period'    => 'day',
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => null,
                'date_to'   => null
            ],
            [
                'period'    => 'undefinedPeriod',
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => null,
                'date_to'   => null
            ],
            [
                'period'    => null,
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateNow
            ],
            [
                'period'    => null,
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => $dateNow,
                'date_to'   => $dateNow
            ],
            [
                'period'    => null,
                'table'     => 'report_viewed_product_aggregated_daily',
                'date_from' => $dateYearAgo,
                'date_to'   => $dateYearAgo
            ],
            [
                'period'    => null,
                'table'     => 'report_viewed_product_aggregated_yearly',
                'date_from' => null,
                'date_to'   => null
            ],
        );
    }
}
