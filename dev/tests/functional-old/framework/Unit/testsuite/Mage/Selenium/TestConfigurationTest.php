<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
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
        $this->assertAttributeInternalType('array', '_configUimap', $this->_testConfig);
        $this->assertAttributeInternalType('array', '_configData', $this->_testConfig);
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
     * @covers Mage_Selenium_TestConfiguration::getConfigUimap
     */
    public function testGetConfigUimap()
    {
        $sameValue = $this->_testConfig->getConfigUimap();
        $this->assertEquals($sameValue, $this->_testConfig->getConfigUimap());
        $this->assertInternalType('array', $sameValue);
        $this->assertNotEmpty($sameValue);
        $this->assertInternalType('array', current($sameValue));
        $this->assertNotEmpty(current($sameValue));
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getConfigData
     */
    public function testGetConfigData()
    {
        $sameValue = $this->_testConfig->getConfigData();
        $this->assertEquals($sameValue, $this->_testConfig->getConfigData());
        $this->assertInternalType('array', $sameValue);
        $this->assertNotEmpty($sameValue);
        $this->assertNotEmpty(current($sameValue));
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getTestHelperClassNames
     */
    public function testGetTestHelperNames()
    {
        $helperClassNames = $this->_testConfig->getTestHelperNames();
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
}
