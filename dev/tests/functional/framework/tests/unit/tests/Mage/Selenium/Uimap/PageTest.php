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
class Mage_Selenium_Uimap_PageTest extends Mage_PHPUnit_TestCase
{
    private function getPageContainerData()
    {
        return array(
            'mca' => '',
            'title' => ''
        );
    }

    /**
     * @covers Mage_Selenium_Uimap_Page::__construct
     */
    public function test__construct()
    {
        $uipage = new Mage_Selenium_Uimap_Page('pageId', $this->getPageContainerData());
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Uimap_Page::getId
     */
    public function testGetId()
    {
        $pageId = 'testId';
        $uipage = new Mage_Selenium_Uimap_Page($pageId, $this->getPageContainerData());
        $this->assertEquals($uipage->getPageId(), $pageId);
    }

    /**
     * @covers Mage_Selenium_Uimap_Page::getMainButtons
     */
    public function testGetMainButtons()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $pageContainers = $fileHelper->loadYamlFile(
            SELENIUM_TESTS_BASEDIR . '/fixture/default/core/Mage/UnitTest/uimap/frontend/UnitTests.yml'
        );
        $uipage = new Mage_Selenium_Uimap_Page(
            'pageId',
            array_merge($this->getPageContainerData(),
            $pageContainers['get_main_buttons'])
        );
        $mainButtons = $uipage->getMainButtons();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $mainButtons);

        $pageContainers = array();
        $uipage = new Mage_Selenium_Uimap_Page('pageId', $pageContainers);
        $mainButtons = $uipage->getMainButtons();
        $this->assertNull($mainButtons);
    }
}