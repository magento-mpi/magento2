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
        $address = $soapResult[0];

        foreach (reset($soapResult) as $field => $value) {
            if ($field == 'customer_address_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($customerAddress->getData($field), $value, "Field '{$field}' has invalid value");
        }

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
        foreach ($soapResult as $field => $value) {
            if ($field == 'customer_address_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($customerAddress->getData($field), $value, "Field '{$field}' has invalid value");
        }
    }


    /**
     * Test customer address create and delete
     */
    public function testCustomerAddressCreateAndDelete()
    {
        $customer = Mage::registry('customer');

        // New address to create
        $newAddressData = array(
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
                $newAddressData
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
        $this->assertTrue((bool)$soapResult, 'Error during customer address info via API call, getting newly created address');

        // Verify all field values were correctly set
        reset($soapResult);
        foreach ( $newAddressData as $field => $value) {
            if ($field == 'street') {
                $this->assertEquals( trim(implode("\n",$newAddressData[$field])), $soapResult[$field], "Field '{$field}' has invalid value");
            } else {
                $this->assertEquals($newAddressData[$field], $soapResult[$field], "Field '{$field}' has invalid value");
            }
        }

        // ***********************
        // Now delete the address
        // ***********************
        $customerAddress = Mage::registry('customer_address');
        $customer = Mage::registry('customer');

        // get the customer's current address list
        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'customerAddressList',
            array(
                'customerId' => $customer->getId()
            )
        );
        $this->assertTrue((bool)$soapResult, 'Error getting customer address list via API call.');

        $this->assertEquals(2, count($soapResult), 'Number of addresses returned was not 2.');

        // find the one that is $customerAddress id:
        $found = false;
        foreach ($soapResult as $returnedAddress ) {
            if ($returnedAddress['customer_address_id'] == $newAddressId){ //$customerAddress->getId()) {
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
                'addressId' => $newAddressId // $customerAddress->getId()
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

        $this->assertEquals(1, count($soapResult), 'Number of addresses returned was not 1.');

        // find the one that is $customerAddress id:
        $found = false;
        foreach ($soapResult as $returnedAddress ) {
            if ($returnedAddress['customer_address_id'] == $newAddressId) { // $customerAddress->getId()) {
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
        $updateData = array(
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
        $this->assertTrue((bool)$soapResult, 'Error during customer address info via API call, getting newly created address');

        // Verify all field values were correctly set
        foreach ($soapResult as $field => $value) {
            if ($field == 'customer_address_id') {
                // process field mapping
                $field = 'entity_id';
            }

            if ($field == 'firstname') {
                $this->assertEquals($newFirstname, $value, "Firstname was not set to '{$newFirstname}'.");
            }
            else if ($field != 'updated_at') {
                // ignore updated_at field, it will change
                $this->assertEquals($customerAddress->getData($field), $value, "Field '{$field}' has invalid value");
            }
        }
    }
}