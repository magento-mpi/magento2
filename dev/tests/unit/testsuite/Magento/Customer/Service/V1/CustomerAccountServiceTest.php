<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;

/**
 * \Magento\Customer\Service\V1\CustomerAccountService
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerAccountServiceTest extends \PHPUnit_Framework_TestCase
{
    /** Sample values for testing */
    const ID = 1;
    const FIRSTNAME = 'Jane';
    const LASTNAME = 'Doe';
    const NAME = 'J';
    const EMAIL = 'janedoe@example.com';
    const EMAIL_CONFIRMATION_KEY = 'blj487lkjs4confirmation_key';
    const PASSWORD = 'password';
    const ATTRIBUTE_CODE = 'random_attr_code';
    const ATTRIBUTE_VALUE = 'random_attr_value';
    const WEBSITE_ID = 1;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\CustomerFactory
     */
    private $_customerFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Customer
     */
    private $_customerModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Event\ManagerInterface
     */
    private $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\StoreManagerInterface
     */
    private $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Math\Random
     */
    private $_mathRandomMock;

    /**
     * @var \Magento\Customer\Model\Converter
     */
    private $_converter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\Store
     */
    private $_storeMock;

    /**
     * @var \Magento\Customer\Model\Metadata\Validator
     */
    private $_validator;

    /**
     * @var \Magento\Customer\Service\V1\CustomerService
     */
    private $_customerServiceMock;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressService
     */
    private $_customerAddressServiceMock;

    public function setUp()
    {
        $this->_customerFactoryMock = $this->getMockBuilder('Magento\Customer\Model\CustomerFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $this->_customerModelMock = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getId',
                    'getFirstname',
                    'getLastname',
                    'getName',
                    'getEmail',
                    'getAttributes',
                    'getConfirmation',
                    'setConfirmation',
                    'save',
                    'load',
                    '__wakeup',
                    'authenticate',
                    'getData',
                    'getDefaultBilling',
                    'getDefaultShipping',
                    'getDefaultShippingAddress',
                    'getDefaultBillingAddress',
                    'getStoreId',
                    'getAddressById',
                    'getAddresses',
                    'getAddressItemById',
                    'getParentId',
                    'isConfirmationRequired',
                    'addAddress',
                    'loadByEmail',
                    'sendNewAccountEmail',
                    'setFirstname',
                    'setLastname',
                    'setEmail',
                    'setPassword',
                    'setData',
                    'setWebsiteId',
                    'getAttributeSetId',
                    'setAttributeSetId',
                    'validate',
                    'getRpToken',
                    'setRpToken',
                    'setRpTokenCreatedAt',
                    'isResetPasswordLinkTokenExpired',
                    'changeResetPasswordLinkToken',
                    'sendPasswordResetConfirmationEmail',
                )
            )
            ->getMock();

        $this->_eventManagerMock =
            $this->getMockBuilder('\Magento\Event\ManagerInterface')
                ->disableOriginalConstructor()
                ->getMock();

        $this->_customerModelMock
            ->expects($this->any())
            ->method('getData')
            ->with($this->equalTo(self::ATTRIBUTE_CODE))
            ->will($this->returnValue(self::ATTRIBUTE_VALUE));

        $this->_customerModelMock
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(TRUE));

        $this->_setupStoreMock();

        $this->_mathRandomMock = $this->getMockBuilder('\Magento\Math\Random')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_validator = $this->getMockBuilder('\Magento\Customer\Model\Metadata\Validator')
            ->disableOriginalConstructor()
            ->getMock();

        $customerBuilder = new Dto\CustomerBuilder();

        $this->_converter = new \Magento\Customer\Model\Converter($customerBuilder, $this->_customerFactoryMock);

        $this->_customerServiceMock = $this->getMockBuilder('\Magento\Customer\Service\V1\CustomerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_customerAddressServiceMock =
            $this->getMockBuilder('\Magento\Customer\Service\V1\CustomerAddressService')
            ->disableOriginalConstructor()
            ->getMock();
    }


    public function testActivateAccount()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getConfirmation' => self::EMAIL_CONFIRMATION_KEY,
                'getAttributes' => array(),
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        // Assertions
        $this->_customerModelMock->expects($this->once())
            ->method('save');
        $this->_customerModelMock->expects($this->once())
            ->method('setConfirmation')
            ->with($this->isNull());

        $customerService = $this->_createService();

        $customer = $customerService->activateAccount(self::ID, self::EMAIL_CONFIRMATION_KEY);

        $this->assertEquals(self::ID, $customer->getCustomerId());
    }

    /**
     * @expectedException  \Magento\Exception\InputException
     */
    public function testActivateAccountAlreadyActive()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getConfirmation' => null,
                'getAttributes' => array()
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        // Assertions
        $this->_customerModelMock->expects($this->never())
            ->method('save');
        $this->_customerModelMock->expects($this->never())
            ->method('setConfirmation');

        $customerService = $this->_createService();

        $customerService->activateAccount(self::ID, self::EMAIL_CONFIRMATION_KEY);
    }

    public function testActivateAccountDoesntExist()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => 0,
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        // Assertions
        $this->_customerModelMock->expects($this->never())
            ->method('save');
        $this->_customerModelMock->expects($this->never())
            ->method('setConfirmation');

        $customerService = $this->_createService();

        try {
            $customerService->activateAccount(self::ID, self::EMAIL_CONFIRMATION_KEY);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (\Magento\Exception\NoSuchEntityException $nsee) {
            $this->assertSame($nsee->getCode(), \Magento\Exception\NoSuchEntityException::NO_SUCH_ENTITY);
            $this->assertSame(
                $nsee->getParams(),
                [
                    'customerId' => self::ID,
                ]
            );
        } catch (\Exception $unexpected) {
            $this->fail('Unexpected exception type thrown. '. $unexpected->getMessage());
        }
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage DB was down
     */
    public function testActivateAccountLoadError()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->throwException(new \Exception('DB was down')));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => 0,
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        // Assertions
        $this->_customerModelMock->expects($this->never())
            ->method('save');
        $this->_customerModelMock->expects($this->never())
            ->method('setConfirmation');

        $customerService = $this->_createService();

        $customerService->activateAccount(self::ID, self::EMAIL_CONFIRMATION_KEY);
    }

    public function testActivateAccountBadKey()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getConfirmation' => self::EMAIL_CONFIRMATION_KEY . 'BAD',
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        // Assertions
        $this->_customerModelMock->expects($this->never())
            ->method('save');
        $this->_customerModelMock->expects($this->never())
            ->method('setConfirmation');

        $customerService = $this->_createService();

        try {
            $customerService->activateAccount(self::ID, self::EMAIL_CONFIRMATION_KEY);
            $this->fail('Expected exception not thrown.');
        } catch (InputException $e) {
            $this->assertEquals(InputException::INVALID_FIELD_VALUE, $e->getParams()[0]['code']);
            $this->assertEquals('confirmation', $e->getParams()[0]['fieldName']);
            $this->assertEquals('Wrong confirmation key.', $e->getParams()[0]['message']);
        } catch (\Exception $unexpected) {
            $this->fail('Unexpected exception type thrown. '. $unexpected->getMessage());
        }
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage DB is down
     */
    public function testActivateAccountSaveFailed()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getConfirmation' => self::EMAIL_CONFIRMATION_KEY,
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        // Assertions/Mocking
        $this->_customerModelMock->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \Exception('DB is down')));
        $this->_customerModelMock->expects($this->once())
            ->method('setConfirmation');

        $customerService = $this->_createService();

        $customerService->activateAccount(self::ID, self::EMAIL_CONFIRMATION_KEY);
    }

    public function testLoginAccount()
    {
        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'authenticate' => true,
                'load' => $this->_customerModelMock,
                'getAttributes' => array()
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $customer = $customerService->authenticate(self::EMAIL, self::PASSWORD, self::WEBSITE_ID);

        $this->assertEquals(self::ID, $customer->getCustomerId());
    }

    /**
     * @expectedException \Magento\Exception\AuthenticationException
     * @expectedExceptionMessage exception message
     */
    public function testLoginAccountWithException()
    {
        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
            )
        );

        $this->_customerModelMock->expects($this->any())
            ->method('authenticate')
            ->will($this->throwException(new \Magento\Core\Exception('exception message') ));

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $customerService->authenticate(self::EMAIL, self::PASSWORD, self::WEBSITE_ID);
    }

    public function testValidateResetPasswordLinkToken()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $customerService->validateResetPasswordLinkToken(self::ID, $resetToken);
    }

    public function testValidateResetPasswordLinkTokenExpired()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => true,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        try {
            $customerService->validateResetPasswordLinkToken(self::ID, $resetToken);
            $this->fail('Expected exception not thrown.');
        } catch ( InputException $e) {
            $expectedParams = [
                [
                    'code' => InputException::TOKEN_EXPIRED,
                    'fieldName' => 'resetPasswordLinkToken',
                    'message' => 'Your password reset link has expired.',
                ]
            ];
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }

    public function testValidateResetPasswordLinkTokenInvalid()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $invalidToken = $resetToken . 'extra_stuff';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        try {
            $customerService->validateResetPasswordLinkToken(self::ID, $invalidToken);
            $this->fail('Expected exception not thrown.');
        } catch ( InputException $e) {
            $expectedParams = [
                [
                    'code' => InputException::TOKEN_EXPIRED,
                    'fieldName' => 'resetPasswordLinkToken',
                    'message' => 'Your password reset link has expired.',
                ]
            ];
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }

    public function testValidateResetPasswordLinkTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => 0,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        try {
            $customerService->validateResetPasswordLinkToken(1, $resetToken);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (\Magento\Exception\NoSuchEntityException $nsee) {
            $this->assertSame($nsee->getCode(), \Magento\Exception\NoSuchEntityException::NO_SUCH_ENTITY);
            $this->assertSame(
                $nsee->getParams(),
                [
                    'customerId' => self::ID,
                ]
            );
        }
    }

    public function testValidateResetPasswordLinkTokenNull()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => 0,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        try {
            $customerService->validateResetPasswordLinkToken(null, null);
            $this->fail('Expected exception not thrown.');
        } catch ( InputException $e) {
            $expectedParams = [
                [
                    'code' => InputException::INVALID_FIELD_VALUE,
                    'fieldName' => 'resetPasswordLinkToken',
                    'message' => 'Invalid password reset token.',
                ]
            ];
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }

    public function testSendPasswordResetLink()
    {
        $email = 'foo@example.com';
        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'setWebsiteId' => $this->_customerModelMock,
                'loadByEmail' => $this->_customerModelMock,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->once())
            ->method('sendPasswordResetConfirmationEmail');

        $customerService = $this->_createService();

        $customerService->sendPasswordResetLink($email, self::WEBSITE_ID);
    }

    public function testSendPasswordResetLinkBadEmailOrWebsite()
    {
        $email = 'foo@example.com';
        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => 0,
                'setWebsiteId' => $this->_customerModelMock,
                'loadByEmail' => $this->_customerModelMock,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->never())
            ->method('sendPasswordResetConfirmationEmail');

        $customerService = $this->_createService();

        try {
            $customerService->sendPasswordResetLink($email, 0);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (\Magento\Exception\NoSuchEntityException $nsee) {
            $this->assertSame($nsee->getCode(), \Magento\Exception\NoSuchEntityException::NO_SUCH_ENTITY);
            $this->assertSame(
                $nsee->getParams(),
                [
                    'email' => $email,
                    'websiteId' => 0
                ]
            );
        }
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Invalid transactional email code: 0
     */
    public function testSendPasswordResetLinkSendException()
    {
        $email = 'foo@example.com';
        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'setWebsiteId' => $this->_customerModelMock,
                'loadByEmail' => $this->_customerModelMock,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->once())
            ->method('sendPasswordResetConfirmationEmail')
            ->will($this->throwException(new \Magento\Core\Exception(__('Invalid transactional email code: %1', 0))));

        $customerService = $this->_createService();

        $customerService->sendPasswordResetLink($email, self::WEBSITE_ID);
    }

    public function testResetPassword()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->once())
            ->method('setRpToken')
            ->with(null)
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->once())
            ->method('setRpTokenCreatedAt')
            ->with(null)
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->once())
            ->method('setPassword')
            ->with($password)
            ->will($this->returnSelf());

        $customerService = $this->_createService();

        $customerService->resetPassword(self::ID, $password, $resetToken);
    }

    public function testResetPasswordShortPassword()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = '';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->once())
            ->method('setRpToken')
            ->with(null)
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->once())
            ->method('setRpTokenCreatedAt')
            ->with(null)
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->once())
            ->method('setPassword')
            ->with($password)
            ->will($this->returnSelf());

        $customerService = $this->_createService();

        $customerService->resetPassword(self::ID, $password, $resetToken);
    }

    public function testResetPasswordTokenExpired()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => true,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->never())
            ->method('setRpToken');
        $this->_customerModelMock->expects($this->never())
            ->method('setRpTokenCreatedAt');
        $this->_customerModelMock->expects($this->never())
            ->method('setPassword');

        $customerService = $this->_createService();

        try {
            $customerService->resetPassword(self::ID, $password, $resetToken);
            $this->fail('Expected exception not thrown.');
        } catch ( InputException $e) {
            $expectedParams = [
                [
                    'code' => InputException::TOKEN_EXPIRED,
                    'fieldName' => 'resetPasswordLinkToken',
                    'message' => 'Your password reset link has expired.',
                ]
            ];
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }

    public function testResetPasswordTokenInvalid()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $invalidToken = $resetToken . 'invalid';
        $password = 'password_secret';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->never())
            ->method('setRpToken');
        $this->_customerModelMock->expects($this->never())
            ->method('setRpTokenCreatedAt');
        $this->_customerModelMock->expects($this->never())
            ->method('setPassword');

        $customerService = $this->_createService();

        try {
            $customerService->resetPassword(self::ID, $password, $invalidToken);
            $this->fail('Expected exception not thrown.');
        } catch ( InputException $e) {
            $expectedParams = [
                [
                    'code' => InputException::TOKEN_EXPIRED,
                    'fieldName' => 'resetPasswordLinkToken',
                    'message' => 'Your password reset link has expired.',
                ]
            ];
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }

    public function testResetPasswordTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => 0,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->never())
            ->method('setRpToken');
        $this->_customerModelMock->expects($this->never())
            ->method('setRpTokenCreatedAt');
        $this->_customerModelMock->expects($this->never())
            ->method('setPassword');

        $customerService = $this->_createService();

        try {
            $customerService->resetPassword(4200, $password, $resetToken);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (\Magento\Exception\NoSuchEntityException $nsee) {
            $this->assertSame($nsee->getCode(), \Magento\Exception\NoSuchEntityException::NO_SUCH_ENTITY);
            $this->assertSame(
                $nsee->getParams(),
                [
                    'customerId' => 4200,
                ]
            );
        }
    }

    public function testResetPasswordTokenInvalidUserId()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => 0,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->never())
            ->method('setRpToken');
        $this->_customerModelMock->expects($this->never())
            ->method('setRpTokenCreatedAt');
        $this->_customerModelMock->expects($this->never())
            ->method('setPassword');

        $customerService = $this->_createService();

        try {
            $customerService->resetPassword(0, $password, $resetToken);
            $this->fail('Expected exception not thrown.');
        } catch ( InputException $e) {
            $expectedParams = [
                [
                    'code' => InputException::INVALID_FIELD_VALUE,
                    'fieldName' => 'resetPasswordLinkToken',
                    'message' => 'Invalid password reset token.',
                ]
            ];
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }


    public function testSendConfirmation()
    {
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(55));
        $this->_customerModelMock->expects($this->once())
            ->method('setWebsiteId')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('isConfirmationRequired')
            ->will($this->returnValue(true));
        $this->_customerModelMock->expects($this->any())
            ->method('getConfirmation')
            ->will($this->returnValue('123abc'));

        $customerService = $this->_createService();
        $customerService->sendConfirmation('email');
    }

    public function testSendConfirmationNoEmail()
    {
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(0));
        $this->_customerModelMock->expects($this->once())
            ->method('setWebsiteId')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();
        try {
            $customerService->sendConfirmation('email@no.customer');
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (NoSuchEntityException $nsee) {
            $expectedParams = [
                'email' => 'email@no.customer',
                'websiteId' => null
            ];
            $this->assertEquals($expectedParams, $nsee->getParams());
        }
    }

    public function testSendConfirmationNotNeeded()
    {
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(55));
        $this->_customerModelMock->expects($this->once())
            ->method('setWebsiteId')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();
        try {
            $customerService->sendConfirmation('email');
            $this->fail('Expected exception not thrown');
        } catch ( InputException $e) {
            $expectedParams = [
                [
                    'code' => InputException::INVALID_STATE_CHANGE,
                    'fieldName' => 'email',
                    'message' => 'This email does not require confirmation.',
                ]
            ];
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }


    private function _setupStoreMock()
    {
        $this->_storeManagerMock =
            $this->getMockBuilder('\Magento\Core\Model\StoreManagerInterface')
                ->disableOriginalConstructor()
                ->getMock();

        $this->_storeMock = $this->getMockBuilder('\Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_storeManagerMock
            ->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_storeMock));
    }


    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue($mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())
                ->method($method)
                ->will($this->returnValue($value));
        }
    }

    /**
     * @return CustomerAccountService
     */
    private function _createService()
    {
        $customerService = new CustomerAccountService(
            $this->_customerFactoryMock,
            $this->_eventManagerMock,
            $this->_storeManagerMock,
            $this->_mathRandomMock,
            $this->_converter,
            $this->_validator,
            new Dto\Response\CreateCustomerAccountResponseBuilder(),
            $this->_customerServiceMock,
            $this->_customerAddressServiceMock
        );
        return $customerService;
    }
}
