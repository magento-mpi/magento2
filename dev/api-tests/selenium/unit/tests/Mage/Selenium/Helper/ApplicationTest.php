<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_Helper_ApplicationTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $applicationHelper);
    }

    /**
     * @covers Mage_Selenium_Helper_Application::setArea
     * @depends test__construct
     */
    public function testSetArea()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $applicationHelper->setArea('frontend'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $applicationHelper->setArea('admin'));
    }

    /**
     * @covers Mage_Selenium_Helper_Application::setArea
     * @depends test__construct
     */
    public function testSetAreaOutOfRangeException()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->setExpectedException('OutOfRangeException');
        $applicationHelper->setArea('invalid-area');
    }

    /**
     * @covers Mage_Selenium_Helper_Application::getArea
     * @depends test__construct
     */
    public function testGetArea()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $applicationHelper->setArea('frontend');
        $this->assertInternalType('string', $applicationHelper->getArea());
        $this->assertNotEmpty($applicationHelper->getArea());
        $this->assertEquals('frontend', $applicationHelper->getArea());
    }

    /**
     * @covers Mage_Selenium_Helper_Application::getBaseUrl
     * @depends test__construct
     */
    public function testGetBaseUrl()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInternalType('string', $applicationHelper->getBaseUrl());
        $this->assertNotEmpty($applicationHelper->getBaseUrl());

        $applicationHelper->setArea('admin');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $applicationHelper->getBaseUrl());

        $applicationHelper->setArea('frontend');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $applicationHelper->getBaseUrl());
    }

    /**
     * @covers Mage_Selenium_Helper_Application::isAdmin
     * @depends test__construct
     */
    public function testIsAdmin()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);

        $applicationHelper->setArea('frontend');
        $this->assertFalse($applicationHelper->isAdmin());

        $applicationHelper->setArea('admin');
        $this->assertTrue($applicationHelper->isAdmin());
    }

    /**
     * @covers Mage_Selenium_Helper_Application::getDefaultAdminUsername
     * @depends test__construct
     */
    public function testGetDefaultAdminUsername()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInternalType('string', $applicationHelper->getDefaultAdminUsername());
        $this->assertNotEmpty($applicationHelper->getDefaultAdminUsername());
    }

    /**
     * @covers Mage_Selenium_Helper_Application::getDefaultAdminPassword
     * @depends test__construct
     */
    public function testGetDefaultAdminPassword()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInternalType('string', $applicationHelper->getDefaultAdminPassword());
        $this->assertNotEmpty($applicationHelper->getDefaultAdminPassword());
    }
}
?>
