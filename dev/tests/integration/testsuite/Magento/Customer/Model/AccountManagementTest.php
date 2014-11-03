<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Service\V1;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\ExpiredException;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Integration test for service layer \Magento\Customer\Model\AccountManagementTest
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @magentoAppArea frontend
 */
class AccountManagementTest extends \PHPUnit_Framework_TestCase
{
    /** @var AccountManagementInterface */
    private $accountManagement;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var CustomerAddressServiceInterface needed to setup tests */
    private $_customerAddressService;

    /** @var \Magento\Framework\ObjectManager */
    private $objectManager;

    /** @var \Magento\Customer\Service\V1\Data\Address[] */
    private $_expectedAddresses;

    /** @var \Magento\Customer\Api\Data\AddressDataBuilder */
    private $addressBuilder;

    /** @var \Magento\Customer\Api\Data\CustomerDataBuilder */
    private $customerBuilder;

    protected function setUp()
    {
        //$this->markTestSkipped('As of skipping 11 out of 38 failed. Should be fixed as part of MAGETWO-29378.');
        $this->objectManager = Bootstrap::getObjectManager();
        $this->accountManagement = $this->objectManager
            ->create('Magento\Customer\Api\AccountManagementInterface');
        $this->customerRepository = $this->objectManager
            ->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $this->_customerAddressService =
            $this->objectManager->create('Magento\Customer\Service\V1\CustomerAddressServiceInterface');

        $this->addressBuilder = $this->objectManager->create('Magento\Customer\Api\Data\AddressDataBuilder');
        $this->customerBuilder = $this->objectManager->create('Magento\Customer\Api\Data\CustomerDataBuilder');

        $regionBuilder = $this->objectManager->create('Magento\Customer\Api\Data\RegionDataBuilder');
        $this->addressBuilder->setId(1)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setPostcode('75477')
            ->setRegion(
                $regionBuilder->setRegionCode('AL')->setRegion('Alabama')->setRegionId(1)->create()
            )
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address = $this->addressBuilder->create();

        $this->addressBuilder->setId(2)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setPostcode('47676')
            ->setRegion(
                $regionBuilder->setRegionCode('AL')->setRegion('Alabama')->setRegionId(1)->create()
            )
            ->setStreet(['Black str, 48'])
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address2 = $this->addressBuilder->create();

        $this->_expectedAddresses = [$address, $address2];
    }

    /**
     * Clean up shared dependencies
     */
    protected function tearDown()
    {
        /** @var \Magento\Customer\Model\CustomerRegistry $customerRegistry */
        $customerRegistry = $this->objectManager->get('Magento\Customer\Model\CustomerRegistry');
        //Cleanup customer from registry
        $customerRegistry->remove(1);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testLogin()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $customer = $this->accountManagement->authenticate('customer@example.com', 'password', true);

        $this->assertSame('customer@example.com', $customer->getEmail());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Framework\Exception\InvalidEmailOrPasswordException
     * @expectedExceptionMessage Invalid login or password.
     */
    public function testLoginWrongPassword()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->accountManagement->authenticate('customer@example.com', 'wrongPassword', true);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InvalidEmailOrPasswordException
     * @expectedExceptionMessage Invalid login or password.
     */
    public function testLoginWrongUsername()
    {
        // Customer e-mail and password are pulled from the fixture customer.php
        $this->accountManagement->authenticate('non_existing_user', 'password', true);
    }


    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testChangePassword()
    {
        $this->accountManagement->changePassword('customer@example.com', 'password', 'new_password');

        $this->accountManagement->authenticate('customer@example.com', 'new_password');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @expectedException \Magento\Framework\Exception\InvalidEmailOrPasswordException
     * @expectedExceptionMessage Password doesn't match for this account
     */
    public function testChangePasswordWrongPassword()
    {
        $this->accountManagement->changePassword(1, 'wrongPassword', 'new_password');
    }

    /**
     * @expectedException \Magento\Framework\Exception\InvalidEmailOrPasswordException
     * @expectedExceptionMessage Password doesn't match for this account
     */
    public function testChangePasswordWrongUser()
    {
        $this->accountManagement->changePassword(4200, 'password', 'new_password');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     * @magentoAppArea frontend
     */
    public function testActivateAccount()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        // Assert in just one test that the fixture is working
        $this->assertNotNull($customerModel->getConfirmation(), 'New customer needs to be confirmed');

        $this->accountManagement->activate($customerModel->getEmail(), $customerModel->getConfirmation());

        $customerModel = $this->objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $this->assertNull($customerModel->getConfirmation(), 'Customer should be considered confirmed now');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     * @expectedException \Magento\Framework\Exception\State\InputMismatchException
     */
    public function testActivateCustomerConfirmationKeyWrongKey()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $key = $customerModel->getConfirmation();

        try {
            $this->accountManagement->activate($customerModel->getEmail(), $key . $key);
            $this->fail('Expected exception was not thrown');
        } catch (InputException $ie) {
            $this->assertEquals('', $ie->getMessage());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     */
    public function testActivateCustomerWrongAccount()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $key = $customerModel->getConfirmation();
        try {
            $this->accountManagement->activate('1234' . $customerModel->getEmail(), $key);
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $this->assertEquals(
                'No such entity with email = 1234customer@needAconfirmation.com, websiteId = 1',
                $nsee->getMessage()
            );
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     * @magentoAppArea frontend
     * @expectedException \Magento\Framework\Exception\State\InvalidTransitionException
     */
    public function testActivateCustomerAlreadyActive()
    {
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load(1);
        $key = $customerModel->getConfirmation();
        $this->accountManagement->activate($customerModel->getEmail(), $key);
        // activate it one more time to produce an exception
        $this->accountManagement->activate($customerModel->getEmail(), $key);
    }


    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testValidateResetPasswordLinkToken()
    {
        $this->setResetPasswordData('token', 'Y-m-d');
        $this->accountManagement->validateResetPasswordLinkToken(1, 'token');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @expectedException \Magento\Framework\Exception\State\ExpiredException
     */
    public function testValidateResetPasswordLinkTokenExpired()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $this->setResetPasswordData($resetToken, '1970-01-01');
        $this->accountManagement->validateResetPasswordLinkToken(1, $resetToken);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testValidateResetPasswordLinkTokenInvalid()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $invalidToken = 0;
        $this->setResetPasswordData($resetToken, 'Y-m-d');
        try {
            $this->accountManagement->validateResetPasswordLinkToken(1, $invalidToken);
            $this->fail('Expected exception not thrown.');
        } catch (InputException $ie) {
            $this->assertEquals(InputException::REQUIRED_FIELD, $ie->getRawMessage());
            $this->assertEquals('resetPasswordLinkToken is a required field.', $ie->getMessage());
            $this->assertEquals('resetPasswordLinkToken is a required field.', $ie->getLogMessage());
            $this->assertEmpty($ie->getErrors());
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
            $this->accountManagement->validateResetPasswordLinkToken(4200, $resetToken);
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $this->assertEquals('No such entity with customerId = 4200', $nsee->getMessage());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     */
    public function testValidateResetPasswordLinkTokenNull()
    {
        try {
            $this->accountManagement->validateResetPasswordLinkToken(1, null);
            $this->fail('Expected exception not thrown.');
        } catch (InputException $ie) {
            $this->assertEquals(InputException::REQUIRED_FIELD, $ie->getRawMessage());
            $this->assertEquals('resetPasswordLinkToken is a required field.', $ie->getMessage());
            $this->assertEquals('resetPasswordLinkToken is a required field.', $ie->getLogMessage());
            $this->assertEmpty($ie->getErrors());
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSendPasswordResetLink()
    {
        $email = 'customer@example.com';

        $this->accountManagement->initiatePasswordReset($email, AccountManagement::EMAIL_RESET, 1);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSendPasswordResetLinkDefaultWebsite()
    {
        $email = 'customer@example.com';

        $this->accountManagement->initiatePasswordReset($email, AccountManagement::EMAIL_RESET);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     */
    public function testSendPasswordResetLinkBadEmailOrWebsite()
    {
        $email = 'foo@example.com';

        try {
            $this->accountManagement->initiatePasswordReset(
                $email,
                AccountManagement::EMAIL_RESET,
                0
            );
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $e) {
            $expectedParams = [
                'fieldName' => 'email',
                'fieldValue' => $email,
                'field2Name' => 'websiteId',
                'field2Value' => 0,
            ];
            $this->assertEquals($expectedParams, $e->getParameters());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSendPasswordResetLinkBadEmailDefaultWebsite()
    {
        $email = 'foo@example.com';

        try {
            $this->accountManagement->initiatePasswordReset(
                $email,
                AccountManagement::EMAIL_RESET
            );
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            // App area is frontend, so we expect websiteId of 1.
            $this->assertEquals('No such entity with email = foo@example.com, websiteId = 1', $nsee->getMessage());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testResetPassword()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'new_password';

        $this->setResetPasswordData($resetToken, 'Y-m-d');
        $this->assertTrue($this->accountManagement->resetPassword('customer@example.com', $resetToken, $password));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testResetPasswordTokenExpired()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'new_password';

        $this->setResetPasswordData($resetToken, '1970-01-01');
        try {
            $this->accountManagement->resetPassword('customer@example.com', $resetToken, $password);
            $this->fail('Expected exception not thrown.');
        } catch (ExpiredException $e) {
            $this->assertEquals('Reset password token expired.', $e->getMessage());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     */
    public function testResetPasswordTokenInvalid()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $invalidToken = 0;
        $password = 'new_password';

        $this->setResetPasswordData($resetToken, 'Y-m-d');
        try {
            $this->accountManagement->resetPassword('customer@example.com', $invalidToken, $password);
            $this->fail('Expected exception not thrown.');
        } catch (InputException $ie) {
            $this->assertEquals(InputException::REQUIRED_FIELD, $ie->getRawMessage());
            $this->assertEquals('resetPasswordLinkToken is a required field.', $ie->getMessage());
            $this->assertEquals('resetPasswordLinkToken is a required field.', $ie->getLogMessage());
            $this->assertEmpty($ie->getErrors());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testResetPasswordTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'new_password';
        $this->setResetPasswordData($resetToken, 'Y-m-d');
        try {
            $this->accountManagement->resetPassword('invalid-customer@example.com', $resetToken, $password);
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $this->assertEquals(
                'No such entity with email = invalid-customer@example.com, websiteId = 1',
                $nsee->getMessage()
            );
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testResetPasswordTokenInvalidUserEmail()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'new_password';

        $this->setResetPasswordData($resetToken, 'Y-m-d');

        try {
            $this->accountManagement->resetPassword('invalid', $resetToken, $password);
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $e) {
            $this->assertEquals('No such entity with email = invalid, websiteId = 1', $e->getMessage());
        }

    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     */
    public function testResendConfirmation()
    {
        $this->accountManagement->resendConfirmation('customer@needAconfirmation.com', 1);
        //TODO assert
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/inactive_customer.php
     */
    public function testResendConfirmationBadWebsiteId()
    {
        try {
            $this->accountManagement->resendConfirmation('customer@needAconfirmation.com', 'notAWebsiteId');
        } catch (NoSuchEntityException $nsee) {
            $this->assertEquals(
                'No such entity with email = customer@needAconfirmation.com, websiteId = notAWebsiteId',
                $nsee->getMessage()
            );
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testResendConfirmationNoEmail()
    {
        try {
            $this->accountManagement->resendConfirmation('wrongemail@example.com', 1);
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $nsee) {
            $this->assertEquals(
                'No such entity with email = wrongemail@example.com, websiteId = 1',
                $nsee->getMessage()
            );
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @expectedException \Magento\Framework\Exception\State\InvalidTransitionException
     */
    public function testResendConfirmationNotNeeded()
    {
        $this->accountManagement->resendConfirmation('customer@example.com', 1);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateCustomerException()
    {
        $customerEntity = $this->customerBuilder->create();

        try {
            $this->accountManagement->createAccount($customerEntity);
            $this->fail('Expected exception not thrown');
        } catch (InputException $ie) {
            $this->assertEquals(InputException::DEFAULT_MESSAGE, $ie->getMessage());
            $errors = $ie->getErrors();
            $this->assertCount(3, $errors);
            $this->assertEquals('firstname is a required field.', $errors[0]->getLogMessage());
            $this->assertEquals('lastname is a required field.', $errors[1]->getLogMessage());
            $this->assertEquals('Invalid value of "" provided for the email field.', $errors[2]->getLogMessage());
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDbIsolation enabled
     */
    public function testCreateNonexistingCustomer()
    {
        $existingCustId = 1;
        $existingCustomer = $this->customerRepository->getById($existingCustId);

        $email = 'savecustomer@example.com';
        $firstName = 'Firstsave';
        $lastName = 'Lastsave';
        $customerData = array_merge(
            $existingCustomer->__toArray(),
            [
                'email' => $email,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'created_in' => 'Admin',
                'id' => null
            ]
        );
        $this->customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->customerBuilder->create();

        $customerAfter = $this->accountManagement->createAccount($customerEntity, 'aPassword');
        $this->assertGreaterThan(0, $customerAfter->getId());
        $this->assertEquals($email, $customerAfter->getEmail());
        $this->assertEquals($firstName, $customerAfter->getFirstname());
        $this->assertEquals($lastName, $customerAfter->getLastname());
        $this->assertEquals('Admin', $customerAfter->getCreatedIn());
        $this->accountManagement->authenticate(
            $customerAfter->getEmail(),
            'aPassword',
            true
        );
        $attributesBefore = \Magento\Framework\Api\ExtensibleDataObjectConverter::toFlatArray($existingCustomer);
        $attributesAfter = \Magento\Framework\Api\ExtensibleDataObjectConverter::toFlatArray($customerAfter);
        // ignore 'updated_at'
        unset($attributesBefore['updated_at']);
        unset($attributesAfter['updated_at']);
        $inBeforeOnly = array_diff_assoc($attributesBefore, $attributesAfter);
        $inAfterOnly = array_diff_assoc($attributesAfter, $attributesBefore);
        $expectedInBefore = [
            'email',
            'firstname',
            'id',
            'lastname'
        ];
        sort($expectedInBefore);
        $actualInBeforeOnly = array_keys($inBeforeOnly);
        sort($actualInBeforeOnly);
        $this->assertEquals($expectedInBefore, $actualInBeforeOnly);
        $expectedInAfter = [
            'created_in',
            'email',
            'firstname',
            'id',
            'lastname',
        ];
        sort($expectedInAfter);
        $actualInAfterOnly = array_keys($inAfterOnly);
        sort($actualInAfterOnly);
        $this->assertEquals($expectedInAfter, $actualInAfterOnly);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateCustomerInServiceVsInModel()
    {
        $email = 'email@example.com';
        $email2 = 'email2@example.com';
        $firstname = 'Tester';
        $lastname = 'McTest';
        $groupId = 1;
        $password = 'aPassword';

        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->objectManager->create('Magento\Customer\Model\CustomerFactory')->create();
        $customerModel->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId)
            ->setPassword($password);
        $customerModel->save();
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $savedModel = $this->objectManager
            ->create('Magento\Customer\Model\CustomerFactory')
            ->create()
            ->load($customerModel->getId());
        $dataInModel = $savedModel->getData();

        $this->customerBuilder
            ->setEmail($email2)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        $newCustomerEntity = $this->customerBuilder->create();
        $customerData = $this->accountManagement->createAccount($newCustomerEntity, $password);
        $this->assertNotNull($customerData->getId());
        $savedCustomer = $this->customerRepository->getById($customerData->getId());
        $dataInService = \Magento\Framework\Api\SimpleDataObjectConverter::toFlatArray($savedCustomer);
        $expectedDifferences = [
            'created_at',
            'updated_at',
            'email',
            'is_active',
            'entity_id',
            'entity_type_id',
            'password_hash',
            'attribute_set_id',
            'disable_auto_group_change',
            'confirmation',
            'reward_update_notification',
            'reward_warning_notification'
        ];
        foreach ($dataInModel as $key => $value) {
            if (!in_array($key, $expectedDifferences)) {
                if (is_null($value)) {
                    $this->assertArrayNotHasKey($key, $dataInService);
                } else {
                    $this->assertEquals($value, $dataInService[$key], 'Failed asserting value for ' . $key);
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
    public function testCreateNewCustomer()
    {
        $email = 'email@example.com';
        $storeId = 1;
        $firstname = 'Tester';
        $lastname = 'McTest';
        $groupId = 1;

        $this->customerBuilder
            ->setStoreId($storeId)
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        $newCustomerEntity = $this->customerBuilder->create();
        $savedCustomer = $this->accountManagement->createAccount($newCustomerEntity, 'aPassword');
        $this->assertNotNull($savedCustomer->getId());
        $this->assertEquals($email, $savedCustomer->getEmail());
        $this->assertEquals($storeId, $savedCustomer->getStoreId());
        $this->assertEquals($firstname, $savedCustomer->getFirstname());
        $this->assertEquals($lastname, $savedCustomer->getLastname());
        $this->assertEquals($groupId, $savedCustomer->getGroupId());
        $this->assertTrue(!$savedCustomer->getSuffix());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateNewCustomerWithPasswordHash()
    {
        $email = 'email@example.com';
        $storeId = 1;
        $firstname = 'Tester';
        $lastname = 'McTest';
        $groupId = 1;

        $this->customerBuilder->setStoreId($storeId)
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        $newCustomerEntity = $this->customerBuilder->create();
        /** @var \Magento\Framework\Math\Random $mathRandom */
        $password = $this->objectManager->get('Magento\Framework\Math\Random')->getRandomString(
            AccountManagement::MIN_PASSWORD_LENGTH
        );
        /** @var \Magento\Framework\Encryption\EncryptorInterface $encryptor */
        $encryptor = $this->objectManager->get('Magento\Framework\Encryption\EncryptorInterface');
        $passwordHash = $encryptor->getHash($password);
        $savedCustomer = $this->accountManagement->createAccountWithPasswordHash(
            $newCustomerEntity,
            $passwordHash
        );
        $this->assertNotNull($savedCustomer->getId());
        $this->assertEquals($email, $savedCustomer->getEmail());
        $this->assertEquals($storeId, $savedCustomer->getStoreId());
        $this->assertEquals($firstname, $savedCustomer->getFirstname());
        $this->assertEquals($lastname, $savedCustomer->getLastname());
        $this->assertEquals($groupId, $savedCustomer->getGroupId());
        $this->assertTrue(!$savedCustomer->getSuffix());
        $this->assertEquals(
            $savedCustomer->getId(),
            $this->accountManagement->authenticate($email, $password)->getId()
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCreateNewCustomerFromClone()
    {
        $email = 'savecustomer@example.com';
        $firstName = 'Firstsave';
        $lastname = 'Lastsave';

        $existingCustId = 1;
        $existingCustomer = $this->customerRepository->getById($existingCustId);
        $this->customerBuilder
            ->populate($existingCustomer)
            ->setEmail($email)
            ->setFirstname($firstName)
            ->setLastname($lastname)
            ->setCreatedIn('Admin')
            ->setId(null);
        $customerEntity = $this->customerBuilder->create();

        $customer = $this->accountManagement->createAccount($customerEntity, 'aPassword');
        $this->assertNotEmpty($customer->getId());
        $this->assertEquals($email, $customer->getEmail());
        $this->assertEquals($firstName, $customer->getFirstname());
        $this->assertEquals($lastname, $customer->getLastname());
        $this->assertEquals('Admin', $customer->getCreatedIn());
        $this->accountManagement->authenticate(
            $customer->getEmail(),
            'aPassword',
            true
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsEmailAvailable()
    {
        $this->assertFalse($this->accountManagement->isEmailAvailable('customer@example.com', 1));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsEmailAvailableNoWebsiteSpecified()
    {
        $this->assertFalse($this->accountManagement->isEmailAvailable('customer@example.com'));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsEmailAvailableNoWebsiteSpecifiedNonExistent()
    {
        $this->assertTrue($this->accountManagement->isEmailAvailable('nonexistent@example.com'));
    }

    public function testIsEmailAvailableNonExistentEmail()
    {
        $this->assertTrue($this->accountManagement->isEmailAvailable('nonexistent@example.com', 1));
    }

    /**
     * Set Rp data to Customer in fixture
     *
     * @param $resetToken
     * @param $date
     */
    protected function setResetPasswordData($resetToken, $date)
    {
        $customerIdFromFixture = 1;
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $this->objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->load($customerIdFromFixture);
        $customerModel->setRpToken($resetToken);
        $customerModel->setRpTokenCreatedAt(date($date));
        $customerModel->save();
    }
}
