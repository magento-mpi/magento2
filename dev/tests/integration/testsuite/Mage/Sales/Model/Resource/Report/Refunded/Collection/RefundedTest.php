<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Resource_Report_Refunded_Collection_RefundedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Model_Resource_Report_Refunded_Collection_Refunded
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Report_Refunded_Collection_Refunded');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/creditmemo.php
     * @magentoDataFixture Mage/Sales/_files/report_refunded.php
     */
    public function testGetItems()
    {
        $expectedResult = array(
            array(
                'orders_count' => 1,
                'refunded' => 100,
                'online_refunded' => 80,
                'offline_refunded' => 20,
            ),
        );
        $actualResult = array();
        /** @var Mage_Adminhtml_Model_Report_Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
