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
        /** @var $first Stub_UnitTest_Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection */
        /** @var $second Stub_UnitTest_Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection */

        $collection = new Stub_UnitTest_Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection();

        $collection->setOrder('id', Varien_Data_Collection::SORT_ORDER_ASC);
        list($first, $second) = array_values($collection->getItems());
        $this->assertEquals(9, $first->getId());
        $this->assertEquals(10, $second->getId());

        $collection->setOrder('id', Varien_Data_Collection::SORT_ORDER_DESC);
        list($first, $second) = array_values($collection->getItems());
        $this->assertEquals(10, $first->getId());
        $this->assertEquals(9, $second->getId());
    }

    public function testCompareAttributes()
    {
        $collection = new Stub_UnitTest_Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection();
        $collection->setOrder('id');
        $first = new Varien_Object(array('id' => 9));
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
        $this->addItem(new Varien_Object(array('id' => 9)));
        $this->addItem(new Varien_Object(array('id' => 10)));
    }
}
