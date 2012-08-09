<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_Entity_CustomerComposite
 */
class Mage_ImportExport_Model_Import_Entity_CustomerCompositeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Composite customer entity adapter instance
     *
     * @var Mage_ImportExport_Model_Import_Entity_CustomerComposite
     */
    protected $_entityAdapter;

    /**
     * Additional customer attributes for assertion
     *
     * @var array
     */
    protected $_customerAttributes = array('firstname', 'lastname');

    /**
     * Customers and addresses before import, address ID is postcode
     *
     * @var array
     */
    protected $_beforeImport = array(
        'betsyparker@example.com' => array(
            'addresses' => array('19107', '72701'),
            'data' => array(
                'firstname' => 'Betsy',
                'lastname'  => 'Parker',
            ),
        ),
    );

    /**
     * Customers and addresses after import, address ID is postcode
     *
     * @var array
     */
    protected $_afterImport = array(
        'betsyparker@example.com'   => array(
            'addresses' => array('19107', '72701', '19108'),
            'data' => array(
                'firstname' => 'NotBetsy',
                'lastname'  => 'NotParker',
            ),
        ),
        'anthonyanealy@magento.com' => array('addresses' => array('72701', '92664')),
        'loribbanks@magento.com'    => array('addresses' => array('98801')),
        'kellynilson@magento.com'   => array('addresses' => array()),
    );

    public function setUp()
    {
        $this->_entityAdapter = new Mage_ImportExport_Model_Import_Entity_CustomerComposite();
    }

    public function tearDown()
    {
        unset($this->_entityAdapter);
    }

    /**
     * Test import data method
     *
     * @magentoDataFixture Mage/ImportExport/_files/customers_for_address_import.php
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::_importData
     */
    public function testImportData()
    {
        // set add/update behavior
        $this->_entityAdapter->setParameters(array('behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE));

        // set fixture CSV file and run validation for add/update behavior
        $result = $this->_entityAdapter
            ->setSource(Mage_ImportExport_Model_Import_Adapter::findAdapterFor(
                __DIR__ . '/_files/customer_composite_update.csv'
            ))
            ->isDataValid();
        $this->assertFalse($result);   // row #6 has no website

        // assert validation errors
        // can't use error codes because entity adapter gathers only error messages from aggregated adapters
        $actualErrors = array_values($this->_entityAdapter->getErrorMessages());
        $this->assertEquals(array(array(6)), $actualErrors);

        // assert data before import
        $this->_assertCustomerData($this->_beforeImport);

        // import data with add/update behavior
        $this->_entityAdapter->importData();

        // assert data after import
        $this->_assertCustomerData($this->_afterImport);

        // reset entity adapter
        unset($this->_entityAdapter);
        $this->_entityAdapter = new Mage_ImportExport_Model_Import_Entity_CustomerComposite();

        // set delete behavior
        $this->_entityAdapter->setParameters(array('behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_DELETE));

        // set fixture CSV file and run validation for delete behavior
        $result = $this->_entityAdapter
            ->setSource(Mage_ImportExport_Model_Import_Adapter::findAdapterFor(
                __DIR__ . '/_files/customer_composite_delete.csv'
            ))
            ->isDataValid();
        $this->assertTrue($result);

        // assert error messages
        $this->assertEmpty($this->_entityAdapter->getErrorMessages());

        // import data with delete behavior
        $this->_entityAdapter->importData();

        // assert data after import
        $this->_assertCustomerData(array());
    }

    /**
     * Assertion of current customer and address data
     *
     * @param array $expectedData
     */
    protected function _assertCustomerData(array $expectedData)
    {
        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection');

        $collection->addAttributeToSelect($this->_customerAttributes);
        $customers = $collection->getItems();

        $this->assertSameSize($expectedData, $customers);

        /** @var $customer Mage_Customer_Model_Customer */
        foreach ($customers as $customer) {
            // assert customer existence
            $email = strtolower($customer->getEmail());
            $this->assertArrayHasKey($email, $expectedData);

            // assert customer data (only for required customers)
            if (isset($expectedData[$email]['data'])) {
                foreach ($expectedData[$email]['data'] as $attribute => $expectedValue) {
                    $this->assertEquals($expectedValue, $customer->getData($attribute));
                }
            }

            // assert address data
            $addresses = $customer->getAddresses();
            $this->assertSameSize($expectedData[$email]['addresses'], $addresses);
            /** @var $address Mage_Customer_Model_Address */
            foreach ($addresses as $address) {
                $this->assertContains($address->getData('postcode'), $expectedData[$email]['addresses']);
            }
        }
    }
}
