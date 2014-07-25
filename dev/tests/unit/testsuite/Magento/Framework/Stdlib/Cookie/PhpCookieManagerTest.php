<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace {
    // Setting $mockSetcookie to true will force all calls to
    // setcookie() to use our mocked method. To use
    // the PHP global function setcookie(), set $mockSetCookie to false.
    $mockSetcookie = true;

    $superglobalCookieArray = [];
}

namespace Magento\Framework\Stdlib\Cookie {

    use Magento\TestFramework\Helper\ObjectManager;

    function setcookie(
        $name,
        $value = null,
        $expire = 0,
        $path = null,
        $domain = null,
        $secure = false,
        $httponly = false
    ) {
        global $superglobalCookieArray;
        global $mockSetcookie;
        if (isset($mockSetcookie) && $mockSetcookie === true) {
            if (trim($value) == false) {
                if (isset($superglobalCookieArray[$name])) {
                    unset($superglobalCookieArray[$name]);
                }
            }

            if ($expire < time() && $expire != 0) {
                if (isset($superglobalCookieArray[$name])) {
                    unset($superglobalCookieArray[$name]);
                }
            }

            $superglobalCookieArray[$name] = [
                'name' => $name,
                'value' => $value,
                'expire' => $expire,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httponly,
            ];

        } else {
            return call_user_func_array('\setcookie', func_get_args());
        }
    }

    function simulateBrowserClose()
    {
        global $superglobalCookieArray;

        foreach ($superglobalCookieArray as $cookie) {
            if ($cookie['expire'] < time()) {
                unset($superglobalCookieArray[$cookie['name']]);
            }
        }
    }

    function deleteAllCookies()
    {
        global $superglobalCookieArray;
        $superglobalCookieArray = [];
    }

    function getcookie($name)
    {
        global $superglobalCookieArray;

        if (isset($superglobalCookieArray[$name])) {
            return $superglobalCookieArray[$name]['value'];
        } else {
            return null;
        }
    }

    /**
     * Test PhpCookieManager
     *
     */
    class PhpCookieManagerTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var ObjectManager
         */
        protected $objectManager;

        /**
         * Cookie Manager
         *
         * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
         */
        protected $cookieManager;

        /**
         * Mock superglobal cookie array
         *
         * @var array
         */
        protected $mockCookieArray;

        protected function setUp()
        {
            $this->objectManager = new ObjectManager($this);
            $this->cookieManager = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PhpCookieManager');
            deleteAllCookies();
        }

        public function testGetCookie()
        {
            $cookieName           = 'cookie name';
            $cookieValue          = 'cookie value';
            $defaultCookieValue   = 'default';
            $_COOKIE[$cookieName] = $cookieValue;
            $this->assertEquals(
                $defaultCookieValue,
                $this->cookieManager->getCookie('unknown cookieName', $defaultCookieValue)
            );
            $this->assertEquals($cookieValue, $this->cookieManager->getCookie($cookieName, $defaultCookieValue));
            $this->assertEquals($defaultCookieValue, $this->cookieManager->getCookie(null, $defaultCookieValue));
            $this->assertNull($this->cookieManager->getCookie(null));
        }

        public function testDeleteCookie()
        {
            $cookieName           = 'cookie name';
            $cookieValue          = 'cookie value';
            $_COOKIE[$cookieName] = $cookieValue;

            /** @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetaData $publicCookieMetaData */
            $publicCookieMetaData = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PublicCookieMetaData');
            /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\Cookie\PhpCookieManager $mockCookieManager */
            $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
                ->setMethods(['setPublicCookie'])
                ->disableOriginalConstructor()->getMock();
            $mockCookieManager->expects($this->once())->method('setPublicCookie')->with(
                $cookieName,
                false,
                $publicCookieMetaData
            );
            $this->assertEquals($cookieValue, $this->cookieManager->getCookie($cookieName));
            $mockCookieManager->deleteCookie($cookieName, $publicCookieMetaData);
            $this->assertNull($this->cookieManager->getCookie($cookieName));
        }
    }
}
