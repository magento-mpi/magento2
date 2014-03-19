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
}
