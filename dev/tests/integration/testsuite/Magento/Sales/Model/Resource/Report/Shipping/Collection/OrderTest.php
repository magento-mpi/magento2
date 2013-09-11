<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Resource_Report_Shipping_Collection_OrderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\Report\Shipping\Collection\Order
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Shipping\Collection\Order');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_shipping.php
     * @magentoDataFixture Magento/Sales/_files/report_shipping.php
     */
    public function testGetItems()
    {
        $expectedResult = array(
            array(
                'orders_count' => 1,
                'total_shipping' => 36,
                'total_shipping_actual' => 34,
            ),
        );
        $actualResult = array();
        /** @var \Magento\Adminhtml\Model\Report\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
