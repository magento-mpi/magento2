<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Resource_Report_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Model\Resource\Report\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Magento\Tax\Model\Resource\Report\Collection');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Magento/Tax/_files/order_with_tax.php
     * @magentoDataFixture Magento/Tax/_files/report_tax.php
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
        /** @var \Magento\Adminhtml\Model\Report\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
