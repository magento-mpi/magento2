<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Resource_Report_Shipping_Collection_OrderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Model_Resource_Report_Shipping_Collection_Order
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Report_Shipping_Collection_Order');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order_shipping.php
     * @magentoDataFixture Mage/Sales/_files/report_shipping.php
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
        /** @var Magento_Adminhtml_Model_Report_Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
