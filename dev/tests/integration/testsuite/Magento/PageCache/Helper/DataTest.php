<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_PageCache_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Helper\Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Magento\PageCache\Helper\Data');
    }

    public function testSetNoCacheCookie()
    {
        /** @var $cookie \Magento\Core\Model\Cookie */
        $cookie = Mage::getSingleton('Magento\Core\Model\Cookie');
        $this->assertEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));
        $this->_helper->setNoCacheCookie();
        $this->assertNotEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));
    }

    public function testRemoveNoCacheCookie()
    {
        /** @var $cookie \Magento\Core\Model\Cookie */
        $cookie = Mage::getSingleton('Magento\Core\Model\Cookie');
        $this->_helper->setNoCacheCookie();
        $this->_helper->removeNoCacheCookie();
        $this->assertEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));
    }

    public function testLockUnlockNoCacheCookie()
    {
        /** @var $cookie \Magento\Core\Model\Cookie */
        $cookie = Mage::getSingleton('Magento\Core\Model\Cookie');
        $this->_helper->setNoCacheCookie();
        $this->assertNotEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));

        $this->_helper->lockNoCacheCookie();
        $this->_helper->removeNoCacheCookie();
        $this->assertNotEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));

        $this->_helper->unlockNoCacheCookie();
        $this->_helper->removeNoCacheCookie();
        $this->assertEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));

        $this->_helper->lockNoCacheCookie();
        $this->_helper->setNoCacheCookie();
        $this->assertEmpty($cookie->get(\Magento\PageCache\Helper\Data::NO_CACHE_COOKIE));
    }
}
