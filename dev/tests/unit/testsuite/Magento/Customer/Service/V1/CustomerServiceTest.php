<?php

namespace Magento\Customer\Service\V1;
use Magento\Customer\Service\V1;
use Magento\Customer\Service\Entity;

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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerServiceTest extends \PHPUnit_Framework_TestCase
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
    const ATTRIBUTE_CODE = 'random_attr_code';
    const ATTRIBUTE_VALUE = 'random_attr_value';
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
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\CustomerMetadataServiceInterface
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
     * @var \Magento\Customer\Model\Converter
     */
    protected $_converter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\Store
     */
    protected $_storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Dto\AddressBuilder
     */
    protected $_addressBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Dto\CustomerBuilder
     */
    protected $_customerBuilder;

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

        $this->_validator = $this->getMockBuilder('\Magento\Customer\Model\Metadata\Validator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_addressBuilder = new V1\Dto\AddressBuilder(
            new V1\Dto\RegionBuilder());

        $this->_customerBuilder = new V1\Dto\CustomerBuilder();

        $customerBuilder = new V1\Dto\CustomerBuilder();

        $this->_converter = new \Magento\Customer\Model\Converter($customerBuilder, $this->_customerFactoryMock);
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

    public function testSaveCustomer()
    {
        $customerData = [
            'customer_id' => self::ID,
            'email' => self::EMAIL,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'create_in' => 'Admin',
            'password' => 'password'
        ];
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();

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
        $customerData = [
            'customer_id' => self::ID,
            'email' => self::EMAIL,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'create_in' => 'Admin',
            'password' => 'password'
        ];
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();

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

    public function testSaveNewCustomer()
    {
        $customerData = [
            'email' => self::EMAIL,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'create_in' => 'Admin',
            'password' => 'password'
        ];
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();

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
        $customerData = [
            'email' => self::EMAIL,
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'create_in' => 'Admin',
            'password' => 'password'
        ];
        $this->_customerBuilder->populateWithArray($customerData);
        $customerEntity = $this->_customerBuilder->create();

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
     * @return CustomerService
     */
    private function _createService()
    {
        $customerService = new CustomerService(
            $this->_converter
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
