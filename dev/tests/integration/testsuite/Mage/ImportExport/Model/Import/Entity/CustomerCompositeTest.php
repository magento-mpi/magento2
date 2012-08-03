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
     * Important data from address_import_update.csv (postcode is key)
     *
     * @var array
     */
    protected $_updateData = array(
        'address' => array( // address records
            'update'            => '19107',  // address with updates
            'new'               => '85034',  // new address
            'no_customer'       => '33602',  // there is no customer with this primary key (email+website)
            'new_no_address_id' => '32301',  // new address without address id
        ),
        'update'  => array( // this data is changed in CSV file
            '19107' => array(
                'firstname'  => 'Katy',
                'middlename' => 'T.',
            ),
        ),
        'remove'  => array( // this data is not set in CSV file
            '19107' => array(
                'city'   => 'Philadelphia',
                'region' => 'Pennsylvania',
            ),
        ),
        'default' => array( // new default billing/shipping addresses
            'billing'  => '19108',
            'shipping' => '19108',
        ),
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
     * Test import data method with add/update behaviour
     *
     * @magentoDataFixture Mage/ImportExport/_files/customers_for_address_import.php
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::_importData
     * @todo finish implementation
     */
    public function testImportDataAddUpdate()
    {
        $this->markTestIncomplete('Not implemented yet');
        // set behaviour
        $this->_entityAdapter->setParameters(
            array('behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE)
        );

        // set fixture CSV file
        $sourceFile = __DIR__ . '/_files/customer_composite.csv';
        $result = $this->_entityAdapter
            ->setSource(Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile))
            ->isDataValid();
        $this->assertFalse($result, 'Validation result must be false.');

        // are default billing/shipping addresses have new value
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->setWebsiteId(0);
        $customer->loadByEmail('BetsyParker@example.com');

        $this->assertCount(2, $customer->getAddresses());

        // import data
        $this->_entityAdapter->importData();

        $keyAttribute = 'postcode';

        // are default billing/shipping addresses have new value
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->setWebsiteId(0);
        $customer->loadByEmail('BetsyParker@example.com');

        $this->assertCount(3, $customer->getAddresses());

        $defaultsData = $this->_updateData['default'];
        $this->assertEquals(
            $defaultsData['billing'],
            $customer->getDefaultBillingAddress()->getData($keyAttribute),
            'Incorrect default billing address'
        );
        $this->assertEquals(
            $defaultsData['shipping'],
            $customer->getAddresses()->getData($keyAttribute),
            'Incorrect default shipping address'
        );
    }
}
