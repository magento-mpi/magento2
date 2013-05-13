<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tax_Model_Resource_Report_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Tax_Model_Resource_Report_Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Mage_Tax_Model_Resource_Report_Collection');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Mage/Tax/_files/order_with_tax.php
     * @magentoDataFixture Mage/Tax/_files/report_tax.php
     */
    public function testGetItems()
    {
        $expectedResult = array(
            array(
                'code' => 'tax_code',
                'percent' => 10,
                'orders_count' => 1,
                'tax_base_amount_sum' => 20,
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
