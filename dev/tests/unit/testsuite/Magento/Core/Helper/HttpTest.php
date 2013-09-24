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

namespace Magento\Core\Helper;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\Http
     */
    protected $_object = null;

    protected function setUp()
    {
        $this->_object = new \Magento\Core\Helper\Http(
            $this->getMock('Magento\Core\Helper\String', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Model\Config', array(), array(), '', false, false)
        );
    }

    /**
     * @param array $server
     * @param string $expectedLogin
     * @param string $expectedPass
     * @dataProvider getHttpAuthCredentialsDataProvider
     */
    public function testGetHttpAuthCredentials($server, $expectedLogin, $expectedPass)
    {
        $request = $this->getMock('Zend_Controller_Request_Http', array('getServer'));
        $request->expects($this->once())->method('getServer')->will($this->returnValue($server));
        $this->assertSame(array($expectedLogin, $expectedPass), $this->_object->getHttpAuthCredentials($request));
    }

    /**
     * @return array
     */
    public function getHttpAuthCredentialsDataProvider()
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

    public function testFailHttpAuthentication()
    {
        $response = new \Zend_Controller_Response_Http;
        $realm = uniqid();
        $response->headersSentThrowsException = false;
        $this->_object->failHttpAuthentication($response, $realm);
        $headers = $response->getHeaders();
        $this->assertArrayHasKey(0, $headers);
        $this->assertEquals('401 Unauthorized', $headers[0]['value']);
        $this->assertArrayHasKey(1, $headers);
        $this->assertContains('realm="' . $realm .'"', $headers[1]['value']);
        $this->assertContains('401', $response->getBody());
    }
}
