<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\Entity\V1\Exception;
use Magento\Customer\Service\V1;

/**
 * Integration test for service layer \Magento\Customer\Service\CustomerV1
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class CustomerServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomerServiceInterface */
    private $_service;

    /** @var CustomerAccountServiceInterface Needed for password checking */
    private $_accountService;

    /** @var CustomerAddressServiceInterface Needed for verifying if addresses are deleted */
    private $_addressService;

    /** @var \Magento\ObjectManager */
    private $_objectManager;

    /** @var \Magento\Customer\Service\V1\Dto\Address[] */
    private $_expectedAddresses;

    /** @var \Magento\Customer\Service\V1\Dto\AddressBuilder */
    private $_addressBuilder;

    /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder */
    private $_customerBuilder;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_service = $this->_objectManager->create('Magento\Customer\Service\V1\CustomerServiceInterface');
        $this->_accountService = $this->_objectManager
            ->create('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->_addressService = $this->_objectManager
            ->create('Magento\Customer\Service\V1\CustomerAddressServiceInterface');

        $this->_addressBuilder = $this->_objectManager->create('Magento\Customer\Service\V1\Dto\AddressBuilder');
        $this->_customerBuilder = $this->_objectManager->create('Magento\Customer\Service\V1\Dto\CustomerBuilder');

        $this->_addressBuilder->setId(1)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion(new V1\Dto\Region([
                'region_code' => 'AL',
                'region' => 'Alabama',
                'region_id' => 1
            ]))
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address = $this->_addressBuilder->create();

        /* XXX: would it be better to have a clear method for this? */
        $this->_addressBuilder->setId(2)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->setPostcode('47676')
            ->setRegion(new V1\Dto\Region([
                'region_code' => 'AL',
                'region' => 'Alabama',
                'region_id' => 1
            ]))
            ->setStreet(['Black str, 48'])
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address2 = $this->_addressBuilder->create();

        $this->_expectedAddresses = [$address, $address2];
    }

    /**
     * @param mixed $custId
     * @dataProvider invalidCustomerIdsDataProvider
     * @expectedException Exception
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
     * @magentoAppIsolation enabled
     */
    public function testGetCustomer()
    {
        // _files/customer.php sets the customer id to 1
        $customer = $this->_service->getCustomer(1);

        // All these expected values come from _files/customer.php fixture
        $this->assertEquals(1, $customer->getCustomerId());
        $this->assertEquals('customer@example.com', $customer->getEmail());
        $this->assertEquals('Firstname', $customer->getFirstname());
        $this->assertEquals('Lastname', $customer->getLastname());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage No customer with customerId 1 exists.
     */
    public function testGetCustomerNotExist()
    {
        // No fixture, so customer with id 1 shouldn't exist, exception should be thrown
        $this->_service->getCustomer(1);
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

        $customerData = array_merge($customerBefore->__toArray(), array(
            'id' => 1,
            'email' => $email,
            'firstname' => $firstName,
            'lastname' => $lastname,
            'created_in' => 'Admin',
            'password' => 'notsaved'
        ));
        $this->_customerBuilder->populateWithArray($customerData);
        $modifiedCustomer = $this->_customerBuilder->create();

        $returnedCustomerId = $this->_service->saveCustomer($modifiedCustomer, 'aPassword');
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_service->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastname, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_accountService->authenticate(
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
            'email',
            'firstname',
            'lastname',
        );
        $this->assertEquals($expectedInBefore, array_keys($inBeforeOnly));
        $this->assertContains('created_in', array_keys($inAfterOnly));
        $this->assertContains('firstname', array_keys($inAfterOnly));
        $this->assertContains('lastname', array_keys($inAfterOnly));
        $this->assertContains('email', array_keys($inAfterOnly));
        $this->assertNotContains('password_hash', array_keys($inAfterOnly));
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
        $lastName = 'Lastsave';

        $customerBefore = $this->_service->getCustomer($existingCustId);
        $customerData = array_merge($customerBefore->__toArray(),
            [
                'id' => 1,
                'email' => $email,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'created_in' => 'Admin'
            ]
        );
        $this->_customerBuilder->populateWithArray($customerData);
        $modifiedCustomer = $this->_customerBuilder->create();

        $returnedCustomerId = $this->_service->saveCustomer($modifiedCustomer);
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_service->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastName, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_accountService->authenticate(
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
        sort($expectedInBefore);
        $actualInBeforeOnly = array_keys($inBeforeOnly);
        sort($actualInBeforeOnly);
        $this->assertEquals($expectedInBefore, $actualInBeforeOnly);
        $expectedInAfter = array(
            'firstname',
            'lastname',
            'email',
            'created_in',
        );
        sort($expectedInAfter);
        $actualInAfterOnly = array_keys($inAfterOnly);
        sort($actualInAfterOnly);
        $this->assertEquals($expectedInAfter, $actualInAfterOnly);
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
        $lastName = 'Lastsave';

        $customerBefore = $this->_service->getCustomer($existingCustId);
        $customerData = array_merge($customerBefore->__toArray(),
            [
                'id' => 1,
                'email' => $email,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'created_in' => 'Admin',
                'password' => 'aPassword'
            ]
        );
        $this->_customerBuilder->populateWithArray($customerData);
        $modifiedCustomer = $this->_customerBuilder->create();

        $returnedCustomerId = $this->_service->saveCustomer($modifiedCustomer);
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_service->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastName, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_accountService->authenticate(
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
        sort($expectedInBefore);
        $actualInBeforeOnly = array_keys($inBeforeOnly);
        sort($actualInBeforeOnly);
        $this->assertEquals($expectedInBefore, $actualInBeforeOnly);
        $expectedInAfter = array(
            'firstname',
            'lastname',
            'email',
            'created_in',
        );
        sort($expectedInAfter);
        $actualInAfterOnly = array_keys($inAfterOnly);
        sort($actualInAfterOnly);
        $this->assertEquals($expectedInAfter, $actualInAfterOnly);
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Validator\ValidatorException
     * @expectedExceptionMessage Please correct this email address
     */
    public function testSaveCustomerException()
    {
        $customerData = [
            'id' => 1,
            'password' => 'aPassword'
        ];
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();

        try {
            $this->_service->saveCustomer($customerEntity);
        } catch (Exception $e) {
            $this->assertEquals('There were one or more errors validating the customer object.', $e->getMessage());
            $this->assertEquals(Exception::CODE_VALIDATION_FAILED, $e->getCode());
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
        $lastName = 'Lastsave';
        $customerData = array_merge($existingCustomer->__toArray(),
            [
                'id' => $newCustId,
                'email' => $email,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'created_in' => 'Admin'
            ]
        );
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();

        $customerId = $this->_service->saveCustomer($customerEntity, 'aPassword');
        $this->assertEquals($newCustId, $customerId);
        $customerAfter = $this->_service->getCustomer($customerId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastName, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_accountService->authenticate(
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
            'email',
            'firstname',
            'id',
            'lastname'
        );
        sort($expectedInBefore);
        $actualInBeforeOnly = array_keys($inBeforeOnly);
        sort($actualInBeforeOnly);
        $this->assertEquals($expectedInBefore, $actualInBeforeOnly);
        $expectedInAfter = array(
            'created_in',
            'email',
            'firstname',
            'id',
            'lastname',
        );
        sort($expectedInAfter);
        $actualInAfterOnly = array_keys($inAfterOnly);
        sort($actualInAfterOnly);
        $this->assertEquals($expectedInAfter, $actualInAfterOnly);
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

        $this->_customerBuilder->setEmail($email2)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        $newCustomerEntity = $this->_customerBuilder->create();
        $customerId = $this->_service->saveCustomer($newCustomerEntity, $password);
        $this->assertNotNull($customerId);
        $savedCustomer = $this->_service->getCustomer($customerId);
        $dataInService = $savedCustomer->getAttributes();
        foreach ($dataInModel as $key => $value) {
            $fieldsToValidate = [
                'id',
                'created_in',
                'firstname',
                'group_id',
                'lastname',
                'store_id',
                'website_id',
            ];
            if (in_array($key, $fieldsToValidate)) {
                $this->assertEquals($value, $dataInService[$key], 'Failed asserting value for ' . $key);
            }
        }
        $this->assertEquals($email2, $dataInService['email']);
        $this->assertArrayNotHasKey('is_active', $dataInService);
        $this->assertArrayNotHasKey('updated_at', $dataInService);
        $this->assertArrayNotHasKey('password_hash', $dataInService);
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

        $this->_customerBuilder->setStoreId($storeId)
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        $newCustomerEntity = $this->_customerBuilder->create();
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
        $customerData = array_merge($existingCustomer->__toArray(),
            [
                'email' => $email,
                'firstname' => $firstName,
                'lastname' => $lastname,
                'created_in' => 'Admin'
            ]
        );
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();

        $customerId = $this->_service->saveCustomer($customerEntity, 'aPassword');
        $this->assertNotEmpty($customerId);
        $customer = $this->_service->getCustomer($customerId);
        $this->assertEquals($email, $customer->getEmail());
        $this->assertEquals($firstName, $customer->getFirstname());
        $this->assertEquals($lastname, $customer->getLastname());
        $this->assertEquals('Admin', $customer->getAttribute('created_in'));
        $this->_accountService->authenticate(
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
        $this->_customerBuilder->populateWithArray(array_merge($this->_service->getCustomer(1)->__toArray(), [
            'rp_token' => 'token',
            'rp_token_created_at' => '2013-11-05'
        ]));
        $customer = $this->_customerBuilder->create();
        $this->_service->saveCustomer($customer);

        // Empty current reset password token i.e. invalidate it
        $this->_customerBuilder->populateWithArray(array_merge($this->_service->getCustomer(1)->__toArray(), [
            'rp_token' => null,
            'rp_token_created_at' => null
        ]));
        $this->_customerBuilder->setConfirmation(null);
        $customer = $this->_customerBuilder->create();

        $this->_service->saveCustomer($customer, 'password');

        $customer = $this->_service->getCustomer(1);
        $this->assertEquals('Firstname', $customer->getFirstname());
        $this->assertNull($customer->getAttribute('rp_token'));
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage No customer with customerId 1 exists.
     */
    public function testDeleteCustomer()
    {
        // _files/customer.php sets the customer id to 1
        $this->_service->deleteCustomer(1);
        $this->_service->getCustomer(1);
    }

    /**
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppArea adminhtml
     * @magentoAppIsolation enabled
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage No customer with customerId 1 exists.
     */
    public function testDeleteCustomerWithAddress()
    {
        //Verify address is created for the customer;
        $result = $this->_addressService->getAddresses(1);
        $this->assertEquals(2, count($result));
        // _files/customer.php sets the customer id to 1
        $this->_service->deleteCustomer(1);

        // Verify by directly loading the address by id
        $this->_verifyDeletedAddress(1);
        $this->_verifyDeletedAddress(2);

        //Verify by calling the Address Service. This will throw the expected exception since customerId doesn't exist
        $result = $this->_addressService->getAddresses(1);
        $this->assertTrue(empty($result));
    }

    /**
     * Check if the Address with the give addressid is deleted
     *
     * @param int $addressId
     */
    private function _verifyDeletedAddress($addressId)
    {
        /** @var $addressFactory \Magento\Customer\Model\AddressFactory*/
        $addressFactory = $this->_objectManager
            ->create('Magento\Customer\Model\AddressFactory');
        $addressModel = $addressFactory->create()->load($addressId);
        $addressData = $addressModel->getData();
        $this->assertTrue(empty($addressData));
    }
}
