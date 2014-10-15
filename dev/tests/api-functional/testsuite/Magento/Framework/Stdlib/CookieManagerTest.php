<?php
namespace Magento\Framework\Stdlib;

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\Webapi\Curl;

/**
 * End to end test of the Cookie Manager, using curl.
 *
 * Uses controllers in TestModule1 to set and delete cookies and verify 'Set-Cookie' headers that come back.
 */
class CookieManagerTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{

    private $cookieTesterUrl = 'testmoduleone/CookieTester';

    /** @var Curl */
    protected $curlClient;

    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->config = $objectManager->get('Magento\Webapi\Model\Config');
        $this->curlClient = $objectManager->get('Magento\TestFramework\TestCase\Webapi\Curl');
    }

    /**
     * Set a sensitive Cookie and delete it.
     *
     */
    public function testSensitiveCookie()
    {
        $url = $this->cookieTesterUrl . '/SetSensitiveCookie';
        $cookieData =
            [
                'cookie_name' => 'test-sensitive-cookie',
                'cookie_value' => 'test-sensitive-cookie-value',
            ];
        $response = $this->curlClient->get($url, $cookieData);

        // secure and httponly attributes should be set
        $expectedCookie = [
                'name' => 'test-sensitive-cookie',
                'value' => 'test-sensitive-cookie-value',
                'httponly' => 'true',

        ];

        $this->assertContains($expectedCookie, $response['cookies']);
    }

    /**
     * Set a public cookie
     *
     */
    public function testPublicCookieNameValue()
    {
        $url = $this->cookieTesterUrl . '/SetPublicCookie';
        $cookieParams =
            [
                'cookie_name' => 'test-cookie',
                'cookie_value' => 'test-cookie-value',
            ];

        $response = $this->curlClient->get($url, $cookieParams);

        // Just name and value set
        $expectedCookie = [
            'name' => 'test-cookie',
            'value' => 'test-cookie-value'
        ];

        $this->assertContains($expectedCookie, $response['cookies']);
    }

    /**
     * Set a public cookie
     *
     */
    public function testPublicCookieAll()
    {
        $this->markTestIncomplete('MAGETWO-29179');
        $url = $this->cookieTesterUrl . '/SetPublicCookie';
        $cookieParams =
            [
                'cookie_name' => 'test-cookie',
                'cookie_value' => 'test-cookie-value',
                'cookie_domain' => 'www.example.com',
                'cookie_path' => '/test/path',
                'cookie_httponly' => 'true',
                'cookie_secure' => 'true',
                'cookie_duration' => '600',
            ];

        $response = $this->curlClient->get($url, $cookieParams);

        // All values, masking expires
        $expectedCookie = [
            'name' => 'test-cookie',
            'value' => 'test-cookie-value',
            'domain' => 'www.example.com',
            'httponly' => 'true',
            'path' => '/test/path',
            'secure' => 'true',
            'expires' => 'set'
        ];

        $this->assertContains($expectedCookie, $this->maskExpires($response['cookies']));
    }

    /**
     * Delete a cookie
     *
     */
    public function testDeleteCookie()
    {
        $this->markTestIncomplete('MAGETWO-29179');
        $url = $this->cookieTesterUrl . '/DeleteCookie';
        $cookieParams =
            [
                'cookie_name' => 'test-cookie',
                'cookie_value' => 'test-cookie-value',
            ];

        $response = $this->curlClient->get(
            $url,
            $cookieParams,
            ['Cookie: test-cookie=test-cookie-value; anothertestcookie=anothertestcookievalue']
        );

        $expectedCookie = [
            'name' => 'test-cookie',
            'value' => 'deleted',
            'expires' => 'Thu, 01-Jan-1970 00:00:01 GMT'
        ];

        $this->assertContains($expectedCookie, $response['cookies']);
    }

    /**
     * Masks any 'expires' value in Set-Cookie array with the value 'set'.
     *
     * @param array $cookies input array of cookies
     * @return array input array with any 'expires' value masked.
     */
    private function maskExpires($cookies)
    {
        foreach ($cookies as $cookieIndex => $cookie) {
            if (isset($cookie['expires'])) {
                $cookies[$cookieIndex]['expires'] = 'set';
            }
        }
        return $cookies;
    }
}
