<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// @codingStandardsIgnoreStart
namespace {
    $mockTranslateSetCookie = false;
}

namespace Magento\Framework\Stdlib\Cookie {
    // @codingStandardsIgnoreEnd
    use Magento\Framework\Exception\InputException;

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
        global $mockTranslateSetCookie;

        if (isset($mockTranslateSetCookie) && $mockTranslateSetCookie === true) {
            PhpCookieManagerTest::$isSetCookieInvoked = true;
            return PhpCookieManagerTest::assertCookie($name, $value, $expiry, $path, $domain, $secure, $httpOnly);
        } else {

            return call_user_func_array('\setcookie', func_get_args());
        }
    }

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
        const DELETE_COOKIE_NAME_NO_METADATA = 'delete_cookie_name_no_metadata';
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
         * @var \Magento\TestFramework\Helper\ObjectManager
         */
        protected $objectManager;

        /**
         * Cookie Manager
         *
         * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
         */
        protected $cookieManager;
        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\Cookie\CookieScope
         */
        protected $scopeMock;

        /**
         * @var bool
         */
        public static $isSetCookieInvoked;

        protected function setUp()
        {
            global $mockTranslateSetCookie;
            $mockTranslateSetCookie = true;
            self::$isSetCookieInvoked = false;
            $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
            $this->scopeMock = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\CookieScope')
                ->setMethods(['getPublicCookieMetadata', 'getCookieMetadata', 'getSensitiveCookieMetadata'])
                ->disableOriginalConstructor()
                ->getMock();
            $this->cookieManager = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PhpCookieManager',
                ['scope' => $this->scopeMock]
            );
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
            self::$isSetCookieInvoked = false;
            $_COOKIE[self::DELETE_COOKIE_NAME] = self::COOKIE_VALUE;

            /** @var \Magento\Framework\Stdlib\Cookie\CookieMetaData $cookieMetadata */
            $cookieMetadata = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\CookieMetaData',
                [
                    'metadata' => [
                        'domain' => 'magento.url',
                        'path' => '/backend',
                    ]
                ]
            );

            $this->scopeMock->expects($this->once())
                ->method('getCookieMetadata')
                ->with($cookieMetadata)
                ->will(
                    $this->returnValue($cookieMetadata)
                );
            // $this->invokeCookieManager();
            $this->assertEquals(self::COOKIE_VALUE, $this->cookieManager->getCookie(self::DELETE_COOKIE_NAME));
            $this->cookieManager->deleteCookie(self::DELETE_COOKIE_NAME, $cookieMetadata);
            $this->assertNull($this->cookieManager->getCookie(self::DELETE_COOKIE_NAME));
            $this->assertTrue(self::$isSetCookieInvoked);
        }

        public function testDeleteCookieWithNoCookieMetadata()
        {
            self::$isSetCookieInvoked = false;
            $_COOKIE[self::DELETE_COOKIE_NAME_NO_METADATA] = self::COOKIE_VALUE;

            $cookieMetadata = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\CookieMetaData');
            $this->scopeMock->expects($this->once())
                ->method('getCookieMetadata')
                ->with()
                ->will(
                    $this->returnValue($cookieMetadata)
                );
            //$this->invokeCookieManager();
            $this->assertEquals(
                self::COOKIE_VALUE,
                $this->cookieManager->getCookie(self::DELETE_COOKIE_NAME_NO_METADATA)
            );
            $this->cookieManager->deleteCookie(self::DELETE_COOKIE_NAME_NO_METADATA);
            $this->assertNull($this->cookieManager->getCookie(self::DELETE_COOKIE_NAME_NO_METADATA));
            $this->assertTrue(self::$isSetCookieInvoked);
        }

        public function testDeleteCookieWithFailureToSendException()
        {
            self::$isSetCookieInvoked = false;
            $_COOKIE[self::EXCEPTION_COOKIE_NAME] = self::COOKIE_VALUE;

            $cookieMetadata = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\CookieMetaData');
            $this->scopeMock->expects($this->once())
                ->method('getCookieMetadata')
                ->with()
                ->will(
                    $this->returnValue($cookieMetadata)
                );
            try {
                $this->cookieManager->deleteCookie(self::EXCEPTION_COOKIE_NAME, $cookieMetadata);
                $this->fail('Expected exception not thrown.');
            } catch (FailureToSendException $fse) {
                $this->assertTrue(self::$isSetCookieInvoked);
                $this->assertSame(
                    'Unable to delete the cookie with cookieName = exception_cookie_name',
                    $fse->getMessage()
                );
            }
        }

        public function testSetSensitiveCookieNoMetadata()
        {
            self::$isSetCookieInvoked = false;
            /** @var SensitiveCookieMetadata $sensitiveCookieMetadata */
            $sensitiveCookieMetadata = $this->objectManager
                ->getObject('Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata');

            $scopeMock = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\CookieScope')
                ->setMethods(['getSensitiveCookieMetadata'])
                ->disableOriginalConstructor()
                ->getMock();
            $scopeMock->expects($this->once())
                ->method('getSensitiveCookieMetadata')
                ->with()
                ->will(
                    $this->returnValue($sensitiveCookieMetadata)
                );

            $mockCookieManager = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PhpCookieManager',
                ['scope' => $scopeMock]
            );

            $mockCookieManager->setSensitiveCookie(
                self::SENSITIVE_COOKIE_NAME_NO_METADATA,
                'cookie_value'
            );
            $this->assertTrue(self::$isSetCookieInvoked);
        }

        public function testSetSensitiveCookieNullDomainAndPath()
        {
            self::$isSetCookieInvoked = false;
            /** @var SensitiveCookieMetadata $sensitiveCookieMetadata */
            $sensitiveCookieMetadata = $this->objectManager
                ->getObject(
                    'Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata',
                    [
                        'metadata' => [
                            'domain' => null,
                            'path' => null,
                        ],
                    ]
                );

            $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
                ->setMethods(['deleteCookie'])
                ->disableOriginalConstructor()->getMock();
            $mockCookieManager->expects($this->never())->method('deleteCookie');

            $mockCookieManager->setSensitiveCookie(
                self::SENSITIVE_COOKIE_NAME_NO_DOMAIN_NO_PATH,
                'cookie_value',
                $sensitiveCookieMetadata
            );
            $this->assertTrue(self::$isSetCookieInvoked);
        }

        public function testSetSensitiveCookieWithPathAndDomain()
        {
            self::$isSetCookieInvoked = false;
            /** @var SensitiveCookieMetadata $sensitiveCookieMetadata */
            $sensitiveCookieMetadata = $this->objectManager
                ->getObject(
                    'Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata',
                    [
                        'metadata' => [
                            'domain' => 'magento.url',
                            'path' => '/backend',
                        ],
                    ]
                );

            $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
                ->setMethods(['deleteCookie'])
                ->disableOriginalConstructor()->getMock();
            $mockCookieManager->expects($this->never())->method('deleteCookie');

            $mockCookieManager->setSensitiveCookie(
                self::SENSITIVE_COOKIE_NAME_WITH_DOMAIN_AND_PATH,
                'cookie_value',
                $sensitiveCookieMetadata
            );
            $this->assertTrue(self::$isSetCookieInvoked);
        }

        public function testSetPublicCookieNoMetadata()
        {
            self::$isSetCookieInvoked = false;
            /** @var PublicCookieMetadata $publicCookieMetadata */
            $publicCookieMetadata = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata'
            );

            $scopeMock = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\CookieScope')
                ->setMethods(['getPublicCookieMetadata'])
                ->disableOriginalConstructor()
                ->getMock();
            $scopeMock->expects($this->once())
                ->method('getPublicCookieMetadata')
                ->with()
                ->will(
                    $this->returnValue($publicCookieMetadata)
                );

            $mockCookieManager = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PhpCookieManager',
                ['scope' => $scopeMock]
            );

            $mockCookieManager->setPublicCookie(
                self::PUBLIC_COOKIE_NAME_NO_METADATA,
                'cookie_value'
            );
            $this->assertTrue(self::$isSetCookieInvoked);
        }

        public function testSetPublicCookieDefaultValues()
        {
            /** @var PublicCookieMetadata $publicCookieMetadata */
            $publicCookieMetadata = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
                [
                    'metadata' => [
                        'domain' => null,
                        'path' => null,
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
                self::PUBLIC_COOKIE_NAME_DEFAULT_VALUES,
                'cookie_value',
                $publicCookieMetadata
            );
            $this->assertTrue(self::$isSetCookieInvoked);
        }

        public function testSetPublicCookieSomeFieldsSet()
        {
            self::$isSetCookieInvoked = false;
            /** @var PublicCookieMetadata $publicCookieMetadata */
            $publicCookieMetadata = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
                [
                    'metadata' => [
                        'domain' => 'magento.url',
                        'path' => '/backend',
                        'http_only' => true,
                    ],
                ]
            );

            $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
                ->setMethods(['deleteCookie'])
                ->disableOriginalConstructor()->getMock();
            $mockCookieManager->expects($this->never())->method('deleteCookie');

            $mockCookieManager->setPublicCookie(
                self::PUBLIC_COOKIE_NAME_SOME_FIELDS_SET,
                'cookie_value',
                $publicCookieMetadata
            );
            $this->assertTrue(self::$isSetCookieInvoked);
        }

        public function testSetCookieBadName()
        {
            /** @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata $publicCookieMetadata */
            $publicCookieMetadata = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
                [
                    'metadata' => [
                        'domain' => null,
                        'path' => null,
                        'secure' => false,
                        'http_only' => false,
                    ],
                ]
            );

            $cookieManager = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PhpCookieManager'
            );

            $cookieValue = 'some_value';

            try {
                $cookieManager->setPublicCookie(
                    '',
                    $cookieValue,
                    $publicCookieMetadata
                );
                $this->fail('Failed to throw exception of bad cookie name');
            } catch (InputException $e) {
                $this->assertEquals(
                    'Cookie name cannot be empty and cannot contain these characters: =,; \\t\\r\\n\\013\\014',
                    $e->getMessage()
                );
            }
        }

        public function testSetCookieSizeTooLarge()
        {
            /** @var PublicCookieMetadata $publicCookieMetadata */
            $publicCookieMetadata = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
                [
                    'metadata' => [
                        'domain' => null,
                        'path' => null,
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
            for ($i = 0; $i < self::MAX_COOKIE_SIZE + 1; $i++) {
                $cookieValue = $cookieValue . 'a';
            }

            try {
                $mockCookieManager->setPublicCookie(
                    self::MAX_COOKIE_SIZE_TEST_NAME,
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
            $publicCookieMetadata = $this->objectManager->getObject(
                'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata'
            );

            $mockCookieManager = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PhpCookieManager')
                ->setMethods(['deleteCookie'])
                ->disableOriginalConstructor()->getMock();
            $mockCookieManager->expects($this->never())->method('deleteCookie');

            $cookieValue = 'some_value';

            // Set self::MAX_NUM_COOKIES number of cookies in superglobal $_COOKIE.
            for ($i = count($_COOKIE); $i < self::MAX_NUM_COOKIES; $i++) {
                $_COOKIE['test_cookie_' . $i] = 'some_value';
            }

            try {
                $mockCookieManager->setPublicCookie(
                    self::MAX_COOKIE_SIZE_TEST_NAME,
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

        /**
         * Assert public, sensitive and delete cookie
         *
         * @param $name
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         * @return bool
         */
        public static function assertCookie($name, $value, $expiry, $path, $domain, $secure, $httpOnly)
        {
            switch ($name) {
                case self::EXCEPTION_COOKIE_NAME:
                    return false;
                case self::DELETE_COOKIE_NAME:
                    self::assertDeleteCookie($value, $expiry, $path, $domain, $secure, $httpOnly);
                    break;
                case self::DELETE_COOKIE_NAME_NO_METADATA:
                    self::assertDeleteCookieWithNoMetadata($value, $expiry, $path, $domain, $secure, $httpOnly);
                    break;
                case self::SENSITIVE_COOKIE_NAME_NO_METADATA:
                    self::assertSensitiveCookieWithNoMetaData($value, $expiry, $path, $domain, $secure, $httpOnly);
                    break;
                case self::SENSITIVE_COOKIE_NAME_NO_DOMAIN_NO_PATH:
                    self::assertSensitiveCookieNoDomainNoPath($value, $expiry, $path, $domain, $secure, $httpOnly);
                    break;
                case self::SENSITIVE_COOKIE_NAME_WITH_DOMAIN_AND_PATH:
                    self::assertPublicCookieWithNoDomainNoPath($value, $expiry, $path, $domain, $secure, $httpOnly);
                    break;
                case self::PUBLIC_COOKIE_NAME_NO_METADATA:
                    self::assertPublicCookieWithNoMetaData($value, $expiry, $path, $domain, $secure, $httpOnly);
                    break;
                case self::PUBLIC_COOKIE_NAME_DEFAULT_VALUES:
                    self::assertPublicCookieWithDefaultValues($value, $expiry, $path, $domain, $secure, $httpOnly);
                    break;
                case self::PUBLIC_COOKIE_NAME_SOME_FIELDS_SET:
                    self::assertPublicCookieWithSomeFieldSet($value, $expiry, $path, $domain, $secure, $httpOnly);
                    break;
                case self::MAX_COOKIE_SIZE_TEST_NAME:
                    self::assertCookieSize($value, $path, $domain, $secure, $httpOnly);
                    break;
                default:
                    self::fail('Non-tested case in mock setcookie()');
            }
            return true;
        }

        /**
         * Assert delete cookie
         *
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertDeleteCookie($value, $expiry, $path, $domain, $secure, $httpOnly)
        {
            self::assertEquals('', $value);
            self::assertEquals($expiry, PhpCookieManager::EXPIRE_NOW_TIME);
            self::assertFalse($secure);
            self::assertFalse($httpOnly);
            self::assertEquals('magento.url', $domain);
            self::assertEquals('/backend', $path);
        }

        /**
         * Assert delete cookie with no meta data
         *
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertDeleteCookieWithNoMetadata($value, $expiry, $path, $domain, $secure, $httpOnly)
        {
            self::assertEquals('', $value);
            self::assertEquals($expiry, PhpCookieManager::EXPIRE_NOW_TIME);
            self::assertFalse($secure);
            self::assertFalse($httpOnly);
            self::assertEquals('', $domain);
            self::assertEquals('', $path);
        }

        /**
         * Assert sensitive cookie with no meta data
         *
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertSensitiveCookieWithNoMetaData($value, $expiry, $path, $domain, $secure, $httpOnly)
        {
            self::assertEquals(self::COOKIE_VALUE, $value);
            self::assertEquals(PhpCookieManager::EXPIRE_AT_END_OF_SESSION_TIME, $expiry);
            self::assertTrue($secure);
            self::assertTrue($httpOnly);
            self::assertEquals('', $domain);
            self::assertEquals('', $path);
        }

        /**
         * Assert sensitive cookie with no domain and path
         *
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertSensitiveCookieNoDomainNoPath($value, $expiry, $path, $domain, $secure, $httpOnly)
        {
            self::assertEquals(self::COOKIE_VALUE, $value);
            self::assertEquals(PhpCookieManager::EXPIRE_AT_END_OF_SESSION_TIME, $expiry);
            self::assertTrue($secure);
            self::assertTrue($httpOnly);
            self::assertEquals('', $domain);
            self::assertEquals('', $path);
        }

        /**
         * Assert public cookie with no metadata
         *
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertPublicCookieWithNoMetaData($value, $expiry, $path, $domain, $secure, $httpOnly)
        {
            self::assertEquals(self::COOKIE_VALUE, $value);
            self::assertEquals(self::COOKIE_EXPIRE_END_OF_SESSION, $expiry);
            self::assertFalse($secure);
            self::assertFalse($httpOnly);
            self::assertEquals('', $domain);
            self::assertEquals('', $path);
        }

        /**
         * Assert public cookie with no domain and path
         *
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertPublicCookieWithNoDomainNoPath(
            $value,
            $expiry,
            $path,
            $domain,
            $secure,
            $httpOnly
        ) {
            self::assertEquals(self::COOKIE_VALUE, $value);
            self::assertEquals(PhpCookieManager::EXPIRE_AT_END_OF_SESSION_TIME, $expiry);
            self::assertTrue($secure);
            self::assertTrue($httpOnly);
            self::assertEquals('magento.url', $domain);
            self::assertEquals('/backend', $path);
        }

        /**
         * Assert public cookie with default values
         *
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertPublicCookieWithDefaultValues($value, $expiry, $path, $domain, $secure, $httpOnly)
        {
            self::assertEquals(self::COOKIE_VALUE, $value);
            self::assertEquals(self::COOKIE_EXPIRE_END_OF_SESSION, $expiry);
            self::assertFalse($secure);
            self::assertFalse($httpOnly);
            self::assertEquals('', $domain);
            self::assertEquals('', $path);
        }

        /**
         * Assert public cookie with no field set
         *
         * @param $value
         * @param $expiry
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertPublicCookieWithSomeFieldSet($value, $expiry, $path, $domain, $secure, $httpOnly)
        {
            self::assertEquals(self::COOKIE_VALUE, $value);
            self::assertEquals(self::COOKIE_EXPIRE_END_OF_SESSION, $expiry);
            self::assertFalse($secure);
            self::assertTrue($httpOnly);
            self::assertEquals('magento.url', $domain);
            self::assertEquals('/backend', $path);
        }

        /**
         * Assert cookie size
         *
         * @param $value
         * @param $path
         * @param $domain
         * @param $secure
         * @param $httpOnly
         */
        private static function assertCookieSize($value, $path, $domain, $secure, $httpOnly)
        {
            self::assertEquals(self::COOKIE_VALUE, $value);
            self::assertFalse($secure);
            self::assertFalse($httpOnly);
            self::assertEquals('', $domain);
            self::assertEquals('', $path);
        }

        public function tearDown()
        {
            global $mockTranslateSetCookie;
            $mockTranslateSetCookie = false;
        }
    }
}