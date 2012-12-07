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
class Mage_Selenium_TestConfigurationTest extends Unit_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_TestConfiguration::getInstance
     * @covers Mage_Selenium_TestConfiguration::init
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf('Mage_Selenium_TestConfiguration', $this->_testConfig);
        $this->assertEquals($this->_testConfig, Mage_Selenium_TestConfiguration::getInstance());
        $this->assertAttributeInstanceOf('Mage_Selenium_Helper_Config', '_configHelper', $this->_testConfig);
        $this->assertAttributeInternalType('array', '_configFixtures', $this->_testConfig);
        $this->assertAttributeInstanceOf('Mage_Selenium_Helper_Uimap', '_uimapHelper', $this->_testConfig);
        $this->assertAttributeInstanceOf('Mage_Selenium_Helper_Data', '_dataHelper', $this->_testConfig);
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getHelper
     */
    public function testGetHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $this->_testConfig->getHelper('config'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Data', $this->_testConfig->getHelper('data'));
        $this->assertInstanceOf('Mage_Selenium_Helper_DataGenerator', $this->_testConfig->getHelper('dataGenerator'));
        $this->assertInstanceOf('Mage_Selenium_Helper_File', $this->_testConfig->getHelper('file'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Params', $this->_testConfig->getHelper('params'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Uimap', $this->_testConfig->getHelper('uimap'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Uimap', $this->_testConfig->getHelper('Uimap'));
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getHelper
     * @expectedException OutOfRangeException
     */
    public function testGetHelperException()
    {
        $this->_testConfig->getHelper('NotExistingHelper');
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getConfigFixtures
     */
    public function testGetConfigFixtures()
    {
        $sameValue = $this->_testConfig->getConfigFixtures();
        $this->assertEquals($sameValue, $this->_testConfig->getConfigFixtures());
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
        $helperClassNames = $this->_testConfig->getTestHelperClasses();
        $this->assertInternalType('array', $helperClassNames);
        $this->assertGreaterThan(0, count($helperClassNames));
        foreach ($helperClassNames as $name) {
            $this->assertContains('_Helper', $name);
        }
    }

    public function testGetInstanceWithSpecifiedInitialOptionsReturnsSuccess()
    {
        Mage_Selenium_TestConfiguration::setInstance();
        $config = Mage_Selenium_TestConfiguration::getInstance(array('sample_data' => 'example'));
        $this->assertEquals(array('sample_data' => 'example'), $config->getInitialOptions());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetInstanceWithInitializedInstanceAndOptionsThrowsException()
    {
        Mage_Selenium_TestConfiguration::getInstance(array('sample_data' => 'example'));
    }

    /**
     * Test will be marked as success only if in specified theme will be at least one YAML file
     *
     * @dataProvider fallbackOrderFixturesDataProvider
     */
    public function testGetConfigFixturesWithCustomFallbackOrderFixturesReturnsSuccess(array $fallbackOrderFixtures)
    {
        Mage_Selenium_TestConfiguration::setInstance();
        $config = Mage_Selenium_TestConfiguration::getInstance(array('fallbackOrderFixture' => $fallbackOrderFixtures));
        $config->setInitialPath(realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR);
        $keysExists = true;
        foreach ($fallbackOrderFixtures as $fixturesPath) {
            $keysExists = array_key_exists($fixturesPath, $config->getConfigFixtures());
            if (false === $keysExists) {
                break;
            }
        }

        $this->assertTrue($keysExists);
    }

    public function fallbackOrderFixturesDataProvider()
    {
        $defaultThemePath = implode(DIRECTORY_SEPARATOR, array('themes', 'frontend', 'default', 'default'));
        $modernThemePath = implode(DIRECTORY_SEPARATOR, array('themes', 'frontend', 'default', 'modern'));
        return array(array(array($defaultThemePath, $modernThemePath)));
    }
}