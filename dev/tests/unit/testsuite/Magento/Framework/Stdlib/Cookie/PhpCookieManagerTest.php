<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Stdlib\Cookie;

use Magento\TestFramework\Helper\ObjectManager;

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

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->cookieManager = $this->objectManager->getObject('Magento\Framework\Stdlib\Cookie\PhpCookieManager');
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
