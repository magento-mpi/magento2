<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ImportExport_Model_Resource_Customer_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ImportExport_Model_Resource_Customer_Storage
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_entityTable = 'test';

    /**
     * @var array
     */
    protected $_expectedFields = array('entity_id', 'website_id', 'email');

    protected function setUp()
    {
        $this->_model = new Magento_ImportExport_Model_Resource_Customer_Storage($this->_getModelDependencies());
        $this->_model->load();
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Retrieve all necessary objects mocks which used inside customer storage
     *
     * @return array
     */
    protected function _getModelDependencies()
    {
        $select = $this->getMock('Magento_DB_Select', array('from'), array(), '', false);
        $select->expects($this->any())
            ->method('from')
            ->will($this->returnCallback(array($this, 'validateFrom')));
        $customerCollection = $this->getMock('Magento_Customer_Model_Resource_Customer_Collection',
            array('load', 'removeAttributeToSelect', 'getResource', 'getSelect'), array(), '', false
        );

        $resourceStub = new Magento_Object();
        $resourceStub->setEntityTable($this->_entityTable);
        $customerCollection->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resourceStub));

        $customerCollection->expects($this->once())
            ->method('getSelect')
            ->will($this->returnValue($select));

        $byPagesIterator = $this->getMock('stdClass', array('iterate'));
        $byPagesIterator->expects($this->once())
            ->method('iterate')
            ->will($this->returnCallback(array($this, 'iterate')));

        return array(
            'customer_collection'          => $customerCollection,
            'collection_by_pages_iterator' => $byPagesIterator,
            'page_size'                    => 10
        );
    }

    /**
     * Iterate stub
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Magento_Data_Collection $collection
     * @param int $pageSize
     * @param array $callbacks
     */
    public function iterate(Magento_Data_Collection $collection, $pageSize, array $callbacks)
    {
        foreach ($collection as $customer) {
            foreach ($callbacks as $callback) {
                call_user_func($callback, $customer);
            }
        }
    }

    /**
     * @param string $tableName
     * @param array $fields
     */
    public function validateFrom($tableName, $fields)
    {
        $this->assertEquals($this->_entityTable, $tableName);
        $this->assertEquals($this->_expectedFields, $fields);
    }

    /**
     * @covers Magento_ImportExport_Model_Resource_Customer_Storage::load
     */
    public function testLoad()
    {
        $this->assertAttributeEquals(true, '_isCollectionLoaded', $this->_model);
    }

    /**
     * @covers Magento_ImportExport_Model_Resource_Customer_Storage::addCustomer
     */
    public function testAddCustomer()
    {
        $propertyName = '_customerIds';
        $customer = $this->_addCustomerToStorage();

        $this->assertAttributeCount(1, $propertyName, $this->_model);

        $expectedCustomerData = array(
            $customer->getWebsiteId() => $customer->getId()
        );
        $this->assertAttributeContains($expectedCustomerData, $propertyName, $this->_model);
    }

    /**
     * @covers Magento_ImportExport_Model_Resource_Customer_Storage::addCustomer
     */
    public function testGetCustomerId()
    {
        $customer = $this->_addCustomerToStorage();

        $this->assertEquals(
            $customer->getId(),
            $this->_model->getCustomerId($customer->getEmail(), $customer->getWebsiteId())
        );
        $this->assertFalse($this->_model->getCustomerId('new@test.com', $customer->getWebsiteId()));
    }

    /**
     * @return Magento_Object
     */
    protected function _addCustomerToStorage()
    {
        $customer = new Magento_Object(array(
            'id'         => 1,
            'website_id' => 1,
            'email'      => 'test@test.com'
        ));
        $this->_model->addCustomer($customer);

        return $customer;
    }
}
