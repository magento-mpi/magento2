<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_Helper_CacheTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Uimap_Abstract::__construct
     */
    public function test__construct()
    {
        $instance = new Mage_Selenium_Helper_Cache($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Cache', $instance);
    }

    /**
     * @covers Mage_Selenium_Uimap_Abstract::getCache
     */
    public function testGetCache()
    {
        $instance = new Mage_Selenium_Helper_Cache($this->_config);
        $cache = $instance->getCache();
        $this->assertStringStartsWith('Zend_Cache_', get_class($cache));
        $this->assertEquals($cache, $instance->getCache());
    }
}