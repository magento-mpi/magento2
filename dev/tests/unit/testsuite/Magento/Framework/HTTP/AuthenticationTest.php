<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\HTTP;

class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $server
     * @param string $expectedLogin
     * @param string $expectedPass
     * @dataProvider getCredentialsDataProvider
     */
    public function testGetCredentials($server, $expectedLogin, $expectedPass)
    {
        $request = $this->getMock('\Magento\Framework\App\Request\Http', array(), array(), '', false);
        $request->expects($this->once())->method('getServer')->will($this->returnValue($server));
        $response = $this->getMock('\Magento\Framework\App\Response\Http', array(), array(), '', false);
        $authentication = new \Magento\Framework\HTTP\Authentication($request, $response);
        $this->assertSame(array($expectedLogin, $expectedPass), $authentication->getCredentials());
    }

    /**
     * @return array
     */
    public function getCredentialsDataProvider()
    {
        $login = 'login';
        $password = 'password';
        $header = 'Basic bG9naW46cGFzc3dvcmQ=';

        $anotherLogin = 'another_login';
        $anotherPassword = 'another_password';
        $anotherHeader = 'Basic YW5vdGhlcl9sb2dpbjphbm90aGVyX3Bhc3N3b3Jk';

        return array(
            array(array(), '', ''),
            array(array('REDIRECT_HTTP_AUTHORIZATION' => $header), $login, $password),
            array(array('HTTP_AUTHORIZATION' => $header), $login, $password),
            array(array('Authorization' => $header), $login, $password),
            array(
                array(
                    'REDIRECT_HTTP_AUTHORIZATION' => $header,
                    'PHP_AUTH_USER' => $anotherLogin,
                    'PHP_AUTH_PW' => $anotherPassword
                ),
                $anotherLogin,
                $anotherPassword
            ),
            array(
                array(
                    'REDIRECT_HTTP_AUTHORIZATION' => $header,
                    'PHP_AUTH_USER' => $anotherLogin,
                    'PHP_AUTH_PW' => $anotherPassword
                ),
                $anotherLogin,
                $anotherPassword
            ),
            array(
                array('REDIRECT_HTTP_AUTHORIZATION' => $header, 'HTTP_AUTHORIZATION' => $anotherHeader),
                $anotherLogin,
                $anotherPassword
            )
        );
    }

    public function testSetAuthenticationFailed()
    {
        $request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $cookieManagerMock = $this->getMock('Magento\Framework\Stdlib\CookieManager', [], [], '', false);
        $contextMock = $this->getMock('Magento\Framework\App\Http\Context', [], [], '', false);
        $cookieFactoryMock = $this->getMock('Magento\Framework\Stdlib\Cookie\CookieMetadataFactory', [], [], '', false);
        $response = new \Magento\Framework\App\Response\Http($cookieManagerMock, $cookieFactoryMock, $contextMock);
        $authentication = new \Magento\Framework\HTTP\Authentication($request, $response);
        $realm = uniqid();
        $response->headersSentThrowsException = false;
        $authentication->setAuthenticationFailed($realm);
        $headers = $response->getHeaders();
        $this->assertArrayHasKey(0, $headers);
        $this->assertEquals('401 Unauthorized', $headers[0]['value']);
        $this->assertArrayHasKey(1, $headers);
        $this->assertContains('realm="' . $realm . '"', $headers[1]['value']);
        $this->assertContains('401', $response->getBody());
    }
}
