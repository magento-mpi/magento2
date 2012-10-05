<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mage_ImportExport_Model_Import_Entity_CustomerComposite
 *
 * This test is placed in Enterprise scope because it uses enterprise set of customer attributes,
 * and there is no simple solution to run this test separately for EE and CE
 */
class Enterprise_ImportExport_Model_Import_Entity_CustomerCompositeTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Attributes used in test assertions
     */
    const ATTRIBUTE_CODE_FIRST_NAME = 'firstname';
    const ATTRIBUTE_CODE_LAST_NAME  = 'lastname';
    /**#@-*/

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
    protected $_customerAttributes = array(
        self::ATTRIBUTE_CODE_FIRST_NAME,
        self::ATTRIBUTE_CODE_LAST_NAME,
    );

    /**
     * Customers and addresses before import, address ID is postcode
     *
     * @var array
     */
    protected $_beforeImport = array(
        'betsyparker@example.com' => array(
            'addresses' => array('19107', '72701'),
            'data' => array(
                self::ATTRIBUTE_CODE_FIRST_NAME => 'Betsy',
                self::ATTRIBUTE_CODE_LAST_NAME  => 'Parker',
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
                self::ATTRIBUTE_CODE_FIRST_NAME => 'NotBetsy',
                self::ATTRIBUTE_CODE_LAST_NAME  => 'NotParker',
            ),
        ),
        'anthonyanealy@magento.com' => array('addresses' => array('72701', '92664')),
        'loribbanks@magento.com'    => array('addresses' => array('98801')),
        'kellynilson@magento.com'   => array('addresses' => array()),
    );

    protected function setUp()
    {
        $this->_entityAdapter = new Mage_ImportExport_Model_Import_Entity_CustomerComposite();
    }

    protected function tearDown()
    {
        unset($this->_entityAdapter);
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

    /**
     * @param string $behavior
     * @param string $sourceFile
     * @param array $dataBefore
     * @param array $dataAfter
     * @param array $errors
     *
     * @magentoDataFixture Mage/ImportExport/_files/customers_for_address_import.php
     * @magentoAppIsolation enabled
     *
     * @dataProvider importDataDataProvider
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::_importData
     */
    public function testImportData($behavior, $sourceFile, array $dataBefore, array $dataAfter, array $errors = array())
    {
        // set entity adapter parameters
        $this->_entityAdapter->setParameters(array('behavior' => $behavior));

        // set fixture CSV file
        $result = $this->_entityAdapter
            ->setSource(Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile))
            ->isDataValid();
        if ($errors) {
            $this->assertFalse($result);
        } else {
            $this->assertTrue($result);
        }

        // assert validation errors
        // can't use error codes because entity adapter gathers only error messages from aggregated adapters
        $actualErrors = array_values($this->_entityAdapter->getErrorMessages());
        $this->assertEquals($errors, $actualErrors);

        // assert data before import
        $this->_assertCustomerData($dataBefore);

        // import data
        $this->_entityAdapter->importData();

        // assert data after import
        $this->_assertCustomerData($dataAfter);
    }

    /**
     * Data provider for testImportData
     *
     * @return array
     */
    public function importDataDataProvider()
    {
        return array(
            'add_update_behavior' => array(
                '$behavior'   => Mage_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE,
                '$sourceFile' => __DIR__ . '/_files/customer_composite_update.csv',
                '$dataBefore' => $this->_beforeImport,
                '$dataAfter'  => $this->_afterImport,
                '$errors'     => array(array(6)),     // row #6 has no website
            ),
            'delete_behavior' => array(
                '$behavior'   => Mage_ImportExport_Model_Import::BEHAVIOR_DELETE,
                '$sourceFile' => __DIR__ . '/_files/customer_composite_delete.csv',
                '$dataBefore' => $this->_beforeImport,
                '$dataAfter'  => array(),
            ),
        );
    }
}
