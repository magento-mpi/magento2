<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_PageCache
 */
class Mage_PageCache_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_PageCache_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Mage_PageCache_Helper_Data;
    }

    public function testSetNoCacheCookie()
    {
        /** @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('Mage_Core_Model_Cookie');
        $this->assertEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
        $this->_helper->setNoCacheCookie();
        $this->assertNotEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
    }

    public function testRemoveNoCacheCookie()
    {
        /** @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('Mage_Core_Model_Cookie');
        $this->_helper->setNoCacheCookie();
        $this->_helper->removeNoCacheCookie();
        $this->assertEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
    }

    public function testLockUnlockNoCacheCookie()
    {
        /** @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('Mage_Core_Model_Cookie');
        $this->_helper->setNoCacheCookie();
        $this->assertNotEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));

        $this->_helper->lockNoCacheCookie();
        $this->_helper->removeNoCacheCookie();
        $this->assertNotEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));

        $this->_helper->unlockNoCacheCookie();
        $this->_helper->removeNoCacheCookie();
        $this->assertEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));

        $this->_helper->lockNoCacheCookie();
        $this->_helper->setNoCacheCookie();
        $this->assertEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
    }
}