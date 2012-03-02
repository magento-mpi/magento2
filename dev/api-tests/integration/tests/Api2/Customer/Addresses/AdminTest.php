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
 * Test for customer addresses API2 (admin)
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Addresses_AdminTest extends Magento_Test_Webservice_Rest_Admin
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
     * @dataProvider providerCreateData
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     */
    public function testCreateCustomerAddress($dataForCreate)
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');
        $restResponse = $this->callPost('customers/' . $fixtureCustomer->getId() . '/addresses', $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        list($addressId) = array_reverse(explode('/', $restResponse->getHeader('Location')));
        /* @var $createdAddress Mage_Customer_Model_Address */
        $createdAddress = Mage::getModel('customer/address')->load($addressId);
        $this->assertGreaterThan(0, $createdAddress->getId());

        $this->addModelToDelete($createdAddress, true);
    }

    /**
     * Test unsuccessful address create with missing required fields
     *
     * @param string $attributeCode
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     * @dataProvider providerRequiredAttributes
     */
    public function testCreateCustomerAddressMissingRequired($attributeCode)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $dataForCreate = $fixtureCustomerAddress->getData();

        // Remove required field
        unset($dataForCreate[$attributeCode]);

        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForCreate);
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
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     * @dataProvider providerRequiredAttributes
     */
    public function testCreateCustomerAddressEmptyRequired($attributeCode)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $dataForCreate = $fixtureCustomerAddress->getData();

        // Set required field empty
        $dataForCreate[$attributeCode] = NULL;

        $restResponse = $this->callPut('customers/addresses/' . $fixtureCustomerAddress->getId(), $dataForCreate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertArrayHasKey('error', $responseData['messages'], "The response doesn't has errors.");
        $this->assertGreaterThanOrEqual(1, count($responseData['messages']['error']));

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test get customer addresses for admin
     *
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
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
            $this->assertContains($fixtureCustomerAddress->getId(), $customerAddressesIds,
                'Address item should be in response');
        }
    }

    /**
     * Test retrieving addresses for not existing customer
     */
    public function testGetUnavailableCustomerAddresses()
    {
        $restResponse = $this->callGet('customers/invalid_id/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test update customer address
     *
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     */
    public function testUpdateCustomerAddress()
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');
        $restResponse = $this->callPut('customers/' . $fixtureCustomer->getId() . '/addresses', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $restResponse->getStatus());
    }

    /**
     * Test delete customer address
     *
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     */
    public function testDeleteCustomerAddress()
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');
        $restResponse = $this->callDelete('customers/' . $fixtureCustomer->getId() . '/addresses', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $restResponse->getStatus());
    }

    /**
     * Data provider create data
     *
     * @return array
     */
    public function providerCreateData()
    {
        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');
        /* @var $customerAddressFixture Mage_Customer_Model_Address */
        $fixtureCustomerAddress = require $fixturesDir . '/Customer/Address.php';
        $dataForUpdate = $fixtureCustomerAddress->getData();
        unset($dataForUpdate['is_default_billing']);
        unset($dataForUpdate['is_default_shipping']);
        // Get address eav required attributes
        foreach ($this->_getAddressEavRequiredAttributes() as $attributeCode => $requiredAttribute) {
            if (!isset($dataForUpdate[$attributeCode])) {
                $dataForUpdate[$attributeCode] = $requiredAttribute . uniqid();
            }
        }

        return array(array($dataForUpdate));
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
     * Get address eav required attributes
     *
     * @return Api2_Customer_Customers_AdminTest
     */
    protected function _getAddressEavRequiredAttributes()
    {
        if (null !== $this->_requiredAttributes) {
            $this->_requiredAttributes = array();
            /* @var $address Mage_Customer_Model_Address */
            $address = Mage::getModel('customer/address');
            foreach ($address->getAttributes() as $attribute) {
                if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
                    $this->_requiredAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
                }
            }
        }
        return $this->_requiredAttributes;
    }
}
