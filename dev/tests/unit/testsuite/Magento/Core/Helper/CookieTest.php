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

class CookieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\Cookie
     */
    protected $_object = null;

    protected function setUp()
    {
        $this->_object = new \Magento\Core\Helper\Cookie(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Model\Cookie', array(), array(), '', false, false),
            array(
                'current_store' => $this->_getStoreStub(),
                'cookie_model' => $this->_getCookieStub(array(1 => 1)),
                'website' => $this->_getWebsiteStub(),
            )
        );
    }

    public function testIsUserNotAllowSaveCookie()
    {
        $this->assertFalse($this->_object->isUserNotAllowSaveCookie());
        $this->_object = new \Magento\Core\Helper\Cookie(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Model\Cookie', array(), array(), '', false, false),
            array(
                'current_store' => $this->_getStoreStub(),
                'cookie_model' => $this->_getCookieStub(array()),
                'website' => $this->_getWebsiteStub(),
            )
        );
        $this->assertTrue($this->_object->isUserNotAllowSaveCookie());
    }

    public function testGetAcceptedSaveCookiesWebsiteIds()
    {
        $this->assertEquals(
            $this->_object->getAcceptedSaveCookiesWebsiteIds(),
            json_encode(array(1 => 1))
        );
    }

    public function testGetCookieRestrictionLifetime()
    {
        $storeStub = $this->_getStoreStub();
        $storeStub->expects($this->once())
            ->method('getConfig')
            ->will($this->returnCallback('Magento\\Core\\Helper\\CookieTest::getConfigMethodStub'))
            ->with($this->equalTo('web/cookie/cookie_restriction_lifetime'));
        $this->_object = new \Magento\Core\Helper\Cookie(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false, false),
            $this->getMock('Magento\Core\Model\Cookie', array(), array(), '', false, false),
            array(
                'current_store' => $storeStub,
                'cookie_model' => $this->_getCookieStub(array(1 => 1)),
                'website' => $this->_getWebsiteStub()
            )
        );
        $this->assertEquals($this->_object->getCookieRestrictionLifetime(), 60*60*24*365);
    }

    /**
     * Create store stub
     * @return \Magento\Core\Model\Store
     */
    protected function _getStoreStub()
    {
        $store = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);

        $store->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback('Magento\\Core\\Helper\\CookieTest::getConfigMethodStub'));

        return $store;
    }

    /**
     * Create cookie model stub
     * @param array $cookieString
     * @return \Magento\Core\Model\Cookie
     */
    protected function _getCookieStub($cookieString = array())
    {
        $cookieMock = $this->getMock('Magento\Core\Model\Cookie', array(), array(), '', false);

        $cookieMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(json_encode($cookieString)));

        return $cookieMock;
    }

    /**
     * Create Website Stub
     * @return \Magento\Core\Model\Website
     */
    protected function _getWebsiteStub()
    {
        $websiteMock = $this->getMock('Magento\Core\Model\Website', array(), array(), '', false);

        $websiteMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        return $websiteMock;
    }

    /**
     * Mock get config method
     * @static
     * @param string $hashName
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getConfigMethodStub($hashName)
    {

        $defaultConfig = array(
            'web/cookie/cookie_restriction' => 1,
            'web/cookie/cookie_restriction_lifetime' => 60*60*24*365,
        );

        if (array_key_exists($hashName, $defaultConfig)) {
            return $defaultConfig[$hashName];
        }

        throw new \InvalidArgumentException('Unknow id = ' . $hashName);
    }
}
