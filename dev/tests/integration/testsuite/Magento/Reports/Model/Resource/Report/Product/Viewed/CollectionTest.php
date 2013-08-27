<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Reports_Model_Resource_Report_Product_Viewed_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Reports_Model_Resource_Report_Product_Viewed_Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Magento_Reports_Model_Resource_Report_Product_Viewed_Collection');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Magento/Reports/_files/viewed_products.php
     */
    public function testGetItems()
    {
        $expectedResult = array(1 => 3, 2 => 1, 21 => 2);
        $actualResult = array();
        /** @var Magento_Adminhtml_Model_Report_Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[$reportItem->getData('product_id')] = $reportItem->getData('views_num');
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
