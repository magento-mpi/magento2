<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for customer address API2 (admin)
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Address_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Lazy loaded address eav required attributes
     *
     * @var null|array
     */
    protected $_requiredAttributes = array();

    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_Test_Webservice::deleteFixture('customer', true);

        parent::tearDown();
    }

    /**
     * Test create customer address
     *
     * @param array $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @resourceOperation customer_address::create
     * @dataProvider providerAddressData
     */
    public function testCreateCustomerAddress($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        list($addressId) = array_reverse(explode('/', $restResponse->getHeader('Location')));
        /* @var $createdCustomerAddress Mage_Customer_Model_Address */
        $createdCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')->load($addressId);
        $this->assertGreaterThan(0, $createdCustomerAddress->getId());

        foreach ($dataForCreate as $field => $value) {
            if ('street' == $field) {
                $streets = explode("\n", $createdCustomerAddress->getData('street'));
                $this->assertEquals($dataForCreate['street'][0], $streets[0]);
                $this->assertEquals(implode(
                        Mage_Customer_Model_Api2_Customer_Address_Validator::STREET_SEPARATOR,
                        array_slice($dataForCreate['street'], 1)
                    ),
                    $streets[1]
                );
                continue;
            }
            $this->assertEquals($value, $createdCustomerAddress->getData($field));
        }

        $this->addModelToDelete($createdCustomerAddress, true);
    }

    /**
     * Test unsuccessful address create with missing required fields
     *
     * @param string $attributeCode
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerRequiredAttributes
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithMissingRequiredFields($attributeCode)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');
        $dataForCreate = $this->_getAddressData();

        // Remove required field
        unset($dataForCreate[$attributeCode]);

        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertGreaterThanOrEqual(1, count($responseData['messages']['error']));

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test unsuccessful address create with empty required fields
     *
     * @param string $attributeCode
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerRequiredAttributes
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithEmptyRequiredFields($attributeCode)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');
        $dataForCreate = $this->_getAddressData();

        // Set required field as empty
        $dataForCreate[$attributeCode] = NULL;

        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertGreaterThanOrEqual(1, count($responseData['messages']['error']));

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test unsuccessful address create with invalid country identifier
     *
     * @param array $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithInvalidCountryIdentifier($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        $dataForCreate['country_id'] = array('testsdata');
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid value type for country_id', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with country identifier as spaces
     *
     * @param array $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithCountryIdentifierAsSpaces($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        $dataForCreate['country_id'] = '   ';
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid value "   " for country_id', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with unavailable country
     *
     * @param array $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithUnavailableCountry($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        $dataForCreate['country_id'] = '_C';
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid value "_C" for country_id', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with empty region when region is required
     *
     * @param array $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithEmtyRegionWhenRegionIsRequired($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        $dataForCreate['country_id'] = 'US'; // for US region is required
        unset($dataForCreate['region']);
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('"State/Province" is required.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with invalid region when region is required
     *
     * @param array $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithInvalidRegionWhenRegionIsRequired($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        $dataForCreate['country_id'] = 'US'; // for US region is required
        $dataForCreate['region'] = 123;
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid "State/Province" type.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with unavailable region when region is required
     *
     * @param array $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithUnavailableRegionWhenRegionIsRequired($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        $dataForCreate['country_id'] = 'US'; // for US region is required
        $dataForCreate['region'] = 'INVALID_REGION';
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('State/Province does not exist.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with invalid region when region is not required
     *
     * @param array $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressWithInvalidRegionWhenRegionIsNotRequired($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        $dataForCreate['country_id'] = 'UA'; // for UA region is not required
        $dataForCreate['region'] = 123;
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid "State/Province" type.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test filter data in create customer address
     *
     * @param $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testFilteringInCreateCustomerAddress($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        /* @var $attribute Mage_Customer_Model_Entity_Attribute */
        $attribute = Mage::getSingleton('Mage_Eav_Model_Config')->getAttribute('customer_address', 'firstname');
        $currentInputFilter = $attribute->getInputFilter();
        $attribute->setInputFilter('striptags')->save();

        // Set data for filtering
        $dataForCreate['firstname'] = 'testFirstname<b>Test</b>';

        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        list($addressId) = array_reverse(explode('/', $restResponse->getHeader('Location')));
        /* @var $createdCustomerAddress Mage_Customer_Model_Address */
        $createdCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')
            ->load($addressId);
        $this->assertEquals('testFirstnameTest', $createdCustomerAddress->getData('firstname'));

        // Restore data
        $attribute->setInputFilter($currentInputFilter)->save();
    }

    /**
     * Test get customer address for admin
     *
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @resourceOperation customer_address::get
     */
    public function testGetCustomerAddress()
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $restResponse = $this->callGet('customers/addresses/' . $fixtureCustomerAddress->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        foreach ($responseData as $field => $value) {
            if ($field == 'street') {
                $this->assertEquals($value, explode("\n", $fixtureCustomerAddress->getData('street')));
                continue;
            }
            $this->assertEquals($value, $fixtureCustomerAddress->getData($field));
        }
    }

    /**
     * Test get customer addresses for admin
     *
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @resourceOperation customer_address::get
     */
    public function testGetCustomerAddresses()
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        $restResponse = $this->callGet('customers/' . $fixtureCustomer->getId() . '/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $customerAddressesIds = array();
        foreach ($responseData as $customerAddress) {
            $customerAddressesIds[] = $customerAddress['entity_id'];
        }
        /* @var $fixtureCustomerAddresses Mage_Customer_Model_Resource_Address_Collection */
        $fixtureCustomerAddresses = $fixtureCustomer->getAddressesCollection();
        foreach ($fixtureCustomerAddresses as $fixtureCustomerAddress) {
            $this->assertContains($fixtureCustomerAddress->getId(), $customerAddressesIds);
        }
    }

    /**
     * Test successful update customer address
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddress($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedCustomerAddress Mage_Customer_Model_Address */
        $updatedCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')
            ->load($fixtureCustomerAddress->getId());

        $updatedData = $updatedCustomerAddress->getData();
        foreach ($dataForUpdate as $field => $value) {
            if ('street' == $field) {
                $streets = explode("\n", $updatedCustomerAddress->getData('street'));
                $this->assertEquals($dataForUpdate['street'][0], $streets[0]);
                $this->assertEquals(
                    implode(
                        Mage_Customer_Model_Api2_Customer_Address_Validator::STREET_SEPARATOR,
                        array_slice($dataForUpdate['street'], 1)
                    ),
                    $streets[1]
                );
                continue;
            }
            $this->assertEquals($value, $updatedCustomerAddress->getData($field));
        }
    }

    /**
     * Test successful update customer address with partial data
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithPartialData($dataForUpdate)
    {
        $dataForUpdate = array_slice($dataForUpdate, count($dataForUpdate)/2);

        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedCustomerAddress Mage_Customer_Model_Address */
        $updatedCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')
            ->load($fixtureCustomerAddress->getId());

        $updatedData = $updatedCustomerAddress->getData();
        foreach ($dataForUpdate as $field => $value) {
            if ('street' == $field) {
                $streets = explode("\n", $updatedCustomerAddress->getData('street'));
                $this->assertEquals($dataForUpdate['street'][0], $streets[0]);
                $this->assertEquals(
                    implode(
                        Mage_Customer_Model_Api2_Customer_Address_Validator::STREET_SEPARATOR,
                        array_slice($dataForUpdate['street'], 1)
                    ),
                    $streets[1]
                );
                continue;
            }
            $this->assertEquals($value, $updatedCustomerAddress->getData($field));
        }
    }

    /**
     * Test successful update customer address country association when region is required (WITH passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressCountryAssociationWhenRegionIsRequiredCase1($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = 'US'; // for US region is required
        $dataForUpdate['region'] = 'New York';
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
    }

    /**
     * Test successful update customer address country association when region is required (WITHOUT passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressCountryAssociationWhenRegionIsRequiredCase2($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        unset($dataForUpdate['country_id']); // for US (default country for fixture) region is required
        $dataForUpdate['region'] = 'New York';
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
    }

    /**
     * Test successful update customer address country association when region is not required (WITH passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressCountryAssociationWhenRegionIsNotRequiredCase1($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = 'UA'; // for UA region is not required
        $dataForUpdate['region'] = 'New York';
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
    }

    /**
     * Test successful update customer address country association when region is not required
     * (WITHOUT passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressCountryAssociationWhenRegionIsNotRequiredCase2($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $fixtureCustomerAddress->setCountryId('UA')->save();

        unset($dataForUpdate['country_id']); // for UA (current country for fixture) region is NOT required
        $dataForUpdate['region'] = 'New York';
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
    }

    /**
     * Test successful update customer address country association when region is not required (WITH passed country_id
     * and WITHOUT passed region)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressCountryAssociationWhenRegionIsNotRequiredCase3($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = 'UA'; // for UA region is not required
        unset($dataForUpdate['region']);
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful address update with empty required fields
     *
     * @param string $attributeCode
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerRequiredAttributes
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithEmptyRequiredFields($attributeCode)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $dataForUpdate = $this->_getAddressData();

        // Set the required field as empty
        $dataForUpdate[$attributeCode] = NULL;

        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertGreaterThanOrEqual(1, count($responseData['messages']['error']));

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test unsuccessful address update with invalid country identifier
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithInvalidCountryIdentifier($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = array('testsdata');
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid value type for country_id', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address update with country identifier as spaces
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithCountryIdentifierAsSpaces($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = '   ';
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid value "   " for country_id', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address update with unavailable country
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithUnavailableCountry($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = '_C';
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid value "_C" for country_id', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with empty region when region is required (WITH passed country_id)
     * If country_id not passed too then it is successful update
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithEmtyRegionWhenRegionIsRequired($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = 'US'; // for US region is required
        unset($dataForUpdate['region']);
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('"State/Province" is required.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with invalid region when region is required (WITH passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithInvalidRegionWhenRegionIsRequiredCase1($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = 'US'; // for US region is required
        $dataForUpdate['region'] = 123;
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid "State/Province" type.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with invalid region when region is not required (WITHOUT passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithInvalidRegionWhenRegionIsRequiredCase2($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        unset($dataForUpdate['country_id']); // for US (default country for fixture) region is required
        $dataForUpdate['region'] = 123;
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid "State/Province" type.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with unavailable region when region is required (WITH passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithUnavailableRegionWhenRegionIsRequiredCase1($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = 'US'; // for US region is required
        $dataForUpdate['region'] = 'INVALID_REGION';
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('State/Province does not exist.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with unavailable region when region is required (WITHOUT passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithUnavailableRegionWhenRegionIsRequiredCase2($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        unset($dataForUpdate['country_id']); // for US (default country for fixture) region is required
        $dataForUpdate['region'] = 'INVALID_REGION';
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('State/Province does not exist.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with invalid region when region is not required (WITH passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithInvalidRegionWhenRegionIsNotRequiredCase1($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        $dataForUpdate['country_id'] = 'UA'; // for UA region is not required
        $dataForUpdate['region'] = 123;
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid "State/Province" type.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test unsuccessful address create with invalid region when region is not required (WITHOUT passed country_id)
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithInvalidRegionWhenRegionIsNotRequiredCase2($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $fixtureCustomerAddress->setCountryId('UA')->save();

        unset($dataForUpdate['country_id']); // for UA (current country for fixture) region is NOT required
        $dataForUpdate['region'] = 123;
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertEquals('Invalid "State/Province" type.', $responseData['messages']['error'][0]['message']);
    }

    /**
     * Test filter data in update customer address
     *
     * @param $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testFilteringInUpdateCustomerAddress($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();

        /* @var $attribute Mage_Customer_Model_Entity_Attribute */
        $attribute = Mage::getSingleton('Mage_Eav_Model_Config')->getAttribute('customer_address', 'firstname');
        $currentInputFilter = $attribute->getInputFilter();
        $attribute->setInputFilter('striptags')->save();

        // Set data for filtering
        $dataForUpdate['firstname'] = 'testFirstname<b>Test</b>';

        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedCustomerAddress Mage_Customer_Model_Address */
        $updatedCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')
            ->load($fixtureCustomerAddress->getId());
        $this->assertEquals('testFirstnameTest', $updatedCustomerAddress->getData('firstname'));

        // Restore data
        $attribute->setInputFilter($currentInputFilter)->save();
    }

    /**
     * Test delete address
     *
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @resourceOperation customer_address::delete
     */
    public function testDeleteCustomerAddress()
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $restResponse = $this->callDelete('customers/addresses/' . $fixtureCustomerAddress->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = Mage::getModel('Mage_Customer_Model_Address')->load($fixtureCustomerAddress->getId());
        $this->assertEmpty($customerAddress->getId());
    }

    /**
     * Test actions for not existing resources
     * @resourceOperation customer_address::get
     * @resourceOperation customer_address::update
     * @resourceOperation customer_address::delete
     */
    public function testActionsForUnavailableResorces()
    {
        // data is not empty
        $data = array('firstname' => 'test firstname');

        // get
        $restResponse = $this->callGet('customers/addresses/invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        // put
        $restResponse = $this->callPut('customers/addresses/invalid_id', $data);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        // delete
        $restResponse = $this->callDelete('customers/addresses/invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Data provider address data
     * Support for custom eav attributes are not implemented
     *
     * @dataSetNumber 1
     * @return array
     */
    public function providerAddressData()
    {
        return array(array($this->_getAddressData()));
    }

    /**
     * Data provider required attributes (dataSet number depends of number of required attributes)
     *
     * @dataSetNumber 7
     * @return array
     */
    public function providerRequiredAttributes()
    {
        $output = array();
        foreach ($this->_getAddressEavRequiredAttributes() as $attributeCode => $requiredAttribute) {
            $output[] = array($attributeCode);
        }
        return $output;
    }

    /**
     * Get address data
     *
     * @return array
     */
    protected function _getAddressData()
    {
        /* @var $customerAddressFixture Mage_Customer_Model_Address */
        $customerAddressFixture = require TEST_FIXTURE_DIR . '/_block/Customer/Address.php';
        $data = array_intersect_key($customerAddressFixture->getData(), array_flip(array(
            'city', 'country_id', 'firstname', 'lastname', 'postcode', 'region', 'street', 'telephone'
        )));
        $data['street'] = array(
            'Main Street' . uniqid(),
            'Addithional Street 1' . uniqid(),
            'Addithional Street 2' . uniqid()
        );

        // Get address eav required attributes
        foreach ($this->_getAddressEavRequiredAttributes() as $attributeCode => $requiredAttribute) {
            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = $requiredAttribute . uniqid();
            }
        }

        return $data;
    }

    /**
     * Get address eav required attributes
     *
     * @return array
     */
    protected function _getAddressEavRequiredAttributes()
    {
        if (null !== $this->_requiredAttributes) {
            $this->_requiredAttributes = array();
            /* @var $address Mage_Customer_Model_Address */
            $address = Mage::getModel('Mage_Customer_Model_Address');
            foreach ($address->getAttributes() as $attribute) {
                // admin can see all attributes
                if ($attribute->getIsRequired()) {
                    $this->_requiredAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
                }
            }
        }
        return $this->_requiredAttributes;
    }
}
