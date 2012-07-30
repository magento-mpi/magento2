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
 * @magentoDataFixture Mage/ImportExport/_files/import_data.php
 */
class Mage_ImportExport_Model_ImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model object which is used for tests
     *
     * @var Mage_ImportExport_Model_Import
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_ImportExport_Model_Import();
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @covers Mage_ImportExport_Model_Import::_getEntityAdapter
     */
    public function testImportSource()
    {
        /** @var $customersCollection Mage_Customer_Model_Resource_Customer_Collection */
        $customersCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection');

        $existCustomersCount = count($customersCollection->load());

        $customersCollection->resetData();
        $customersCollection->clear();

        $this->_model->importSource();

        $customers = $customersCollection->getItems();

        $addedCustomers = count($customers) - $existCustomersCount;

        $this->assertGreaterThan($existCustomersCount, $addedCustomers);
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Entity is unknown
     */
    public function testGetEntityAdapterEntityIsNotSet()
    {
        $this->_model->validateSource('');
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Invalid entity
     */
    public function testGetEntityAdapterInvalidEntity()
    {
        $this->_model->setEntity('invalid_entity_name');
        $this->_model->validateSource('');
    }

    public function testGetEntity()
    {
        $entityName = 'entity_name';
        $this->_model->setEntity($entityName);
        $this->assertSame($entityName, $this->_model->getEntity());
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Entity is unknown
     */
    public function testGetEntityEntityIsNotSet()
    {
        $this->_model->getEntity();
    }
}
