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
    if ($name == PhpCookieManagerTest::EXCEPTION_COOKIE_NAME) {
        return false;
    } elseif (PhpCookieManagerTest::DELETE_COOKIE_NAME == $name) {
        PhpCookieManagerTest::assertEquals('', $value);
        PhpCookieManagerTest::assertEquals($expiry, PhpCookieManager::EXPIRE_NOW_TIME);
        PhpCookieManagerTest::assertFalse($secure);
        PhpCookieManagerTest::assertFalse($httpOnly);
        PhpCookieManagerTest::assertEquals('', $domain);
        PhpCookieManagerTest::assertEquals('', $path);
    } elseif (PhpCookieManagerTest::SENSITIVE_COOKIE_NAME_NO_METADATA == $name) {
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_VALUE, $value);
        PhpCookieManagerTest::assertEquals(PhpCookieManager::EXPIRE_AT_END_OF_SESSION_TIME, $expiry);
        PhpCookieManagerTest::assertTrue($secure);
        PhpCookieManagerTest::assertTrue($httpOnly);
        PhpCookieManagerTest::assertEquals('', $domain);
        PhpCookieManagerTest::assertEquals('', $path);
    } elseif (PhpCookieManagerTest::SENSITIVE_COOKIE_NAME_NO_DOMAIN_NO_PATH == $name) {
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_VALUE, $value);
        PhpCookieManagerTest::assertEquals(PhpCookieManager::EXPIRE_AT_END_OF_SESSION_TIME, $expiry);
        PhpCookieManagerTest::assertTrue($secure);
        PhpCookieManagerTest::assertTrue($httpOnly);
        PhpCookieManagerTest::assertEquals('', $domain);
        PhpCookieManagerTest::assertEquals('', $path);
    } elseif (PhpCookieManagerTest::SENSITIVE_COOKIE_NAME_WITH_DOMAIN_AND_PATH == $name) {
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_VALUE, $value);
        PhpCookieManagerTest::assertEquals(PhpCookieManager::EXPIRE_AT_END_OF_SESSION_TIME, $expiry);
        PhpCookieManagerTest::assertTrue($secure);
        PhpCookieManagerTest::assertTrue($httpOnly);
        PhpCookieManagerTest::assertEquals('magento.url', $domain);
        PhpCookieManagerTest::assertEquals('/backend', $path);
    } elseif (PhpCookieManagerTest::PUBLIC_COOKIE_NAME_NO_METADATA == $name) {
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_VALUE, $value);
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_EXPIRE_END_OF_SESSION, $expiry);
        PhpCookieManagerTest::assertFalse($secure);
        PhpCookieManagerTest::assertFalse($httpOnly);
        PhpCookieManagerTest::assertEquals('', $domain);
        PhpCookieManagerTest::assertEquals('', $path);
    } elseif (PhpCookieManagerTest::PUBLIC_COOKIE_NAME_DEFAULT_VALUES == $name) {
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_VALUE, $value);
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_EXPIRE_END_OF_SESSION, $expiry);
        PhpCookieManagerTest::assertFalse($secure);
        PhpCookieManagerTest::assertFalse($httpOnly);
        PhpCookieManagerTest::assertEquals('', $domain);
        PhpCookieManagerTest::assertEquals('', $path);
    } elseif (PhpCookieManagerTest::PUBLIC_COOKIE_NAME_SOME_FIELDS_SET == $name) {
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_VALUE, $value);
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_EXPIRE_END_OF_SESSION, $expiry);
        PhpCookieManagerTest::assertFalse($secure);
        PhpCookieManagerTest::assertTrue($httpOnly);
        PhpCookieManagerTest::assertEquals('magento.url', $domain);
        PhpCookieManagerTest::assertEquals('/backend', $path);
    } elseif (PhpCookieManagerTest::MAX_COOKIE_SIZE_TEST_NAME == $name) {
        PhpCookieManagerTest::assertEquals(PhpCookieManagerTest::COOKIE_VALUE, $value);
        PhpCookieManagerTest::assertFalse($secure);
        PhpCookieManagerTest::assertFalse($httpOnly);
        PhpCookieManagerTest::assertEquals('', $domain);
        PhpCookieManagerTest::assertEquals('', $path);
    } else {
        PhpCookieManagerTest::fail('Non-tested case in mock setcookie()');
    }

    return true;
}

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Test PhpCookieManager
 *
 */
class PhpCookieManagerTest extends \PHPUnit_Framework_TestCase
{
    const COOKIE_NAME = 'cookie_name';
    const SENSITIVE_COOKIE_NAME_NO_METADATA = 'sensitive_cookie_name_no_metadata';
    const SENSITIVE_COOKIE_NAME_NO_DOMAIN_NO_PATH = 'sensitive_cookie_name_no_domain_no_path';
    const SENSITIVE_COOKIE_NAME_WITH_DOMAIN_AND_PATH = 'sensitive_cookie_name_with_domain_and_path';
    const PUBLIC_COOKIE_NAME_NO_METADATA = 'public_cookie_name_no_metadata';
    const PUBLIC_COOKIE_NAME_DEFAULT_VALUES = 'public_cookie_name_default_values';
    const PUBLIC_COOKIE_NAME_SOME_FIELDS_SET = 'public_cookie_name_some_fields_set';
    const MAX_COOKIE_SIZE_TEST_NAME = 'max_cookie_size_test_name';
    const MAX_NUM_COOKIE_TEST_NAME = 'max_num_cookie_test_name';
    const DELETE_COOKIE_NAME = 'delete_cookie_name';
    const EXCEPTION_COOKIE_NAME = 'exception_cookie_name';
    const COOKIE_VALUE = 'cookie_value';
    const COOKIE_SECURE = true;
    const COOKIE_NOT_SECURE = false;
    const COOKIE_HTTP_ONLY = true;
    const COOKIE_NOT_HTTP_ONLY = false;
    const COOKIE_EXPIRE_END_OF_SESSION = 0;
    const MAX_NUM_COOKIES = 20;
    const MAX_COOKIE_SIZE = 4096;

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

    public function testDeleteCookieWithNullCookieMetadata()
    {
        $_COOKIE[self::DELETE_COOKIE_NAME] = self::COOKIE_VALUE;

        /** @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata $publicCookieMetadata */
        $publicCookieMetadata = $this->objectManager->getObject(
            'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata'
        );
        $scopeMock= $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\CookieScope')
                 ->setMethods(['getPublicCookieMetadata'])
            ->disableOriginalConstructor()->getMock();
        $scopeMock->expects($this->once())->method('getPublicCookieMetadata')->withAnyParameters()->will(
            $this->returnValue($publicCookieMetadata)
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\Cookie\PhpCookieManager $mockCookieManager */
        $mockCookieManager = $this->objectManager->getObject(
            'Magento\Framework\Stdlib\Cookie\PhpCookieManager',
            ['scope' => $scopeMock]
        );
        $this->assertEquals(self::COOKIE_VALUE, $this->cookieManager->getCookie(self::DELETE_COOKIE_NAME));
        $mockCookieManager->deleteCookie(self::DELETE_COOKIE_NAME);
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
                'Unable to delete the cookie with cookieName = exception_cookie_name',
                $fse->getMessage()
            );
        }
    }

    public function testSetSensitiveCookieNoMetadata()
    {
        /** @var SensitiveCookieMetadata $sensitiveCookieMetadata */
        $sensitiveCookieMetadata = $this->objectManager
            ->getObject('Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata');

        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['deleteCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('deleteCookie');

        $mockCookieManager->setSensitiveCookie(
            PhpCookieManagerTest::SENSITIVE_COOKIE_NAME_NO_METADATA,
            'cookie_value',
            $sensitiveCookieMetadata
        );
    }

    public function testSetSensitiveCookieNullDomainAndPath()
    {
        /** @var SensitiveCookieMetadata $sensitiveCookieMetadata */
        $sensitiveCookieMetadata = $this->objectManager
            ->getObject('Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata',
                [
                    'metadata' => [
                        'domain' => null,
                        'path'   => null,
                    ],
                ]
            );

        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['deleteCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('deleteCookie');

        $mockCookieManager->setSensitiveCookie(
            PhpCookieManagerTest::SENSITIVE_COOKIE_NAME_NO_DOMAIN_NO_PATH,
            'cookie_value',
            $sensitiveCookieMetadata
        );
    }

    public function testSetSensitiveCookieWithPathAndDomain()
    {
        /** @var SensitiveCookieMetadata $sensitiveCookieMetadata */
        $sensitiveCookieMetadata = $this->objectManager
            ->getObject('Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata',
                [
                    'metadata' => [
                        'domain' => 'magento.url',
                        'path'   => '/backend',
                    ],
                ]
            );

        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['deleteCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('deleteCookie');

        $mockCookieManager->setSensitiveCookie(
            PhpCookieManagerTest::SENSITIVE_COOKIE_NAME_WITH_DOMAIN_AND_PATH,
            'cookie_value',
            $sensitiveCookieMetadata
        );
    }

    public function testSetPublicCookieNoMetadata()
    {
        /** @var PublicCookieMetadata $publicCookieMetadata */
        $publicCookieMetadata = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PublicCookieMetadata');

        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['deleteCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('deleteCookie');

        $mockCookieManager->setPublicCookie(
            PhpCookieManagerTest::PUBLIC_COOKIE_NAME_NO_METADATA,
            'cookie_value',
            $publicCookieMetadata
        );
    }

    public function testSetPublicCookieDefaultValues()
    {
        /** @var PublicCookieMetadata $publicCookieMetadata */
        $publicCookieMetadata = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
            [
                'metadata' => [
                    'domain' => null,
                    'path'   => null,
                    'secure' => false,
                    'http_only' => false,
                ],
            ]
        );

        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['deleteCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('deleteCookie');

        $mockCookieManager->setPublicCookie(
            PhpCookieManagerTest::PUBLIC_COOKIE_NAME_DEFAULT_VALUES,
            'cookie_value',
            $publicCookieMetadata
        );
    }

    public function testSetPublicCookieSomeFieldsSet()
    {
        /** @var PublicCookieMetadata $publicCookieMetadata */
        $publicCookieMetadata = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
            [
                'metadata' => [
                    'domain' => 'magento.url',
                    'path'   => '/backend',
                    'http_only' => true,
                ],
            ]
        );

        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['deleteCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('deleteCookie');

        $mockCookieManager->setPublicCookie(
            PhpCookieManagerTest::PUBLIC_COOKIE_NAME_SOME_FIELDS_SET,
            'cookie_value',
            $publicCookieMetadata
        );
    }

    public function testSetCookieSizeTooLarge()
    {
        /** @var PublicCookieMetadata $publicCookieMetadata */
        $publicCookieMetadata = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
            [
                'metadata' => [
                    'domain' => null,
                    'path'   => null,
                    'secure' => false,
                    'http_only' => false,
                    'duration' => 3600,
                ],
            ]
        );

        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['deleteCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('deleteCookie');

        $cookieValue = '';
        for ($i = 0; $i < PhpCookieManagerTest::MAX_COOKIE_SIZE + 1; $i++) {
            $cookieValue = $cookieValue . 'a';
        }

        try {
            $mockCookieManager->setPublicCookie(
                PhpCookieManagerTest::MAX_COOKIE_SIZE_TEST_NAME,
                $cookieValue,
                $publicCookieMetadata
            );
            $this->fail('Failed to throw exception of excess cookie size.');
        } catch (CookieSizeLimitReachedException $e) {
            $this->assertEquals(
                "Unable to send the cookie. Size of 'max_cookie_size_test_name' is 4123 bytes.",
                $e->getMessage()
            );
        }
    }

    public function testSetTooManyCookies()
    {
        /** @var PublicCookieMetadata $publicCookieMetadata */
        $publicCookieMetadata = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PublicCookieMetadata');

        $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
            ->setMethods(['deleteCookie'])
            ->disableOriginalConstructor()->getMock();
        $mockCookieManager->expects($this->never())->method('deleteCookie');

        $cookieValue = 'some_value';

        // Set PhpCookieManagerTest::MAX_NUM_COOKIES number of cookies in superglobal $_COOKIE.
        for ($i = count($_COOKIE); $i < PhpCookieManagerTest::MAX_NUM_COOKIES; $i++) {
            $_COOKIE['test_cookie_' . $i] = 'some_value';
        }

        try {
            $mockCookieManager->setPublicCookie(
                PhpCookieManagerTest::MAX_COOKIE_SIZE_TEST_NAME,
                $cookieValue,
                $publicCookieMetadata
            );
            $this->fail('Failed to throw exception of too many cookies.');
        } catch (CookieSizeLimitReachedException $e) {
            $this->assertEquals(
                'Unable to send the cookie. Maximum number of cookies would be exceeded.',
                $e->getMessage()
            );
        }
    }
}
