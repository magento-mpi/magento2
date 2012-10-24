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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var array
     */
    protected $_tokenOptionsFixture = array();

    /**
     * @var string
     */
    protected $_validFixturePassword;

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

        $this->_objectFactoryMock = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getModelInstance'))
            ->getMock();

        $nonce = mt_rand();
        $created = date('c');
        $this->_validFixturePassword = '123123qa';
        $this->_tokenOptionsFixture = array(
            'username' => 'testuser',
            'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_DIGEST,
            'password' => base64_encode(hash('sha1', $nonce . $created . $this->_validFixturePassword, true)),
            'nonce' => base64_encode($nonce),
            'created' => $created,
            'nonceStorage' => $this->_nonceStorageMock,
            'objectFactory' => $this->_objectFactoryMock,
        );
    }

    /**
     * Test construction of object with valid datetime input.
     *
     * @dataProvider validDateTimeProvider()
     * @param string $validDateTime
     */
    public function testConstructNewUsernameToken($validDateTime)
    {
        $this->_tokenOptionsFixture['created'] = $validDateTime;

        $this->_nonceStorageMock
            ->expects($this->once())
            ->method('validateNonce')
            ->with($this->_tokenOptionsFixture['nonce'], strtotime($validDateTime));

        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);
        $this->assertInstanceOf('Mage_Webapi_Model_Soap_Security_UsernameToken', $usernameToken);
    }

    /**
     * Data provider for testConstructNewUsernameToken
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
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidDateException
     */
    public function testConstructNewUsernameTokenWithInvalidCreatedDate($invalidDateTime)
    {
        $this->_tokenOptionsFixture['created'] = $invalidDateTime;

        new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);
    }

    /**
     * Data provider for testConstructNewUsernameTokenWithInvalidCreatedDate
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
     * Test construction of object with missing username.
     *
     * @dataProvider missingUsernameDataProvider()
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_MissingUsernameException
     */
    public function testConstructNewUsernameTokenWithMissingUsername($action, $field)
    {
        $this->_prepareFixture($action, $field);

        new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);
    }

    /**
     * Data provider for testConstructNewUsernameTokenWithMissingUsername
     *
     * @return array
     */
    public static function missingUsernameDataProvider()
    {
        return array(
            'Username is not set' => array('unset', 'username'),
            'Username is empty' => array('empty', 'username')
        );
    }

    /**
     * Test construction of object with invalid password type.
     *
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidPasswordTypeException
     */
    public function testConstructNewUsernameTokenWithInvalidPasswordType()
    {
        $this->_tokenOptionsFixture['passwordType'] = 'INVALID_TYPE';

        new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);
    }

    /**
     * Test construction of object with missing password input.
     *
     * @dataProvider missingPasswordDataProvider()
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_MissingPasswordException
     */
    public function testConstructNewUsernameTokenWithMissingPassword($action, $field)
    {
        $this->_prepareFixture($action, $field);
        new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);
    }

    /**
     * Data provider for testConstructNewUsernameTokenWithMissingPassword
     *
     * @return array
     */
    public static function missingPasswordDataProvider()
    {
        return array(
            'Password is not set' => array('unset', 'password'),
            'Password is empty' => array('empty', 'password')
        );
    }

    /**
     * Test construction of object with missing nonce input.
     *
     * @dataProvider missingNonceDataProvider()
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_MissingNonceException
     */
    public function testConstructNewUsernameTokenWithMissingNonce($action, $field)
    {
        $this->_prepareFixture($action, $field);
        new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);
    }

    /**
     * Data provider for testConstructNewUsernameTokenWithMissingNonce
     *
     * @return array
     */
    public static function missingNonceDataProvider()
    {
        return array(
            'Nonce is not set' => array('unset', 'nonce'),
            'Nonce is empty' => array('empty', 'nonce')
        );
    }

    /**
     * Test construction of object with missing created timestamp input.
     *
     * @dataProvider missingCreatedDataProvider()
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_MissingCreatedException
     */
    public function testConstructNewUsernameTokenWithMissingCreated($action, $field)
    {
        $this->_prepareFixture($action, $field);
        new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);
    }

    /**
     * Data provider for testConstructNewUsernameTokenWithMissingCreated
     *
     * @return array
     */
    public static function missingCreatedDataProvider()
    {
        return array(
            'Created is not set' => array('unset', 'created'),
            'Created is empty' => array('empty', 'created')
        );
    }

    /**
     * Prepare usernameTokenOptions fixture for empty/is set tests.
     *
     * @param string $action to perform: unset or empty
     * @param string $field of the fixture to which perform the action
     */
    protected function _prepareFixture($action, $field)
    {
        if ($action == 'unset') {
            unset($this->_tokenOptionsFixture[$field]);
        } elseif ($action == 'empty') {
            $this->_tokenOptionsFixture[$field] = '';
        }
    }

    /**
     * Test positive token authentication.
     */
    public function testAuthenticate()
    {
        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);

        $userMock = $this->_getUserMock();
        $this->_objectFactoryMock
            ->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Webapi_Model_Acl_User')
            ->will($this->returnValue($userMock));

        $userMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $userMock->expects($this->once())
            ->method('getApiSecret')
            ->will($this->returnValue($this->_validFixturePassword));

        $user = $usernameToken->authenticate();
        $this->assertInstanceOf('Mage_Webapi_Model_Acl_User', $user);
    }

    /**
     * Test negative token authentication - username is invalid.
     *
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException
     */
    public function testAuthenticateWithInvalidUsername()
    {
        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);

        $userMock = $this->_getUserMock();
        $this->_objectFactoryMock
            ->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Webapi_Model_Acl_User')
            ->will($this->returnValue($userMock));

        $userMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(false));

        $usernameToken->authenticate();
    }

    /**
     * Test negative token authentication - password is invalid.
     *
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException
     */
    public function testAuthenticateWithInvalidPassword()
    {
        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($this->_tokenOptionsFixture);

        $userMock = $this->_getUserMock();
        $this->_objectFactoryMock
            ->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Webapi_Model_Acl_User')
            ->will($this->returnValue($userMock));

        $userMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $userMock->expects($this->once())
            ->method('getApiSecret')
            ->will($this->returnValue('INVALID_PASSWORD'));

        $usernameToken->authenticate();
    }

    /**
     * Prepare mock for user model.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getUserMock()
    {
        $userMock = $this->getMockBuilder('Mage_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getId', 'getApiSecret'))
            ->getMock();
        $userMock->expects($this->once())
            ->method('load')
            ->with($this->_tokenOptionsFixture['username'], 'api_key')
            ->will($this->returnSelf());

        return $userMock;
    }
}
