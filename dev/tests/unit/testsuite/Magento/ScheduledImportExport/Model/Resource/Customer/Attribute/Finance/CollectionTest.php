<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection
 */
class Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_CollectionTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * Returns mock for finance collection
     *
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection
     */
    protected function _getFinanceCollectionMock()
    {
        return $this->getMock(
            '\Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection',
            null,
            array(),
            '',
            false
        );
    }

    /**
     * Test setOrder method
     */
    public function testSetOrder()
    {
        $collection = $this->_getFinanceCollectionMock();

        $first  = new \Magento\Object(array('id' => 9));
        $second = new \Magento\Object(array('id' => 10));

        $collection->addItem($first);
        $collection->addItem($second);

        /** @var $orderFirst \Magento\Object */
        /** @var $orderSecond \Magento\Object */

        $collection->setOrder('id', \Magento\Data\Collection::SORT_ORDER_ASC);
        list($orderFirst, $orderSecond) = array_values($collection->getItems());
        $this->assertEquals($first->getId(), $orderFirst->getId());
        $this->assertEquals($second->getId(), $orderSecond->getId());

        $collection->setOrder('id', \Magento\Data\Collection::SORT_ORDER_DESC);
        list($orderFirst, $orderSecond) = array_values($collection->getItems());
        $this->assertEquals($second->getId(), $orderFirst->getId());
        $this->assertEquals($first->getId(), $orderSecond->getId());
    }

    /**
     * Test compare attributes method
     */
    public function testCompareAttributes()
    {
        $collection = $this->_getFinanceCollectionMock();
        $collection->setOrder('id');
        $first  = new \Magento\Object(array('id' => 9));
        $second = new \Magento\Object(array('id' => 10));

        $this->assertLessThan(0, $collection->compareAttributes($first, $second));
        $this->assertGreaterThan(0, $collection->compareAttributes($second, $first));
        $this->assertEquals(0, $collection->compareAttributes($first, $first));
    }
}
