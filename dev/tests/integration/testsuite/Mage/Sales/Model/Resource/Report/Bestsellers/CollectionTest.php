<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Resource_Report_Bestsellers_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Model_Resource_Report_Bestsellers_Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Report_Bestsellers_Collection');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     * @magentoDataFixture Mage/Sales/_files/report_bestsellers.php
     */
    public function testGetItems()
    {
        $expectedResult = array(1 => 2);
        $actualResult = array();
        /** @var Mage_Adminhtml_Model_Report_Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[$reportItem->getData('product_id')] = $reportItem->getData('qty_ordered');
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}