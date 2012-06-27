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

/**
 * Unit test for TestConfiguration
 */
class Mage_Selenium_TestConfigurationTest extends Mage_PHPUnit_TestCase
{
    /**
     * Testing Mage_Selenium_TestConfiguration::init()
     */
    public function testInit()
    {
        $this->assertInstanceOf('Mage_Selenium_TestConfiguration', $this->_config->init());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getFileHelper()
     */
    public function testGetFileHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_File', $this->_config->getFileHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getPageHelper()
     */
    public function testGetPageHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $this->_config->getPageHelper());
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $this->_config->getPageHelper(new Mage_Selenium_TestCase()));
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $this->_config->getPageHelper(null, new Mage_Selenium_Helper_Application($this->_config)));
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $this->_config->getPageHelper(new Mage_Selenium_TestCase(), new Mage_Selenium_Helper_Application($this->_config)));
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getDataGenerator()
     */
    public function testGetDataGenerator()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_DataGenerator', $this->_config->getDataGenerator());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getDataHelper()
     */
    public function testGetDataHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_Data', $this->_config->getDataHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getApplicationHelper()
     */
    public function testGetApplicationHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $this->_config->getApplicationHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getConfigValue()
     */
    public function testGetConfigValue()
    {
        $this->assertInternalType('array', $this->_config->getConfigValue());
        $this->assertNotEmpty($this->_config->getConfigValue());

        $this->assertFalse($this->_config->getConfigValue('invalid-path'));

        $this->assertInternalType('array', $this->_config->getConfigValue('browsers'));
        $this->assertArrayHasKey('default', $this->_config->getConfigValue('browsers'));

        $this->assertInternalType('array', $this->_config->getConfigValue('browsers/default'));
        $this->assertArrayHasKey('browser', $this->_config->getConfigValue('browsers/default'));

        $this->assertInternalType('string', $this->_config->getConfigValue('browsers/default/browser'));
        $this->assertInternalType('int', $this->_config->getConfigValue('browsers/default/port'));
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getDataValue()
     */
    public function testGetDataValue()
    {
        $this->assertInternalType('array', $this->_config->getDataValue());
        $this->assertNotEmpty($this->_config->getDataValue());

        $this->assertFalse($this->_config->getDataValue('invalid-path'));

        $this->assertArrayHasKey('generic_admin_user', $this->_config->getDataValue());
        $this->assertInternalType('array',  $this->_config->getDataValue('generic_admin_user'));
        $this->assertInternalType('string', $this->_config->getDataValue('generic_admin_user/user_name'));
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getUimapValue()
     */
    public function testGetUimapValue()
    {
        $this->assertInternalType('array', $this->_config->getUimapHelper()->getUimap('frontend'));
        $this->assertNotEmpty($this->_config->getUimapHelper()->getUimap('frontend'));

        $this->assertInternalType('array', $this->_config->getUimapHelper()->getUimap('admin'));
        $this->assertNotEmpty($this->_config->getUimapHelper()->getUimap('admin'));

        $this->assertNull($this->getUimapPage('frontend', 'invalid-path'));
        $this->assertNull($this->getUimapPage('admin', 'invalid-path'));

        $this->assertInternalType('string', $this->getUimapPage('admin', 'manage_admin_users')->getMca());
        $this->assertInternalType('string', $this->getUimapPage('frontend', 'customer_account')->getMca());
    }

    /**
     * Testing exception throwing in Mage_Selenium_TestConfiguration::getUimapValue()
     */
    public function testGetUimapValueOutOfRangeException()
    {
        $this->setExpectedException('OutOfRangeException');
        $this->_config->getUimapHelper()->getUimap('invalid-area');
    }

    public function testGetInstanceReturnsSuccess()
    {
        $this->assertInstanceOf(
            'Mage_Selenium_TestConfiguration',
            Mage_Selenium_TestConfiguration::getInstance()
        );
    }

    public function testGetInstanceWithSpecifiedInitialOptionsReturnsSuccess()
    {
        Mage_Selenium_TestConfiguration::setInstance();
        $config = Mage_Selenium_TestConfiguration::getInstance($this->_getInitialOptions());

        $this->assertEquals($this->_getInitialOptions(), $config->getInitialOptions());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetInstanceWithInitializedInstanceAndOptionsThrowsException()
    {
        Mage_Selenium_TestConfiguration::getInstance($this->_getInitialOptions());
    }

    /**
     * Test will be marked as success only if in specified theme will be at least one YAML file
     *
     * @dataProvider fallbackOrderFixturesDataProvider
     */
    public function testGetConfigFixturesWithCustomFallbackOrderFixturesReturnsSuccess(array $fallbackOrderFixtures)
    {
        Mage_Selenium_TestConfiguration::setInstance();
        $config = Mage_Selenium_TestConfiguration::getInstance(array(
            'fallbackOrderFixture' => $fallbackOrderFixtures
        ));

        $config->setInitialPath($this->_getFilesPath());

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
        return array(
            array(array(
                $defaultThemePath, $modernThemePath
            ))
        );
    }

    /**
     * Retrieve initial options
     *
     * @return array
     */
    protected function _getInitialOptions()
    {
        return array('sample_data' => 'example');
    }

    /**
     * Retrieve path to test's files
     *
     * @return string
     */
    protected function _getFilesPath()
    {
        return realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
    }
}
