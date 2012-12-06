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
class Mage_Selenium_Helper_AbstractTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Uimap_Abstract::__construct
     */
    public function test__construct()
    {
        $instance = new Mage_Selenium_Helper_Abstract($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Abstract', $instance);
    }

    /**
     * @covers Mage_Selenium_Uimap_Abstract::getConfig
     */
    public function testGetConfig()
    {
        $instance = new Mage_Selenium_Helper_Abstract($this->_config);
        $this->assertInstanceOf('Mage_Selenium_TestConfiguration', $instance->getConfig());
    }
}