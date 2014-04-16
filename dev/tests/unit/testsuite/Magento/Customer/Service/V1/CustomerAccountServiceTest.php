<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Model\Converter;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Service\V1\Data\FilterBuilder;
use Magento\Mail\Exception as MailException;

/**
 * Test for \Magento\Customer\Service\V1\CustomerAccountService
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
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
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Event\ManagerInterface
     */
    private $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManagerMock;

    /**
     * @var Converter
     */
    private $_converter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Store\Model\Store
     */
    private $_storeMock;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder
     */
    private $_customerBuilder;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder
     */
    private $_customerDetailsBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\CustomerAddressService
     */
    private $_customerAddressServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\CustomerMetadataService
     */
    private $_customerMetadataService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | CustomerRegistry
     */
    private $_customerRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject  | \Magento\Logger
     */
    private $_loggerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Helper\Data
     */
    private $_customerHelperMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Config\Share */
    private $_configShareMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Encryption\EncryptorInterface  */
    private $_encryptorMock;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchBuilder;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $filterGroupBuilder = $this->_objectManager
            ->getObject('Magento\Service\V1\Data\Search\FilterGroupBuilder');
        /** @var SearchCriteriaBuilder $searchBuilder */
        $this->_searchBuilder = $this->_objectManager->getObject(
            'Magento\Service\V1\Data\SearchCriteriaBuilder',
            ['filterGroupBuilder' => $filterGroupBuilder]
        );

        $this->_customerFactoryMock = $this->getMockBuilder(
            'Magento\Customer\Model\CustomerFactory'
        )->disableOriginalConstructor()->setMethods(
            array('create')
        )->getMock();

        $this->_customerModelMock = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getCollection',
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
                    'isDeleteable',
                    'isReadonly',
                    'addAddress',
                    'loadByEmail',
                    'sendNewAccountEmail',
                    'setFirstname',
                    'setLastname',
                    'setEmail',
                    'setPassword',
                    'setPasswordHash',
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
                    'sendPasswordResetNotificationEmail',
                )
        )->getMock();

        $this->_eventManagerMock = $this->getMockBuilder(
            '\Magento\Framework\Event\ManagerInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_customerModelMock->expects($this->any())->method('validate')->will($this->returnValue(true));

        $this->_setupStoreMock();

        $this->_mathRandomMock = $this->getMockBuilder(
            '\Magento\Math\Random'
        )->disableOriginalConstructor()->getMock();

        $this->_validator = $this->getMockBuilder(
            '\Magento\Customer\Model\Metadata\Validator'
        )->disableOriginalConstructor()->getMock();

        $this->_customerMetadataService = $this->getMockForAbstractClass(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface',
            [],
            '',
            false
        );
        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getCustomCustomerAttributeMetadata'
        )->will(
            $this->returnValue(array())
        );

        $this->_customerBuilder = new Data\CustomerBuilder($this->_customerMetadataService);

        $customerBuilder = new CustomerBuilder($this->_customerMetadataService);
        $this->_customerDetailsBuilder = new Data\CustomerDetailsBuilder(
            $this->_customerBuilder,
            new Data\AddressBuilder(new Data\RegionBuilder(), $this->_customerMetadataService)
        );

        $this->_converter = new Converter($customerBuilder, $this->_customerFactoryMock);

        $this->_customerRegistry = $this->getMockBuilder('\Magento\Customer\Model\CustomerRegistry')
            ->setMethods(['retrieve', 'retrieveByEmail'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieve')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieveByEmail')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerAddressServiceMock =
            $this->getMockBuilder('Magento\Customer\Service\V1\CustomerAddressService')
                ->disableOriginalConstructor()
                ->getMock();

        $this->_customerHelperMock =
            $this->getMockBuilder('Magento\Customer\Helper\Data')
                ->disableOriginalConstructor()
                ->setMethods(['isCustomerInStore'])
                ->getMock();
        $this->_customerHelperMock->expects($this->any())
            ->method('isCustomerInStore')
            ->will($this->returnValue(false));

        $this->_urlMock = $this->getMockBuilder('Magento\UrlInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_loggerMock = $this->getMockBuilder('Magento\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_encryptorMock = $this->getMockBuilder('Magento\Encryption\EncryptorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_configShareMock = $this->getMockBuilder('Magento\Customer\Model\Config\Share')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testActivateAccount()
    {
        $this->_customerModelMock->expects($this->any())->method('load')->will($this->returnSelf());

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array('getId' => self::ID, 'getConfirmation' => self::EMAIL_CONFIRMATION_KEY, 'getAttributes' => array())
        );

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        // Assertions
        $this->_customerModelMock->expects($this->once())->method('save');
        $this->_customerModelMock->expects($this->once())->method('setConfirmation')->with($this->isNull());

        $customerService = $this->_createService();

        $customer = $customerService->activateCustomer(self::ID, self::EMAIL_CONFIRMATION_KEY);

        $this->assertEquals(self::ID, $customer->getId());
    }

    /**
     * @expectedException  \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::INVALID_STATE
     */
    public function testActivateAccountAlreadyActive()
    {
        $this->_customerModelMock->expects($this->any())->method('load')->will($this->returnSelf());

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array('getId' => self::ID, 'getConfirmation' => null, 'getAttributes' => array())
        );

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        // Assertions
        $this->_customerModelMock->expects($this->never())->method('save');
        $this->_customerModelMock->expects($this->never())->method('setConfirmation');

        $customerService = $this->_createService();

        $customerService->activateCustomer(self::ID, self::EMAIL_CONFIRMATION_KEY);
    }

    public function testActivateAccountDoesntExist()
    {
        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieve')
            ->will($this->throwException(new NoSuchEntityException('customerId', 1)));

        // Assertions
        $this->_customerModelMock->expects($this->never())->method('save');
        $this->_customerModelMock->expects($this->never())->method('setConfirmation');

        $customerService = $this->_createService();

        try {
            $customerService->activateCustomer(self::ID, self::EMAIL_CONFIRMATION_KEY);
            $this->fail('Expected exception not thrown.');
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            $this->assertSame($e->getCode(), \Magento\Exception\NoSuchEntityException::NO_SUCH_ENTITY);
            $this->assertSame(
                $e->getParams(),
                [
                    'customerId' => self::ID,
                ]
            );
        }
    }

    /**
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::INPUT_MISMATCH
     */
    public function testActivateAccountBadKey()
    {
        $this->_customerModelMock->expects($this->any())->method('load')->will($this->returnSelf());

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array('getId' => self::ID, 'getConfirmation' => self::EMAIL_CONFIRMATION_KEY)
        );

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        // Assertions
        $this->_customerModelMock->expects($this->never())->method('save');
        $this->_customerModelMock->expects($this->never())->method('setConfirmation');

        $customerService = $this->_createService();

        $customerService->activateCustomer(self::ID, self::EMAIL_CONFIRMATION_KEY . 'BAD');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage DB is down
     */
    public function testActivateAccountSaveFailed()
    {
        $this->_customerModelMock->expects($this->any())->method('load')->will($this->returnSelf());

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array('getId' => self::ID, 'getConfirmation' => self::EMAIL_CONFIRMATION_KEY)
        );

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        // Assertions/Mocking
        $this->_customerModelMock->expects(
            $this->once()
        )->method(
            'save'
        )->will(
            $this->throwException(new \Exception('DB is down'))
        );
        $this->_customerModelMock->expects($this->once())->method('setConfirmation');

        $customerService = $this->_createService();

        $customerService->activateCustomer(self::ID, self::EMAIL_CONFIRMATION_KEY);
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

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $customerService = $this->_createService();

        $customer = $customerService->authenticate(self::EMAIL, self::PASSWORD, self::WEBSITE_ID);

        $this->assertEquals(self::ID, $customer->getId());
    }

    /**
     * @expectedException \Magento\Exception\AuthenticationException
     * @expectedExceptionMessage exception message
     */
    public function testLoginAccountWithException()
    {
        $this->_mockReturnValue(
            $this->_customerModelMock,
            array('getId' => self::ID, 'load' => $this->_customerModelMock)
        );

        $this->_customerModelMock->expects(
            $this->any()
        )->method(
            'authenticate'
        )->will(
            $this->throwException(new \Magento\Framework\Model\Exception('exception message'))
        );

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

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
                'isResetPasswordLinkTokenExpired' => false
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $customerService = $this->_createService();

        $customerService->validateResetPasswordLinkToken(self::ID, $resetToken);
    }

    /**
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::EXPIRED
     */
    public function testValidateResetPasswordLinkTokenExpired()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => true
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $customerService = $this->_createService();

        $customerService->validateResetPasswordLinkToken(self::ID, $resetToken);
    }

    /**
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::INPUT_MISMATCH
     */
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
                'isResetPasswordLinkTokenExpired' => false
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $customerService = $this->_createService();

        $customerService->validateResetPasswordLinkToken(self::ID, $invalidToken);
    }

    public function testValidateResetPasswordLinkTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';

        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieve')
            ->will($this->throwException(new NoSuchEntityException('customerId', 1)));

        $customerService = $this->_createService();

        try {
            $customerService->validateResetPasswordLinkToken(1, $resetToken);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            $this->assertSame($e->getCode(), \Magento\Exception\NoSuchEntityException::NO_SUCH_ENTITY);
            $this->assertSame(
                $e->getParams(),
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
                'isResetPasswordLinkTokenExpired' => false
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $customerService = $this->_createService();

        try {
            $customerService->validateResetPasswordLinkToken(14, null);
            $this->fail('Expected exception not thrown.');
        } catch (InputException $e) {
            $expectedParams = array(
                array(
                    'code' => InputException::INVALID_FIELD_VALUE,
                    'fieldName' => 'resetPasswordLinkToken',
                    'value' => null
                )
            );
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }

    public function testSendPasswordResetLink()
    {
        $email = 'foo@example.com';

        $this->_customerModelMock->expects($this->once())->method('sendPasswordResetConfirmationEmail');

        $customerService = $this->_createService();

        $customerService->initiatePasswordReset(
            $email,
            CustomerAccountServiceInterface::EMAIL_RESET,
            self::WEBSITE_ID
        );
    }

    public function testSendPasswordResetLinkBadEmailOrWebsite()
    {
        $email = 'foo@example.com';

        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieveByEmail')
            ->will($this->throwException((new NoSuchEntityException('email', $email))->addField('websiteId', 0)));

        $this->_customerModelMock->expects($this->never())->method('sendPasswordResetConfirmationEmail');

        $customerService = $this->_createService();

        try {
            $customerService->initiatePasswordReset($email, CustomerAccountServiceInterface::EMAIL_RESET, 0);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            $this->assertSame($e->getCode(), \Magento\Exception\NoSuchEntityException::NO_SUCH_ENTITY);
            $this->assertSame(
                $e->getParams(),
                [
                    'email' => $email,
                    'websiteId' => 0
                ]
            );
        }
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
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
                'loadByEmail' => $this->_customerModelMock
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerModelMock->expects($this->once())
            ->method('sendPasswordResetConfirmationEmail')
            ->will($this->throwException(
                new \Magento\Framework\Model\Exception(__('Invalid transactional email code: %1', 0))
            ));

        $customerService = $this->_createService();

        $customerService->initiatePasswordReset(
            $email,
            CustomerAccountServiceInterface::EMAIL_RESET,
            self::WEBSITE_ID
        );
    }

    public function testSendPasswordResetLinkSendMailException()
    {
        $email = 'foo@example.com';
        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId'        => self::ID,
                'setWebsiteId' => $this->_customerModelMock,
                'loadByEmail'  => $this->_customerModelMock,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $exception = new MailException(__('The mail server is down'));

        $this->_customerModelMock->expects($this->once())
            ->method('sendPasswordResetConfirmationEmail')
            ->will($this->throwException($exception));

        $this->_loggerMock->expects($this->once())
            ->method('logException')
            ->with($exception);

        $customerService = $this->_createService();

        $customerService->initiatePasswordReset($email, CustomerAccountServiceInterface::EMAIL_RESET, self::WEBSITE_ID);
    }

    public function testResetPassword()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';
        $encryptedHash = 'password_encrypted_hash';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_customerModelMock->expects($this->once())
            ->method('setRpToken')
            ->with(null)
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->once())
            ->method('setRpTokenCreatedAt')
            ->with(null)
            ->will($this->returnSelf());
        $this->_encryptorMock->expects($this->once())
            ->method('getHash')
            ->with($password, true)
            ->will($this->returnValue($encryptedHash));
        $this->_customerModelMock->expects($this->once())
            ->method('setPasswordHash')
            ->with($encryptedHash)
            ->will($this->returnSelf());

        $customerService = $this->_createService();

        $customerService->resetPassword(self::ID, $resetToken, $password);
    }

    public function testResetPasswordShortPassword()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = '';
        $encryptedHash = 'password_encrypted_hash';

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getRpToken' => $resetToken,
                'isResetPasswordLinkTokenExpired' => false
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_customerModelMock->expects($this->once())
            ->method('setRpToken')
            ->with(null)
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->once())
            ->method('setRpTokenCreatedAt')
            ->with(null)
            ->will($this->returnSelf());
        $this->_encryptorMock->expects($this->once())
            ->method('getHash')
            ->with($password, true)
            ->will($this->returnValue($encryptedHash));
        $this->_customerModelMock->expects($this->once())
            ->method('setPasswordHash')
            ->with($encryptedHash)
            ->will($this->returnSelf());

        $customerService = $this->_createService();

        $customerService->resetPassword(self::ID, $resetToken, $password);
    }

    /**
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::EXPIRED
     */
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
                'isResetPasswordLinkTokenExpired' => true
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_customerModelMock->expects($this->never())->method('setRpToken');
        $this->_customerModelMock->expects($this->never())->method('setRpTokenCreatedAt');
        $this->_customerModelMock->expects($this->never())->method('setPassword');

        $customerService = $this->_createService();

        $customerService->resetPassword(self::ID, $resetToken, $password);
    }

    /**
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::INPUT_MISMATCH
     */
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
                'isResetPasswordLinkTokenExpired' => false
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_customerModelMock->expects($this->never())->method('setRpToken');
        $this->_customerModelMock->expects($this->never())->method('setRpTokenCreatedAt');
        $this->_customerModelMock->expects($this->never())->method('setPassword');

        $customerService = $this->_createService();

        $customerService->resetPassword(self::ID, $invalidToken, $password);
    }

    public function testResetPasswordTokenWrongUser()
    {
        $resetToken = 'lsdj579slkj5987slkj595lkj';
        $password = 'password_secret';
        $invalidCustomerId = 4200;

        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieve')
            ->will($this->throwException(new NoSuchEntityException('customerId', $invalidCustomerId)));

        $this->_customerModelMock->expects($this->never())->method('setRpToken');
        $this->_customerModelMock->expects($this->never())->method('setRpTokenCreatedAt');
        $this->_customerModelMock->expects($this->never())->method('setPassword');

        $customerService = $this->_createService();

        try {
            $customerService->resetPassword($invalidCustomerId, $resetToken, $password);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            $this->assertSame($e->getCode(), \Magento\Exception\NoSuchEntityException::NO_SUCH_ENTITY);
            $this->assertSame(
                $e->getParams(),
                [
                    'customerId' => $invalidCustomerId,
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
                'isResetPasswordLinkTokenExpired' => false
            )
        );
        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_customerModelMock->expects($this->never())->method('setRpToken');
        $this->_customerModelMock->expects($this->never())->method('setRpTokenCreatedAt');
        $this->_customerModelMock->expects($this->never())->method('setPassword');

        $customerService = $this->_createService();

        try {
            $customerService->resetPassword(0, $resetToken, $password);
            $this->fail('Expected exception not thrown.');
        } catch (InputException $e) {
            $expectedParams = array(
                array('code' => InputException::INVALID_FIELD_VALUE, 'fieldName' => 'customerId', 'value' => 0)
            );
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }

    public function testResendConfirmation()
    {
        $this->_customerModelMock->expects(
            $this->any()
        )->method(
            'isConfirmationRequired'
        )->will(
            $this->returnValue(true)
        );
        $this->_customerModelMock->expects(
            $this->any()
        )->method(
            'getConfirmation'
        )->will(
            $this->returnValue('123abc')
        );

        $customerService = $this->_createService();
        $customerService->resendConfirmation('email', 1);
    }

    public function testResendConfirmationNoEmail()
    {
        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieveByEmail')
            ->will(
                $this->throwException(
                    (new NoSuchEntityException('email', self::EMAIL))->addField('websiteId', self::WEBSITE_ID)
                )
            );

        $customerService = $this->_createService();
        try {
            $customerService->resendConfirmation('email@no.customer', 1);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (NoSuchEntityException $e) {
            $expectedParams = [
                'email' => self::EMAIL,
                'websiteId' => self::WEBSITE_ID
            ];
            $this->assertEquals($expectedParams, $e->getParams());
        }
    }

    /**
     * @expectedException \Magento\Exception\StateException
     * @expectedExceptionCode \Magento\Exception\StateException::INVALID_STATE
     */
    public function testResendConfirmationNotNeeded()
    {
        $customerService = $this->_createService();
        $customerService->resendConfirmation('email@test.com', 2);
    }

    public function testResendConfirmationWithMailException()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('isConfirmationRequired')
            ->will($this->returnValue(true));
        $this->_customerModelMock->expects($this->any())
            ->method('getConfirmation')
            ->will($this->returnValue('123abc'));

        $exception = new MailException(__('The mail server is down'));

        $this->_customerModelMock->expects($this->once())
            ->method('sendNewAccountEmail')
            ->withAnyParameters()
            ->will($this->throwException($exception));

        $this->_loggerMock->expects($this->once())
            ->method('logException')
            ->with($exception);

        $customerService = $this->_createService();
        $customerService->resendConfirmation('email', 1);
        // If we call sendNewAccountEmail and no exception is returned, the test succeeds
    }

    /**
     * @dataProvider testGetConfirmationStatusDataProvider
     * @param string $expected The expected confirmation status.
     */
    public function testGetConfirmationStatus($expected)
    {
        $customerId = 1234;
        if (CustomerAccountServiceInterface::ACCOUNT_CONFIRMED == $expected) {
            $this->_customerModelMock->expects(
                $this->once()
            )->method(
                'getConfirmation'
            )->will(
                $this->returnValue(false)
            );
        } else {
            $this->_customerModelMock->expects(
                $this->once()
            )->method(
                'getConfirmation'
            )->will(
                $this->returnValue(true)
            );
        }
        if (CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_REQUIRED == $expected) {
            $this->_customerModelMock->expects(
                $this->once()
            )->method(
                'isConfirmationRequired'
            )->will(
                $this->returnValue(true)
            );
        } elseif (CustomerAccountServiceInterface::ACCOUNT_CONFIRMED != $expected) {
            $this->_customerModelMock->expects(
                $this->once()
            )->method(
                'getConfirmation'
            )->will(
                $this->returnValue(false)
            );
        }

        $customerService = $this->_createService();
        $this->assertEquals($expected, $customerService->getConfirmationStatus($customerId));
    }

    public function testGetConfirmationStatusDataProvider()
    {
        return array(
            array(CustomerAccountServiceInterface::ACCOUNT_CONFIRMED),
            array(CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_REQUIRED),
            array(CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_NOT_REQUIRED)
        );
    }

    /**
     * @param bool $isBoolean If the customer is or is not readonly/deleteable
     *
     * @dataProvider isBooleanDataProvider
     */
    public function testCanModify($isBoolean)
    {
        $this->_customerModelMock->expects($this->once())->method('isReadonly')->will($this->returnValue($isBoolean));

        $customerService = $this->_createService();
        $this->assertEquals(!$isBoolean, $customerService->canModify(self::ID));
    }

    /**
     * @param bool $isBoolean If the customer is or is not readonly/deleteable
     *
     * @dataProvider isBooleanDataProvider
     */
    public function testCanDelete($isBoolean)
    {
        $this->_mockReturnValue($this->_customerModelMock, array('getId' => self::ID));

        $this->_customerModelMock->expects(
            $this->once()
        )->method(
            'isDeleteable'
        )->will(
            $this->returnValue($isBoolean)
        );

        $customerService = $this->_createService();
        $this->assertEquals($isBoolean, $customerService->canDelete(self::ID));
    }

    /**
     * Data provider for checking isReadonly() and isDeleteable()
     *
     * @return array
     */
    public function isBooleanDataProvider()
    {
        return array('true' => array(true), 'false' => array(false));
    }

    public function testCreateCustomer()
    {
        $customerData = array(
            'customer_id' => self::ID,
            'email' => self::EMAIL,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'create_in' => 'Admin',
            'password' => 'password'
        );
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();
        $customerDetails = $this->_customerDetailsBuilder->setCustomer($customerEntity)->create();

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_converter = $this->getMock('Magento\Customer\Model\Converter', [], [], '', false);
        $this->_converter
            ->expects($this->once())
            ->method('createCustomerFromModel')
            ->will($this->returnValue($customerEntity));
        $this->_converter
            ->expects($this->any())
            ->method('getCustomerModel')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
                'getEmail' => self::EMAIL,
                'getFirstname' => self::FIRSTNAME,
                'getLastname' => self::LASTNAME
            )
        );

        $mockAttribute = $this->getMockBuilder(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata'
        )->disableOriginalConstructor()->getMock();
        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getCustomerAttributeMetadata'
        )->will(
            $this->returnValue($mockAttribute)
        );

        // verify
        $this->_converter
            ->expects($this->once())
            ->method('createCustomerModel')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $this->assertSame($customerEntity, $customerService->createCustomer($customerDetails));
    }

    public function testCreateNewCustomer()
    {
        $customerData = array(
            'email' => self::EMAIL,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'create_in' => 'Admin',
            'password' => 'password'
        );
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();
        $customerDetails = $this->_customerDetailsBuilder->setCustomer($customerEntity)->create();

        $this->_converter = $this->getMock('Magento\Customer\Model\Converter', [], [], '', false);
        $this->_converter
            ->expects($this->once())
            ->method('createCustomerFromModel')
            ->will($this->returnValue($customerEntity));
        $this->_converter
            ->expects($this->any())
            ->method('getCustomerModel')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getEmail' => self::EMAIL,
                'getFirstname' => self::FIRSTNAME,
                'getLastname' => self::LASTNAME
            )
        );

        $mockAttribute = $this->getMockBuilder(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata'
        )->disableOriginalConstructor()->getMock();
        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getCustomerAttributeMetadata'
        )->will(
            $this->returnValue($mockAttribute)
        );

        // verify
        $this->_converter
            ->expects($this->once())
            ->method('createCustomerModel')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $this->assertSame($customerEntity, $customerService->createCustomer($customerDetails));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage exception message
     */
    public function testCreateCustomerWithException()
    {
        $customerData = array(
            'email' => self::EMAIL,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'create_in' => 'Admin',
            'password' => 'password'
        );
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();
        $customerDetails = $this->_customerDetailsBuilder->setCustomer($customerEntity)->create();

        $this->_converter = $this->getMock('Magento\Customer\Model\Converter', [], [], '', false);
        $this->_converter
            ->expects($this->once())
            ->method('createCustomerModel')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_converter
            ->expects($this->any())
            ->method('getCustomerModel')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getEmail' => self::EMAIL,
                'getFirstname' => self::FIRSTNAME,
                'getLastname' => self::LASTNAME
            )
        );

        $mockAttribute = $this->getMockBuilder(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata'
        )->disableOriginalConstructor()->getMock();
        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getCustomerAttributeMetadata'
        )->will(
            $this->returnValue($mockAttribute)
        );

        $this->_converter
            ->expects($this->once())
            ->method('createCustomerFromModel')
            ->will($this->throwException(new \Exception('exception message')));

        $customerService = $this->_createService();
        $customerService->createCustomer($customerDetails);
    }

    public function testCreateCustomerWithInputException()
    {
        $customerData = array(
            'email' => self::EMAIL,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'create_in' => 'Admin',
            'password' => 'password'
        );
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();
        $customerDetails = $this->_customerDetailsBuilder->setCustomer($customerEntity)->create();

        $this->_converter = $this->getMock('Magento\Customer\Model\Converter', [], [], '', false);
        $this->_converter
            ->expects($this->once())
            ->method('createCustomerModel')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_mockReturnValue($this->_customerModelMock, array('getId' => self::ID, 'getEmail' => 'missingAtSign'));

        $mockAttribute = $this->getMockBuilder(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata'
        )->disableOriginalConstructor()->getMock();
        $mockAttribute->expects($this->atLeastOnce())->method('isRequired')->will($this->returnValue(true));
        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getCustomerAttributeMetadata'
        )->will(
            $this->returnValue($mockAttribute)
        );

        // verify
        $customerService = $this->_createService();

        try {
            $customerService->createCustomer($customerDetails);
        } catch (InputException $inputException) {
            $this->assertContains(
                array('fieldName' => 'firstname', 'code' => InputException::REQUIRED_FIELD, 'value' => null),
                $inputException->getParams()
            );
            $this->assertContains(
                array('fieldName' => 'lastname', 'code' => InputException::REQUIRED_FIELD, 'value' => null),
                $inputException->getParams()
            );
            $this->assertContains(
                array(
                    'fieldName' => 'email',
                    'code' => InputException::INVALID_FIELD_VALUE,
                    'value' => 'missingAtSign'
                ),
                $inputException->getParams()
            );
            $this->assertContains(
                array('fieldName' => 'dob', 'code' => InputException::REQUIRED_FIELD, 'value' => null),
                $inputException->getParams()
            );
            $this->assertContains(
                array('fieldName' => 'taxvat', 'code' => InputException::REQUIRED_FIELD, 'value' => null),
                $inputException->getParams()
            );
            $this->assertContains(
                array('fieldName' => 'gender', 'code' => InputException::REQUIRED_FIELD, 'value' => null),
                $inputException->getParams()
            );
        }
    }

    public function testGetCustomer()
    {
        $attributeModelMock = $this->getMockBuilder(
            '\Magento\Customer\Model\Attribute'
        )->disableOriginalConstructor()->getMock();

        $this->_customerModelMock->expects(
            $this->any()
        )->method(
            'load'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getFirstname' => self::FIRSTNAME,
                'getLastname' => self::LASTNAME,
                'getName' => self::NAME,
                'getEmail' => self::EMAIL,
                'getAttributes' => array($attributeModelMock)
            )
        );

        $attributeModelMock->expects(
            $this->any()
        )->method(
            'getAttributeCode'
        )->will(
            $this->returnValue('attribute_code')
        );

        $this->_customerModelMock->expects(
            $this->any()
        )->method(
            'getData'
        )->with(
            $this->equalTo('attribute_code')
        )->will(
            $this->returnValue('ATTRIBUTE_VALUE')
        );

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $customerService = $this->_createService();

        $actualCustomer = $customerService->getCustomer(self::ID);
        $this->assertEquals(self::ID, $actualCustomer->getId(), 'customer id does not match');
        $this->assertEquals(self::FIRSTNAME, $actualCustomer->getFirstName());
        $this->assertEquals(self::LASTNAME, $actualCustomer->getLastName());
        $this->assertEquals(self::EMAIL, $actualCustomer->getEmail());
        $this->assertEquals(4, count(\Magento\Service\DataObjectConverter::toFlatArray($actualCustomer)));
    }

    public function testSearchCustomersEmpty()
    {
        $collectionMock = $this->getMockBuilder(
            'Magento\Customer\Model\Resource\Customer\Collection'
        )->disableOriginalConstructor()->setMethods(
            array('addNameToSelect', 'addFieldToFilter', 'getSize', 'load', 'joinAttribute')
        )->getMock();
        $collectionMock->expects($this->any())->method('joinAttribute')->will($this->returnSelf());

        $this->_mockReturnValue($collectionMock, array('getSize' => 0));
        $this->_customerFactoryMock->expects(
            $this->atLeastOnce()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_customerModelMock->expects($this->any())->method('load')->will($this->returnSelf());

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array('getId' => self::ID, 'getCollection' => $collectionMock)
        );

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );
        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getAllCustomerAttributeMetadata'
        )->will(
            $this->returnValue(array())
        );

        $customerService = $this->_createService();
        $filterBuilder = new FilterBuilder();
        $filter = $filterBuilder->setField('email')->setValue('customer@search.example.com')->create();
        $this->_searchBuilder->addFilter([$filter]);

        $searchResults = $customerService->searchCustomers($this->_searchBuilder->create());
        $this->assertEquals(0, $searchResults->getTotalCount());
    }

    public function testSearchCustomers()
    {
        $collectionMock = $this->getMockBuilder(
            '\Magento\Customer\Model\Resource\Customer\Collection'
        )->disableOriginalConstructor()->setMethods(
            array('addNameToSelect', 'addFieldToFilter', 'getSize', 'load', 'getItems', 'getIterator', 'joinAttribute')
        )->getMock();
        $collectionMock->expects($this->any())->method('joinAttribute')->will($this->returnSelf());

        $this->_mockReturnValue(
            $collectionMock,
            array(
                'getSize' => 1,
                '_getItems' => array($this->_customerModelMock),
                'getIterator' => new \ArrayIterator(array($this->_customerModelMock))
            )
        );

        $this->_customerFactoryMock->expects(
            $this->atLeastOnce()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'load' => $this->returnSelf(),
                'getId' => self::ID,
                'getEmail' => self::EMAIL,
                'getCollection' => $collectionMock,
                'getAttributes' => array()
            )
        );

        $this->_customerFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerModelMock)
        );

        $this->_customerAddressServiceMock->expects(
            $this->once()
        )->method(
            'getAddresses'
        )->will(
            $this->returnValue(array())
        );

        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getAllCustomerAttributeMetadata'
        )->will(
            $this->returnValue(array())
        );

        $customerService = $this->_createService();
        $filterBuilder = new FilterBuilder();
        $filter = $filterBuilder->setField('email')->setValue(self::EMAIL)->create();
        $this->_searchBuilder->addFilter([$filter]);

        $searchResults = $customerService->searchCustomers($this->_searchBuilder->create());
        $this->assertEquals(1, $searchResults->getTotalCount());
        $this->assertEquals(self::EMAIL, $searchResults->getItems()[0]->getCustomer()->getEmail());
    }

    public function testGetCustomerDetails()
    {
        $customerMock = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $addressMock = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_converter = $this->getMockBuilder('\Magento\Customer\Model\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $service = $this->_createService();
        $this->_converter->expects(
            $this->once()
        )->method(
            'createCustomerFromModel'
        )->will(
            $this->returnValue($customerMock)
        );
        $this->_customerAddressServiceMock->expects(
            $this->once()
        )->method(
            'getAddresses'
        )->will(
            $this->returnValue(array($addressMock))
        );
        $customerDetails = $service->getCustomerDetails(1);
        $this->assertEquals($customerMock, $customerDetails->getCustomer());
        $this->assertEquals(array($addressMock), $customerDetails->getAddresses());
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testGetCustomerDetailsWithException()
    {
        $customerMock = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $addressMock = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_converter = $this->getMockBuilder('\Magento\Customer\Model\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $service = $this->_createService();
        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieve')
            ->will($this->throwException(new NoSuchEntityException('customerId', 1)));
        $this->_converter->expects(
            $this->any()
        )->method(
            'createCustomerFromModel'
        )->will(
            $this->returnValue($customerMock)
        );
        $this->_customerAddressServiceMock->expects(
            $this->any()
        )->method(
            'getAddresses'
        )->will(
            $this->returnValue(array($addressMock))
        );
        $service->getCustomerDetails(1);
    }

    public function testIsEmailAvailable()
    {
        $service = $this->_createService();
        $this->_customerRegistry
            ->expects($this->any())
            ->method('retrieveByEmail')
            ->will(
                $this->throwException(
                    (new NoSuchEntityException('email', self::EMAIL))->addField('websiteId', self::WEBSITE_ID)
                )
            );
        $this->assertTrue($service->isEmailAvailable(self::EMAIL, self::WEBSITE_ID));
    }

    public function testIsEmailAvailableNegative()
    {
        $service = $this->_createService();
        $this->assertFalse($service->isEmailAvailable('email', 1));
    }

    public function testIsEmailAvailableDefaultWebsite()
    {
        $customerMock = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\Customer'
        )->disableOriginalConstructor()->getMock();
        $this->_converter = $this->getMockBuilder(
            '\Magento\Customer\Model\Converter'
        )->disableOriginalConstructor()->getMock();
        $service = $this->_createService();

        $defaultWebsiteId = 7;
        $this->_storeMock->expects($this->once())->method('getWebSiteId')->will($this->returnValue($defaultWebsiteId));
        $this->_customerRegistry->expects(
            $this->once()
        )->method('retrieveByEmail')->with('email', $defaultWebsiteId)->will($this->returnValue($customerMock));
        $this->assertFalse($service->isEmailAvailable('email'));
    }

    public function testCreateAccountMailException()
    {
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        // This is to get the customer model through validation
        $this->_customerModelMock->expects($this->any())
            ->method('getFirstname')
            ->will($this->returnValue('John'));

        $this->_customerModelMock->expects($this->any())
            ->method('getLastname')
            ->will($this->returnValue('Doe'));

        $this->_customerModelMock->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue('somebody@example.com'));

        

        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(true));

        $exception = new MailException(__('The mail server is down'));

        $this->_customerModelMock->expects($this->once())
            ->method('sendNewAccountEmail')
            ->will($this->throwException($exception));

        $this->_loggerMock->expects($this->once())
            ->method('logException')
            ->with($exception);

        $mockCustomer = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();

        $mockCustomer->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue(true));

        $mockCustomer->expects($this->once())
            ->method('__toArray')
            ->will($this->returnValue(['attributeSetId' => true]));

        $this->_customerModelMock->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue([]));

        /**
         * @var Data\CustomerDetails | \PHPUnit_Framework_MockObject_MockObject
         */
        $mockCustomerDetail = $this->getMockBuilder('Magento\Customer\Service\V1\Data\CustomerDetails')
            ->disableOriginalConstructor()
            ->getMock();

        $mockCustomerDetail->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($mockCustomer));

        $service = $this->_createService();
        $service->createCustomer($mockCustomerDetail, 'abc123');
        // If we get no mail exception, the test in considered a success
    }

    private function _setupStoreMock()
    {
        $this->_storeManagerMock = $this->getMockBuilder(
            '\Magento\Store\Model\StoreManagerInterface'
        )->disableOriginalConstructor()->getMock();

        $this->_storeMock = $this->getMockBuilder(
            '\Magento\Store\Model\Store'
        )->disableOriginalConstructor()->getMock();

        $this->_storeManagerMock->expects(
            $this->any()
        )->method(
            'getStore'
        )->will(
            $this->returnValue($this->_storeMock)
        );
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue($mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())->method($method)->will($this->returnValue($value));
        }
    }

    /**
     * @return CustomerAccountService
     */
    private function _createService()
    {
        $customerService = $this->_objectManager->getObject('Magento\Customer\Service\V1\CustomerAccountService',
            [
                'customerFactory' => $this->_customerFactoryMock,
                'storeManager' => $this->_storeManagerMock,
                'converter' => $this->_converter,
                'searchResultsBuilder' => new Data\SearchResultsBuilder,
                'customerBuilder' => $this->_customerBuilder,
                'customerDetailsBuilder' => $this->_customerDetailsBuilder,
                'customerAddressService' => $this->_customerAddressServiceMock,
                'customerMetadataService' => $this->_customerMetadataService,
                'customerRegistry' => $this->_customerRegistry,
                'encryptor' => $this->_encryptorMock,
                'logger' => $this->_loggerMock
            ]
        );
        return $customerService;
    }
}
