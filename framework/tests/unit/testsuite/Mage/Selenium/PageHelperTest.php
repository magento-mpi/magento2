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
 * Unit test for Page helper
 */
class Mage_Selenium_Helper_PageTest extends Mage_PHPUnit_TestCase
{
    /**
     * Testing Mage_Selenium_Helper_Page::validateCurrentPage()
     */
    public function testValidateCurrentPage()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $_pageHelper->validateCurrentPage());
    }

    /**
     * Testing Mage_Selenium_Helper_Page::validationFailed()
     */
    public function testValidationFailed()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_pageHelper->validateCurrentPage();
        $this->assertFalse($_pageHelper->validationFailed());
    }

    /**
     * Testing Mage_Selenium_Helper_Page::setApplicationHelper()
     */
    public function testSetApplicationHelper()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_suitHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $_pageHelper->setApplicationHelper($_suitHelper));
    }

    /**
     * Testing Mage_Selenium_Helper_Page::getPageUrl()
     */
    public function testGetPageUrl()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_suitHelper = new Mage_Selenium_Helper_Application($this->_config);
        $_suitHelper->setArea('admin');
        $_pageHelper->setApplicationHelper($_suitHelper);

        $this->assertStringEndsWith('/control/permissions_user/', $_pageHelper->getPageUrl('manage_admin_users'));
        //$this->assertFalse($_pageHelper->getPageUrl(''));
    }

    /**
     * Test Mage_Selenium_Helper_Page::getPageUrl() on uninitialized object
     *
     * @expectedException Mage_Selenium_Exception
     */
    public function testGetPageUrlUninitializedException()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $this->assertStringEndsWith('/control/permissions_user/', $_pageHelper->getPageUrl('manage_admin_users'));
    }

    /**
     * Test Mage_Selenium_Helper_Page::getPageUrl() wrong Area
     *
     * @expectedException OutOfRangeException
     */
    public function testGetPageUrlWrongAreaException()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_suitHelper = new Mage_Selenium_Helper_Application($this->_config);
        $_suitHelper->setArea('admin-bla-bla-bla');
        $_pageHelper->setApplicationHelper($_suitHelper);

        $this->assertFalse($_pageHelper->getPageUrl('some_page'));
    }

    /**
     * Test Mage_Selenium_Helper_Page::getPageUrl() wrong url
     *
     * @expectedException Mage_Selenium_Exception
     */
    public function testGetPageUrlWrongUrlException()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_suitHelper = new Mage_Selenium_Helper_Application($this->_config);
        $_suitHelper->setArea('admin');
        $_pageHelper->setApplicationHelper($_suitHelper);

        $this->assertFalse($_pageHelper->getPageUrl('some_page'));
    }

}
