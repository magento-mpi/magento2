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
 * Tests for Import model
 *
 * @magentoDataFixture Mage/ImportExport/_files/import_data.php
 */
class Mage_ImportExport_Model_ImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model object which used for tests
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
     * Test import from import data storage.
     * Covers _getEntityAdapter() in case when entity adapter was successfully returned
     *
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
     * Test _getEntityAdapter() through validateSource() method in case when entity was not set
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Entity is unknown
     */
    public function testGetEntityAdapterEntityIsNotSet()
    {
        $this->_model->validateSource('');
    }
}
