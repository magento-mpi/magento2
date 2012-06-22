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
 * Unit test for ApplicationHelper
 */
class Mage_Selenium_Helper_ApplicationTest extends Mage_PHPUnit_TestCase
{

    /**
     * Selenium ApplicationHelper instance
     *
     * @var Mage_Selenium_Helper_Application
     */
    protected $_applicationHelper = null;

    public function  __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
    }

    /**
     * Testing Mage_Selenium_Helper_Application::setArea()
     */
    public function testSetArea()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $this->_applicationHelper->setArea('frontend'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $this->_applicationHelper->setArea('admin'));
    }

    /**
     * Testing OutOfRangeException throwing in Mage_Selenium_Helper_Application::setArea()
     */
    public function testSetAreaOutOfRangeException()
    {
        $this->setExpectedException('OutOfRangeException');
        $this->_applicationHelper->setArea('invalid-area');
    }

    /**
     * Testing Mage_Selenium_Helper_Application::getArea()
     */
    public function testGetArea()
    {
        $this->_applicationHelper->setArea('frontend');

        $this->assertInternalType('string', $this->_applicationHelper->getArea());
        $this->assertNotEmpty($this->_applicationHelper->getArea());
        $this->assertEquals('frontend', $this->_applicationHelper->getArea());
    }

    /**
     * Testing Mage_Selenium_Helper_Application::getBaseUrl()
     */
    public function testGetBaseUrl()
    {
        $this->assertInternalType('string', $this->_applicationHelper->getBaseUrl());
        $this->assertNotEmpty($this->_applicationHelper->getBaseUrl());

        $this->_applicationHelper->setArea('admin');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $this->_applicationHelper->getBaseUrl());

        $this->_applicationHelper->setArea('frontend');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $this->_applicationHelper->getBaseUrl());
    }

    /**
     * Testing Mage_Selenium_Helper_Application::isAdmin()
     */
    public function testIsAdmin()
    {
        $this->_applicationHelper->setArea('frontend');
        $this->assertFalse($this->_applicationHelper->isAdmin());

        $this->_applicationHelper->setArea('admin');
        $this->assertTrue($this->_applicationHelper->isAdmin());
    }

    /**
     * Testing Mage_Selenium_Helper_Application::getDefaultAdminUsername()
     */
    public function testGetDefaultAdminUsername()
    {
        $this->assertInternalType('string', $this->_applicationHelper->getDefaultAdminUsername());
        $this->assertNotEmpty($this->_applicationHelper->getDefaultAdminUsername());
    }

    /**
     * Testing Mage_Selenium_Helper_Application::getDefaultAdminPassword()
     */
    public function testGetDefaultAdminPassword()
    {
        $this->assertInternalType('string', $this->_applicationHelper->getDefaultAdminPassword());
        $this->assertNotEmpty($this->_applicationHelper->getDefaultAdminPassword());
    }
}
?>
