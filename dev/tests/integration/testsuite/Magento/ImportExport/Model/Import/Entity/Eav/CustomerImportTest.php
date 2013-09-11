<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for customer V2 import model
 */
class Magento_ImportExport_Model_Import_Entity_Eav_CustomerImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model object which used for tests
     *
     * @var \Magento\ImportExport\Model\Import\Entity\Eav\Customer
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        $this->_model = Mage::getModel('\Magento\ImportExport\Model\Import\Entity\Eav\Customer');
    }

    /**
     * Test importData() method
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\Customer::_importData
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\Customer::_prepareDataForUpdate
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\Customer::_saveCustomerEntity
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\Customer::_saveCustomerAttributes
     *
     * @magentoDataFixture Magento/ImportExport/_files/customer.php
     */
    public function testImportData()
    {
        // 3 customers will be imported.
        // 1 of this customers is already exist, but its first and last name were changed in file
        $expectAddedCustomers = 5;
        $source = new \Magento\ImportExport\Model\Import\Source\Csv(__DIR__ . '/_files/customers_to_import.csv');

        /** @var $customersCollection \Magento\Customer\Model\Resource\Customer\Collection */
        $customersCollection = Mage::getResourceModel('\Magento\Customer\Model\Resource\Customer\Collection');
        $customersCollection->addAttributeToSelect('firstname', 'inner')
            ->addAttributeToSelect('lastname', 'inner');

        $existCustomersCount = count($customersCollection->load());

        $customersCollection->resetData();
        $customersCollection->clear();

        $this->_model->setParameters(
                array(
                    'behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE
                )
            )
            ->setSource($source)
            ->isDataValid();

        $this->_model->importData();

        $customers = $customersCollection->getItems();

        $addedCustomers = count($customers) - $existCustomersCount;

        $this->assertEquals($expectAddedCustomers, $addedCustomers, 'Added unexpected amount of customers');

        $existingCustomer = Mage::registry('_fixture/Magento_ImportExport_Customer');

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

        $this->assertNotEquals(
            $existingCustomer->getCreatedAt(),
            $updatedCustomer->getCreatedAt(),
            'Creation date must be changed'
        );
    }

    /**
     * Test importData() method (delete behavior)
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\Customer::_importData
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\Customer::_deleteCustomers
     *
     * @magentoDataFixture Magento/ImportExport/_files/customers.php
     */
    public function testDeleteData()
    {
        Mage::app()->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $source = new \Magento\ImportExport\Model\Import\Source\Csv(__DIR__ . '/_files/customers_to_import.csv');

        /** @var $customerCollection \Magento\Customer\Model\Resource\Customer\Collection */
        $customerCollection = Mage::getResourceModel('\Magento\Customer\Model\Resource\Customer\Collection');
        $this->assertEquals(3, $customerCollection->count(), 'Count of existing customers are invalid');

        $this->_model->setParameters(
                array(
                    'behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE
                )
            )
            ->setSource($source)
            ->isDataValid();

        $this->_model->importData();

        $customerCollection->resetData();
        $customerCollection->clear();
        $this->assertEmpty($customerCollection->count(), 'Customers were not imported');
    }

    /**
     * Test entity type code value
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\Customer::getAttributeCollection
     */
    public function testGetEntityTypeCode()
    {
        $this->assertEquals('customer', $this->_model->getEntityTypeCode());
    }
}
