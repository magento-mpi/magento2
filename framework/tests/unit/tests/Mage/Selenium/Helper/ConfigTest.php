<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $configHelper->getBaseUrl());

        $configHelper->setArea('frontend');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $configHelper->getBaseUrl());
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
}
