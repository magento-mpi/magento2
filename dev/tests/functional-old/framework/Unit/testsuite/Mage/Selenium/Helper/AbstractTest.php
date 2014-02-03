<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Selenium_Helper_AbstractTest extends Unit_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Uimap_Abstract::__construct
     */
    public function test__construct()
    {
        $instance = new Mage_Selenium_Helper_Abstract($this->_testConfig);
        $this->assertInstanceOf('Mage_Selenium_Helper_Abstract', $instance);
    }

    /**
     * @covers Mage_Selenium_Uimap_Abstract::getConfig
     */
    public function testGetConfig()
    {
        $instance = new Mage_Selenium_Helper_Abstract($this->_testConfig);
        $this->assertInstanceOf('Mage_Selenium_TestConfiguration', $instance->getConfig());
    }
}