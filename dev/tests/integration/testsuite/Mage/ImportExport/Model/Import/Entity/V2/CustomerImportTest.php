<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for customer V2 import model
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_CustomerImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model object which used for tests
     *
     * @var Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        $this->_model = new Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer();
    }

    protected function tearDown()
    {
        unset($this->_model);

        parent::tearDown();
    }

    /**
     * Test importData() method
     *
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer::_importData
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer::_saveCustomers
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer::_saveCustomerEntity
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer::_saveCustomerAttributes
     *
     * magentoDataFixture Mage/ImportExport/_files/customer.php
     */
    public function testImportData()
    {
        $this->markTestIncomplete('BUG MAGETWO-1953');

        // 3 customers will be imported.
        // 1 of this customers is already exist, but its first and last name were changed in file
        $expectAddedCustomers = 2;
        $source = new Mage_ImportExport_Model_Import_Adapter_Csv(__DIR__ . '/_files/customers_to_import.csv');

        /** @var $customersCollection Mage_Customer_Model_Resource_Customer_Collection */
        $customersCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection');
        $customersCollection->addAttributeToSelect('firstname', 'inner')
            ->addAttributeToSelect('lastname', 'inner');

        $existCustomersCount = count($customersCollection->load());

        $customersCollection->resetData();
        $customersCollection->clear();

        $this->_model->setSource($source)
            ->isDataValid();

        $this->_model->importData();

        $customers = $customersCollection->getItems();

        $addedCustomers = count($customers) - $existCustomersCount;

        $this->assertEquals($expectAddedCustomers, $addedCustomers, 'Added unexpected amount of customers');

        $existingCustomer = Mage::registry('_fixture/Mage_ImportExport_Customer');

        $updatedCustomer = $customers[$existingCustomer->getId()];

        $this->assertNotEquals(
            $existingCustomer->getFirstname(),
            $updatedCustomer->getFirstname(),
            'Firstname must be changed'
        );

        $this->assertNotEquals(
            $existingCustomer->getLastname(),
            $updatedCustomer->getLastname(),
            'Lastname must be changed'
        );
    }

    /**
     * Test entity type code value
     *
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer::getAttributeCollection
     */
    public function testGetEntityTypeCode()
    {
        $this->assertEquals('customer', $this->_model->getEntityTypeCode());
    }
}
