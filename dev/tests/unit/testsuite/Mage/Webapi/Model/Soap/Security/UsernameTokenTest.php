<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test SOAP WS-Security UsernameToken implementation.
 */
class Mage_Webapi_Model_Soap_Security_UsernameTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_nonceStorageMock;

    /**
     * Set up nonce storage mock to be used in further tests.
     */
    public function setUp()
    {
        $this->_nonceStorageMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage')
            ->disableOriginalConstructor()
            ->setConstructorArgs(array('validateNonce'))
            ->getMock();

        $this->_nonceStorageMock->expects($this->any())
            ->method('validateNonce')
            ->will($this->returnValue(true));
    }

    /**
     * @dataProvider validDateTimeProvider()
     * @param string $validDateTime
     */
    public function testConstructNewUsernameToken($validDateTime)
    {
        $usernameTokenOptions = self::_getUserTokenOptions();
        $usernameTokenOptions['created'] = $validDateTime;
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;

        $this->_nonceStorageMock
            ->expects($this->once())
            ->method('validateNonce')
            ->with($usernameTokenOptions['nonce'], strtotime($validDateTime));

        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);
        $this->assertInstanceOf('Mage_Webapi_Model_Soap_Security_UsernameToken', $usernameToken);
    }

    public static function validDateTimeProvider()
    {
        return array(
            'Valid ISO8601 date' => array(date('c')),
            'Date in UTC timezone "Z"' => array(date('Y-m-d\TH:i:s\Z')),
            'Date in +2 hours timezone' => array(date('Y-m-d\TH:i:s+02:00')),
            'Date in -2.5 hours timezone' => array(date('Y-m-d\TH:i:s-02:30')),
        );
    }

    /**
     * @dataProvider invalidDateTimeProvider()
     * @param string $invalidDateTime
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidDateException
     */
    public function testConstructNewUsernameTokenWithInvalidCreatedDate($invalidDateTime)
    {
        $usernameTokenOptions = self::_getUserTokenOptions();
        $usernameTokenOptions['created'] = $invalidDateTime;
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;

        new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);
    }

    public static function invalidDateTimeProvider()
    {
        return array(
            'No time specified' => array(date('Y-m-d')),
            'No seconds specified' => array(date('Y-m-dTH:i')),
            'Hours value is out of range' => array(date('Y-m-dT25:00:52+02:00')),
        );
    }

    /**
     * @dataProvider missingUsernameDataProvider()
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_MissingUsernameException
     */
    public function testConstructNewUsernameTokenWithMissingUsername($usernameTokenOptions)
    {
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;
        new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);
    }

    public static function missingUsernameDataProvider()
    {
        $usernameTokenOptions = self::_getUserTokenOptions();
        $unsetUsername = $usernameTokenOptions;
        unset($unsetUsername['username']);
        $emptyUsername = $usernameTokenOptions;
        $emptyUsername['username'] = '';

        return array(
            'Username is not set' => array($unsetUsername),
            'Username is empty' => array($emptyUsername)
        );
    }

    /**
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidPasswordTypeException
     */
    public function testConstructNewUsernameTokenWithInvalidPasswordType()
    {
        $usernameTokenOptions = self::_getUserTokenOptions();
        $usernameTokenOptions['passwordType'] = 'INVALID_TYPE';
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;

        new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);
    }

    /**
     * @dataProvider missingPasswordDataProvider()
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_MissingPasswordException
     */
    public function testConstructNewUsernameTokenWithMissingPassword($usernameTokenOptions)
    {
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;
        new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);
    }

    public static function missingPasswordDataProvider()
    {
        $usernameTokenOptions = self::_getUserTokenOptions();
        $unsetPassword = $usernameTokenOptions;
        unset($unsetPassword['password']);
        $emptyPassword = $usernameTokenOptions;
        $emptyPassword['password'] = '';

        return array(
            'Password is not set' => array($unsetPassword),
            'Password is empty' => array($emptyPassword)
        );
    }

    /**
     * @dataProvider missingNonceDataProvider()
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_MissingNonceException
     */
    public function testConstructNewUsernameTokenWithMissingNonce($usernameTokenOptions)
    {
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;
        new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);
    }

    public static function missingNonceDataProvider()
    {
        $usernameTokenOptions = self::_getUserTokenOptions();
        $unsetNonce = $usernameTokenOptions;
        unset($unsetNonce['nonce']);
        $emptyNonce = $usernameTokenOptions;
        $emptyNonce['nonce'] = '';

        return array(
            'Nonce is not set' => array($unsetNonce),
            'Nonce is empty' => array($emptyNonce)
        );
    }

    /**
     * @dataProvider missingCreatedDataProvider()
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_MissingCreatedException
     */
    public function testConstructNewUsernameTokenWithMissingCreated($usernameTokenOptions)
    {
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;
        new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);
    }

    public static function missingCreatedDataProvider()
    {
        $usernameTokenOptions = self::_getUserTokenOptions();
        $unsetCreated = $usernameTokenOptions;
        unset($unsetCreated['created']);
        $emptyCreated = $usernameTokenOptions;
        $emptyCreated['created'] = '';

        return array(
            'Created is not set' => array($unsetCreated),
            'Created is empty' => array($emptyCreated)
        );
    }

    public function testAuthenticate()
    {
        $objectFactoryMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelInstance'))
            ->getMock();
        $usernameTokenOptions = self::_getUserTokenOptions();
        $usernameTokenOptions['objectFactory'] = $objectFactoryMock;
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;


        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);

        $userMock = $this->getMockBuilder('Mage_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId', 'getApiSecret'))
            ->getMock();

        $objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Webapi_Model_Acl_User')
            ->will($this->returnValue($userMock));

        $userMock->expects($this->once())
            ->method('load')
            ->with($usernameTokenOptions['username'], 'user_name')
            ->will($this->returnSelf());
        $userMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $userMock->expects($this->once())
            ->method('getApiSecret')
            ->will($this->returnValue('123123qa'));

        $user = $usernameToken->authenticate();
        $this->assertInstanceOf('Mage_Webapi_Model_Acl_User', $user);
    }

    /**
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_UserNotFoundException
     */
    public function testAuthenticateWithInvalidUsername()
    {
        $objectFactoryMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelInstance'))
            ->getMock();
        $usernameTokenOptions = self::_getUserTokenOptions();
        $usernameTokenOptions['objectFactory'] = $objectFactoryMock;
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;

        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);

        $userMock = $this->getMockBuilder('Mage_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId'))
            ->getMock();

        $objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Webapi_Model_Acl_User')
            ->will($this->returnValue($userMock));

        $userMock->expects($this->once())
            ->method('load')
            ->with($usernameTokenOptions['username'], 'user_name')
            ->will($this->returnSelf());
        $userMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(false));

        $usernameToken->authenticate();
    }

    /**
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_NotAuthenticatedException
     */
    public function testAuthenticateWithInvalidPassword()
    {
        $objectFactoryMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelInstance'))
            ->getMock();
        $usernameTokenOptions = self::_getUserTokenOptions();
        $usernameTokenOptions['objectFactory'] = $objectFactoryMock;
        $usernameTokenOptions['nonceStorage'] = $this->_nonceStorageMock;


        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($usernameTokenOptions);

        $userMock = $this->getMockBuilder('Mage_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId', 'getApiSecret'))
            ->getMock();

        $objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Webapi_Model_Acl_User')
            ->will($this->returnValue($userMock));

        $userMock->expects($this->once())
            ->method('load')
            ->with($usernameTokenOptions['username'], 'user_name')
            ->will($this->returnSelf());
        $userMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $userMock->expects($this->once())
            ->method('getApiSecret')
            ->will($this->returnValue('INVALID_PASSWORD'));

        $usernameToken->authenticate();
    }

    /**
     * Fixture data for UsernameToken constructor
     *
     * @return array
     */
    protected static function _getUserTokenOptions()
    {
        $nonce = mt_rand();
        $created = date('c');
        $password = '123123qa';
        $passwordDigest = base64_encode(hash('sha1', $nonce . $created . $password, true));

        return array(
            'username' => 'testuser',
            'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_DIGEST,
            'password' => $passwordDigest,
            'nonce' => base64_encode($nonce),
            'created' => $created
        );
    }
}
