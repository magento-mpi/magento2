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
class Mage_Selenium_Helper_UimapTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Helper_Uimap::__construct
     */
    public function test__construct()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $this->assertInstanceOf('Mage_Selenium_Helper_Uimap', $uimapHelper);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPage
     */
    public function testGetUimapPage()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $uipage = $uimapHelper->getUimapPage('admin', 'create_customer');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPage
     */
    public function testGetUimapPageWrongPageException()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'Cannot find page');
        $uimapHelper->getUimapPage('admin', 'wrong_name');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMca()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $uipage = $uimapHelper->getUimapPageByMca('admin', 'customer/new/');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     * @expectedException OutOfRangeException
     * @expectedExceptionMessage catalog_product/new/set/9/type/simple" in "admin" area
     */
    public function testGetUimapPageByMcaWithParamNegative()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $uimapHelper->getUimapPageByMca('admin', 'catalog_product/new/set/9/type/simple/',
                                        $this->_config->getHelper('params'));
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMcaWithParam()
    {
        $this->_config->getHelper('params')->setParameter('setId', 9);
        $this->_config->getHelper('params')->setParameter('productType', 'simple');
        $uimapHelper = $this->_config->getHelper('uimap');
        $uipage = $uimapHelper->getUimapPageByMca('admin', 'catalog_product/new/set/9/type/simple/',
                                                  $this->_config->getHelper('params'));
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMcaForPaypal()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $uipage = $uimapHelper->getUimapPageByMca('paypal_developer',
                                                  'cgi-bin/devscr?__track=_home:login/main:_login-submit');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMcaWrongPageException()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'Cannot find page with mca');
        $uimapHelper->getUimapPageByMca('admin', 'wrong-path');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageUrl
     */
    public function testGetPageUrl()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $this->assertStringEndsWith('/home', $uimapHelper->getPageUrl('frontend', 'home'));
        $this->assertStringEndsWith('/permissions_user/', $uimapHelper->getPageUrl('admin', 'manage_admin_users'));
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageUrl
     */
    public function testGetPageUrlWrongPageException()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'Cannot find page');
        $uimapHelper->getPageUrl('admin', 'not_existing_page');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageUrl
     */
    public function testGetPageUrlEmptyPageException()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'Cannot find page');
        $uimapHelper->getPageUrl('admin', '');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageUrl
     */
    public function testGetPageUrlWrongAreaException()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'area do not exist');
        $uimapHelper->getPageUrl('admin-bla-bla-bla', 'not_existing_page');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageMca
     */
    public function testGetPageMca()
    {
        $uimapHelper = $this->_config->getHelper('uimap');
        $this->assertEquals('home', $uimapHelper->getPageMca('frontend', 'home'));
        $this->assertEquals('permissions_user/', $uimapHelper->getPageMca('admin', 'manage_admin_users'));
    }
}