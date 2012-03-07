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
class Mage_PageCache_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_PageCache_Model_Observer
     */
    protected $_observer;

    protected function setUp()
    {
        $this->_observer = new Mage_PageCache_Model_Observer;
    }

    public function testLaunchDesignEditor()
    {
        /** @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('Mage_Core_Model_Cookie');
        $this->assertEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
        $this->_observer->launchDesignEditor(new Varien_Event_Observer());
        $this->assertNotEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
    }

    /**
     * @depends testLaunchDesignEditor
     */
    public function testExitDesignEditor()
    {
        /** @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('Mage_Core_Model_Cookie');
        $this->assertNotEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
        $this->_observer->exitDesignEditor(new Varien_Event_Observer());
        $this->assertEmpty($cookie->get(Mage_PageCache_Helper_Data::NO_CACHE_COOKIE));
    }
}
