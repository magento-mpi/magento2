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
 * Test class for Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested class name
     *
     * @var string
     */
    protected $_testClassName = 'Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address';

    /**
     * Fixture key from fixture
     *
     * @var string
     */
    protected $_fixtureKey = '_fixture/Mage_ImportExport_Customers_Array';

    /**
     * Address entity adapter instance
     *
     * @var Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
     */
    protected $_entityAdapter;

    /**
     * Init new instance of address entity adapter
     */
    public function setUp()
    {
        parent::setUp();
        $this->_entityAdapter = Mage::getModel($this->_testClassName);
    }

    /**
     * Unset entity adapter
     */
    public function tearDown()
    {
        unset($this->_entityAdapter);
        parent::tearDown();
    }

    /**
     * Test constructor
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     */
    public function testConstruct()
    {
        // check entity table
        $entityReflection = new ReflectionProperty($this->_testClassName, '_entityTable');
        $entityReflection->setAccessible(true);
        $entityTable = $entityReflection->getValue($this->_entityAdapter);
        $this->assertInternalType('string', $entityTable, 'Entity table must be a string.');
        $this->assertNotEmpty($entityTable, 'Entity table must not be empty');

        // check message templates
        $templatesReflection = new ReflectionProperty($this->_testClassName, '_messageTemplates');
        $templatesReflection->setAccessible(true);
        $templates = $templatesReflection->getValue($this->_entityAdapter);
        $this->assertInternalType('array', $templates, 'Templates must be an array.');
        $this->assertNotEmpty($templates, 'Templates must not be empty.');

        // check attributes
        $attributesReflection = new ReflectionProperty($this->_testClassName, '_attributes');
        $attributesReflection->setAccessible(true);
        $attributes = $attributesReflection->getValue($this->_entityAdapter);
        $this->assertInternalType('array', $attributes, 'Attributes must be an array.');
        $this->assertNotEmpty($attributes, 'Attributes must not be empty.');

        // check addresses
        $addressesReflection = new ReflectionProperty($this->_testClassName, '_addresses');
        $addressesReflection->setAccessible(true);
        $addresses = $addressesReflection->getValue($this->_entityAdapter);
        $this->assertInternalType('array', $addresses, 'Addresses must be an array.');
        $this->assertNotEmpty($addresses, 'Addresses must not be empty.');

        // check country regions adn regions
        $countriesReflection = new ReflectionProperty($this->_testClassName, '_countryRegions');
        $countriesReflection->setAccessible(true);
        $countryRegions = $countriesReflection->getValue($this->_entityAdapter);
        $this->assertInternalType('array', $countryRegions, 'Country regions must be an array.');
        $this->assertNotEmpty($countryRegions, 'Country regions must not be empty.');

        $regionsReflection = new ReflectionProperty($this->_testClassName, '_regions');
        $regionsReflection->setAccessible(true);
        $regions = $regionsReflection->getValue($this->_entityAdapter);
        $this->assertInternalType('array', $regions, 'Regions must be an array.');
        $this->assertNotEmpty($regions, 'Regions must not be empty.');
    }

    /**
     * Test _initAddresses
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     */
    public function testInitAddresses()
    {
        // get addressed from fixture
        $customers = Mage::registry($this->_fixtureKey);
        $correctAddresses = array();
        /** @var $customer Mage_Customer_Model_Customer */
        foreach ($customers as $customer) {
            $correctAddresses[$customer->getId()] = array();
            /** @var $address Mage_Customer_Model_Address */
            foreach ($customer->getAddressesCollection() as $address) {
                $correctAddresses[$customer->getId()][] = $address->getId();
            }
        }

        // invoke _initAddresses
        $initAddresses = new ReflectionMethod($this->_testClassName, '_initAddresses');
        $initAddresses->setAccessible(true);
        $initAddresses->invoke($this->_entityAdapter);

        // check addresses
        $addressesReflection = new ReflectionProperty($this->_testClassName, '_addresses');
        $addressesReflection->setAccessible(true);
        $testAddresses = $addressesReflection->getValue($this->_entityAdapter);
        $this->assertInternalType('array', $testAddresses, 'Addresses must be an array.');
        $this->assertNotEmpty($testAddresses, 'Addresses must not be empty.');

        $correctCustomerIds = array_keys($correctAddresses);
        $testCustomerIds = array_keys($testAddresses);
        sort($correctCustomerIds);
        sort($testCustomerIds);
        $this->assertEquals($correctCustomerIds, $testCustomerIds, 'Incorrect customer IDs in addresses array.');

        foreach ($correctCustomerIds as $customerId) {
            $this->assertInternalType('array', $correctAddresses[$customerId], 'Addresses must be an array.');
            $correctAddressIds = $correctAddresses[$customerId];
            $testAddressIds = $testAddresses[$customerId];
            sort($correctAddressIds);
            sort($testAddressIds);
            $this->assertEquals($correctAddressIds, $testAddressIds, 'Incorrect addresses IDs.');
        }
    }

    /**
     * Test _saveAddressEntity
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     */
    public function testSaveAddressEntities()
    {
        // invoke _saveAddressEntities
        list($customerId, $addressId) = $this->_addTestAddress($this->_entityAdapter);

        // check DB
        $testAddress = Mage::getModel('Mage_Customer_Model_Address');
        $testAddress->load($addressId);
        $this->assertEquals($addressId, $testAddress->getId(), 'Incorrect address ID.');
        $this->assertEquals($customerId, $testAddress->getParentId(), 'Incorrect address customer ID.');
    }

    /**
     * Add new test address for existing customer
     *
     * @param Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address $entityAdapter
     * @return array (customerID, addressID)
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     */
    protected function _addTestAddress(Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address $entityAdapter)
    {
        $customers = Mage::registry($this->_fixtureKey);
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = reset($customers);
        $customerId = $customer->getId();

        /** @var $resource Mage_Customer_Model_Address */
        $resource  = Mage::getModel('Mage_Customer_Model_Address');
        $table     = $resource->getResource()->getEntityTable();
        $addressId = Mage::getResourceHelper('Mage_ImportExport')->getNextAutoincrement($table);

        $entityData = array(
            'entity_id'      => $addressId,
            'entity_type_id' => $resource->getEntityTypeId(),
            'parent_id'      => $customerId,
            'created_at'     => now(),
            'updated_at'     => now()
        );

        // invoke _saveAddressEntities
        $saveAddressEntities = new ReflectionMethod($this->_testClassName, '_saveAddressEntities');
        $saveAddressEntities->setAccessible(true);
        $saveAddressEntities->invoke($entityAdapter, $entityData);

        return array($customerId, $addressId);
    }

    /**
     * Test _saveAddressAttributes
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     */
    public function testSaveAddressAttributes()
    {
        // get attributes list
        $attributesReflection = new ReflectionProperty($this->_testClassName, '_attributes');
        $attributesReflection->setAccessible(true);
        $attributes = $attributesReflection->getValue($this->_entityAdapter);

        // get some attribute
        $attributeName = 'city';
        $this->assertArrayHasKey($attributeName, $attributes, 'Key "' . $attributeName . '" should be an attribute.');
        $attributeParams = $attributes[$attributeName];
        $this->assertArrayHasKey('id', $attributeParams, 'Attribute must have an ID.');
        $this->assertArrayHasKey('table', $attributeParams, 'Attribute must have a table.');

        // create new address with attributes
        $data = $this->_addTestAddress($this->_entityAdapter);
        $addressId = $data[1];
        $attributeId = $attributeParams['id'];
        $attributeTable = $attributeParams['table'];
        $attributeValue = 'Test City';

        $attributeArray = array();
        $attributeArray[$attributeTable][$addressId][$attributeId] = $attributeValue;

        // invoke _saveAddressAttributes
        $saveAttributes = new ReflectionMethod($this->_testClassName, '_saveAddressAttributes');
        $saveAttributes->setAccessible(true);
        $saveAttributes->invoke($this->_entityAdapter, $attributeArray);

        // check DB
        /** @var $testAddress Mage_Customer_Model_Address */
        $testAddress = Mage::getModel('Mage_Customer_Model_Address');
        $testAddress->load($addressId);
        $this->assertEquals($addressId, $testAddress->getId(), 'Incorrect address ID.');
        $this->assertEquals($attributeValue, $testAddress->getData($attributeName), 'There is no attribute value.');
    }

    /**
     * Test _saveCustomerDefaults
     *
     * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
     */
    public function testSaveCustomerDefaults()
    {
        $this->_entityAdapter = Mage::getModel($this->_testClassName);

        // get not default address
        $customers = Mage::registry($this->_fixtureKey);
        /** @var $notDefaultAddress Mage_Customer_Model_Address */
        $notDefaultAddress = null;
        /** @var $addressCustomer Mage_Customer_Model_Customer */
        $addressCustomer = null;
        /** @var $customer Mage_Customer_Model_Customer */
        foreach ($customers as $customer) {
            /** @var $address Mage_Customer_Model_Address */
            foreach ($customer->getAddressesCollection() as $address) {
                if (!$customer->getDefaultBillingAddress() && !$customer->getDefaultShippingAddress()) {
                    $notDefaultAddress = $address;
                    $addressCustomer = $customer;
                    break;
                }
                if ($notDefaultAddress) {
                    break;
                }
            }
        }
        $this->assertNotNull($notDefaultAddress, 'Not default address must exists.');
        $this->assertNotNull($addressCustomer, 'Not default address customer must exists.');

        $addressId  = $notDefaultAddress->getId();
        $customerId = $addressCustomer->getId();

        // set customer defaults
        $defaults = array();
        foreach (Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address::getDefaultAddressAttributeMapping()
            as $customerAttributeCode) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            $attribute = $addressCustomer->getAttribute($customerAttributeCode);
            $attributeTable = $attribute->getBackend()->getTable();
            $attributeId = $attribute->getId();
            $defaults[$attributeTable][$customerId][$attributeId] = $addressId;
        }

        // invoke _saveCustomerDefaults
        $saveDefaults = new ReflectionMethod($this->_testClassName, '_saveCustomerDefaults');
        $saveDefaults->setAccessible(true);
        $saveDefaults->invoke($this->_entityAdapter, $defaults);

        // check DB
        /** @var $testCustomer Mage_Customer_Model_Customer */
        $testCustomer = Mage::getModel('Mage_Customer_Model_Customer');
        $testCustomer->load($customerId);
        $this->assertEquals($customerId, $testCustomer->getId(), 'Customer must exists.');
        $this->assertNotNull($testCustomer->getDefaultBillingAddress(), 'Default billing address must exists.');
        $this->assertNotNull($testCustomer->getDefaultShippingAddress(), 'Default shipping address must exists.');
        $this->assertEquals(
            $addressId,
            $testCustomer->getDefaultBillingAddress()->getId(),
            'Incorrect default billing address.'
        );
        $this->assertEquals(
            $addressId,
            $testCustomer->getDefaultShippingAddress()->getId(),
            'Incorrect default shipping address.'
        );
    }

    /**
     * Test attribute collection getter
     */
    public function testGetAttributeCollection()
    {
        $getCollection = new ReflectionMethod($this->_testClassName, '_getAttributeCollection');
        $getCollection->setAccessible(true);
        $collection = $getCollection->invoke($this->_entityAdapter);
        $this->assertInstanceOf(
            'Mage_Customer_Model_Resource_Address_Attribute_Collection',
            $collection,
            'Incorrect attribute collection class.'
        );
    }

    /**
     * Test import data method
     *
     * @magentoDataFixture Mage/ImportExport/Model/Import/Entity/V2/_files/customers_for_address_import.php
     */
    public function testImportData()
    {
        // set fixture CSV file
        $sourceFile = __DIR__ . '/../../_files/address_import.csv';
        $result = $this->_entityAdapter
            ->setSource(Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile))
            ->isDataValid();
        $this->assertFalse($result, 'Validation result must be false.');

        // fixture registry keys
        $fixtureCustomer = '_fixture/Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest_Customer';
        $fixtureCsv      = '_fixture/Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AddressTest_Csv';

        // get customer
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::registry($fixtureCustomer);
        $customerId = $customer->getId();

        // get csv fixture data
        $csvData = Mage::registry($fixtureCsv);

        // import data
        $importData = new ReflectionMethod($this->_testClassName, '_importData');
        $importData->setAccessible(true);
        $importData->invoke($this->_entityAdapter);

        // get addresses
        /** @var $addressCollection Mage_Customer_Model_Resource_Address_Collection */
        $addressCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Collection');
        $addressCollection->addAttributeToSelect('*');
        $addresses = array();
        /** @var $address Mage_Customer_Model_Address */
        foreach ($addressCollection as $address) {
            $addresses[$address->getData('postcode')] = $address;
        }

        // is addresses exists
        $this->assertArrayHasKey($csvData['address']['update'], $addresses, 'Address must exists.');
        $this->assertArrayHasKey($csvData['address']['new'], $addresses, 'Address must exists.');
        $this->assertArrayNotHasKey($csvData['address']['no_customer'], $addresses, 'Address must not exists.');

        // is updated address fields have new values
        $updatedAddressId = $csvData['address']['update'];
        /** @var $updatedAddress Mage_Customer_Model_Address */
        $updatedAddress = $addresses[$updatedAddressId];
        $updatedData = $csvData['update'][$updatedAddressId];
        foreach ($updatedData as $fieldName => $fieldValue) {
            $this->assertEquals($fieldValue, $updatedAddress->getData($fieldName));
        }

        // is removed data fields have old values
        $removedData = $csvData['remove'][$updatedAddressId];
        foreach ($removedData as $fieldName => $fieldValue) {
            $this->assertEquals($fieldValue, $updatedAddress->getData($fieldName));
        }

        // is default billing/shipping addresses are new
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load($customerId);
        $defaultsData = $csvData['default'];
        $this->assertEquals(
            $defaultsData['billing'],
            $customer->getDefaultBillingAddress()->getData('postcode'),
            'Incorrect default billing address'
        );
        $this->assertEquals(
            $defaultsData['shipping'],
            $customer->getDefaultShippingAddress()->getData('postcode'),
            'Incorrect default shipping address'
        );
    }
}
