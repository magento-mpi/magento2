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
     * Test get customer address for admin
     *
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
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
     * Test update customer address
     *
     * @param array $dataForUpdate
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     * @dataProvider providerUpdateData
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
        $updatedCustomerAddress = Mage::getModel('customer/address')
            ->load($fixtureCustomerAddress->getId());

        $updatedData = $updatedCustomerAddress->getData();
        foreach ($dataForUpdate as $field => $value) {
            if ('street' == $field) {
                $streets = explode("\n", $updatedCustomerAddress->getData('street'));
                $this->assertEquals($dataForUpdate['street'][0], $streets[0]);
                $this->assertEquals(
                    implode(
                        Mage_Customer_Model_Api2_Customer_Addresses::STREET_SEPARATOR,
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
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     * @dataProvider providerRequiredAttributes
     */
    public function testUpdateCustomerAddressMissingRequired($attributeCode)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $dataForUpdate = $fixtureCustomerAddress->getData();

        // Remove the required field
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
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     * @dataProvider providerRequiredAttributes
     */
    public function testUpdateCustomerAddressEmptyRequired($attributeCode)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $dataForUpdate = $fixtureCustomerAddress->getData();

        // Set the required field as empty
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
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     * @dataProvider providerUpdateData
     */
    public function testFilteringInUpdateCustomerAddress($dataForUpdate)
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
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
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
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
        $customerAddress = Mage::getModel('customer/address')->load($fixtureCustomerAddress->getId());
        $this->assertEmpty($customerAddress->getId());
    }

    /**
     * Test actions for not existing resources
     */
    public function testActionsForUnavailableResorces()
    {
        // get
        $restResponse = $this->callGet('customers/addresses/invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        // put
        $restResponse = $this->callPut('customers/addresses/invalid_id', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());

        // delete
        $restResponse = $this->callDelete('customers/addresses/invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test not allowed actions with customer address
     *
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     */
    public function testNotAllowedActionsWithCustomerAddress()
    {
        /* @var $fixtureCustomer Mage_Customer_Model_Customer */
        $fixtureCustomer = $this->getFixture('customer');

        // post
        $response = $this->callPost('customers/addresses/' . $fixtureCustomer->getId(), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
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
