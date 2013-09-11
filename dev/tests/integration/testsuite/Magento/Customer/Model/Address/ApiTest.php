<?php
/**
 * Test class for \Magento\Customer\Model\Address\Api.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
 */
class Magento_Customer_Model_Address_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for customer address list
     */
    public function testCustomerAddressList()
    {
        // Get the customer's addresses
        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'customerAddressList',
            array(
                'customerId' => 1
            )
        );

        $this->assertNotEmpty($soapResult, 'Error during customer address list via API call');
        $this->assertCount(2, $soapResult, 'Result did not contain 2 addresses');

        /** @var $firstAddress \Magento\Customer\Model\Address */
        $firstAddress = Mage::getModel('\Magento\Customer\Model\Address');
        $firstAddress->load(1);
        $this->_verifyAddress($firstAddress->getData(), $soapResult[0]);

        /** @var $secondAddress \Magento\Customer\Model\Address */
        $secondAddress = Mage::getModel('\Magento\Customer\Model\Address');
        $secondAddress->load(2);
        $this->_verifyAddress($secondAddress->getData(), $soapResult[1]);
    }

    /**
     * Test for customer address info
     */
    public function testCustomerAddressInfo()
    {
        /** @var $customerAddress \Magento\Customer\Model\Address */
        $customerAddress = Mage::getModel('\Magento\Customer\Model\Address');
        $customerAddress->load(1);

        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'customerAddressInfo',
            array(
                'addressId' => $customerAddress->getId()
            )
        );

        $this->assertNotEmpty($soapResult, 'Error during customer address info via API call');
        $this->_verifyAddress($customerAddress->getData(), $soapResult);
    }

    /**
     * Test customer address create
     *
     * @magentoDbIsolation enabled
     */
    public function testCustomerAddressCreate()
    {
        $this->markTestIncomplete('Unreliable implementation');
        $customerId = 1;

        // New address to create
        $newAddressData = array(
            'city' => 'Kyle',
            'company' => 'HBM',
            'country_id' => 'US',
            'firstname' => 'Sherry',
            'lastname' => 'Berry',
            'postcode' => '77777',
            'prefix' => 'Ms',
            'street' => array('123 FM 101'),
            'suffix' => 'M',
            'telephone' => '5',
            'is_default_billing' => false,
            'is_default_shipping' => false
        );

        // Call api to create the address
        $newAddressId = Magento_TestFramework_Helper_Api::call(
            $this,
            'customerAddressCreate',
            array(
                'customerId' => $customerId,
                'addressData' => (object)$newAddressData
            )
        );

        // Verify the new address was added
        /** @var $newAddressModel \Magento\Customer\Model\Address */
        $newAddressModel = Mage::getModel('\Magento\Customer\Model\Address');
        $newAddressModel->load($newAddressId);

        // Verify all field values were correctly set
        $newAddressData['street'] = trim(implode("\n", $newAddressData['street']));
        $newAddressData['customer_address_id'] = $newAddressId;
        $this->_verifyAddress($newAddressData, $newAddressModel->getData());
    }

    /**
     * Test customer address delete
     *
     * @magentoDbIsolation enabled
     */
    public function testCustomerAddressDelete()
    {
        $addressId = 1;

        // Delete address
        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'customerAddressDelete',
            array(
                'addressId' => $addressId
            )
        );
        $this->assertTrue($soapResult, 'Error during customer address delete via API call');

        /** @var \Magento\Customer\Model\Address $address */
        $address = Mage::getModel('\Magento\Customer\Model\Address')->load($addressId);
        $this->assertNull($address->getEntityId());
    }

    /**
     * Test customer address update
     *
     * @magentoDbIsolation enabled
     */
    public function testCustomerAddressUpdate()
    {
        $addressId = 1;
        $newFirstname = 'Eric';
        $newTelephone = '888-555-8888';

        // Data to set in existing address
        $updateData = (object)array(
            'firstname' => $newFirstname,
            'telephone' => $newTelephone
        );

        // update a customer's address
        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'customerAddressUpdate',
            array(
                'addressId' => $addressId,
                'addressData' => $updateData
            )
        );

        $this->assertTrue($soapResult, 'Error during customer address update via API call');

        // Verify all field values were correctly set
        /** @var $customerAddress \Magento\Customer\Model\Address */
        $customerAddress = Mage::getModel('\Magento\Customer\Model\Address');
        $customerAddress->load($addressId);

        $this->assertEquals(
            $newFirstname,
            $customerAddress->getFirstname(),
            'First name is not updated.'
        );
        $this->assertEquals(
            $newTelephone,
            $customerAddress->getTelephone(),
            'Telephone is not updated.'
        );
    }

    /**
     * Verify fields in an address array
     *
     * Compares two arrays containing address data.  Throws assertion error if
     * data does not match.
     *
     * @param array $expectedData Expected values of address array
     * @param array $actualData Values that are to be tested
     */
    protected function _verifyAddress($expectedData, $actualData)
    {
        $fieldsToCompare = array(
            'entity_id' => 'customer_address_id',
            'city',
            'country_id',
            'firstname',
            'lastname',
            'postcode',
            'street',
            'telephone'
        );

        Magento_TestFramework_Helper_Api::checkEntityFields(
            $this,
            $expectedData,
            $actualData,
            $fieldsToCompare
        );
    }
}
