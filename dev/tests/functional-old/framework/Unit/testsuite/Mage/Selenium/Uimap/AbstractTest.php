<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Selenium_Uimap_AbstractTest extends Unit_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Uimap_Abstract::__call
     */
    public function test__call()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $page = $uimapHelper->getUimapPage('admin', 'create_customer');

        //Test getAll
        $buttons = $page->getAllButtons();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $buttons);

        $fieldsets = $page->getMainForm()->getAllFieldsets();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $fieldsets);
        $this->assertGreaterThanOrEqual(1, count($fieldsets));

        $buttons = $page->getMainForm()->getAllButtons();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $buttons);

        //Test get
        $tabs = $page->getMainForm()->getTabs();
        $this->assertInstanceOf('Mage_Selenium_Uimap_TabsCollection', $tabs);
        $this->assertGreaterThanOrEqual(1, count($tabs));

        //Test find
        $field = $page->findField('first_name');
        $this->assertInternalType('string', $field);

        $message = $page->findMessage('success_saved_customer');
        $this->assertInternalType('string', $message);
    }
}