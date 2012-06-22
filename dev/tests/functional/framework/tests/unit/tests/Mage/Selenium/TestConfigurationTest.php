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
class Mage_Selenium_TestConfigurationTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_TestConfiguration::getInstance
     * @covers Mage_Selenium_TestConfiguration::init
     */
    public function testGetInstance()
    {
        $instance = Mage_Selenium_TestConfiguration::getInstance();
        $this->assertInstanceOf('Mage_Selenium_TestConfiguration', $instance);
        $this->assertEquals($instance, Mage_Selenium_TestConfiguration::getInstance());
        $this->assertAttributeInstanceOf('Mage_Selenium_Helper_Config', '_configHelper', $instance);
        $this->assertAttributeInternalType('array', '_configFixtures', $instance);
        $this->assertAttributeInstanceOf('Mage_Selenium_Helper_Uimap', '_uimapHelper', $instance);
        $this->assertAttributeInstanceOf('Mage_Selenium_Helper_Data', '_dataHelper', $instance);
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getHelper
     */
    public function testGetHelper()
    {
        $instance = Mage_Selenium_TestConfiguration::getInstance();
        $this->assertInstanceOf('Mage_Selenium_Helper_Cache', $instance->getHelper('cache'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $instance->getHelper('config'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Data', $instance->getHelper('data'));
        $this->assertInstanceOf('Mage_Selenium_Helper_DataGenerator', $instance->getHelper('dataGenerator'));
        $this->assertInstanceOf('Mage_Selenium_Helper_File', $instance->getHelper('file'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Params', $instance->getHelper('params'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Uimap', $instance->getHelper('uimap'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Uimap', $instance->getHelper('Uimap'));

        $sameValue = $instance->getHelper('cache');
        $this->assertEquals($sameValue, $instance->getHelper('cache'));
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getHelper
     * @expectedException OutOfRangeException
     */
    public function testGetHelperException()
    {
        $instance = Mage_Selenium_TestConfiguration::getInstance();
        $instance->getHelper('NotExistingHelper');
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getConfigFixtures
     */
    public function testGetConfigFixtures()
    {
        $instance = Mage_Selenium_TestConfiguration::getInstance();
        $sameValue = $instance->getConfigFixtures();
        $this->assertEquals($sameValue, $instance->getConfigFixtures());
        $this->assertInternalType('array', $sameValue);
        $this->assertNotEmpty($sameValue);
        $this->assertInternalType('array', current($sameValue));
        $this->assertNotEmpty(current($sameValue));
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getTestHelperClassNames
     */
    public function testGetTestHelperClassNames()
    {
        $instance = Mage_Selenium_TestConfiguration::getInstance();
        $helperClassNames = $instance->getTestHelperClassNames();
        $this->assertInternalType('array', $helperClassNames);
        $this->assertGreaterThan(0, count($helperClassNames));
        foreach ($helperClassNames as $name) {
            $this->assertContains('_Helper', $name);
        }
    }
}