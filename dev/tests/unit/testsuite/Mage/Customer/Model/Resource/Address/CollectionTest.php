<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for customer addresses collection
 */
class Mage_Customer_Model_Resource_Address_CollectionTest extends Magento_Test_TestCase_ZendDbAdapterAbstract
{
    /**
     * @var Mage_Customer_Model_Resource_Address_Collection|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collection;

    public function setUp()
    {
        parent::setUp();

        $this->_collection = $this->getMock('Mage_Customer_Model_Resource_Address_Collection',
            array('getEntity'), array(), '', false);

        $entityMock = $this->getMock('Varien_Object', array('isAttributeStatic'));
        $entityMock->expects($this->any())
            ->method('isAttributeStatic')
            ->will($this->returnValue(true));

        $this->_collection->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($entityMock));

        $adapter = $this->_getAdapterMock(
            'Zend_Db_Adapter_Pdo_Mysql',
            array('fetchAll', 'prepareSqlCondition'),
            null
        );

        $this->_collection->setConnection($adapter);
    }

    public function tearDown()
    {
        unset($this->_collection);

        parent::tearDown();
    }

    /**
     * Prepare adapter mock for future tests
     *
     * @param $expectedMethodParam
     */
    protected function _mockPrepareSqlCondition($expectedMethodParam)
    {
        $this->_collection->getConnection()
            ->expects($this->any())
            ->method('prepareSqlCondition')
            ->with(
            $this->stringContains('parent_id'),
            $expectedMethodParam
        )
        ->will($this->returnValue('parent_id = ' . $expectedMethodParam));
    }

    /**
     * Test setCustomerFilter() using empty object as possible filter
     */
    public function testSetEmptyObjectAsCustomerFilter()
    {
        $expectedValue = -1;
        $this->_mockPrepareSqlCondition($expectedValue);

        $this->_collection->setCustomerFilter(new Varien_Object());

        $this->assertContains("(parent_id = " . $expectedValue . ")",
            $this->_collection->getSelect()->getPart(Zend_Db_Select::WHERE));
    }

    /**
     * Test setCustomerFilter() using object with existing Id as possible filter
     */
    public function testSetCorrectObjectAsCustomerFilter()
    {
        $expectedValue = 10;
        $this->_mockPrepareSqlCondition($expectedValue);

        $customer = new Varien_Object(array('id' => $expectedValue));
        $this->_collection->setCustomerFilter($customer);

        $this->assertContains("(parent_id = " . $expectedValue . ")",
            $this->_collection->getSelect()->getPart(Zend_Db_Select::WHERE));
    }

    /**
     * Test setCustomerFilter() using array of Ids as possible filter
     */
    public function testSetArrayAsCustomerFilter()
    {
        $customerIds = array(1, 2);
        $expectedString = "parent_id IN (" . implode(',', $customerIds) . ")";

        $this->_collection->getConnection()
            ->expects($this->any())
            ->method('prepareSqlCondition')
            ->with(
                $this->stringContains('parent_id'),
                array('in' => $customerIds)
            )
            ->will($this->returnValue($expectedString));

        $this->_collection->setCustomerFilter($customerIds);
        $this->assertContains('(' . $expectedString . ')',
            $this->_collection->getSelect()->getPart(Zend_Db_Select::WHERE));
    }
}
