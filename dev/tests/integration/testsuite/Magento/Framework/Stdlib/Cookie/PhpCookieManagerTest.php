<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Stdlib\Cookie;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test PhpCookieManager
 *
 */
class PhpCookieManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * Cookie Manager
     *
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $cookieManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->cookieManager = $this->objectManager->create('Magento\Framework\Stdlib\Cookie\PhpCookieManager');
    }

    public function testGetCookie()
    {
        $cookieName = 'cookie name';
        $cookieValue = 'cookie value';
        $defaultCookieValue = 'default';
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
        $cookieName = 'cookie name';
        $cookieValue = 'cookie value';
        $_COOKIE[$cookieName] = $cookieValue;
        /** @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetaData $publicCookieMetaData */
        $publicCookieMetaData = $this->objectManager->create('Magento\Framework\Stdlib\Cookie\PublicCookieMetaData');
        $this->assertEquals($cookieValue, $this->cookieManager->getCookie($cookieName));
        $this->cookieManager->deleteCookie($cookieName, $publicCookieMetaData);
        $this->assertNull($this->cookieManager->getCookie($cookieName));
    }

    /**
     * It is not possible to write integration tests for CookieManager::setSensitiveCookie().
     * PHPUnit the following error when calling the function:
     *
     * PHPUnit_Framework_Error_Warning : Cannot modify header information - headers already sent
     */
    public function testSetSensitiveCookie()
    {
    }

    /**
     * It is not possible to write integration tests for CookieManager::setSensitiveCookie().
     * PHPUnit the following error when calling the function:
     *
     * PHPUnit_Framework_Error_Warning : Cannot modify header information - headers already sent
     */
    public function testSetPublicCookie()
    {
    }

    /**
     * @return mixed
    /*
    $this->cookieManager = $this->objectManager
    ->create(
    'Magento\Framework\Stdlib\Cookie\PhpCookieManager',
    [
    'scope' => $this->createCookieScope(),
    ]
    );
     */
/*
    public function createCookieScope()
    {
        $cookieMetadataFactory = $this->objectManager
            ->create('\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory');

        $sensitiveMetadataValues = [
            SensitiveCookieMetadata::KEY_PATH => 'default path',
            SensitiveCookieMetadata::KEY_DOMAIN => 'default domain',
        ];

        $publicMetadataValues = [
            PublicCookieMetadata::KEY_PATH => 'default path',
            PublicCookieMetadata::KEY_DOMAIN => 'default domain',
            PublicCookieMetadata::KEY_DURATION => 'default duration',
            PublicCookieMetadata::KEY_HTTP_ONLY => 'default http',
            PublicCookieMetadata::KEY_SECURE => 'default secure',
        ];

        $cookieScope = $this->objectManager->create(
            '\Magento\Framework\Stdlib\Cookie\CookieScope',
            [
                $cookieMetadataFactory,
                'sensitiveCookieMetadata' => $cookieMetadataFactory
                    ->createSensitiveCookieMetadata($publicMetadataValues),
                'publicCookieMetadata' => $cookieMetadataFactory
                    ->createPublicCookieMetadata($sensitiveMetadataValues),
            ]
        );

        return $cookieScope;
    }*/
}
