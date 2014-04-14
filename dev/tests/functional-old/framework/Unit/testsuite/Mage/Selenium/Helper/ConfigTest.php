<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Selenium_Helper_ConfigTest extends Unit_PHPUnit_TestCase
{
    /**
     * Config helper instance
     * @var Mage_Selenium_Helper_Config
     */
    private $_configHelper;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_configHelper = $this->_testConfig->getHelper('config');
    }

    public function test__construct()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $this->_configHelper);
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getConfigValue
     */
    public function testGetConfigValue()
    {
        $this->assertInternalType('array', $this->_configHelper->getConfigValue());
        $this->assertNotEmpty($this->_configHelper->getConfigValue());

        $this->assertFalse($this->_configHelper->getConfigValue('invalid-path'));

        $this->assertInternalType('array', $this->_configHelper->getConfigValue('browsers'));
        $this->assertArrayHasKey('default', $this->_configHelper->getConfigValue('browsers'));

        $this->assertInternalType('array', $this->_configHelper->getConfigValue('browsers/default'));
        $this->assertArrayHasKey('browser', $this->_configHelper->getConfigValue('browsers/default'));

        $this->assertInternalType('string', $this->_configHelper->getConfigValue('browsers/default/browser'));
        $this->assertInternalType('int', $this->_configHelper->getConfigValue('browsers/default/port'));
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setArea
     */
    public function testSetArea()
    {

        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $this->_configHelper->setArea('frontend'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $this->_configHelper->setArea('admin'));
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setArea
     */
    public function testSetAreaOutOfRangeException()
    {
        $this->setExpectedException('OutOfRangeException', 'Area with name');
        $this->_configHelper->setArea('invalid-area');
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getArea
     */
    public function testGetArea()
    {
        $this->_configHelper->setArea('frontend');
        $this->assertInternalType('string', $this->_configHelper->getArea());
        $this->assertNotEmpty($this->_configHelper->getArea());
        $this->assertEquals('frontend', $this->_configHelper->getArea());
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getBaseUrl
     */
    public function testGetBaseUrl()
    {
        $this->assertInternalType('string', $this->_configHelper->getBaseUrl());
        $this->assertNotEmpty($this->_configHelper->getBaseUrl());

        $this->_configHelper->setArea('admin');
        $this->assertRegExp(
            '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/',
            $this->_configHelper->getBaseUrl()
        );

        $this->_configHelper->setArea('frontend');
        $this->assertRegExp(
            '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/',
            $this->_configHelper->getBaseUrl()
        );
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getDefaultLogin
     */
    public function testGetDefaultLogin()
    {
        $this->_configHelper->setArea('admin');
        $login = $this->_configHelper->getDefaultLogin();
        $this->assertInternalType('string', $login);
        $this->assertNotEmpty($login);
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getDefaultPassword
     */
    public function testGetDefaultPassword()
    {
        $this->_configHelper->setArea('admin');
        $password = $this->_configHelper->getDefaultPassword();
        $this->assertInternalType('string', $password);
        $this->assertNotEmpty($password);
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getBasePath
     */
    public function testGetBasePath()
    {
        $this->_configHelper->setApplication('ce');
        $this->_configHelper->setArea('admin');
        $uimapPath = $this->_configHelper->getBasePath();
        $this->assertInternalType('string', $uimapPath);
        $this->assertSame($uimapPath, 'admin');
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getFixturesFallbackOrder
     */
    public function testGetFixturesFallbackOrder()
    {
        $this->_configHelper->setApplication('ce');
        $fallbackOrder = $this->_configHelper->getFixturesFallbackOrder();
        $this->assertInternalType('array', $fallbackOrder);
        $this->assertSame($fallbackOrder, array('default'));
        $this->_configHelper->setApplication('ee');
        $fallbackOrder = $this->_configHelper->getFixturesFallbackOrder();
        $this->assertInternalType('array', $fallbackOrder);
        $this->assertSame($fallbackOrder, array('default', 'enterprise'));
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getHelpersFallbackOrder
     */
    public function testGetHelpersFallbackOrder()
    {
        $this->_configHelper->setApplication('ce');
        $fallbackOrder = $this->_configHelper->getHelpersFallbackOrder();
        $this->assertInternalType('array', $fallbackOrder);
        $this->assertSame($fallbackOrder, array('Core'));
        $this->_configHelper->setApplication('ee');
        $fallbackOrder = $this->_configHelper->getHelpersFallbackOrder();
        $this->assertInternalType('array', $fallbackOrder);
        $this->assertSame($fallbackOrder, array('Core', 'Enterprise'));
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setScreenshotDir
     * @covers Mage_Selenium_Helper_Config::getScreenshotDir
     */
    public function testGetSetScreenshotDir()
    {
        //Default directory
        $this->assertEquals(SELENIUM_TESTS_SCREENSHOTDIR, $this->_configHelper->getScreenshotDir());
        //Create a directory
        $parentDir = 'test testGetSetScreenshotDir';
        $dirName = $parentDir . '/ss-dir-test';
        $this->assertTrue(!is_dir($dirName) || (rmdir($dirName) && rmdir($parentDir)));
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $this->_configHelper->setScreenshotDir($dirName));
        $this->assertTrue(is_dir($dirName));
        $this->assertEquals($dirName, $this->_configHelper->getScreenshotDir());
        //Set to existing directory
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $this->_configHelper->setScreenshotDir($dirName));
        $this->assertTrue(is_dir($dirName));
        $this->assertEquals($dirName, $this->_configHelper->getScreenshotDir());
        //Cleanup
        rmdir($dirName);
        rmdir($parentDir);
    }

    /**
     * @covers  Mage_Selenium_Helper_Config::setScreenshotDir
     * @depends testGetSetScreenshotDir
     */
    public function testSetScreenshotDirInvalidParameterException()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning', 'mkdir():');
        $this->_configHelper->setScreenshotDir(null);
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setLogDir
     * @covers Mage_Selenium_Helper_Config::getLogDir
     */
    public function testGetSetLogDir()
    {
        //Default directory
        $this->assertEquals(SELENIUM_TESTS_LOGS, $this->_configHelper->getLogDir());
        //Create a directory
        $dirName = 'log-dir-test';
        $this->assertTrue(!is_dir($dirName) || rmdir($dirName));
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $this->_configHelper->setLogDir($dirName));
        $this->assertTrue(is_dir($dirName));
        $this->assertEquals($dirName, $this->_configHelper->getLogDir());
        //Set to existing directory
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $this->_configHelper->setLogDir($dirName));
        $this->assertTrue(is_dir($dirName));
        $this->assertEquals($dirName, $this->_configHelper->getLogDir());
        //Cleanup
        rmdir($dirName);
    }

    /**
     * @covers  Mage_Selenium_Helper_Config::setLogDir
     * @depends testGetSetLogDir
     */
    public function testSetLogDirInvalidParameterException()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning', 'mkdir():');
        $this->_configHelper->setLogDir(null);
    }
}
