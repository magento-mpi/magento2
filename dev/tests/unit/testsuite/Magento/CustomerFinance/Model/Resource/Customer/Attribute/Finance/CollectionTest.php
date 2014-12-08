<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerFinance\Model\Resource\Customer\Attribute\Finance;

/**
 * Test class for \Magento\CustomerFinance\Model\Resource\Customer\Attribute\Finance\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns mock for finance collection
     *
     * @return \Magento\CustomerFinance\Model\Resource\Customer\Attribute\Finance\Collection
     */
    protected function _getFinanceCollectionMock()
    {
        return $this->getMock(
            'Magento\CustomerFinance\Model\Resource\Customer\Attribute\Finance\Collection',
            null,
            [],
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

        $first = new \Magento\Framework\Object(['id' => 9]);
        $second = new \Magento\Framework\Object(['id' => 10]);

        $collection->addItem($first);
        $collection->addItem($second);

        /** @var $orderFirst \Magento\Framework\Object */
        /** @var $orderSecond \Magento\Framework\Object */

        $collection->setOrder('id', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        list($orderFirst, $orderSecond) = array_values($collection->getItems());
        $this->assertEquals($first->getId(), $orderFirst->getId());
        $this->assertEquals($second->getId(), $orderSecond->getId());

        $collection->setOrder('id', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);
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
        $first = new \Magento\Framework\Object(['id' => 9]);
        $second = new \Magento\Framework\Object(['id' => 10]);

        $this->assertLessThan(0, $collection->compareAttributes($first, $second));
        $this->assertGreaterThan(0, $collection->compareAttributes($second, $first));
        $this->assertEquals(0, $collection->compareAttributes($first, $first));
    }
}
