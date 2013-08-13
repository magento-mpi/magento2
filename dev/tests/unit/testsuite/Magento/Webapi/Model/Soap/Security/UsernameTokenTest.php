<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test SOAP WS-Security UsernameToken implementation.
 */
class Magento_Webapi_Model_Soap_Security_UsernameTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_nonceStorageMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_userFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_userMock;

    /**
     * Set up nonce storage mock to be used in further tests.
     */
    protected function setUp()
    {
        $this->_nonceStorageMock = $this->getMockBuilder(
            'Magento_Webapi_Model_Soap_Security_UsernameToken_NonceStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('validateNonce'))
            ->getMock();
        $this->_userMock = $this->getMockBuilder('Magento_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId', 'getSecret'))
            ->getMock();
        $this->_userFactoryMock = $this->getMockBuilder('Magento_Webapi_Model_Acl_User_Factory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
    }

    /**
     * Test construction of object with valid datetime input.
     *
     * @dataProvider validDateTimeProvider()
     * @param string $validDateTime
     */
    public function testAuthenticateUsernameToken($validDateTime)
    {
        $username = 'test_user';
        $password = 'test_password';
        $nonce = mt_rand();
        $data = $nonce . $validDateTime . $password;
        $tokenPassword = base64_encode(hash('sha1', $data, true));
        $tokenNonce = base64_encode($nonce);
        $this->_nonceStorageMock
            ->expects($this->once())
            ->method('validateNonce')
            ->with($tokenNonce, strtotime($validDateTime));
        $this->_userFactoryMock->expects($this->once())
            ->method('create')
            ->with()
            ->will($this->returnValue($this->_userMock));
        $this->_userMock->expects($this->once())
            ->method('load')
            ->with($username, 'api_key')
            ->will($this->returnSelf());
        $this->_userMock->expects($this->once())
            ->method('getId')
            ->with()
            ->will($this->returnValue(1));
        $this->_userMock->expects($this->once())
            ->method('getSecret')
            ->with()
            ->will($this->returnValue($password));

        $usernameToken = new Magento_Webapi_Model_Soap_Security_UsernameToken(
            $this->_nonceStorageMock,
            $this->_userFactoryMock
        );
        $this->assertEquals(
            $this->_userMock,
            $usernameToken->authenticate($username, $tokenPassword, $validDateTime, $tokenNonce)
        );
    }

    /**
     * Data provider for testConstructNewUsernameToken.
     *
     * @return array
     */
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
     * Test construction of object with invalid datetime input.
     *
     * @dataProvider invalidDateTimeProvider()
     * @param string $invalidDateTime
     * @expectedException Magento_Webapi_Model_Soap_Security_UsernameToken_InvalidDateException
     */
    public function testAuthenticateUsernameTokenWithInvalidCreatedDate($invalidDateTime)
    {
        $username = 'test_user';
        $password = 'test_password';
        $nonce = mt_rand();

        $usernameToken = new Magento_Webapi_Model_Soap_Security_UsernameToken(
            $this->_nonceStorageMock,
            $this->_userFactoryMock
        );
        $usernameToken->authenticate($username, $password, $invalidDateTime, $nonce);
    }

    /**
     * Data provider for testConstructNewUsernameTokenWithInvalidCreatedDate.
     *
     * @return array
     */
    public static function invalidDateTimeProvider()
    {
        return array(
            'No time specified' => array(date('Y-m-d')),
            'No seconds specified' => array(date('Y-m-dTH:i')),
            'Hours value is out of range' => array(date('Y-m-dT25:00:52+02:00')),
        );
    }

    /**
     * Test construction of object with invalid password type.
     *
     * @expectedException Magento_Webapi_Model_Soap_Security_UsernameToken_InvalidPasswordTypeException
     */
    public function testConstructNewUsernameTokenWithInvalidPasswordType()
    {
        new Magento_Webapi_Model_Soap_Security_UsernameToken(
            $this->_nonceStorageMock,
            $this->_userFactoryMock,
            'INVALID_TYPE'
        );
    }

    /**
     * Test negative token authentication - username is invalid.
     *
     * @expectedException Magento_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException
     */
    public function testAuthenticateWithInvalidUsername()
    {
        $username = 'test_user';
        $password = 'test_password';
        list($created, $tokenPassword, $tokenNonce) = $this->_getTokenData($password);

        $this->_nonceStorageMock
            ->expects($this->once())
            ->method('validateNonce')
            ->with($tokenNonce, strtotime($created));
        $this->_userFactoryMock->expects($this->once())
            ->method('create')
            ->with()
            ->will($this->returnValue($this->_userMock));
        $this->_userMock->expects($this->once())
            ->method('load')
            ->with($username, 'api_key')
            ->will($this->returnSelf());
        $this->_userMock->expects($this->once())
            ->method('getId')
            ->with()
            ->will($this->returnValue(false));

        $usernameToken = new Magento_Webapi_Model_Soap_Security_UsernameToken(
            $this->_nonceStorageMock,
            $this->_userFactoryMock
        );
        $usernameToken->authenticate($username, $tokenPassword, $created, $tokenNonce);
    }

    /**
     * Test negative token authentication - password is invalid.
     *
     * @expectedException Magento_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException
     */
    public function testAuthenticateWithInvalidPassword()
    {
        $username = 'test_user';
        $password = 'test_password';
        $invalidPassword = 'invalid_password';
        list($created, $tokenPassword, $tokenNonce) = $this->_getTokenData($password);

        $this->_nonceStorageMock
            ->expects($this->once())
            ->method('validateNonce')
            ->with($tokenNonce, strtotime($created));
        $this->_userFactoryMock->expects($this->once())
            ->method('create')
            ->with()
            ->will($this->returnValue($this->_userMock));
        $this->_userMock->expects($this->once())
            ->method('load')
            ->with($username, 'api_key')
            ->will($this->returnSelf());
        $this->_userMock->expects($this->once())
            ->method('getId')
            ->with()
            ->will($this->returnValue(1));
        $this->_userMock->expects($this->once())
            ->method('getSecret')
            ->with()
            ->will($this->returnValue($invalidPassword));

        $usernameToken = new Magento_Webapi_Model_Soap_Security_UsernameToken(
            $this->_nonceStorageMock,
            $this->_userFactoryMock
        );
        $usernameToken->authenticate($username, $tokenPassword, $created, $tokenNonce);
    }

    protected function _getTokenData($password)
    {
        $nonce = mt_rand();
        $created = date('c');
        $tokenPassword = base64_encode(hash('sha1', $nonce . $created . $password, true));
        $tokenNonce = base64_encode($nonce);
        return array($created, $tokenPassword, $tokenNonce);
    }
}
