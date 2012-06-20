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
 *
 * @magentoDataFixture Mage/ImportExport/_files/customer_with_addresses.php
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
     * Test constructor
     */
    public function testConstruct()
    {
        $entityAdapter = Mage::getModel($this->_testClassName);

        // check entity table
        $entityReflection = new ReflectionProperty($this->_testClassName, '_entityTable');
        $entityReflection->setAccessible(true);
        $entityTable = $entityReflection->getValue($entityAdapter);
        $this->assertInternalType('string', $entityTable, 'Entity table must be a string.');
        $this->assertNotEmpty($entityTable, 'Entity table must not be empty');

        // check message templates
        $templatesReflection = new ReflectionProperty($this->_testClassName, '_messageTemplates');
        $templatesReflection->setAccessible(true);
        $templates = $templatesReflection->getValue($entityAdapter);
        $this->assertInternalType('array', $templates, 'Templates must be an array.');
        $this->assertNotEmpty($templates, 'Templates must not be empty.');

        // check attributes
        $attributesReflection = new ReflectionProperty($this->_testClassName, '_attributes');
        $attributesReflection->setAccessible(true);
        $attributes = $attributesReflection->getValue($entityAdapter);
        $this->assertInternalType('array', $attributes, 'Attributes must be an array.');
        $this->assertNotEmpty($attributes, 'Attributes must not be empty.');

        // check addresses
        $addressesReflection = new ReflectionProperty($this->_testClassName, '_addresses');
        $addressesReflection->setAccessible(true);
        $addresses = $addressesReflection->getValue($entityAdapter);
        $this->assertInternalType('array', $addresses, 'Addresses must be an array.');
        $this->assertNotEmpty($addresses, 'Addresses must not be empty.');

        // check country regions adn regions
        $countriesReflection = new ReflectionProperty($this->_testClassName, '_countryRegions');
        $countriesReflection->setAccessible(true);
        $countryRegions = $countriesReflection->getValue($entityAdapter);
        $this->assertInternalType('array', $countryRegions, 'Country regions must be an array.');
        $this->assertNotEmpty($countryRegions, 'Country regions must not be empty.');

        $regionsReflection = new ReflectionProperty($this->_testClassName, '_regions');
        $regionsReflection->setAccessible(true);
        $regions = $regionsReflection->getValue($entityAdapter);
        $this->assertInternalType('array', $regions, 'Regions must be an array.');
        $this->assertNotEmpty($regions, 'Regions must not be empty.');
    }

    /**
     * Test _initAddresses
     */
    public function testInitAddresses()
    {
        $entityAdapter = Mage::getModel($this->_testClassName);

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
        $initAddresses->invoke($entityAdapter);

        // check addresses
        $addressesReflection = new ReflectionProperty($this->_testClassName, '_addresses');
        $addressesReflection->setAccessible(true);
        $testAddresses = $addressesReflection->getValue($entityAdapter);
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
     */
    public function testSaveAddressEntities()
    {
        $entityAdapter = Mage::getModel($this->_testClassName);

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

        // check DB
        $testAddress = Mage::getModel('Mage_Customer_Model_Address');
        $testAddress->load($addressId);
        $this->assertEquals($addressId, $testAddress->getId(), 'Incorrect address ID.');
        $this->assertEquals($customerId, $testAddress->getParentId(), 'Incorrect address customer ID.');
    }
}
