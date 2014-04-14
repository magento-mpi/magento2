<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Selenium_Uimap_ElementsCollectionTest extends Unit_PHPUnit_TestCase
{
    public function test__construct()
    {
        $objects = array();
        $instance = new Mage_Selenium_Uimap_ElementsCollection('elementType', $objects);
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $instance);
    }

    /**
     * @covers Mage_Selenium_Uimap_ElementsCollection::__get
     */
    public function test__get()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $page = $uimapHelper->getUimapPage('admin', 'create_customer');
        $buttons = $page->getMainForm()->getAllButtons();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
        foreach ($buttons as $buttonXPath) {
            $this->assertNotEmpty($buttonXPath);
        }
    }

    /**
     * @covers Mage_Selenium_Uimap_ElementsCollection::get
     */
    public function testGet()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $page = $uimapHelper->getUimapPage('admin', 'create_customer');
        $button = $page->getAllButtons()->get('save_customer');
        $this->assertInternalType('string', $button);
    }

    /**
     * @covers Mage_Selenium_Uimap_ElementsCollection::getType
     */
    public function testGetType()
    {
        $instance = new Mage_Selenium_Uimap_ElementsCollection('elementType', array());
        $this->assertEquals('elementType', $instance->getType());

        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $page = $uimapHelper->getUimapPage('admin', 'create_customer');
        $fieldsets = $page->getMainForm()->getAllFieldsets();
        $fieldsetsType = $fieldsets->getType();
        $this->assertEquals('fieldsets', $fieldsetsType);
    }
}