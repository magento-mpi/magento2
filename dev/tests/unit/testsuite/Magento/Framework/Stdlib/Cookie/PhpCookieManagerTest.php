<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Stdlib\Cookie;

/**
 * Mock global setcookie function
 *
 * @param string $name
 * @param string $value
 * @param int $expiry
 * @param string $path
 * @param string $domain
 * @param bool $secure
 * @param bool $httpOnly
 * @return bool
 */
function setcookie($name, $value, $expiry, $path, $domain, $secure, $httpOnly)
{
    if (PhpCookieManagerTest::DELETE_COOKIE_NAME == $name) {
        PhpCookieManagerTest::assertEquals('', $value);
        PhpCookieManagerTest::assertEquals($expiry, PhpCookieManager::EXPIRE_NOW_TIME);
        PhpCookieManagerTest::assertEquals($secure, PhpCookieManagerTest::COOKIE_SECURE);
        PhpCookieManagerTest::assertEquals($httpOnly, PhpCookieManagerTest::COOKIE_HTTP_ONLY);
    }

    PhpCookieManagerTest::assertNull($domain);
    PhpCookieManagerTest::assertNull($path);

    return $name == PhpCookieManagerTest::EXCEPTION_COOKIE_NAME ? false : true;
}

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Test PhpCookieManager
 *
 */
class PhpCookieManagerTest extends \PHPUnit_Framework_TestCase
{
    const COOKIE_NAME = "cookie_name";
    const SENSITIVE_COOKIE_NAME = "sensitive_cookie_name";
    const DELETE_COOKIE_NAME = "delete_cookie_name";
    const EXCEPTION_COOKIE_NAME = "exception_cookie_name";
    const COOKIE_VALUE = "cookie_value";
    const COOKIE_SECURE = false;
    const COOKIE_HTTP_ONLY = false;

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

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->cookieManager = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PhpCookieManager');
    }

    public function testGetCookie()
    {
        $_COOKIE[self::COOKIE_NAME] = self::COOKIE_VALUE;
        $defaultCookieValue = 'default';
        $this->assertEquals(
            $defaultCookieValue,
            $this->cookieManager->getCookie('unknown cookieName', $defaultCookieValue)
        );
        $this->assertEquals(
            self::COOKIE_VALUE,
            $this->cookieManager->getCookie(self::COOKIE_NAME, $defaultCookieValue)
        );
        $this->assertEquals($defaultCookieValue, $this->cookieManager->getCookie(null, $defaultCookieValue));
        $this->assertNull($this->cookieManager->getCookie(null));
    }

    public function testDeleteCookie()
    {
        $_COOKIE[self::DELETE_COOKIE_NAME] = self::COOKIE_VALUE;

        /** @var \Magento\Framework\Stdlib\Cookie\CookieMetaData $cookieMetaData */
        $cookieMetaData = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\CookieMetaData');
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\Cookie\PhpCookieManager $mockCookieManager */
        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['setPublicCookie', 'setSensitiveCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('setPublicCookie');
        $mockCookieManager->expects($this->never())->method('setSensitiveCookie');
        $this->assertEquals(self::COOKIE_VALUE, $this->cookieManager->getCookie(self::DELETE_COOKIE_NAME));
        $mockCookieManager->deleteCookie(self::DELETE_COOKIE_NAME, $cookieMetaData);
        $this->assertNull($this->cookieManager->getCookie(self::DELETE_COOKIE_NAME));
    }

    public function testDeleteCookieWithFailureToSendException()
    {
        $_COOKIE[self::EXCEPTION_COOKIE_NAME] = self::COOKIE_VALUE;

        /** @var \Magento\Framework\Stdlib\Cookie\CookieMetaData $cookieMetaData */
        $cookieMetaData = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\CookieMetaData');
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\Cookie\PhpCookieManager $mockCookieManager */
        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['setPublicCookie', 'setSensitiveCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('setPublicCookie');
        $mockCookieManager->expects($this->never())->method('setSensitiveCookie');
        try {
            $mockCookieManager->deleteCookie(self::EXCEPTION_COOKIE_NAME, $cookieMetaData);
            $this->fail('Expected exception not thrown.');
        } catch (FailureToSendException $fse) {
            $this->assertSame(
                'Unable to delete the cookie name with cookieName = exception_cookie_name',
                $fse->getMessage()
            );
        }
    }
}
