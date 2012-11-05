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
class Mage_Selenium_Helper_UimapTest extends Unit_PHPUnit_TestCase
{
    const ERROR_REQUIRED_FIELD_MESSAGE = "some-x-path/following-sibling::*[text()='This is a required field.' and not(contains(@style,'display: none;'))]";

    /**
     * @covers Mage_Selenium_Helper_Uimap::__construct
     */
    public function test__construct()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->assertInstanceOf('Mage_Selenium_Helper_Uimap', $uimapHelper);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPage
     */
    public function testGetUimapPage()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $uipage = $uimapHelper->getUimapPage('admin', 'create_customer');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPage
     */
    public function testGetUimapPageWrongPageException()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'Cannot find page');
        $uimapHelper->getUimapPage('admin', 'wrong_name');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMca()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
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
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $uimapHelper->getUimapPageByMca('admin', 'catalog_product/new/set/9/type/simple/',
            $this->_testConfig->getHelper('params'));
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMcaWithParam()
    {
        $this->_testConfig->getHelper('params')->setParameter('setId', 9);
        $this->_testConfig->getHelper('params')->setParameter('productType', 'simple');
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $uipage = $uimapHelper->getUimapPageByMca('admin', 'catalog_product/new/set/9/type/simple/',
            $this->_testConfig->getHelper('params'));
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMcaForPaypal()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $uipage = $uimapHelper->getUimapPageByMca('paypal_developer',
            'cgi-bin/devscr?__track=_home:login/main:_login-submit');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMcaWrongPageException()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'Cannot find page with mca');
        $uimapHelper->getUimapPageByMca('admin', 'wrong-path');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageUrl
     */
    public function testGetPageUrl()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->assertStringEndsWith('/home', $uimapHelper->getPageUrl('frontend', 'home'));
        $this->assertStringEndsWith('/getMainButtons/', $uimapHelper->getPageUrl('frontend', 'get_main_buttons'));
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageUrl
     */
    public function testGetPageUrlWrongPageException()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'Cannot find page');
        $uimapHelper->getPageUrl('admin', 'not_existing_page');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageUrl
     */
    public function testGetPageUrlEmptyPageException()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'Cannot find page');
        $uimapHelper->getPageUrl('admin', '');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageUrl
     */
    public function testGetPageUrlWrongAreaException()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->setExpectedException('OutOfRangeException', 'area do not exist');
        $uimapHelper->getPageUrl('admin-bla-bla-bla', 'not_existing_page');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getPageMca
     */
    public function testGetPageMca()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->assertEquals('home', $uimapHelper->getPageMca('frontend', 'home'));
        $this->assertEquals('getMainButtons/', $uimapHelper->getPageMca('frontend', 'get_main_buttons'));
    }

    /**
     * Test UIMap helper
     */
    public function testUimapHelper()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->assertNotNull($uimapHelper);

        $uimap = $uimapHelper->getAreaUimaps('admin');
        $this->assertNotNull($uimap);
        $this->assertInternalType('array', $uimap);

        $uipage = $uimapHelper->getUimapPage('admin', 'create_customer');
        $this->assertNotNull($uipage);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPageByMca('admin', 'customer/new/');
        $this->assertNotNull($uipage);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * Test all UIMap classes
     */
    public function testUimapClasses()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        ;
        $uipage = $uimapHelper->getUimapPage('admin', 'create_customer');
        $this->assertNotNull($uipage);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $fieldsets = $uipage->getMainForm()->getAllFieldsets();
        $this->assertNotNull($fieldsets);
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $fieldsets);
        $this->assertGreaterThanOrEqual(1, count($fieldsets));
        $this->assertEquals('fieldsets', $fieldsets->getType());

        $buttons = $uipage->getMainForm()->getAllButtons();
        $this->assertNotNull($buttons);
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
        foreach ($buttons as $buttonXPath) {
            $this->assertNotEmpty($buttonXPath);
        }

        $tabs = $uipage->getMainForm()->getTabs();
        $this->assertNotNull($tabs);
        $this->assertInstanceOf('Mage_Selenium_Uimap_TabsCollection', $tabs);
        $this->assertGreaterThanOrEqual(1, count($tabs));

        $tab = $tabs->getTab('addresses');
        $this->assertNotNull($tabs);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Tab', $tab);

        $button = $uipage->getAllButtons()->get('save_customer');
        $this->assertNotNull($button);
        $this->assertInternalType('string', $button);

        $field = $uipage->findField('first_name');
        $this->assertNotNull($field);
        $this->assertInternalType('string', $field);

        $message = $uipage->findMessage('success_saved_customer');
        $this->assertNotNull($message);
        $this->assertInternalType('string', $message);
    }

    /**
     * Test UIMap params helper
     */
    public function testUimapParams()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $this->assertNotNull($uimapHelper);

        $uipage = $uimapHelper->getUimapPage('admin', 'edit_admin_user');
        $this->assertNotNull($uipage);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $params = $this->_testConfig->getHelper('params');
        $params->setParameter('id', 100);
        $params->setParameter('elementTitle', 'Alex');
        $params->setParameter('fieldXpath', 'some-x-path');

        $uipage = $uimapHelper->getUimapPageByMca('admin', 'user/edit/user_id/100/', $params);
        $this->assertNotNull($uipage);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $this->assertEquals('user/edit/user_id/100/', $uipage->getMca($params));
        $this->assertEquals('Alex / Users / Permissions / System / Magento Admin', $uipage->getTitle($params));

        $this->assertEquals(self::ERROR_REQUIRED_FIELD_MESSAGE, $uipage->findMessage('empty_required_field', $params));
        $this->assertEquals(self::ERROR_REQUIRED_FIELD_MESSAGE,
            $uipage->getMessages()->get('empty_required_field', $params));
    }
}