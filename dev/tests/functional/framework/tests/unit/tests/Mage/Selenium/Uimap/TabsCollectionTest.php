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
class Mage_Selenium_Uimap_TabsCollectionTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $instance = new Mage_Selenium_Uimap_TabsCollection();
        $this->assertInstanceOf('Mage_Selenium_Uimap_TabsCollection', $instance);
    }

    /**
     * @covers Mage_Selenium_TestCase::getTab
     */
    public function testGetTabNotNull()
    {
        $instance = new Mage_Selenium_Uimap_TabsCollection();
        $tabValue = array();
        $tab = new Mage_Selenium_Uimap_Tab('tabId', $tabValue);
        $instance['testName'] = $tab;
        $this->assertEquals($instance->getTab('testName'), $tab);
    }

    /**
     * @covers Mage_Selenium_TestCase::getTab
     */
    public function testGetTabNull()
    {
        $instance = new Mage_Selenium_Uimap_TabsCollection();
        $tabValue = array();
        $tab = new Mage_Selenium_Uimap_Tab('tabId', $tabValue);
        $instance['testName'] = $tab;
        $this->assertEquals($instance->getTab('notExistingTab'), null);
    }

}