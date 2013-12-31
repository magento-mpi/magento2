<?php

namespace Magento\Customer\Service;

/**
 * \Magento\Customer\Service\CustomerV1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class CustomerV1Test extends \PHPUnit_Framework_TestCase
{
    const STREET = 'Parmer';
    const CITY = 'Albuquerque';
    const POSTCODE = '90014';
    const TELEPHONE = '7143556767';
    const REGION = 'Alabama';
    const REGION_ID = 1;
    const COUNTRY_ID = 'US';

    /** Sample values for testing */
    const ID = 1;
    const FIRSTNAME = 'Jane';
    const LASTNAME = 'Doe';
    const NAME = 'J';
    const EMAIL = 'janedoe@example.com';
    const EMAIL_CONFIRMATION_KEY = 'blj487lkjs4confirmation_key';
    const PASSWORD = 'password';
    const ATTRIBUTE_CODE = 'first_name';
    const ATTRIBUTE_VALUE = 'Jane';
    const WEBSITE_ID = 1;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\CustomerFactory
     */
    private $_customerFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\AddressFactory
     */
    private $_addressFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Customer
     */
    private $_customerModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Attribute
     */
    private $_attributeModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\Eav\AttributeMetadataServiceV1Interface
     */
    private $_eavMetadataServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Event\ManagerInterface
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Math\Random
     */
    protected $_mathRandomMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | |Magneto\Customer\Model\Converter
     */
    protected $_converter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\Store
     */
    protected $_storeMock;

    protected $_validator;

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

        $this->_addressFactoryMock = $this->getMockBuilder('Magento\Customer\Model\AddressFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $this->_eavMetadataServiceMock =
            $this->getMockBuilder('Magento\Customer\Service\Eav\AttributeMetadataServiceV1Interface')
                ->disableOriginalConstructor()
                ->getMock();

        $this->_eventManagerMock =
            $this->getMockBuilder('\Magento\Event\ManagerInterface')
                ->disableOriginalConstructor()
                ->getMock();

        $this->_attributeModelMock =
            $this->getMockBuilder('\Magento\Customer\Model\Attribute')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_attributeModelMock
            ->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue(self::ATTRIBUTE_CODE));

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

        $this->_converter = new \Magento\Customer\Model\Converter();

        $this->_validator = $this->getMockBuilder('\Magento\Customer\Model\Metadata\Validator')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetCustomer()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                 'getId' => self::ID,
                 'getFirstname' => self::FIRSTNAME,
                 'getLastname' => self::LASTNAME,
                 'getName' => self::NAME,
                 'getEmail' => self::EMAIL,
                 'getAttributes' => array($this->_attributeModelMock),
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $actualCustomer = $customerService->getCustomer(self::ID);
        $this->assertEquals(self::ID, $actualCustomer->getCustomerId(), 'customer id does not match');
        $this->assertEquals(self::FIRSTNAME, $actualCustomer->getFirstName());
        $this->assertEquals(self::LASTNAME, $actualCustomer->getLastName());
        $this->assertEquals(self::EMAIL, $actualCustomer->getEmail());
        $this->assertEquals(4, count($actualCustomer->getAttributes()));
        $attribute = $actualCustomer->getAttribute(self::ATTRIBUTE_CODE);
        $this->assertEquals(self::ATTRIBUTE_VALUE, $attribute);
    }

    public function testGetCustomerCached()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getFirstname' => self::FIRSTNAME,
                'getLastname' => self::LASTNAME,
                'getName' => self::NAME,
                'getEmail' => self::EMAIL,
                'getAttributes' => array($this->_attributeModelMock),
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $service = $this->_createService();

        $firstCall = $service->getCustomer(self::ID);
        $secondCall = $service->getCustomer(1);

        $this->assertSame($firstCall, $secondCall);
    }

    public function testGetCustomerLocked()
    {
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'getFirstname' => self::FIRSTNAME,
                'getLastname' => self::LASTNAME,
                'getName' => self::NAME,
                'getEmail' => self::EMAIL,
                'getAttributes' => array($this->_attributeModelMock),
            )
        );

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $service = $this->_createService();

        $customer = $service->getCustomer(1);

        $this->assertTrue($customer->isLocked(), 'Service should return locked DTO');
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
     * @expectedException  \Magento\Customer\Service\Entity\V1\Exception
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

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage No customer with customerId 1 exists.
     */
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

        $customerService->activateAccount(self::ID, self::EMAIL_CONFIRMATION_KEY);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
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

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Wrong confirmation key
     */
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

        $customerService->activateAccount(self::ID, self::EMAIL_CONFIRMATION_KEY);
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Failed to confirm customer account
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
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
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

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_RESET_TOKEN_EXPIRED
     * @expectedExceptionMessage Your password reset link has expired.
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
                'isResetPasswordLinkTokenExpired' => true,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $customerService->validateResetPasswordLinkToken(self::ID, $resetToken);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_RESET_TOKEN_EXPIRED
     * @expectedExceptionMessage Your password reset link has expired.
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
                'isResetPasswordLinkTokenExpired' => false,
            )
        );
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $customerService->validateResetPasswordLinkToken(self::ID, $invalidToken);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_CUSTOMER_ID
     * @expectedExceptionMessage No customer with customerId 1 exists
     */
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

        $customerService->validateResetPasswordLinkToken(1, $resetToken);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_RESET_TOKEN
     * @expectedExceptionMessage Invalid password reset token
     */
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

        $customerService->validateResetPasswordLinkToken(null, null);
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

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_EMAIL_NOT_FOUND
     * @expectedExceptionMessage No customer found for the provided email and website ID
     */
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

        $customerService->sendPasswordResetLink($email, 0);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_UNKNOWN
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

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_RESET_TOKEN_EXPIRED
     * @expectedExceptionMessage Your password reset link has expired.
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

        $customerService->resetPassword(self::ID, $password, $resetToken);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_RESET_TOKEN_EXPIRED
     * @expectedExceptionMessage Your password reset link has expired.
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

        $customerService->resetPassword(self::ID, $password, $invalidToken);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_CUSTOMER_ID
     * @expectedExceptionMessage No customer with customerId 4200 exists
     */
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

        $customerService->resetPassword(4200, $password, $resetToken);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_RESET_TOKEN
     * @expectedExceptionMessage Invalid password reset token
     */
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

        $customerService->resetPassword(0, $password, $resetToken);
    }

    public function testGetAddressesDefaultBilling()
    {
        $addressMock = $this->_createAddress(1, 'John');
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getDefaultBillingAddress')
            ->will($this->returnValue($addressMock));
        $this->_customerModelMock->expects($this->any())
            ->method('getDefaultBilling')
            ->will($this->returnValue(1));
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $customerId = 1;
        $address = $customerService->getDefaultBillingAddress($customerId);

        $expected = [
            'id' => 1,
            'default_billing' => true,
            'default_shipping' => false,
            'customer_id' => self::ID,
            'region' => new Entity\V1\Region('', self::REGION, self::REGION_ID),
            'country_id' => self::COUNTRY_ID,
            'street' => [self::STREET],
            'telephone' => self::TELEPHONE,
            'postcode' => self::POSTCODE,
            'city' => self::CITY,
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];

        $this->assertEquals($expected, $address->__toArray());
    }

    public function testGetAddressesDefaultShipping()
    {
        $addressMock = $this->_createAddress(1, 'John');
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getDefaultShippingAddress')
            ->will($this->returnValue($addressMock));
        $this->_customerModelMock->expects($this->any())
            ->method('getDefaultShipping')
            ->will($this->returnValue(1));
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $customerId = 1;
        $address = $customerService->getDefaultShippingAddress($customerId);

        $expected = [
            'id' => 1,
            'default_shipping' => true,
            'default_billing' => false,
            'customer_id' => self::ID,
            'region' => new Entity\V1\Region('', self::REGION, self::REGION_ID),
            'country_id' => self::COUNTRY_ID,
            'street' => [self::STREET],
            'telephone' => self::TELEPHONE,
            'postcode' => self::POSTCODE,
            'city' => self::CITY,
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];

        $this->assertEquals($expected, $address->__toArray());
    }

    public function testGetAddressesById()
    {
        $addressMock = $this->_createAddress(1, 'John');
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddressById')
            ->will($this->returnValue($addressMock));
        $this->_customerModelMock->expects($this->any())
            ->method('getDefaultShipping')
            ->will($this->returnValue(1));
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $customerId = 1;
        $addressId = 1;
        $address = $customerService->getAddressById($customerId, $addressId);

        $expected = [
            'id' => 1,
            'default_shipping' => true,
            'default_billing' => false,
            'customer_id' => self::ID,
            'region' => new Entity\V1\Region('', self::REGION, self::REGION_ID),
            'country_id' => self::COUNTRY_ID,
            'street' => [self::STREET],
            'telephone' => self::TELEPHONE,
            'postcode' => self::POSTCODE,
            'city' => self::CITY,
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];

        $this->assertEquals($expected, $address->__toArray());
    }

    public function testGetAddresses()
    {
        $addressMock = $this->_createAddress(1, 'John');
        $addressMock2 = $this->_createAddress(2, 'Genry');
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddresses')
            ->will($this->returnValue([$addressMock, $addressMock2]));
        $this->_customerModelMock->expects($this->any())
            ->method('getDefaultShipping')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getDefaultBilling')
            ->will($this->returnValue(2));
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $customerService = $this->_createService();

        $addresses = $customerService->getAddresses(1);

        $expected = [
            [
                'id' => 1,
                'default_shipping' => true,
                'default_billing' => false,
                'customer_id' => self::ID,
                'region' => new Entity\V1\Region('', self::REGION, self::REGION_ID),
                'country_id' => self::COUNTRY_ID,
                'street' => [self::STREET],
                'telephone' => self::TELEPHONE,
                'postcode' => self::POSTCODE,
                'city' => self::CITY,
                'firstname' => 'John',
                'lastname' => 'Doe',
            ], [
                'id' => 2,
                'default_billing' => true,
                'default_shipping' => false,
                'customer_id' => self::ID,
                'region' => new Entity\V1\Region('', self::REGION, self::REGION_ID),
                'country_id' => self::COUNTRY_ID,
                'street' => [self::STREET],
                'telephone' => self::TELEPHONE,
                'postcode' => self::POSTCODE,
                'city' => self::CITY,
                'firstname' => 'Genry',
                'lastname' => 'Doe',
            ]
        ];

        $this->assertEquals($expected[0], $addresses[0]->__toArray());
        $this->assertEquals($expected[1], $addresses[1]->__toArray());
    }

    public function testSaveAddresses()
    {
        // Setup Customer mock
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddresses')
            ->will($this->returnValue([]));

        // Setup address mock
        $mockAddress = $this->_createAddress(1, 'John');
        $mockAddress->expects($this->once())
            ->method('save');
        $mockAddress->expects($this->any())
            ->method('setData');
        $this->_addressFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockAddress));
        $customerService = $this->_createService();

        $address = new Entity\V1\Address();
        $address->setFirstname('John')
            ->setLastname(self::LASTNAME)
            ->setRegion(new Entity\V1\Region('', self::REGION, self::REGION_ID))
            ->setStreet([self::STREET])
            ->setTelephone(self::TELEPHONE)
            ->setCity(self::CITY)
            ->setCountryId(self::COUNTRY_ID)
            ->setPostcode(self::POSTCODE);
        $ids = $customerService->saveAddresses(1, [$address]);
        $this->assertEquals([1], $ids);
    }

    public function testSaveAddressesChanges()
    {
        // Setup address mock
        $mockAddress = $this->_createAddress(1, 'John');

        // Setup Customer mock
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddressItemById')
            ->with(1)
            ->will($this->returnValue($mockAddress));

        // Assert
        $mockAddress->expects($this->once())
            ->method('save');
        $mockAddress->expects($this->any())
            ->method('setData');

        $customerService = $this->_createService();
        $address = new Entity\V1\Address();
        $address->setId(1)
            ->setFirstname('Jane')
            ->setLastname(self::LASTNAME)
            ->setRegion(new Entity\V1\Region('', self::REGION, self::REGION_ID))
            ->setStreet([self::STREET])
            ->setTelephone(self::TELEPHONE)
            ->setCity(self::CITY)
            ->setCountryId(self::COUNTRY_ID)
            ->setPostcode(self::POSTCODE);
        $ids = $customerService->saveAddresses(1, [$address]);
        $this->assertEquals([1], $ids);
    }

    public function testSaveAddressesNoAddresses()
    {
        // Setup Customer mock
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $customerService = $this->_createService();

        $ids = $customerService->saveAddresses(1, []);
        $this->assertEmpty($ids);
    }

    public function testSaveAddressesIdSetButNotAlreadyExisting()
    {
        // Setup Customer mock
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddresses')
            ->will($this->returnValue([]));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddressItemById')
            ->with(1)
            ->will($this->returnValue(null));

        // Setup address mock
        $mockAddress = $this->_createAddress(1, 'John');
        $mockAddress->expects($this->once())
            ->method('save');
        $mockAddress->expects($this->any())
            ->method('setData');
        $this->_addressFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockAddress));
        $customerService = $this->_createService();

        $address = new Entity\V1\Address();
        $address->setId(1)
            ->setFirstname('John')
            ->setLastname(self::LASTNAME)
            ->setRegion(new Entity\V1\Region('', self::REGION, self::REGION_ID))
            ->setStreet([self::STREET])
            ->setTelephone(self::TELEPHONE)
            ->setCity(self::CITY)
            ->setCountryId(self::COUNTRY_ID)
            ->setPostcode(self::POSTCODE);
        $ids = $customerService->saveAddresses(1, [$address]);
        $this->assertEquals([1], $ids);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage No customer with customerId 4200 exists
     */
    public function testSaveAddressesCustomerIdNotExist()
    {
        // Setup Customer mock
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(0));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddresses')
            ->will($this->returnValue([]));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddressItemById')
            ->with(1)
            ->will($this->returnValue(null));
        $customerService = $this->_createService();
        $address = new Entity\V1\Address();
        $address->setFirstname('John')
            ->setLastname(self::LASTNAME)
            ->setRegion(new Entity\V1\Region('', self::REGION, self::REGION_ID))
            ->setStreet([self::STREET])
            ->setTelephone(self::TELEPHONE)
            ->setCity(self::CITY)
            ->setCountryId(self::COUNTRY_ID)
            ->setPostcode(self::POSTCODE);

        $failures = $customerService->saveAddresses(4200, [$address]);
        $this->assertEmpty($failures);
    }

    public function testSaveCustomer()
    {
        $customerEntity = new Entity\V1\Customer();
        $customerEntity->setCustomerId(self::ID);
        $customerEntity->setEmail(self::EMAIL);
        $customerEntity->setFirstName(self::FIRSTNAME);
        $customerEntity->setLastName(self::LASTNAME);
        $attributes = array(
            'create_in' => 'Admin',
            'password' => 'password',
        );
        $customerEntity->setAttributes($attributes);

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
                'load' => $this->_customerModelMock,
            )
        );

        // verify
        $this->_customerModelMock->expects($this->atLeastOnce())
            ->method('setData');

        $customerService = $this->_createService();

        $this->assertEquals(self::ID, $customerService->saveCustomer($customerEntity));
    }

    public function testSaveNonexistingCustomer()
    {
        $customerEntity = new Entity\V1\Customer();
        $customerEntity->setCustomerId(self::ID);
        $customerEntity->setEmail(self::EMAIL);
        $customerEntity->setFirstName(self::FIRSTNAME);
        $customerEntity->setLastName(self::LASTNAME);
        $attributes = array(
            'create_in' => 'Admin',
            'password' => 'password',
        );
        $customerEntity->setAttributes($attributes);

        $this->_customerFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => '2',
            )
        );

        // verify
        $this->_customerModelMock->expects($this->atLeastOnce())
            ->method('setData');

        $customerService = $this->_createService();

        $this->assertEquals(2, $customerService->saveCustomer($customerEntity));
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     */
    public function testNewCustomerIdException()
    {
        $customerEntity = new Entity\V1\Customer();
        $customerEntity->setEmail(self::EMAIL);
        $customerEntity->setFirstName(self::FIRSTNAME);
        $customerEntity->setLastName(self::LASTNAME);
        $attributes = array(
            'create_in' => 'Admin',
            'password' => 'password',
            'id' => '3', // setting id causes the exception
        );
        $customerEntity->setAttributes($attributes);

    }

    public function testSaveNewCustomer()
    {
        $customerEntity = new Entity\V1\Customer();
        $customerEntity->setEmail(self::EMAIL);
        $customerEntity->setFirstName(self::FIRSTNAME);
        $customerEntity->setLastName(self::LASTNAME);
        $attributes = array(
            'create_in' => 'Admin',
            'password' => 'password',
        );
        $customerEntity->setAttributes($attributes);

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
            )
        );

        // verify
        $this->_customerModelMock->expects($this->atLeastOnce())
            ->method('setData');

        $customerService = $this->_createService();

        $this->assertEquals(self::ID, $customerService->saveCustomer($customerEntity));
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionMessage exception message
     */
    public function testSaveCustomerWithException()
    {
        $customerEntity = new Entity\V1\Customer();
        $customerEntity->setEmail(self::EMAIL);
        $customerEntity->setFirstName(self::FIRSTNAME);
        $customerEntity->setLastName(self::LASTNAME);
        $attributes = array(
            'create_in' => 'Admin',
            'password' => 'password',
        );
        $customerEntity->setAttributes($attributes);

        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));

        $this->_mockReturnValue(
            $this->_customerModelMock,
            array(
                'getId' => self::ID,
            )
        );

        $this->_customerModelMock->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \Exception('exception message')));

        // verify
        $customerService = $this->_createService();

        $customerService->saveCustomer($customerEntity);
    }

    public function testGetAddressAttributeMetadata()
    {
        $this->_eavMetadataServiceMock->expects($this->once())
            ->method('getAttributeMetadata')
            ->with($this->equalTo('customer_address'), $this->equalTo('attr_code'))
            ->will($this->returnValue('attr_metadata'));
        $customerService = $this->_createService();
        $attributeMetadata = $customerService->getAddressAttributeMetadata('attr_code');
        $this->assertSame('attr_metadata', $attributeMetadata);
    }

    public function testGetCustomerAttributeMetadata()
    {
        $this->_eavMetadataServiceMock->expects($this->once())
            ->method('getAttributeMetadata')
            ->with($this->equalTo('customer'), $this->equalTo('attr_code'))
            ->will($this->returnValue('attr_metadata'));
        $customerService = $this->_createService();
        $attributeMetadata = $customerService->getCustomerAttributeMetadata('attr_code');
        $this->assertSame('attr_metadata', $attributeMetadata);
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

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_EMAIL_NOT_FOUND
     */
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
        $customerService->sendConfirmation('email');
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_CONFIRMATION_NOT_NEEDED
     */
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
        $customerService->sendConfirmation('email');
    }

    public function testDeleteAddressFromCustomer()
    {
        // Setup address mock
        $mockAddress = $this->_createAddress(1, 'John');
        $mockAddress->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(self::ID));
        $this->_addressFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockAddress));

        // verify delete is called on the mock address model
        $mockAddress->expects($this->once())
            ->method('delete');

        $customerService = $this->_createService();
        $customerService->deleteAddressFromCustomer(1, 1);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_CUSTOMER_ID_MISMATCH
     */
    public function testDeleteAddressFromCustomerMismatch()
    {
        // Setup address mock
        $mockAddress = $this->_createAddress(1, 'John', 55);
        $this->_addressFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockAddress));

        // verify delete is called on the mock address model
        $mockAddress->expects($this->never())
            ->method('delete');

        $customerService = $this->_createService();
        $customerService->deleteAddressFromCustomer(1, 1);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_ADDRESS_NOT_FOUND
     */
    public function testDeleteAddressFromCustomerBadAddrId()
    {
        // Setup address mock
        $mockAddress = $this->_createAddress(0, '');
        $mockAddress->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(self::ID));
        $this->_addressFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockAddress));

        // verify delete is called on the mock address model
        $mockAddress->expects($this->never())
            ->method('delete');

        $customerService = $this->_createService();
        $customerService->deleteAddressFromCustomer(1, 2);
    }

    /**
     * @expectedException \Magento\Customer\Service\Entity\V1\Exception
     * @expectedExceptionCode \Magento\Customer\Service\CustomerV1Interface::CODE_INVALID_ADDRESS_ID
     */
    public function testDeleteAddressFromCustomerInvalidAddrId()
    {
        $customerService = $this->_createService();
        $customerService->deleteAddressFromCustomer(1, 0);
    }


    public function testSaveAddressesWithValidatorException()
    {
        // Setup Customer mock
        $this->_customerFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_customerModelMock));
        $this->_customerModelMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $this->_customerModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_customerModelMock->expects($this->any())
            ->method('getAddresses')
            ->will($this->returnValue([]));

        // Setup address mock
        $mockAddress = $this->getMockBuilder('Magento\Customer\Model\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $mockAddress->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(['some error']));
        $this->_addressFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockAddress));
        $customerService = $this->_createService();

        $address = new Entity\V1\Address();
        $address->setFirstname('John')
            ->setLastname(self::LASTNAME)
            ->setRegion(new Entity\V1\Region('', self::REGION, self::REGION_ID))
            ->setStreet([self::STREET])
            ->setTelephone(self::TELEPHONE)
            ->setCity(self::CITY)
            ->setCountryId(self::COUNTRY_ID)
            ->setPostcode(self::POSTCODE);
        try {
            $customerService->saveAddresses(1, [$address]);
        } catch (Entity\V1\AggregateException $ae) {
            $addressException = $ae->getExceptions()[0];
            $this->assertInstanceOf('\Magento\Customer\Service\Entity\V1\Exception', $addressException);
            $this->assertInstanceOf('\Magento\Validator\ValidatorException', $addressException->getPrevious());
            $this->assertSame('some error', $addressException->getPrevious()->getMessage());
            return;
        }
        $this->fail("Expected AggregateException not caught.");
    }

    /**
     * Helper that returns a mock \Magento\Customer\Model\Address object.
     *
     * @param $addrId
     * @param $firstName
     * @param $customerId
     * @return \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Address
     */
    private function _createAddress($addrId, $firstName, $customerId = self::ID)
    {
        $attributes = [
            $this->_createAttribute('firstname'),
            $this->_createAttribute('lastname'),
            $this->_createAttribute('street'),
            $this->_createAttribute('city'),
            $this->_createAttribute('postcode'),
            $this->_createAttribute('telephone'),
            $this->_createAttribute('region_id'),
            $this->_createAttribute('region'),
            $this->_createAttribute('country_id'),
        ];

        $addressMock = $this->getMockBuilder('Magento\Customer\Model\Address')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getId', 'hasDataChanges', 'getRegion', 'getRegionId',
                    'addData', 'setData', 'setCustomerId', 'setPostIndex',
                    'setFirstname', 'load', 'save', '__sleep', '__wakeup',
                    'getDefaultAttributeCodes', 'getAttributes', 'getData',
                    'getCustomerId', 'getParentId', 'delete', 'validate'
                ]
            )
            ->getMock();
        $addressMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($addrId));
        $addressMock->expects($this->any())
            ->method('getRegion')
            ->will($this->returnValue(self::REGION));
        $addressMock->expects($this->any())
            ->method('getRegionId')
            ->will($this->returnValue(self::REGION_ID));
        $addressMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        $addressMock->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(true));

        $map = [
            ['firstname', null, $firstName],
            ['lastname', null, self::LASTNAME],
            ['street', null, self::STREET],
            ['city', null, self::CITY],
            ['postcode', null, self::POSTCODE],
            ['telephone', null, self::TELEPHONE],
            ['region', null, self::REGION],
            ['country_id', null, self::COUNTRY_ID],
        ];

        $addressMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($map));

        $addressMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $addressMock->expects($this->any())
            ->method('getDefaultAttributeCodes')
            ->will($this->returnValue(['entity_id', 'attribute_set_id']));
        $addressMock->expects($this->any())
            ->method('getAttributes')
            ->will($this->returnValue($attributes));
        return $addressMock;
    }

    private function _createAttribute($attributeCode)
    {
        $attribute = $this->getMockBuilder('\Magento\Customer\Model\Attribute')
            ->disableOriginalConstructor()
            ->getMock();
        $attribute->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));
        return $attribute;
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
     * @return CustomerV1
     */
    private function _createService()
    {
        $customerService = new CustomerV1(
            $this->_customerFactoryMock,
            $this->_addressFactoryMock,
            $this->_eavMetadataServiceMock,
            $this->_eventManagerMock,
            $this->_storeManagerMock,
            $this->_mathRandomMock,
            $this->_converter,
            $this->_validator
        );
        return $customerService;
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

}
