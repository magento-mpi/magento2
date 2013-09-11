<?php
/**
 * SOAP web API authentication model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_Soap_AuthenticationTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenFactoryMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_roleLocatorMock;

    /** @var \Magento\Webapi\Controller\Dispatcher\Soap\Authentication */
    protected $_soapAuthentication;

    /** @var stdClass */
    protected $_usernameToken;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenMock;

    protected function setUp()
    {
        $this->_usernameToken = new stdClass();
        /** Prepare mocks for SUT constructor. */
        $this->_usernameToken->Username = 'userName';
        $this->_usernameToken->Password = 'password';
        $this->_usernameToken->Created = '2012-12-12';
        $this->_usernameToken->Nonce = 'Nonce';

        $this->_tokenFactoryMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Security\UsernameToken\Factory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_tokenMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Security\UsernameToken')
            ->disableOriginalConstructor()
            ->setMethods(array('authenticate'))
            ->getMock();
        $this->_tokenFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_tokenMock));
        $this->_roleLocatorMock = $this->getMockBuilder('Magento\Webapi\Model\Authorization\RoleLocator')
            ->setMethods(array('setRoleId'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_soapAuthentication = new \Magento\Webapi\Controller\Dispatcher\Soap\Authentication(
            $this->_tokenFactoryMock,
            $this->_roleLocatorMock
        );
        parent::setUp();
    }

    public function testAuthenticate()
    {
        /** Prepare mocks for SUT constructor. */
        $user = $this->getMockBuilder('Magento\Webapi\Model\Acl\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getRoleId'))
            ->getMock();
        $roleId = 1;
        $user->expects($this->once())->method('getRoleId')->will($this->returnValue($roleId));
        $this->_tokenMock->expects($this->once())
            ->method('authenticate')
            ->with(
                $this->_usernameToken->Username,
                $this->_usernameToken->Password,
                $this->_usernameToken->Created,
                $this->_usernameToken->Nonce
            )->will($this->returnValue($user));
        $this->_tokenFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_tokenMock));
        $this->_roleLocatorMock->expects($this->once())->method('setRoleId')->with($roleId);
        /** Execute SUT. */
        $this->_soapAuthentication->authenticate($this->_usernameToken);
    }

    /**
     * @dataProvider authenticateExceptionProvider
     */
    public function testAuthenticateWithException($exception, $exceptionMessage)
    {
        /** Prepare mocks for SUT constructor. */
        $this->_tokenMock
            ->expects($this->once())
            ->method('authenticate')
            ->with(
                $this->_usernameToken->Username,
                $this->_usernameToken->Password,
                $this->_usernameToken->Created,
                $this->_usernameToken->Nonce
            )->will($this->throwException($exception));
        $this->setExpectedException(
            '\Magento\Webapi\Exception',
            $exceptionMessage,
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapAuthentication->authenticate($this->_usernameToken);
    }

    /**
     * Exception data provider for authenticate() method.
     *
     * @return array
     */
    public function authenticateExceptionProvider()
    {
        return array(
            'testAuthenticateUsernameTokenInvalidCredentialException.' => array(
                new \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidCredentialException(),
                'Invalid Username or Password.',
            ),
            'testAuthenticateUsernameTokenNonceUsedException.' => array(
                new \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceUsedException(),
                'WS-Security UsernameToken Nonce is already used.',
            ),
            'testAuthenticateUsernameTokenTimestampRefusedException.' => array(
                new \Magento\Webapi\Model\Soap\Security\UsernameToken\TimestampRefusedException(),
                'WS-Security UsernameToken Created timestamp is refused.',
            ),
            'testAuthenticateUsernameTokenInvalidDateException.' => array(
                new \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidDateException(),
                'Invalid UsernameToken Created date.',
            ),
        );
    }
}
