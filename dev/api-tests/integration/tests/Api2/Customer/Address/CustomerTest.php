<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    protected $_currentCustomer = null;

    /**
     * Lazy loaded address eav required attributes
     *
     * @var null|array
     */
    protected $_requiredAttributes = array();

    /**
     * Sets up the current customer
     */
    public function setUp()
    {
        $this->_initCurrentCustomer();

        parent::setUp();
    }

    /**
     * Delete fixtures
     */
    public function tearDown()
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
     * Test create customer address
     *
     * @param array $dataForCreate
     * @dataProvider providerCreateData
     */
    public function testCreateCustomerAddress($dataForCreate)
    {
        $restResponse = $this->callPost('customers/' . $this->_currentCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        list($addressId) = array_reverse(explode('/', $restResponse->getHeader('Location')));
        /* @var $createdCustomerAddress Mage_Customer_Model_Address */
        $createdCustomerAddress = Mage::getModel('customer/address')->load($addressId);
        $this->assertGreaterThan(0, $createdCustomerAddress->getId());

        foreach ($dataForCreate as $field => $value) {
            if ('street' == $field) {
                $streets = explode("\n", $createdCustomerAddress->getData('street'));
                $this->assertEquals($dataForCreate['street'][0], $streets[0]);
                $this->assertEquals(
                    implode(
                        Mage_Customer_Model_Api2_Customer_Address::STREET_SEPARATOR,
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
     */
    public function testCreateCustomerAddressMissingRequired($attributeCode)
    {
        $dataForCreate = $this->_getCreateData();

        // Remove required field
        unset($dataForCreate[$attributeCode]);

        $restResponse = $this->callPost('customers/' . $this->_currentCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages'], "The response doesn't has errors.");
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
     */
    public function testCreateCustomerAddressEmptyRequired($attributeCode)
    {
        $dataForCreate = $this->_getCreateData();

        // Set required field as empty
        $dataForCreate[$attributeCode] = NULL;

        $restResponse = $this->callPost('customers/' . $this->_currentCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages'], "The response doesn't has errors.");
        $this->assertGreaterThanOrEqual(1, count($responseData['messages']['error']));

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test filter data in create customer address
     *
     * @param $dataForCreate
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerCreateData
     */
    public function testFilteringInCreateCustomerAddress($dataForCreate)
    {
        /* @var $attribute Mage_Customer_Model_Entity_Attribute */
        $attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'firstname');
        $currentInputFilter = $attribute->getInputFilter('customer_address', 'firstname');
        $attribute->setInputFilter('striptags')->save();

        // Set data for filtering
        $dataForCreate['firstname'] = 'testFirstname<b>Test</b>';

        $restResponse = $this->callPost('customers/' . $this->_currentCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        list($addressId) = array_reverse(explode('/', $restResponse->getHeader('Location')));
        /* @var $createdCustomerAddress Mage_Customer_Model_Address */
        $createdCustomerAddress = Mage::getModel('customer/address')
            ->load($addressId);
        $this->assertEquals($createdCustomerAddress->getData('firstname'), 'testFirstnameTest');

        // Restore data
        $attribute->setInputFilter($currentInputFilter)->save();
    }

    /**
     * Test get customer addresses for customer
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     */
    public function testGetCustomerAddress()
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->_currentCustomer
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
     * Test get customer addresses for customer
     *
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     */
    public function testGetCustomerAddresses()
    {
        $restResponse = $this->callGet('customers/' . $this->_currentCustomer->getId() . '/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $customerAddressesIds = array();
        foreach ($responseData as $customerAddress) {
            $customerAddressesIds[] = $customerAddress['entity_id'];
        }
        /* @var $customerAddresses Mage_Customer_Model_Resource_Address_Collection */
        $customerAddresses = $this->_currentCustomer->getAddressesCollection();
        foreach ($customerAddresses as $customerAddress) {
            $this->assertContains($customerAddress->getId(), $customerAddressesIds);
        }
    }

    /**
     * Test update customer address
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerUpdateData
     */
    public function testUpdateCustomerAddress($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->_currentCustomer
            ->getAddressesCollection()
            ->getFirstItem();
        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedCustomerAddress Mage_Customer_Model_Address */
        $updatedCustomerAddress = Mage::getModel('customer/address')
            ->load($fixtureCustomerAddress->getId());
        foreach ($dataForUpdate as $field => $value) {
            if ('street' == $field) {
                $streets = explode("\n", $updatedCustomerAddress->getData('street'));
                $this->assertEquals($dataForUpdate['street'][0], $streets[0]);
                $this->assertEquals(
                    implode(
                        Mage_Customer_Model_Api2_Customer_Address::STREET_SEPARATOR,
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
     * Test unsuccessful address update with missing required fields
     *
     * @param string $attributeCode
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerRequiredAttributes
     */
    public function testUpdateCustomerAddressMissingRequired($attributeCode)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->_currentCustomer
            ->getAddressesCollection()
            ->getFirstItem();
        $dataForUpdate = $this->_getUpdateData();

        // Remove required field
        unset($dataForUpdate[$attributeCode]);

        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages'], "The response doesn't has errors.");
        $this->assertGreaterThanOrEqual(1, count($responseData['messages']['error']));

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test unsuccessful address update with empty required fields
     *
     * @param string $attributeCode
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerRequiredAttributes
     */
    public function testUpdateCustomerAddressEmptyRequired($attributeCode)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->_currentCustomer
            ->getAddressesCollection()
            ->getFirstItem();
        $dataForUpdate = $this->_getUpdateData();

        // Set required field empty
        $dataForUpdate[$attributeCode] = NULL;

        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages'], "The response doesn't has errors.");
        $this->assertGreaterThanOrEqual(1, count($responseData['messages']['error']));

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test filter data in update customer address
     *
     * @param $dataForUpdate
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     * @dataProvider providerUpdateData
     */
    public function testFilteringInUpdateCustomerAddress($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->_currentCustomer
            ->getAddressesCollection()
            ->getFirstItem();

        /* @var $attribute Mage_Customer_Model_Entity_Attribute */
        $attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'firstname');
        $currentInputFilter = $attribute->getInputFilter('customer_address', 'firstname');
        $attribute->setInputFilter('striptags')->save();

        // Set data for filtering
        $dataForUpdate['firstname'] = 'testFirstname<b>Test</b>';

        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $updatedCustomerAddress Mage_Customer_Model_Address */
        $updatedCustomerAddress = Mage::getModel('customer/address')
            ->load($fixtureCustomerAddress->getId());
        $this->assertEquals($updatedCustomerAddress->getData('firstname'), 'testFirstnameTest');

        // Restore data
        $attribute->setInputFilter($currentInputFilter)->save();
    }

    /**
     * Test delete address
     *
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     */
    public function testDeleteCustomerAddress()
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->_currentCustomer
            ->getAddressesCollection()
            ->getFirstItem();

        $restResponse = $this->callDelete('customers/addresses/' . $fixtureCustomerAddress->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = Mage::getModel('customer/address')->load($fixtureCustomerAddress->getId());
        $this->assertEmpty($customerAddress->getId());
    }

    /**
     * Test actions with customer address if customer is not owner
     *
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     */
    public function testActionsWithCustomerAddressIfCustomerIsNotOwner()
    {
        // get another customer
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        // get another customer address
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $fixtureCustomer
            ->getAddressesCollection()
            ->getFirstItem();

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
     * Data provider create data
     * Support for custom eav attributes are not implemented
     *
     * @return array
     */
    public function providerCreateData()
    {
        return array(array($this->_getCreateData()));
    }

    /**
     * Data provider update data
     * Support for custom eav attributes are not implemented
     *
     * @return array
     */
    public function providerUpdateData()
    {
        return array(array($this->_getUpdateData()));
    }

    /**
     * Data provider required attributes
     *
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
     * Get create data
     *
     * @return array
     */
    protected function _getCreateData()
    {
        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');
        /* @var $customerAddressFixture Mage_Customer_Model_Address */
        $customerAddressFixture = require $fixturesDir . '/Customer/Address.php';
        $data = array_intersect_key($customerAddressFixture->getData(), array_reverse(array(
            'city', 'country_id', 'firstname', 'lastname', 'postcode', 'region', 'region_id', 'street', 'telephone'
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
     * Get update data
     *
     * @return array
     */
    protected function _getUpdateData()
    {
        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');
        /* @var $customerAddressFixture Mage_Customer_Model_Address */
        $customerAddressFixture = require $fixturesDir . '/Customer/Address.php';
        $data = array_intersect_key($customerAddressFixture->getData(), array_reverse(array(
            'city', 'country_id', 'firstname', 'lastname', 'postcode', 'region', 'region_id', 'street', 'telephone'
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
            $address = Mage::getModel('customer/address');
            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
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

    /**
     * Init current customer
     *
     * @return Api2_Customer_Addresses_CustomerTest
     */
    protected function _initCurrentCustomer()
    {
        if (null === $this->_currentCustomer) {
            $this->_currentCustomer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);
        }
        return $this;
    }
}
