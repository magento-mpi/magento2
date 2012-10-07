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
class Mage_Selenium_Uimap_TabTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Uimap_Tab::__construct
     * @covers Mage_Selenium_Uimap_Tab::getTabId
     */
    public function test__construct()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $dataArray = $fileHelper->loadYamlFile(
            SELENIUM_TESTS_BASEDIR . '/fixture/default/core/Mage/UnitTest/data/UimapTests.yml'
        );
        $tabContainer = $dataArray['tab'];
        $tabId = 'tabId';
        $instance = new Mage_Selenium_Uimap_Tab($tabId, $tabContainer);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Tab', $instance);
        $this->assertEquals($tabId, $instance->getTabId());
    }

    /**
     * @covers Mage_Selenium_Uimap_Tab::getFieldsetNames
     */
    public function testGetFieldsetNames()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $dataArray = $fileHelper->loadYamlFile(
            SELENIUM_TESTS_BASEDIR . '/fixture/default/core/Mage/UnitTest/data/UimapTests.yml'
        );
        $tabContainer = $dataArray['tab'];
        $instance = new Mage_Selenium_Uimap_Tab('tabId', $tabContainer);
        $elements = $instance->getFieldsetNames();
        $this->assertInternalType('array', $elements);
        $this->assertContains('first_fieldset', $elements);
        $this->assertContains('second_fieldset', $elements);

        $tabContainer = array();
        $instance = new Mage_Selenium_Uimap_Tab('tabId', $tabContainer);
        $elements = $instance->getFieldsetNames();
        $this->assertInternalType('array', $elements);
        $this->assertEmpty($elements);
    }
}