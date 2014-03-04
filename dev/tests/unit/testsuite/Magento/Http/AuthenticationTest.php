<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Http;

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
        $request = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $request->expects($this->once())->method('getServer')->will($this->returnValue($server));
        $response = $this->getMock('\Magento\App\Response\Http', array(), array(), '', false);
        $authentication = new \Magento\HTTP\Authentication($request, $response);
        $this->assertSame(array($expectedLogin, $expectedPass), $authentication->getCredentials());
    }

    /**
     * @return array
     */
    public function getCredentialsDataProvider()
    {
        $login    = 'login';
        $password = 'password';
        $header   = 'Basic bG9naW46cGFzc3dvcmQ=';

        $anotherLogin    = 'another_login';
        $anotherPassword = 'another_password';
        $anotherHeader   = 'Basic YW5vdGhlcl9sb2dpbjphbm90aGVyX3Bhc3N3b3Jk';

        return array(
            array(array(), '', ''),
            array(array('REDIRECT_HTTP_AUTHORIZATION' => $header), $login, $password),
            array(array('HTTP_AUTHORIZATION' => $header), $login, $password),
            array(array('Authorization' => $header), $login, $password),
            array(array(
                    'REDIRECT_HTTP_AUTHORIZATION' => $header,
                    'PHP_AUTH_USER' => $anotherLogin,
                    'PHP_AUTH_PW' => $anotherPassword
                ), $anotherLogin, $anotherPassword
            ),
            array(array(
                    'REDIRECT_HTTP_AUTHORIZATION' => $header,
                    'PHP_AUTH_USER' => $anotherLogin,
                    'PHP_AUTH_PW' => $anotherPassword
                ), $anotherLogin, $anotherPassword
            ),
            array(
                array('REDIRECT_HTTP_AUTHORIZATION' => $header, 'HTTP_AUTHORIZATION' => $anotherHeader,),
                $anotherLogin, $anotherPassword
            ),
        );
    }

    public function testSetAuthenticationFailed()
    {
        $request = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $cookieMock = $this->getMock('Magento\Stdlib\Cookie', array(), array(), '', false);
        $contextMock = $this->getMock('Magento\App\Http\Context', array(), array(), '', false);
        $response = new \Magento\App\Response\Http($cookieMock, $contextMock);
        $authentication = new \Magento\HTTP\Authentication($request, $response);
        $realm = uniqid();
        $response->headersSentThrowsException = false;
        $authentication->setAuthenticationFailed($realm);
        $headers = $response->getHeaders();
        $this->assertArrayHasKey(0, $headers);
        $this->assertEquals('401 Unauthorized', $headers[0]['value']);
        $this->assertArrayHasKey(1, $headers);
        $this->assertContains('realm="' . $realm .'"', $headers[1]['value']);
        $this->assertContains('401', $response->getBody());
    }
}
