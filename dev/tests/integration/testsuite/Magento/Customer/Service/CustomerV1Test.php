<?php

namespace Magento\Customer\Service;

/**
 * Integration test for service layer \Magento\Customer\Service\CustomerV1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class CustomerV1Test extends \PHPUnit_Framework_TestCase
{
    /** @var CustomerV1 */
    private $_service;

    /** @var \Magento\ObjectManager */
    private $_objectManager;

    /** @var \Magento\Customer\Service\Entity\V1\Address[] */
    private $_expectedAddresses;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_service = $this->_objectManager->create('Magento\Customer\Service\CustomerV1');

        $address = new Entity\V1\Address();
        $address->setId(1)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion(new Entity\V1\Region('AL', 'Alabama', 1))
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith');

        $address2 = new Entity\V1\Address();
        $address2->setId(2)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->setPostcode('47676')
            ->setRegion(new Entity\V1\Region('AL', 'Alabama', 1))
            ->setStreet(['Black str, 48'])
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');

        $this->_expectedAddresses = [$address, $address2];
    }

    /**
     * Helper function that returns an Address DTO that matches the data from customer_address fixture
     *
     * @return Entity\V1\Address
     */
    private function _createFirstAddress()
    {
        $address = clone $this->_expectedAddresses[0];
        $address->setId(null);
        return $address;
    }

    /**
     * Helper function that returns an Address DTO that matches the data from customer_two_address fixture
     *
     * @return Entity\V1\Address
     */
    private function _createSecondAddress()
    {
        $address = clone $this->_expectedAddresses[1];
        $address->setId(null);
        return $address;
    }


    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testSaveAddressChanges()
    {
        $customerId = 1;
        $address = $this->_service->getAddressById($customerId, 2);
        $proposedAddress = clone $address;
        $proposedAddress->setTelephone('555' . $address->getTelephone());
        $this->_service->saveAddresses($customerId, [$proposedAddress]);

        $addresses = $this->_service->getAddresses($customerId);
        $this->assertEquals(2, count($addresses));
        $this->assertNotEquals($this->_expectedAddresses[1], $addresses[1]);
        $this->_assertAddressAndRegionArrayEquals($proposedAddress->__toArray(), $addresses[1]->__toArray());
    }

    /**
     * @param mixed $custId
     * @dataProvider invalidCustomerIdsDataProvider
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage customerId
     */
    public function testInvalidCustomerIds($custId)
    {
        $this->_service->getCustomer($custId);
    }

    public function invalidCustomerIdsDataProvider()
    {
        return array(
            array('ab'),
            array(' '),
            array(-1),
            array(0),
            array(' 1234'),
            array('-1'),
            array('0'),
        );
    }


    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomerCached()
    {
        $firstCall = $this->_service->getCustomer(1);
        $secondCall = $this->_service->getCustomer(1);

        $this->assertSame($firstCall, $secondCall);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomerLocked()
    {
        $customer = $this->_service->getCustomer(1);

        $this->assertTrue($customer->isLocked(), 'Service should return locked DTO');
    }

    public function testGetAddressAttributeMetadata()
    {
        $vatValidMetadata = $this->_service->getAddressAttributeMetadata('vat_is_valid');

        $this->assertNotNull($vatValidMetadata);
        $this->assertTrue($vatValidMetadata->isLocked(), 'Service should return locked DTO');
        $this->assertEquals('vat_is_valid', $vatValidMetadata->getAttributeCode());
        $this->assertEquals('text', $vatValidMetadata->getFrontendInput());
        $this->assertEquals('VAT number validity', $vatValidMetadata->getStoreLabel());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testSaveAddressesIdSetButNotAlreadyExisting()
    {
        $proposedAddress = $this->_createSecondAddress()
            ->setFirstname('Jane')
            ->setId(4200);

        $customerId = 1;
        $this->_service->saveAddresses($customerId, [$proposedAddress]);
        $addresses = $this->_service->getAddresses($customerId);
        $this->assertEquals($this->_expectedAddresses[0], $addresses[0]);
        $this->assertEquals($this->_expectedAddresses[1], $addresses[1]);

        //unlock object
        $expectedThirdAddress = clone $proposedAddress;
        // set id
        $expectedThirdAddress->setId($addresses[2]->getId());
        $this->_assertAddressAndRegionArrayEquals($expectedThirdAddress->__toArray(), $addresses[2]->__toArray());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testGetAddresses()
    {
        $customerId = 1;
        $addresses = $this->_service->getAddresses($customerId);
        $this->assertEquals(2, count($this->_expectedAddresses) );
        $this->assertEquals(2, count($addresses) );
        $this->_assertAddressAndRegionArrayEquals(
            $this->_expectedAddresses[0]->__toArray(),
            $addresses[0]->__toArray()
        );
        $this->_assertAddressAndRegionArrayEquals(
            $this->_expectedAddresses[1]->__toArray(),
            $addresses[1]->__toArray()
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testGetDefaultBillingAddress()
    {
        $customerId = 1;
        $address = $this->_service->getDefaultBillingAddress($customerId);
        $this->assertEquals($this->_expectedAddresses[0], $address);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testGetCustomer()
    {
        // _files/customer.php sets the customer id to 1
        $customer = $this->_service->getCustomer(1);

        // All these expected values come from _files/customer.php fixture
        $this->assertEquals(1, $customer->getCustomerId());
        $this->assertEquals('customer@example.com', $customer->getEmail());
        $this->assertEquals('Firstname', $customer->getFirstName());
        $this->assertEquals('Lastname', $customer->getLastName());
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage No customer with customerId 1 exists.
     */
    public function testGetCustomerNotExist()
    {
        // No fixture, so customer with id 1 shouldn't exist, exception should be thrown
        $this->_service->getCustomer(1);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testGetAddressById()
    {
        $customerId = 1;
        $addressId = 2;
        $addresses = $this->_service->getAddressById($customerId, $addressId);
        $this->assertEquals($this->_expectedAddresses[1], $addresses);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_ADDRESS_NOT_FOUND
     */
    public function testGetAddressByIdBadAddrId()
    {
        // Should throw the address not found excetion
        $this->_service->getAddressById(1, 12345);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewAddress()
    {
        $proposedAddress = $this->_createSecondAddress();
        $customerId = 1;

        $this->_service->saveAddresses($customerId, [$proposedAddress]);
        $addresses = $this->_service->getAddresses($customerId);
        $this->assertEquals($this->_expectedAddresses[0], $addresses[0]);
        $expectedNewAddress = clone $this->_expectedAddresses[1];
        $expectedNewAddress
            ->setId($addresses[1]->getId());
        $this->assertEquals($expectedNewAddress->__toArray(), $addresses[1]->__toArray());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewAddressWithAttributes()
    {
        $proposedAddress = $this->_createSecondAddress();
        $proposedAddress->setAttribute('weird', 'something_strange_with_hair');
        $customerId = 1;
        $this->_service->saveAddresses($customerId, [$proposedAddress]);

        $addresses = $this->_service->getAddresses($customerId);
        $this->assertNotEquals($proposedAddress->getAttributes(), $addresses[1]->getAttributes());
        $this->assertArrayHasKey('weird', $proposedAddress->getAttributes());
        $this->assertArrayNotHasKey('weird', $addresses[1]->getAttributes());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewInvalidAddresses()
    {
        $firstAddress = $this->_createFirstAddress();
        $firstAddress->setAttribute('firstname', null);
        $secondAddress = $this->_createFirstAddress();
        $secondAddress->setAttribute('lastname', null);
        $customerId = 1;
        try {
            $this->_service->saveAddresses($customerId, [$firstAddress, $secondAddress]);
        } catch (\Magento\Customer\Service\Entity\V1\AggregateException $ae) {
            $failures = $ae->getExceptions();
            $firstAddressError = $failures[0];
            $this->assertInstanceOf('\Magento\Customer\Service\Entity\V1\Exception', $firstAddressError);
            $this->assertInstanceOf('\Magento\Validator\ValidatorException', $firstAddressError->getPrevious());
            $this->assertSame('Please enter the first name.', $firstAddressError->getPrevious()->getMessage());

            $secondAddressError = $failures[1];
            $this->assertInstanceOf('\Magento\Customer\Service\Entity\V1\Exception', $secondAddressError);
            $this->assertInstanceOf('\Magento\Validator\ValidatorException', $secondAddressError->getPrevious());
            $this->assertSame('Please enter the last name.', $secondAddressError->getPrevious()->getMessage());
            return;
        }
        $this->fail('Expected AggregateException not caught.');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewAddressDefaults()
    {
        $addressShipping = $this->_createFirstAddress();
        $addressShipping->setDefaultShipping(true)->setDefaultBilling(false);

        $addressBilling = $this->_createSecondAddress();
        $addressBilling->setDefaultBilling(true)->setDefaultShipping(false);
        $customerId = 1;
        $this->_service->saveAddresses($customerId, [$addressShipping, $addressBilling]);

        $shipping = $this->_service->getDefaultShippingAddress($customerId);
        $addressShipping->setId($shipping->getId());
        $this->_assertAddressAndRegionArrayEquals($addressShipping->__toArray(), $shipping->__toArray());

        $billing = $this->_service->getDefaultBillingAddress($customerId);
        $addressBilling->setId($billing->getId());
        $this->_assertAddressAndRegionArrayEquals($addressBilling->__toArray(), $billing->__toArray());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveSeveralNewAddressesSameDefaults()
    {
        $addressTwo = $this->_createSecondAddress();
        $addressThree = clone $addressTwo;
        $addressThree->setDefaultBilling(true);

        $addressFour = clone $addressTwo;
        $addressFour->setDefaultBilling(false)->setDefaultShipping(true);

        $addressDefault = clone $addressTwo;
        $addressDefault->setDefaultBilling(true)->setDefaultShipping(true)
            ->setFirstName('Dirty Garry');

        $customerId = 1;
        $this->_service->saveAddresses(
            $customerId,
            [$addressTwo, $addressThree, $addressFour, $addressDefault]
        );

        $addresses = $this->_service->getAddresses($customerId);
        $this->assertEquals(5, count($addresses));

        // retrieve defaults
        $addresses = [
            $this->_service->getDefaultBillingAddress($customerId),
            $this->_service->getDefaultShippingAddress($customerId),
        ];
        // Same address is returned twice
        $this->assertEquals($addresses[0], $addresses[1]);
        $this->assertEquals($addressDefault->getFirstname(), $addresses[1]->getFirstName());

        //clone object
        $expectedDefault = clone $addressDefault;
        // It is the same address retrieved as the one which get saved
        $expectedDefault->setId($addresses[1]->getId());
        $this->_assertAddressAndRegionArrayEquals($expectedDefault->__toArray(), $addresses[1]->__toArray());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveSeveralNewAddressesDifferentDefaults()
    {
        $addressTwo = $this->_createSecondAddress();
        $addressThree = clone $addressTwo;
        $addressThree->setDefaultBilling(true);

        $defaultShipping = clone $addressTwo;
        $defaultShipping->setFirstname('Shippy')
            ->setLastname('McShippington')
            ->setDefaultBilling(false)
            ->setDefaultShipping(true);

        $defaultBilling = clone $addressTwo;
        $defaultBilling
            ->setFirstname('Billy')
            ->setLastname('McBillington')
            ->setDefaultBilling(true)
            ->setDefaultShipping(false);

        $customerId = 1;

        $this->_service->saveAddresses($customerId, [$addressTwo, $addressThree, $defaultShipping, $defaultBilling]);
        $addresses = $this->_service->getAddresses($customerId);

        $this->assertEquals(5, count($addresses));

        $addresses = [
            $this->_service->getDefaultBillingAddress($customerId),
            $this->_service->getDefaultShippingAddress($customerId),
        ];
        $this->assertNotEquals($addresses[0], $addresses[1]);
        $this->assertTrue($addresses[0]->isDefaultBilling());
        $this->assertTrue($addresses[1]->isDefaultShipping());

        $expectedDfltShip = clone $defaultShipping;
        $expectedDfltShip->setId($addresses[1]->getId());

        $expectedDfltBill = clone $defaultBilling;
        $expectedDfltBill->setId($addresses[0]->getId());

        $this->_assertAddressAndRegionArrayEquals($expectedDfltShip->__toArray(), $addresses[1]->__toArray());
        $this->_assertAddressAndRegionArrayEquals($expectedDfltBill->__toArray(), $addresses[0]->__toArray());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testSaveAddressesNoAddresses()
    {
        $addressIds = $this->_service->saveAddresses(1, []);
        $this->assertEmpty($addressIds);
        $customerId = 1;
        $addresses = $this->_service->getAddresses($customerId);
        $this->assertEquals($this->_expectedAddresses, $addresses);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage No customer with customerId 4200 exists
     */
    public function testSaveAddressesCustomerIdNotExist()
    {
        $proposedAddress = $this->_createSecondAddress();
        $this->_service->saveAddresses(4200, [$proposedAddress]);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage No customer with customerId this_is_not_a_valid_id exists
     */
    public function testSaveAddressesCustomerIdInvalid()
    {
        $proposedAddress = $this->_createSecondAddress();
        $this->_service->saveAddresses('this_is_not_a_valid_id', [$proposedAddress]);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testLogin()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $customer = $this->_service->authenticate('customer@example.com', 'password', true);

        $this->assertSame('customer@example.com', $customer->getEmail());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage Invalid login or password
     */
    public function testLoginWrongPassword()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->_service->authenticate('customer@example.com', 'wrongPassword', true);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage Invalid login or password
     */
    public function testLoginWrongUsername()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->_service->authenticate('non_existing_user', 'password', true);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     * @magentoAppArea frontend
     */
    public function testActivateAccount()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        // Assert in just one test that the fixture is working
        $this->assertNotNull($customerModel->getConfirmation(), 'New customer needs to be confirmed');

        $this->_service->activateAccount($customerModel->getId(), $customerModel->getConfirmation());

        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $this->assertNull($customerModel->getConfirmation(), 'Customer should be considered confirmed now');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     *
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Wrong confirmation key
     */
    public function testActivateAccountWrongKey()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $key = $customerModel->getConfirmation();

        $this->_service->activateAccount($customerModel->getId(), $key . $key);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage No customer with customerId 12341 exists.
     */
    public function testActivateAccountWrongAccount()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $key = $customerModel->getConfirmation();

        $this->_service->activateAccount('1234' . $customerModel->getId(), $key);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     * @magentoAppArea frontend
     *
     * @expectedException  \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage Customer account is already active
     */
    public function testActivateAccountAlreadyActive()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $key = $customerModel->getConfirmation();
        $this->_service->activateAccount($customerModel->getId(), $key);

        $this->_service->activateAccount($customerModel->getId(), $key);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveCustomer()
    {
        $existingCustId = 1;

        $email = 'savecustomer@example.com';
        $firstName = 'Firstsave';
        $lastname = 'Lastsave';

        $customerBefore = $this->_service->getCustomer($existingCustId);
        $modifiedCustomer = clone $customerBefore;
        $modifiedCustomer->setCustomerId(1);
        $modifiedCustomer->setEmail($email);
        $modifiedCustomer->setFirstName($firstName);
        $modifiedCustomer->setLastName($lastname);
        $attributes = array(
            'created_in' => 'Admin',
            'password' => 'notsaved',
        );
        $modifiedCustomer->setAttributes($attributes);

        $returnedCustomerId = $this->_service->saveCustomer($modifiedCustomer, 'aPassword');
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_service->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstName());
        $this->assertEquals($lastname, $customerAfter->getLastName());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_service->authenticate(
            $customerAfter->getEmail(),
            'aPassword',
            true
        );
        $attributesBefore = $customerBefore->getAttributes();
        $attributesAfter = $customerAfter->getAttributes();
        // ignore 'updated_at'
        unset($attributesBefore['updated_at']);
        unset($attributesAfter['updated_at']);
        $inBeforeOnly = array_diff_assoc($attributesBefore, $attributesAfter);
        $inAfterOnly = array_diff_assoc($attributesAfter, $attributesBefore);
        $expectedInBefore = array(
            'firstname',
            'lastname',
            'email',
            'password_hash',
        );
        $this->assertEquals($expectedInBefore, array_keys($inBeforeOnly));
        $expectedInAfter = array(
            'firstname',
            'lastname',
            'email',
            'created_in',
            'password_hash',
        );
        $this->assertEquals($expectedInAfter, array_keys($inAfterOnly));
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveCustomerWithoutChangingPassword()
    {
        $existingCustId = 1;

        $email = 'savecustomer@example.com';
        $firstName = 'Firstsave';
        $lastname = 'Lastsave';

        $customerBefore = $this->_service->getCustomer($existingCustId);
        $modifiedCustomer = clone $customerBefore;
        $modifiedCustomer->setCustomerId(1);
        $modifiedCustomer->setEmail($email);
        $modifiedCustomer->setFirstName($firstName);
        $modifiedCustomer->setLastName($lastname);
        $attributes = array(
            'created_in' => 'Admin',
        );
        $modifiedCustomer->setAttributes($attributes);

        $returnedCustomerId = $this->_service->saveCustomer($modifiedCustomer);
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_service->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstName());
        $this->assertEquals($lastname, $customerAfter->getLastName());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_service->authenticate(
            $customerAfter->getEmail(),
            'password',
            true
        );
        $attributesBefore = $customerBefore->getAttributes();
        $attributesAfter = $customerAfter->getAttributes();
        // ignore 'updated_at'
        unset($attributesBefore['updated_at']);
        unset($attributesAfter['updated_at']);
        $inBeforeOnly = array_diff_assoc($attributesBefore, $attributesAfter);
        $inAfterOnly = array_diff_assoc($attributesAfter, $attributesBefore);
        $expectedInBefore = array(
            'firstname',
            'lastname',
            'email',
        );
        $this->assertEquals($expectedInBefore, array_keys($inBeforeOnly));
        $expectedInAfter = array(
            'firstname',
            'lastname',
            'email',
            'created_in',
        );
        $this->assertEquals($expectedInAfter, array_keys($inAfterOnly));
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveCustomerPasswordCannotSetThroughAttributeSetting()
    {
        $existingCustId = 1;

        $email = 'savecustomer@example.com';
        $firstName = 'Firstsave';
        $lastname = 'Lastsave';

        $customerBefore = $this->_service->getCustomer($existingCustId);
        $modifiedCustomer = clone $customerBefore;
        $modifiedCustomer->setCustomerId(1);
        $modifiedCustomer->setEmail($email);
        $modifiedCustomer->setFirstName($firstName);
        $modifiedCustomer->setLastName($lastname);
        $attributes = array(
            'created_in' => 'Admin',
            'password' => 'aPassword',
        );
        $modifiedCustomer->setAttributes($attributes);

        $returnedCustomerId = $this->_service->saveCustomer($modifiedCustomer);
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_service->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstName());
        $this->assertEquals($lastname, $customerAfter->getLastName());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_service->authenticate(
            $customerAfter->getEmail(),
            'password',
            true
        );
        $attributesBefore = $customerBefore->getAttributes();
        $attributesAfter = $customerAfter->getAttributes();
        // ignore 'updated_at'
        unset($attributesBefore['updated_at']);
        unset($attributesAfter['updated_at']);
        $inBeforeOnly = array_diff_assoc($attributesBefore, $attributesAfter);
        $inAfterOnly = array_diff_assoc($attributesAfter, $attributesBefore);
        $expectedInBefore = array(
            'firstname',
            'lastname',
            'email',
        );
        $this->assertEquals($expectedInBefore, array_keys($inBeforeOnly));
        $expectedInAfter = array(
            'firstname',
            'lastname',
            'email',
            'created_in',
        );
        $this->assertEquals($expectedInAfter, array_keys($inAfterOnly));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage Cannot set or change attribute id
     */
    public function testSaveCustomerSetIdException()
    {
        $existingCustId = 1;

        $email = 'savecustomer@example.com';
        $firstName = 'Firstsave';
        $lastname = 'Lastsave';

        $customerBefore = $this->_service->getCustomer($existingCustId);
        $modifiedCustomer = clone $customerBefore;
        $modifiedCustomer->setCustomerId(1);
        $modifiedCustomer->setEmail($email);
        $modifiedCustomer->setFirstName($firstName);
        $modifiedCustomer->setLastName($lastname);
        $attributes = array(
            'created_in' => 'Admin',
            'id' => '200',
        );
        $modifiedCustomer->setAttributes($attributes);
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Validator\ValidatorException
     * @expectedExceptionMessage Please correct this email address
     */
    public function testSaveCustomerException()
    {
        $customerEntity = new Entity\V1\Customer();
        $customerEntity->setCustomerId(1);
        $attributes = array(
            'password' => 'aPassword',
        );
        $customerEntity->setAttributes($attributes);

        try {
            $this->_service->saveCustomer($customerEntity);
        } catch (\Magento\Customer\Service\Entity\V1\Exception $e) {
            $this->assertEquals('There were one or more errors validating the customer object.', $e->getMessage());
            $this->assertEquals(CustomerV1Interface::CODE_VALIDATION_FAILED, $e->getCode());
            throw $e->getPrevious();
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveNonexistingCustomer()
    {
        $existingCustId = 1;
        $existingCustomer = $this->_service->getCustomer($existingCustId);

        $newCustId = 2;
        $email = 'savecustomer@example.com';
        $firstName = 'Firstsave';
        $lastname = 'Lastsave';
        $customerEntity = clone $existingCustomer;
        $customerEntity->setCustomerId($newCustId);
        $customerEntity->setEmail($email);
        $customerEntity->setFirstName($firstName);
        $customerEntity->setLastName($lastname);
        $attributes = array(
            'created_in' => 'Admin',
        );
        $customerEntity->setAttributes($attributes);

        $customerId = $this->_service->saveCustomer($customerEntity, 'aPassword');
        $this->assertEquals($newCustId, $customerId);
        $customerAfter = $this->_service->getCustomer($customerId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstName());
        $this->assertEquals($lastname, $customerAfter->getLastName());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_service->authenticate(
            $customerAfter->getEmail(),
            'aPassword',
            true
        );
        $attributesBefore = $existingCustomer->getAttributes();
        $attributesAfter = $customerAfter->getAttributes();
        // ignore 'updated_at'
        unset($attributesBefore['updated_at']);
        unset($attributesAfter['updated_at']);
        unset($attributesAfter['reward_update_notification']);
        unset($attributesAfter['reward_warning_notification']);
        $inBeforeOnly = array_diff_assoc($attributesBefore, $attributesAfter);
        $inAfterOnly = array_diff_assoc($attributesAfter, $attributesBefore);
        $expectedInBefore = array(
            'firstname',
            'lastname',
            'email',
            'entity_id',
            'password_hash',
        );
        $this->assertEquals($expectedInBefore, array_keys($inBeforeOnly));
        $expectedInAfter = array(
            'firstname',
            'lastname',
            'email',
            'entity_id',
            'created_in',
            'password_hash',
        );
        $this->assertEquals($expectedInAfter, array_keys($inAfterOnly));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveCustomerInServiceVsInModel()
    {
        $email = 'email@example.com';
        $email2 = 'email2@example.com';
        $firstname = 'Tester';
        $lastname = 'McTest';
        $groupId = 1;
        $password = 'aPassword';

        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\CustomerFactory')
            ->create();
        $customerModel->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId)
            ->setPassword($password);
        $customerModel->save();
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $savedModel = $this->_objectManager->create('Magento\Customer\Model\CustomerFactory')
            ->create()
            ->load($customerModel->getId());
        $dataInModel = $savedModel->getData();

        $newCustomerEntity = new Entity\V1\Customer();
        $newCustomerEntity->setEmail($email2)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        $customerId = $this->_service->saveCustomer($newCustomerEntity, $password);
        $this->assertNotNull($customerId);
        $savedCustomer = $this->_service->getCustomer($customerId);
        $dataInService = $savedCustomer->getAttributes();
        foreach ($dataInModel as $key => $value) {
            if (!in_array(
                $key,
                array('created_at', 'updated_at', 'email', 'is_active', 'entity_id', 'password_hash',
                    'attribute_set_id')
            )) {
                if (is_null($value)) {
                    $this->assertArrayNotHasKey($key, $dataInService);
                } else {
                    $this->assertEquals($value, $dataInService[$key], 'Failed asserting value for '. $key);
                }
            }
        }
        $this->assertArrayNotHasKey('is_active', $dataInService);
        $this->assertNotNull($dataInService['created_at']);
        $this->assertNotNull($dataInService['updated_at']);
        $this->assertNotNull($dataInService['entity_id']);
        $this->assertNotNull($dataInService['password_hash']);
        $this->assertEquals($email2, $dataInService['email']);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveNewCustomer()
    {
        $email = 'email@example.com';
        $storeId = 1;
        $firstname = 'Tester';
        $lastname = 'McTest';
        $groupId = 1;

        $newCustomerEntity = new Entity\V1\Customer();
        $newCustomerEntity->setStoreId($storeId)
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        $customerId = $this->_service->saveCustomer($newCustomerEntity, 'aPassword');
        $this->assertNotNull($customerId);
        $savedCustomer = $this->_service->getCustomer($customerId);
        $this->assertEquals($email, $savedCustomer->getEmail());
        $this->assertEquals($storeId, $savedCustomer->getStoreId());
        $this->assertEquals($firstname, $savedCustomer->getFirstname());
        $this->assertEquals($lastname, $savedCustomer->getLastname());
        $this->assertEquals($groupId, $savedCustomer->getGroupId());
        $this->assertTrue(!$savedCustomer->getSuffix());
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveNewCustomerFromClone()
    {
        $email = 'savecustomer@example.com';
        $firstName = 'Firstsave';
        $lastname = 'Lastsave';

        $existingCustId = 1;
        $existingCustomer = $this->_service->getCustomer($existingCustId);
        $customerEntity = clone $existingCustomer;
        $customerEntity->setEmail($email);
        $customerEntity->setFirstName($firstName);
        $customerEntity->setLastName($lastname);
        $attributes = array(
            'created_in' => 'Admin',
        );
        $customerEntity->setAttributes($attributes);

        $customerId = $this->_service->saveCustomer($customerEntity, 'aPassword');
        $this->assertNotEmpty($customerId);
        $customer = $this->_service->getCustomer($customerId);
        $this->assertEquals($email, $customer->getEmail());
        $this->assertEquals($firstName, $customer->getFirstName());
        $this->assertEquals($lastname, $customer->getLastName());
        $this->assertEquals('Admin', $customer->getAttribute('created_in'));
        $this->_service->authenticate(
            $customer->getEmail(),
            'aPassword',
            true
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveCustomerRpToken()
    {
        $customer = clone $this->_service->getCustomer(1);
        $customer->setAttribute('rp_token', 'token');
        $customer->setAttribute('rp_token_created_at', '2013-11-05');
        $this->_service->saveCustomer($customer);

        $customer = clone $this->_service->getCustomer(1);
        // Empty current reset password token i.e. invalidate it
        $customer->setAttribute('rp_token', null);
        $customer->setAttribute('rp_token_created_at', null);
        $customer->setConfirmation(null);

        $this->_service->saveCustomer($customer, 'password');

        $customer = $this->_service->getCustomer(1);
        $this->assertEquals('Firstname', $customer->getFirstname());
        $this->assertNull($customer->getAttribute('rp_token'));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testValidateResetPasswordLinkToken()
    {
        $customer = clone $this->_service->getCustomer(1);
        $customer->setAttribute('rp_token', 'token');
        $customer->setAttribute('rp_token_created_at', date('Y-m-d'));
        $this->_service->saveCustomer($customer);

        $this->_service->validateResetPasswordLinkToken(1, 'token');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_RESET_TOKEN_EXPIRED
     */
    public function testValidateResetPasswordLinkTokenExpired()
    {
        $this->_service->validateResetPasswordLinkToken(1, 'some_token');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_RESET_TOKEN
     */
    public function testValidateResetPasswordLinkTokenInvalid()
    {
        $this->_service->validateResetPasswordLinkToken(0, null);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_CUSTOMER_ID
     * @expectedExceptionMessage No customer with customerId 4200 exists
     */
    public function testValidateResetPasswordLinkTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';


        $this->_service->validateResetPasswordLinkToken(4200, $resetToken);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_RESET_TOKEN
     * @expectedExceptionMessage Invalid password reset token
     */
    public function testValidateResetPasswordLinkTokenNull()
    {
        $this->_service->validateResetPasswordLinkToken(null, null);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSendPasswordResetLink()
    {
        $email = 'customer@example.com';

        $this->_service->sendPasswordResetLink($email, 1);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_EMAIL_NOT_FOUND
     * @expectedExceptionMessage No customer found for the provided email and website ID
     */
    public function testSendPasswordResetLinkBadEmailOrWebsite()
    {
        $email = 'foo@example.com';

        $this->_service->sendPasswordResetLink($email, 0);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testResetPassword()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $customer = clone $this->_service->getCustomer(1);
        $customer->setAttribute('rp_token', $resetToken);
        $customer->setAttribute('rp_token_created_at', date('Y-m-d'));
        $this->_service->saveCustomer($customer);

        $this->_service->resetPassword(1, $password, $resetToken);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_RESET_TOKEN_EXPIRED
     * @expectedExceptionMessage Your password reset link has expired.
     */
    public function testResetPasswordTokenExpired()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $customer = clone $this->_service->getCustomer(1);
        $customer->setAttribute('rp_token', $resetToken);
        $customer->setAttribute('rp_token_created_at', '1970-01-01');
        $this->_service->saveCustomer($customer);

        $this->_service->resetPassword(1, $password, $resetToken);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_RESET_TOKEN_EXPIRED
     * @expectedExceptionMessage Your password reset link has expired.
     */
    public function testResetPasswordTokenInvalid()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $invalidToken = $resetToken . 'invalid';
        $password = 'password_secret';

        $customer = clone $this->_service->getCustomer(1);
        $customer->setAttribute('rp_token', $resetToken);
        $customer->setAttribute('rp_token_created_at', date('Y-m-d'));
        $this->_service->saveCustomer($customer);

        $this->_service->resetPassword(1, $password, $invalidToken);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_CUSTOMER_ID
     * @expectedExceptionMessage No customer with customerId 4200 exists
     */
    public function testResetPasswordTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $customer = clone $this->_service->getCustomer(1);
        $customer->setAttribute('rp_token', $resetToken);
        $customer->setAttribute('rp_token_created_at', date('Y-m-d'));
        $this->_service->saveCustomer($customer);

        $this->_service->resetPassword(4200, $password, $resetToken);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_RESET_TOKEN
     * @expectedExceptionMessage Invalid password reset token
     */
    public function testResetPasswordTokenInvalidUserId()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $customer = clone $this->_service->getCustomer(1);
        $customer->setAttribute('rp_token', $resetToken);
        $customer->setAttribute('rp_token_created_at', date('Y-m-d'));
        $this->_service->saveCustomer($customer);

        $this->_service->resetPassword(0, $password, $resetToken);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     */
    public function testSendConfirmation()
    {
        $this->_service->sendConfirmation('customer@needAconfirmation.com');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_EMAIL_NOT_FOUND
     */
    public function testSendConfirmationNoEmail()
    {
        $this->_service->sendConfirmation('wrongemail@example.com');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_CONFIRMATION_NOT_NEEDED
     */
    public function testSendConfirmationNotNeeded()
    {
        $this->_service->sendConfirmation('customer@example.com');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testDeleteAddressFromCustomer()
    {
        $customerId = 1;
        $addressId = 1;
        // See that customer already has an address with expected addressId
        $addressDto = $this->_service->getAddressById($customerId, $addressId);
        $this->assertEquals($addressDto->getId(), $addressId);

        // Delete the address from the customer
        $this->_service->deleteAddressFromCustomer($customerId, $addressId);

        // See that address is deleted
        try {
            $addressDto = $this->_service->getAddressById($customerId, $addressId);
            $this->fail('Did not catch expected exception');
        } catch (\Magento\Customer\Service\Entity\V1\Exception $e) {
            $this->assertEquals($e->getCode(), CustomerV1Interface::CODE_ADDRESS_NOT_FOUND);
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_ADDRESS_NOT_FOUND
     */
    public function testDeleteAddressFromCustomerBadAddrId()
    {
        // Should throw the address not found exception
        $this->_service->deleteAddressFromCustomer(1, 12345);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_ADDRESS_ID
     */
    public function testDeleteAddressFromCustomerAddrIdNotSet()
    {
        // Should throw the address not found exception
        $this->_service->deleteAddressFromCustomer(1, 0);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_CUSTOMER_ID_MISMATCH
     */
    public function testDeleteAddressFromCustomerBadCustMismatch()
    {
        // Should throw the address not found excetion
        $this->_service->deleteAddressFromCustomer(2, 1);
    }
    /**
     * Checks that the arrays are equal, but accounts for the 'region' being an object
     *
     * @param array $expectedArray
     * @param array $actualArray
     */
    protected function _assertAddressAndRegionArrayEquals($expectedArray, $actualArray)
    {
        if (array_key_exists('region', $expectedArray)) {
            /** @var \Magento\Customer\Service\Entity\V1\Region $expectedRegion */
            $expectedRegion = $expectedArray['region'];
            unset($expectedArray['region']);
        }
        if (array_key_exists('region', $actualArray)) {
            /** @var \Magento\Customer\Service\Entity\V1\Region $actualRegion */
            $actualRegion = $actualArray['region'];
            unset($actualArray['region']);
        }

        $this->assertEquals($expectedArray, $actualArray);

        // Either both set or both unset
        $this->assertTrue(!(isset($expectedRegion) xor isset($actualRegion)));
        if (isset($expectedRegion) && isset($actualRegion)) {
            $this->assertInstanceOf('Magento\Customer\Service\Entity\V1\Region', $expectedRegion);
            $this->assertInstanceOf('Magento\Customer\Service\Entity\V1\Region', $actualRegion);
            $this->assertEquals($expectedRegion->__toArray(), $actualRegion->__toArray());
        }
    }
}
