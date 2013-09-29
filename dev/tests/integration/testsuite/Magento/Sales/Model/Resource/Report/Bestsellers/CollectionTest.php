<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Resource\Report\Bestsellers;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\Report\Bestsellers\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Resource\Report\Bestsellers\Collection');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDataFixture Magento/Sales/_files/report_bestsellers.php
     */
    public function testGetItems()
    {
        $expectedResult = array(1 => 2);
        $actualResult = array();
        /** @var \Magento\Adminhtml\Model\Report\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[$reportItem->getData('product_id')] = $reportItem->getData('qty_ordered');
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
