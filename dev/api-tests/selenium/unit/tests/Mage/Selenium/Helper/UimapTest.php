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
class Mage_Selenium_Helper_UimapTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Helper_Uimap::__construct
     */
    public function test__construct()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Uimap', $uimapHelper);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimap
     */
    public function testGetUimap()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);

        $uimap = $uimapHelper->getUimap('admin');
        $this->assertInternalType('array', $uimap);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimap
     *
     * @expectedException OutOfRangeException
     */
    public function testGetUimapException()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);
        $uimap = $uimapHelper->getUimap('invalid_area');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPage
     */
    public function testGetUimapPage()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);

        $uipage = $uimapHelper->getUimapPage('admin', 'create_customer');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPage('admin', 'wrong_name');
        $this->assertNull($uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMca()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);

        $uipage = $uimapHelper->getUimapPageByMca('admin', 'customer/new/');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPageByMca('admin', '');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPageByMca('admin', 'wrong-path');
        $this->assertNull($uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getMainForm
     */
    public function testGetMainForm()
    {
        $uipage = $this->getUimapPage('admin', 'create_customer');
        $mainForm = $uipage->getMainForm();
        $this->assertInstanceOf('Mage_Selenium_Uimap_Form', $mainForm);
    }
}