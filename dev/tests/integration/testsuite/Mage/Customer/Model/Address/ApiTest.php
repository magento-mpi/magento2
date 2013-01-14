<?php
/**
 * Test class for Mage_Customer_Model_Address_Api.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Mage/Customer/Model/Address/Api/_files/customer_address.php
 * @magentoDbIsolation enabled
 */
class Mage_Customer_Model_Address_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for customer address list
     */
    public function testCustomerAddressList()
    {
        // Get the customer's addresses
        $customerAddress = Mage::registry('customer_address');
        $customer = Mage::registry('customer');

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressList',
            array(
                'customerId' => $customer->getId()
            )
        );

        $this->assertTrue((bool)$soapResult, 'Error during customer address list via API call');
        $this->_verifyAddress($customerAddress, $soapResult[0]);
    }

    /**
     * Test for customer address info
     *
     */
    public function testCustomerAddressInfo()
    {

        // customer address
        /** @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = Mage::registry('customer_address');

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressInfo',
            array(
                'addressId' => $customerAddress->getId()
            )
        );

        $this->assertTrue((bool)$soapResult, 'Error during customer address info via API call');
        $this->_verifyAddress($customerAddress, $soapResult);
    }


    /**
     * Test customer address create
     */
    public function testCustomerAddressCreate()
    {
        $customer = Mage::registry('customer');

        // New address to create
        $newAddressData = (object)array(
            'city' => 'Kyle',
            'company' => 'HBM',
            'country_id' => 'US',
            'fax' => '5125551234',
            'firstname' => 'Sherry',
            'lastname' => 'Berry',
            'middlename' => 'Kari',
            'postcode' => '77777',
            'prefix' => 'Ms',
            'region_id'=> 43,
            'street' => array('123 FM 101'),
            'suffix' => 'M',
            'telephone' => '5',
            'is_default_billing' => false,
            'is_default_shipping' => false
        );

        // Call api to create the address
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressCreate',
            array(
                'customerId' => $customer->getId(),
                'addressData' => $newAddressData
            )
        );
        $this->assertTrue((bool)$soapResult, 'Error during customer address create via API call');

        // Verify the new address was added
        $newAddressId = $soapResult;
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressInfo',
            array(
                'addressId' => $newAddressId
            )
        );
        $this->assertTrue((bool)$soapResult,
            'Error during customer address info via API call, getting newly created address');

        // Verify all field values were correctly set
        foreach ( $newAddressData as $field => $value) {
            if ($field == 'street') {
                $this->assertEquals( trim(implode("\n",$value)),
                    $soapResult[$field], "Field '{$field}' has invalid value");
            } else {
                $this->assertEquals($value,
                    $soapResult[$field], "Field '{$field}' has invalid value");
            }
        }
    }

    /**
     * Test customer address delete
     */
    public function testCustomerAddressDelete()
    {
        $customer = Mage::registry('customer');
        $customerAddress = Mage::registry('customer_address2');

        // get the customer's current address list
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressList',
            array(
                'customerId' => $customer->getId()
            )
        );
        $this->assertTrue((bool)$soapResult, 'Error getting customer address list via API call.');
        $originalNumberOfAddresses = count($soapResult);
        $this->assertGreaterThan(0, $originalNumberOfAddresses, 'customer does not have any addresses to delete');

        // find the one that is $customerAddress id:
        $found = false;
        foreach ($soapResult as $returnedAddress ) {
            if ($returnedAddress['customer_address_id'] == $customerAddress->getId()){
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'customer address id not found in list of addresses');

        // Delete one
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressDelete',
            array(
                'addressId' => $customerAddress->getId()
            )
        );
        $this->assertTrue((bool)$soapResult, 'Error during customer address delete via API call');

        // Verify the deleted address is no longer in the list
        // get the customer's current address list
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressList',
            array(
                'customerId' => $customer->getId()
            )
        );
        $this->assertTrue((bool)$soapResult, 'Error getting customer address list via API call.');
        $this->assertEquals(--$originalNumberOfAddresses, count($soapResult),
            'Number of addresses did not go down.');

        // find the one that is $customerAddress id:
        $found = false;
        foreach ($soapResult as $returnedAddress ) {
            if ($returnedAddress['customer_address_id'] == $customerAddress->getId()) {
                $found = true;
            }
        }
        $this->assertFalse($found, 'customer address id found in list of addresses after deletion');
    }

    /**
     * Test customer address update
     */
    public function testCustomerAddressUpdate()
    {
        $customerAddress = Mage::registry('customer_address');

        $newFirstname = 'Eric';

        // Data to set in existing address
        $updateData = (object)array(
            'firstname' => $newFirstname
        );

        // update a customer's address
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressUpdate',
            array(
                'addressId' => $customerAddress->getId(),
                'addressData' => $updateData
            )
        );
        $this->assertTrue((bool)$soapResult, 'Error during customer address update via API call');

        // Verify the address was updated
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressInfo',
            array(
                'addressId' => $customerAddress->getId()
            )
        );
        $this->assertTrue((bool)$soapResult,
            'Error during customer address info via API call, getting newly created address');

        // Verify all field values were correctly set
        foreach ($soapResult as $field => $value) {
            if ($field == 'customer_address_id') {
                // process field mapping
                $field = 'entity_id';
            }

            if ($field == 'firstname') {
                $this->assertEquals($newFirstname, $value, "Firstname was not set to '{$newFirstname}'.");
            } elseif ($field != 'updated_at') {
                // ignore updated_at field, it will change
                $this->assertEquals($customerAddress->getData($field), $value, "Field '{$field}' has invalid value");
            }
        }
    }

    /**
     * Verify fields in a soap address entity match the expected values of a
     * given address model
     *
     * @param $addressModel Mage_Customer_Model_Address containing expected values
     * @param $addressSoapResult array The customerAddressEntityItem from the SOAP API response
     */
    protected function _verifyAddress($addressModel, $addressSoapResult)
    {
        foreach ($addressSoapResult as $field => $value) {
            if ($field == 'customer_address_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($addressModel->getData($field), $value, "Field '{$field}' has invalid value");
        }
    }
}