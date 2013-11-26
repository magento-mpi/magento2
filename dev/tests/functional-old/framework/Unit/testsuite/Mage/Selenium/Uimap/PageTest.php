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
class Mage_Selenium_Uimap_PageTest extends Unit_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Uimap_Page::__construct
     */
    public function test__construct()
    {
        $pageContainer = array('mca' => 'mca', 'title' => 'title');
        $uipage = new Mage_Selenium_Uimap_Page('pageId', $pageContainer);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Uimap_Page::getId
     */
    public function testGetId()
    {
        $pageId = 'testId';
        $pageContainer = array('mca' => 'mca', 'title' => 'title');
        $uipage = new Mage_Selenium_Uimap_Page($pageId, $pageContainer);
        $this->assertEquals($uipage->getPageId(), $pageId);
    }

    /**
     * @covers Mage_Selenium_Uimap_Page::getMainButtons
     */
    public function testGetMainButtons()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_testConfig);
        $pageContainers = $fileHelper
            ->loadYamlFile(SELENIUM_TESTS_BASEDIR . '/fixture/default/core/Mage/UnitTest/uimap/frontend/UnitTests.yml');
        $uipage = new Mage_Selenium_Uimap_Page('pageId', $pageContainers['get_main_buttons']);
        $mainButtons = $uipage->getMainButtons();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $mainButtons);

        $pageContainer = array('mca' => 'mca', 'title' => 'title');
        $uipage = new Mage_Selenium_Uimap_Page('pageId', $pageContainer);
        $mainButtons = $uipage->getMainButtons();
        $this->assertNull($mainButtons);
    }
}