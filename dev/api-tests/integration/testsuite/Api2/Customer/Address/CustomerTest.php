<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for customer address API2 (customer)
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Address_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Current customer
     *
     * @var Mage_Customer_Model_Customer
     */
    protected static $_currentCustomer;

    /**
     * Identifier of existent default billing address for test customer for backup purposes
     *
     * @var Mage_Customer_Model_Address
     */
    protected static $_customerDefaultBillingAddress;

    /**
     * Identifier of existent default shipping address for test customer for backup purposes
     *
     * @var Mage_Customer_Model_Address
     */
    protected static $_customerDefaultShippingAddress;

    /**
     * Lazy loaded address eav required attributes
     *
     * @var null|array
     */
    protected $_requiredAttributes = array();

    /**
     * Prepare ACL
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_currentCustomer = Mage::getModel('Mage_Customer_Model_Customer')
            ->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);

        // backup default addresses
        self::$_customerDefaultBillingAddress = self::$_currentCustomer->getDefaultBillingAddress();
        self::$_customerDefaultShippingAddress = self::$_currentCustomer->getDefaultShippingAddress();
    }

    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_Test_Webservice::deleteFixture('customer', true);
        $fixtureAddresses = $this->getFixture('addresses');
        if ($fixtureAddresses && count($fixtureAddresses)) {
            foreach ($fixtureAddresses as $fixtureAddress) {
                $this->callModelDelete($fixtureAddress, true);
            }
        }

        parent::tearDown();
    }

    /**
     * Delete acl fixture after test case
     */
    public static function tearDownAfterClass()
    {
        // restore customer addresses
        if (is_object(self::$_customerDefaultBillingAddress)) {
            self::$_customerDefaultBillingAddress->setIsDefaultBilling(true)->save();
        }
        if (is_object(self::$_customerDefaultShippingAddress)) {
            self::$_customerDefaultShippingAddress->setIsDefaultShipping(true)->save();
        }
        parent::tearDownAfterClass();
    }

    /**
     * Test create customer address
     *
     * @param array $dataForCreate
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddress($dataForCreate)
    {
        $restResponse = $this->callPost('customers/' . self::$_currentCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        list($addressId) = array_reverse(explode('/', $restResponse->getHeader('Location')));
        /* @var $createdCustomerAddress Mage_Customer_Model_Address */
        $createdCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')->load($addressId);
        $this->assertGreaterThan(0, $createdCustomerAddress->getId());

        foreach ($dataForCreate as $field => $value) {
            if ('street' == $field) {
                $streets = explode("\n", $createdCustomerAddress->getData('street'));
                $this->assertEquals($dataForCreate['street'][0], $streets[0]);
                $this->assertEquals(
                    implode(
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
     * @dataProvider providerRequiredAttributes
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressMissingRequired($attributeCode)
    {
        $dataForCreate = $this->_getAddressData();

        // Remove required field
        unset($dataForCreate[$attributeCode]);

        $restResponse = $this->callPost('customers/' . self::$_currentCustomer->getId() . '/addresses', $dataForCreate);
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
     * @dataProvider providerRequiredAttributes
     * @resourceOperation customer_address::create
     */
    public function testCreateCustomerAddressEmptyRequired($attributeCode)
    {
        $dataForCreate = $this->_getAddressData();

        // Set required field as empty
        $dataForCreate[$attributeCode] = NULL;

        $restResponse = $this->callPost('customers/' . self::$_currentCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages']);
        $this->assertGreaterThanOrEqual(1, count($responseData['messages']['error']));

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test filter data in create customer address
     *
     * @param $dataForCreate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::create
     */
    public function testFilteringInCreateCustomerAddress($dataForCreate)
    {
        /* @var $attribute Mage_Customer_Model_Entity_Attribute */
        $attribute = Mage::getSingleton('Mage_Eav_Model_Config')->getAttribute('customer_address', 'firstname');

        $attribute->setAttributeModel(null);
        $attribute->setBackendModel(null);

        $currentInputFilter = $attribute->getInputFilter();
        $attribute->setInputFilter('striptags')->save();

        // Set data for filtering
        $dataForCreate['firstname'] = 'testFirstname<b>Test</b>';

        $restResponse = $this->callPost('customers/' . self::$_currentCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        list($addressId) = array_reverse(explode('/', $restResponse->getHeader('Location')));
        /* @var $createdCustomerAddress Mage_Customer_Model_Address */
        $createdCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')
            ->load($addressId);
        $this->assertEquals('testFirstnameTest', $createdCustomerAddress->getData('firstname'));

        // Restore data
        $attribute->setInputFilter($currentInputFilter)->save();

        $this->addModelToDelete($createdCustomerAddress, true);
    }

    /**
     * Test get customer addresses for customer
     *
     * @magentoDataFixture Api2/Customer/Address/_fixtures/add_addresses_to_current_customer.php
     * @resourceOperation customer_address::get
     */
    public function testGetCustomerAddress()
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = end(self::getFixture('addresses'));
        $restResponse = $this->callGet('customers/addresses/' . $fixtureCustomerAddress->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        foreach ($responseData as $field => $value) {
            if ($field == 'street') {
                $this->assertEquals($value, explode("\n", $fixtureCustomerAddress->getData('street')));
                continue;
            }
            $this->assertEquals($value, $fixtureCustomerAddress->getData($field), 'Invalid value for ' . $field);
        }
    }

    /**
     * Test get customer addresses for customer
     *
     * @magentoDataFixture Api2/Customer/Address/_fixtures/add_addresses_to_current_customer.php
     * @resourceOperation customer_address::multiget
     */
    public function testGetCustomerAddresses()
    {
        $restResponse = $this->callGet('customers/' . self::$_currentCustomer->getId() . '/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $customerAddressesIds = array();
        foreach ($responseData as $customerAddress) {
            $customerAddressesIds[] = $customerAddress['entity_id'];
        }
        /* @var $customerAddresses Mage_Customer_Model_Resource_Address_Collection */
        $customerAddresses = self::$_currentCustomer->getAddressesCollection();
        foreach ($customerAddresses as $customerAddress) {
            $this->assertContains($customerAddress->getId(), $customerAddressesIds);
        }
    }

    /**
     * Test update customer address
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddress($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = reset(self::getFixture('addresses'));
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedCustomerAddress Mage_Customer_Model_Address */
        $updatedCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')
            ->load($fixtureCustomerAddress->getId());
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
     * Test update customer address
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithPartialData($dataForUpdate)
    {
        $dataForUpdate = array_slice($dataForUpdate, count($dataForUpdate)/2);

        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = reset(self::getFixture('addresses'));
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedCustomerAddress Mage_Customer_Model_Address */
        $updatedCustomerAddress = Mage::getModel('Mage_Customer_Model_Address')
            ->load($fixtureCustomerAddress->getId());
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
     * Test unsuccessful address update with empty required fields
     *
     * @param string $attributeCode
     * @magentoDataFixture Api2/Customer/Address/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerRequiredAttributes
     * @resourceOperation customer_address::update
     */
    public function testUpdateCustomerAddressWithEmptyRequiredFields($attributeCode)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = reset(self::getFixture('addresses'));
        $dataForUpdate = $this->_getAddressData();

        // Set required field as empty
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
     * Test filter data in update customer address
     *
     * @param $dataForUpdate
     * @magentoDataFixture Api2/Customer/Address/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerAddressData
     * @resourceOperation customer_address::update
     */
    public function testFilteringInUpdateCustomerAddress($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = reset(self::getFixture('addresses'));

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
     * @magentoDataFixture Api2/Customer/Address/_fixtures/add_addresses_to_current_customer.php
     * @resourceOperation customer_address::delete
     */
    public function testDeleteCustomerAddress()
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = reset(self::getFixture('addresses'));
        $restResponse = $this->callDelete('customers/addresses/' . $fixtureCustomerAddress->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = Mage::getModel('Mage_Customer_Model_Address')->load($fixtureCustomerAddress->getId());
        $this->assertEmpty($customerAddress->getId());
    }

    /**
     * Test actions with customer address if customer is not owner
     *
     * @magentoDataFixture Api2/Customer/Address/_fixtures/customer_with_addresses.php
     * @resourceOperation customer_address::get
     * @resourceOperation customer_address::multiget
     * @resourceOperation customer_address::create
     * @resourceOperation customer_address::update
     */
    public function testActionsWithCustomerAddressIfCustomerIsNotOwner()
    {
        // get another customer
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        // get another customer address
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = reset(self::getFixture('addresses'));

        // data is not empty
        $data = array('firstname' => 'test firstname');

        // post address
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $data);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        // get addresses
        $restResponse = $this->callGet('customers/' . $fixtureCustomer->getId() . '/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        // get address
        $restResponse = $this->callGet('customers/addresses/' . $fixtureCustomerAddress->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        // put address
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $data);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test actions for not existing resources
     *
     * @resourceOperation customer_address::multiget
     * @resourceOperation customer_address::create
     * @resourceOperation customer_address::update
     */
    public function testActionsForUnavailableResorces()
    {
        // data is not empty
        $data = array('firstname' => 'test firstname');

        // get
        $restResponse = $this->callGet('customers/invalid_id/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        // put
        $response = $this->callPut('customers/addresses/invalid_id', $data);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());

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
        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');
        /* @var $customerAddressFixture Mage_Customer_Model_Address */
        $customerAddressFixture = require $fixturesDir . '/Customer/Address.php';
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
            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('Mage_Customer_Model_Form');
            // when customer create new address in addressbook used customer_address_edit
            $addressForm->setFormCode('customer_address_edit')->setEntity($address);
            foreach ($addressForm->getAttributes() as $attribute) {
                // customer can see only visibled attributes
                if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
                    $this->_requiredAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
                }
            }
        }
        return $this->_requiredAttributes;
    }
}
