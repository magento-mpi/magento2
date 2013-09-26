<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Resource_Report_Order_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Resource_Report_Order_Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Resource_Report_Order_Collection');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     * @magentoDataFixture Magento/Sales/_files/report_order.php
     */
    public function testGetItems()
    {
        $expectedResult = array(
            array('orders_count' => 1, 'total_qty_ordered' => 2, 'total_qty_invoiced' => 2),
        );
        $actualResult = array();
        /** @var Magento_Adminhtml_Model_Report_Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
