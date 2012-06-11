<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection
 */
class Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_CollectionTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * Test setOrder method
     */
    public function testSetOrder()
    {
        $collection = new Stub_UnitTest_Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection();

        $first  = new Varien_Object(array('id' => 9));
        $second = new Varien_Object(array('id' => 10));

        $collection->addItem($first);
        $collection->addItem($second);

        /** @var $orderFirst Varien_Object */
        /** @var $orderSecond Varien_Object */

        $collection->setOrder('id', Varien_Data_Collection::SORT_ORDER_ASC);
        list($orderFirst, $orderSecond) = array_values($collection->getItems());
        $this->assertEquals($first->getId(), $orderFirst->getId());
        $this->assertEquals($second->getId(), $orderSecond->getId());

        $collection->setOrder('id', Varien_Data_Collection::SORT_ORDER_DESC);
        list($first, $second) = array_values($collection->getItems());
        $this->assertEquals($second->getId(), $orderFirst->getId());
        $this->assertEquals($first->getId(), $orderSecond->getId());
    }

    public function testCompareAttributes()
    {
        $collection = new Stub_UnitTest_Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection();
        $collection->setOrder('id');
        $first  = new Varien_Object(array('id' => 9));
        $second = new Varien_Object(array('id' => 10));

        $this->assertLessThan(0, $collection->compareAttributes($first, $second));
        $this->assertGreaterThan(0, $collection->compareAttributes($second, $first));
        $this->assertEquals(0, $collection->compareAttributes($first, $first));
    }
}

/**
 * Stub for attribute finance collection
 *
 * @method int getId() getId()
 */
class Stub_UnitTest_Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection
    extends Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection
{
    /**
     * Modified constructor
     */
    public function __construct()
    {
    }
}
