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
class Mage_Selenium_Helper_ConfigTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $configHelper);
    }

    /**
     * @covers Mage_Selenium_TestConfiguration::getConfigValue
     */
    public function testGetConfigValue()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $this->assertInternalType('array', $configHelper->getConfigValue());
        $this->assertNotEmpty($configHelper->getConfigValue());

        $this->assertFalse($configHelper->getConfigValue('invalid-path'));

        $this->assertInternalType('array', $configHelper->getConfigValue('browsers'));
        $this->assertArrayHasKey('default', $configHelper->getConfigValue('browsers'));

        $this->assertInternalType('array', $configHelper->getConfigValue('browsers/default'));
        $this->assertArrayHasKey('browser', $configHelper->getConfigValue('browsers/default'));

        $this->assertInternalType('string', $configHelper->getConfigValue('browsers/default/browser'));
        $this->assertInternalType('int', $configHelper->getConfigValue('browsers/default/port'));
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setArea
     */
    public function testSetArea()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $configHelper->setArea('frontend'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $configHelper->setArea('admin'));
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setArea
     */
    public function testSetAreaOutOfRangeException()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $this->setExpectedException('OutOfRangeException', 'Area with name');
        $configHelper->setArea('invalid-area');
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getArea
     */
    public function testGetArea()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setArea('frontend');
        $this->assertInternalType('string', $configHelper->getArea());
        $this->assertNotEmpty($configHelper->getArea());
        $this->assertEquals('frontend', $configHelper->getArea());
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getBaseUrl
     */
    public function testGetBaseUrl()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $this->assertInternalType('string', $configHelper->getBaseUrl());
        $this->assertNotEmpty($configHelper->getBaseUrl());

        $configHelper->setArea('admin');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/',
                            $configHelper->getBaseUrl());

        $configHelper->setArea('frontend');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/',
                            $configHelper->getBaseUrl());
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getDefaultLogin
     */
    public function testGetDefaultLogin()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setArea('admin');
        $login = $configHelper->getDefaultLogin();
        $this->assertInternalType('string', $login);
        $this->assertNotEmpty($login);
        $configHelper->setArea('frontend');
        $login = $configHelper->getDefaultLogin();
        $this->assertInternalType('string', $login);
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getDefaultPassword
     */
    public function testGetDefaultPassword()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setArea('admin');
        $password = $configHelper->getDefaultPassword();
        $this->assertInternalType('string', $password);
        $this->assertNotEmpty($password);
        $configHelper->setArea('frontend');
        $password = $configHelper->getDefaultPassword();
        $this->assertInternalType('string', $password);
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getBasePath
     */
    public function testGetBasePath()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setApplication('mage');
        $configHelper->setArea('admin');
        $uimapPath = $configHelper->getBasePath();
        $this->assertInternalType('string', $uimapPath);
        $this->assertSame($uimapPath, 'admin');
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getFixturesFallbackOrder
     */
    public function testGetFixturesFallbackOrder()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setApplication('mage');
        $fallbackOrder = $configHelper->getFixturesFallbackOrder();
        $this->assertInternalType('array', $fallbackOrder);
        $this->assertSame($fallbackOrder, array('default'));
        $configHelper->setApplication('enterprise');
        $fallbackOrder = $configHelper->getFixturesFallbackOrder();
        $this->assertInternalType('array', $fallbackOrder);
        $this->assertSame($fallbackOrder, array('default', 'enterprise'));
    }

    /**
     * @covers Mage_Selenium_Helper_Config::getHelpersFallbackOrder
     */
    public function testGetHelpersFallbackOrder()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setApplication('mage');
        $fallbackOrder = $configHelper->getHelpersFallbackOrder();
        $this->assertInternalType('array', $fallbackOrder);
        $this->assertSame($fallbackOrder, array('Core'));
        $configHelper->setApplication('enterprise');
        $fallbackOrder = $configHelper->getHelpersFallbackOrder();
        $this->assertInternalType('array', $fallbackOrder);
        $this->assertSame($fallbackOrder, array('Core', 'Enterprise'));
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setScreenshotDir
     * @covers Mage_Selenium_Helper_Config::getScreenshotDir
     */
    public function testGetSetScreenshotDir()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        //Default directory
        $this->assertEquals(SELENIUM_TESTS_SCREENSHOTDIR, $configHelper->getScreenshotDir());
        //Create a directory
        $parentDir = 'test testGetSetScreenshotDir';
        $dirName = $parentDir . '/ss-dir-test';
        $this->assertTrue(!is_dir($dirName) || (rmdir($dirName) && rmdir($parentDir)));
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $configHelper->setScreenshotDir($dirName));
        $this->assertTrue(is_dir($dirName));
        $this->assertEquals($dirName, $configHelper->getScreenshotDir());
        //Set to existing directory
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $configHelper->setScreenshotDir($dirName));
        $this->assertTrue(is_dir($dirName));
        $this->assertEquals($dirName, $configHelper->getScreenshotDir());
        //Cleanup
        rmdir($dirName); rmdir($parentDir);
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setScreenshotDir
     * @depends testGetSetScreenshotDir
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetScreenshotDirInvalidPathException()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setScreenshotDir('\0//////////!#$@%*^&:?');
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setScreenshotDir
     * @depends testGetSetScreenshotDir
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetScreenshotDirInvalidParameterException()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setScreenshotDir(null);
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setLogDir
     * @covers Mage_Selenium_Helper_Config::getLogDir
     */
    public function testGetSetLogDir()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        //Default directory
        $this->assertEquals(SELENIUM_TESTS_LOGS, $configHelper->getLogDir());
        //Create a directory
        $dirName = 'log-dir-test';
        $this->assertTrue(!is_dir($dirName) || rmdir($dirName));
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $configHelper->setLogDir($dirName));
        $this->assertTrue(is_dir($dirName));
        $this->assertEquals($dirName, $configHelper->getLogDir());
        //Set to existing directory
        $this->assertInstanceOf('Mage_Selenium_Helper_Config', $configHelper->setLogDir($dirName));
        $this->assertTrue(is_dir($dirName));
        $this->assertEquals($dirName, $configHelper->getLogDir());
        //Cleanup
        rmdir($dirName);
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setLogDir
     * @depends testGetSetLogDir
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetLogDirInvalidPathException()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setLogDir('!#$@%*^&:?');
    }

    /**
     * @covers Mage_Selenium_Helper_Config::setLogDir
     * @depends testGetSetLogDir
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetLogDirInvalidParameterException()
    {
        $configHelper = new Mage_Selenium_Helper_Config($this->_config);
        $configHelper->setLogDir(null);
    }
}