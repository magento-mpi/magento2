<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Selenium_Uimap_FormTest extends Unit_PHPUnit_TestCase
{
    public function test__construct()
    {
        $formContainer = array();
        $instance = new Mage_Selenium_Uimap_Form($formContainer);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Form', $instance);
    }

    /**
     * @covers Mage_Selenium_Uimap_Form::getTab
     */
    public function testGetTab()
    {
        $uimapHelper = $this->_testConfig->getHelper('uimap');
        $page = $uimapHelper->getUimapPage('admin', 'create_customer');
        $tab = $page->getMainForm()->getTabs()->getTab('addresses');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Tab', $tab);
    }
}