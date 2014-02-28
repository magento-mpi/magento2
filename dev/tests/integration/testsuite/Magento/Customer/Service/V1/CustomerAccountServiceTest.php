<?php

namespace Magento\Customer\Service\V1;
use Magento\Customer\Service\V1;
use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;
use Magento\Exception\StateException;

/**
 * Integration test for service layer \Magento\Customer\Service\V1\CustomerAccountService
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @magentoAppArea frontend
 */
class CustomerAccountServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomerAccountServiceInterface */
    private $_customerAccountService;

    /** @var CustomerServiceInterface needed to setup tests */
    private $_customerService;

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
        $this->_customerAccountService = $this->_objectManager
            ->create('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->_customerService = $this->_objectManager->create('Magento\Customer\Service\V1\CustomerServiceInterface');

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
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testLogin()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $customer = $this->_customerAccountService->authenticate('customer@example.com', 'password', true);

        $this->assertSame('customer@example.com', $customer->getEmail());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Exception\AuthenticationException
     * @expectedExceptionMessage Invalid login or password
     */
    public function testLoginWrongPassword()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->_customerAccountService->authenticate('customer@example.com', 'wrongPassword', true);
    }

    /**
     * @expectedException \Magento\Exception\AuthenticationException
     * @expectedExceptionMessage Invalid login or password
     */
    public function testLoginWrongUsername()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->_customerAccountService->authenticate('non_existing_user', 'password', true);
    }


    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testValidatePassword()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->_customerAccountService->validatePassword(1, 'password');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Exception\AuthenticationException
     * @expectedExceptionMessage Password doesn't match for this account
     */
    public function testValidatePasswordWrongPassword()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->_customerAccountService->validatePassword(1, 'wrongPassword');
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testValidatePasswordWrongUser()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->_customerAccountService->validatePassword(4200, 'password');
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

        $this->_customerAccountService->activateAccount($customerModel->getId());

        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $this->assertNull($customerModel->getConfirmation(), 'Customer should be considered confirmed now');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     * @magentoAppArea frontend
     */
    public function testValidateAccountConfirmationKey()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        // Assert in just one test that the fixture is working
        $this->assertNotNull($customerModel->getConfirmation(), 'New customer needs to be confirmed');

        $valid = $this->_customerAccountService->validateAccountConfirmationKey($customerModel->getId(),
            $customerModel->getConfirmation());

        $this->assertTrue($valid);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     *
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::INPUT_MISMATCH
     */
    public function testValidateAccountConfirmationKeyWrongKey()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $key = $customerModel->getConfirmation();

        try {
            $this->_customerAccountService->validateAccountConfirmationKey($customerModel->getId(), $key . $key);
            $this->fail('Expected exception was not thrown');
        } catch (InputException $ie) {
            $expectedParams = [
                [
                    'code' => InputException::INVALID_FIELD_VALUE,
                    'fieldName' => 'confirmation',
                    'value' => $key . $key,
                ]
            ];
            $this->assertEquals($expectedParams, $ie->getParams());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     */
    public function testActivateAccountWrongAccount()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        try {
            $this->_customerAccountService->activateAccount('1234' . $customerModel->getId());
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $expectedParams = [
                'customerId' => '12341',
            ];
            $this->assertEquals($expectedParams, $nsee->getParams());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     * @magentoAppArea frontend
     *
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::INVALID_STATE
     */
    public function testActivateAccountAlreadyActive()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $this->_customerAccountService->activateAccount($customerModel->getId());

        $this->_customerAccountService->activateAccount($customerModel->getId());
    }


    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testValidateResetPasswordLinkToken()
    {
        $this->_customerBuilder->populateWithArray(array_merge($this->_customerService->getCustomer(1)->__toArray(), [
            'rp_token' => 'token',
            'rp_token_created_at' => date('Y-m-d')
        ]));
        $this->_customerAccountService->updateAccount($this->_customerBuilder->create());

        $this->_customerAccountService->validateResetPasswordLinkToken(1, 'token');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::EXPIRED
     */
    public function testValidateResetPasswordLinkTokenExpired()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';

        $this->_customerBuilder->populateWithArray(array_merge($this->_customerService->getCustomer(1)->__toArray(), [
            'rp_token' => $resetToken,
            'rp_token_created_at' => '1970-01-01',
        ]));
        $customerData = $this->_customerBuilder->create();
        $this->_customerAccountService->updateAccount($customerData);

        $this->_customerAccountService->validateResetPasswordLinkToken(1, $resetToken);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     */
    public function testValidateResetPasswordLinkTokenInvalid()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $invalidToken = 0;

        $this->_customerBuilder->populateWithArray(array_merge($this->_customerService->getCustomer(1)->__toArray(), [
            'rp_token' => $resetToken,
            'rp_token_created_at' => date('Y-m-d')
        ]));
        $this->_customerAccountService->updateAccount($this->_customerBuilder->create());

        try {
            $this->_customerAccountService->validateResetPasswordLinkToken(1, $invalidToken);
            $this->fail('Expected exception not thrown.');
        } catch (InputException $ie) {
            $expectedParams = [
                [
                    'value' => $invalidToken,
                    'fieldName' => 'resetPasswordLinkToken',
                    'code' => InputException::INVALID_FIELD_VALUE,
                ]
            ];
            $this->assertEquals($expectedParams, $ie->getParams());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     */
    public function testValidateResetPasswordLinkTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';

        try {
            $this->_customerAccountService->validateResetPasswordLinkToken(4200, $resetToken);
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $expectedParams = [
                'customerId' => '4200',
            ];
            $this->assertEquals($expectedParams, $nsee->getParams());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     */
    public function testValidateResetPasswordLinkTokenNull()
    {
        try {
            $this->_customerAccountService->validateResetPasswordLinkToken(1, null);
            $this->fail('Expected exception not thrown.');
        } catch (InputException $ie) {
            $expectedParams = [
                [
                    'value' => null,
                    'fieldName' => 'resetPasswordLinkToken',
                    'code' => InputException::INVALID_FIELD_VALUE,
                ]
            ];
            $this->assertEquals($expectedParams, $ie->getParams());
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSendPasswordResetLink()
    {
        $email = 'customer@example.com';

        $this->_customerAccountService->sendPasswordResetLink($email, 1, CustomerAccountServiceInterface::EMAIL_RESET);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     */
    public function testSendPasswordResetLinkBadEmailOrWebsite()
    {
        $email = 'foo@example.com';

        try {
            $this->_customerAccountService->sendPasswordResetLink($email, 0,
                CustomerAccountServiceInterface::EMAIL_RESET);
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $expectedParams = [
                'email' => $email,
                'websiteId' => 0,
            ];
            $this->assertEquals($expectedParams, $nsee->getParams());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testChangePassword()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $this->_customerBuilder->populateWithArray(array_merge($this->_customerService->getCustomer(1)->__toArray(), [
            'rp_token' => $resetToken,
            'rp_token_created_at' => date('Y-m-d')
        ]));
        $this->_customerAccountService->updateAccount($this->_customerBuilder->create());

        $this->_customerAccountService->changePassword(1, $password);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testResetPasswordTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $this->_customerBuilder->populateWithArray(array_merge($this->_customerService->getCustomer(1)->__toArray(), [
            'rp_token' => $resetToken,
            'rp_token_created_at' => date('Y-m-d')
        ]));
        $this->_customerAccountService->updateAccount($this->_customerBuilder->create());
        try {
            $this->_customerAccountService->changePassword(4200, $password);
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $expectedParams = [
                'customerId' => '4200',
            ];
            $this->assertEquals($expectedParams, $nsee->getParams());
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     */
    public function testSendConfirmation()
    {
        $this->_customerAccountService->sendConfirmation('customer@needAconfirmation.com');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSendConfirmationNoEmail()
    {
        try {
            $this->_customerAccountService->sendConfirmation('wrongemail@example.com');
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $expectedParams = [
                'email' => 'wrongemail@example.com',
                'websiteId' => '1',
            ];
            $this->assertEquals($expectedParams, $nsee->getParams());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::INVALID_STATE
     */
    public function testSendConfirmationNotNeeded()
    {
        $this->_customerAccountService->sendConfirmation('customer@example.com');
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

        $customerBefore = $this->_customerService->getCustomer($existingCustId);

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

        $returnedCustomerId = $this->_customerAccountService->saveCustomer($modifiedCustomer, 'aPassword');
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_customerService->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastname, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_customerAccountService->authenticate(
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

        $customerBefore = $this->_customerService->getCustomer($existingCustId);
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

        $returnedCustomerId = $this->_customerAccountService->saveCustomer($modifiedCustomer);
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_customerService->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastName, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_customerAccountService->authenticate(
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

        $customerBefore = $this->_customerService->getCustomer($existingCustId);
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

        $returnedCustomerId = $this->_customerAccountService->saveCustomer($modifiedCustomer);
        $this->assertEquals($existingCustId, $returnedCustomerId);
        $customerAfter = $this->_customerService->getCustomer($existingCustId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastName, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_customerAccountService->authenticate(
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
            $this->_customerAccountService->saveCustomer($customerEntity);
            $this->fail('Expected exception not thrown');
        } catch (InputException $ie) {
            $expectedParams = [
                [
                    'fieldName' => 'firstname',
                    'value' => '',
                    'code' => InputException::REQUIRED_FIELD,
                ],
                [
                    'fieldName' => 'lastname',
                    'value' => '',
                    'code' => InputException::REQUIRED_FIELD,
                ],
                [
                    'fieldName' => 'email',
                    'value' => '',
                    'code' => InputException::INVALID_FIELD_VALUE,
                ],
            ];
            $this->assertEquals($expectedParams, $ie->getParams());
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveNonexistingCustomer()
    {
        $existingCustId = 1;
        $existingCustomer = $this->_customerService->getCustomer($existingCustId);

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

        $customerId = $this->_customerAccountService->saveCustomer($customerEntity, 'aPassword');
        $this->assertEquals($newCustId, $customerId);
        $customerAfter = $this->_customerService->getCustomer($customerId);
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastName, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getAttribute('created_in'));
        $this->_customerAccountService->authenticate(
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
        $customerId = $this->_customerAccountService->saveCustomer($newCustomerEntity, $password);
        $this->assertNotNull($customerId);
        $savedCustomer = $this->_customerService->getCustomer($customerId);
        $dataInService = $savedCustomer->getAttributes();
        $expectedDifferences = ['created_at', 'updated_at', 'email', 'is_active', 'entity_id', 'entity_type_id',
            'password_hash', 'attribute_set_id', 'disable_auto_group_change', 'confirmation',
            'reward_update_notification', 'reward_warning_notification'];
        foreach ($dataInModel as $key => $value) {
            if (!in_array($key, $expectedDifferences)) {
                if (is_null($value)) {
                    $this->assertArrayNotHasKey($key, $dataInService);
                } else {
                    $this->assertEquals($value, $dataInService[$key], 'Failed asserting value for '. $key);
                }
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
        $customerId = $this->_customerAccountService->saveCustomer($newCustomerEntity, 'aPassword');
        $this->assertNotNull($customerId);
        $savedCustomer = $this->_customerService->getCustomer($customerId);
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
        $existingCustomer = $this->_customerService->getCustomer($existingCustId);
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

        $customerId = $this->_customerAccountService->saveCustomer($customerEntity, 'aPassword');
        $this->assertNotEmpty($customerId);
        $customer = $this->_customerService->getCustomer($customerId);
        $this->assertEquals($email, $customer->getEmail());
        $this->assertEquals($firstName, $customer->getFirstname());
        $this->assertEquals($lastname, $customer->getLastname());
        $this->assertEquals('Admin', $customer->getAttribute('created_in'));
        $this->_customerAccountService->authenticate(
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
        $this->_customerBuilder->populateWithArray(array_merge($this->_customerService->getCustomer(1)->__toArray(), [
            'rp_token' => 'token',
            'rp_token_created_at' => '2013-11-05'
        ]));
        $customer = $this->_customerBuilder->create();
        $this->_customerAccountService->saveCustomer($customer);

        // Empty current reset password token i.e. invalidate it
        $this->_customerBuilder->populateWithArray(array_merge($this->_customerService->getCustomer(1)->__toArray(), [
            'rp_token' => null,
            'rp_token_created_at' => null
        ]));
        $this->_customerBuilder->setConfirmation(null);
        $customer = $this->_customerBuilder->create();

        $this->_customerAccountService->saveCustomer($customer, 'password');

        $customer = $this->_customerService->getCustomer(1);
        $this->assertEquals('Firstname', $customer->getFirstname());
        $this->assertNull($customer->getAttribute('rp_token'));
    }


    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveCustomerNewThenUpdateFirstName()
    {
        $email = 'first_last@example.com';
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
        $customerId = $this->_customerAccountService->saveCustomer($newCustomerEntity, 'aPassword');

        $this->_customerBuilder->populate($this->_customerService->getCustomer($customerId));
        $this->_customerBuilder->setFirstname('Tested');
        $this->_customerAccountService->saveCustomer($this->_customerBuilder->create());

        $customer = $this->_customerService->getCustomer($customerId);

        $this->assertEquals('Tested', $customer->getFirstname());
        $this->assertEquals($lastname, $customer->getLastname());
    }
}
